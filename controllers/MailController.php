<?php

namespace humhub\modules\mail\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\HttpException;
use humhub\components\Controller;
use humhub\modules\file\models\File;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\User\models\User;
use humhub\modules\mail\models\forms\InviteRecipient;
use humhub\modules\mail\models\forms\ReplyMessage;
use humhub\modules\mail\models\forms\CreateMessage;

/**
 * MailController provides messaging actions.
 *
 * @package humhub.modules.mail.controllers
 * @since 0.5
 */
class MailController extends Controller
{

    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::className(),
            ]
        ];
    }

    /**
     * Overview of all messages
     */
    public function actionIndex()
    {
        // Initially displayed message
        $messageId = (int) Yii::$app->request->get('id');

        $query = UserMessage::find();
        $query->joinWith('message');
        $query->where(['user_message.user_id' => Yii::$app->user->id]);
        $query->orderBy('message.updated_at DESC');

        $countQuery = clone $query;
        $messageCount = $countQuery->count();
        $pagination = new \yii\data\Pagination(['totalCount' => $messageCount, 'pageSize' => 25]);
        $query->offset($pagination->offset)->limit($pagination->limit);

        $userMessages = $query->all();

        // If no messageId is given, use first if available
        if (($messageId == "" || $this->getMessage($messageId) === null) && $messageCount != 0) {
            $messageId = $userMessages[0]->message->id;
        }

        return $this->render('/mail/index', array(
                    'userMessages' => $userMessages,
                    'messageId' => $messageId,
                    'pagination' => $pagination
        ));
    }

    /**
     * Overview of all messages
     * Used by MailNotificationWidget to display all recent messages
     */
    public function actionNotificationList()
    {
        $query = UserMessage::find();
        $query->joinWith('message');
        $query->where(['user_message.user_id' => Yii::$app->user->id]);
        $query->orderBy('message.updated_at DESC');
        $query->limit(5);

        return $this->renderAjax('notificationList', array('userMessages' => $query->all()));
    }

    /**
     * Shows a Message Thread
     */
    public function actionShow()
    {
        // Load Message
        $id = (int) Yii::$app->request->get('id');
        $message = $this->getMessage($id);

        if ($message == null) {
            throw new HttpException(404, 'Could not find message!');
        }

        // Reply Form
        $replyForm = new ReplyMessage();
        if ($replyForm->load(Yii::$app->request->post()) && $replyForm->validate()) {
            // Attach Message Entry
            $messageEntry = new MessageEntry();
            $messageEntry->message_id = $message->id;
            $messageEntry->user_id = Yii::$app->user->id;
            $messageEntry->content = $replyForm->message;
            $messageEntry->save();
            $messageEntry->notify();
            File::attachPrecreated($messageEntry, Yii::$app->request->post('fileUploaderHiddenGuidField'));

            return $this->redirect(['index', 'id' => $message->id]);
        }

        // Marks message as seen
        $message->seen(Yii::$app->user->id);

        return $this->renderAjax('/mail/show', [
                    'message' => $message,
                    'replyForm' => $replyForm,
        ]);
    }

    /**
     * Shows the invite user form
     *
     * This method invite new people to the conversation.
     */
    public function actionAddUser()
    {
        $id = Yii::$app->request->get('id');
        $message = $this->getMessage($id);

        if ($message == null) {
            throw new HttpException(404, 'Could not find message!');
        }

        // Invite Form
        $inviteForm = new InviteRecipient();
        $inviteForm->message = $message;

        if ($inviteForm->load(Yii::$app->request->post()) && $inviteForm->validate()) {
            foreach ($inviteForm->getRecipients() as $user) {
                // Attach User Message
                $userMessage = new UserMessage();
                $userMessage->message_id = $message->id;
                $userMessage->user_id = $user->id;
                $userMessage->is_originator = 0;
                $userMessage->save();
                $message->notify($user);
            }
            return $this->htmlRedirect(['index', 'id' => $message->id]);
        }

        return $this->renderAjax('/mail/adduser', array('inviteForm' => $inviteForm));
    }

    /**
     * Creates a new Message
     * and redirects to it.
     */
    public function actionCreate()
    {
        $userGuid = Yii::$app->request->get('userGuid');
        $model = new CreateMessage();

        // Preselect user if userGuid is given
        if ($userGuid != "") {
            $user = User::findOne(array('guid' => $userGuid));
            if (isset($user)) {
                $model->recipient = $user->guid;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            // Create new Message
            $message = new Message();
            $message->title = $model->title;
            $message->save();

            // Attach Message Entry
            $messageEntry = new MessageEntry();
            $messageEntry->message_id = $message->id;
            $messageEntry->user_id = Yii::$app->user->id;
            $messageEntry->content = $model->message;
            $messageEntry->save();
            File::attachPrecreated($messageEntry, Yii::$app->request->post('fileUploaderHiddenGuidField'));

            // Attach also Recipients
            foreach ($model->getRecipients() as $recipient) {
                $userMessage = new UserMessage();
                $userMessage->message_id = $message->id;
                $userMessage->user_id = $recipient->id;
                $userMessage->save();
            }

            // Inform recipients (We need to add all before)
            foreach ($model->getRecipients() as $recipient) {
                $message->notify($recipient);
            }

            // Attach User Message
            $userMessage = new UserMessage();
            $userMessage->message_id = $message->id;
            $userMessage->user_id = Yii::$app->user->id;
            $userMessage->is_originator = 1;
            $userMessage->last_viewed = new \yii\db\Expression('NOW()');
            $userMessage->save();

            return $this->htmlRedirect(['index', 'id' => $message->id]);
        }

        return $this->renderAjax('create', array('model' => $model));
    }

    /**
     * Leave Message / Conversation
     *
     * Leave is only possible when at least to people are in the
     * conversation.
     */
    public function actionLeave()
    {
        $this->forcePostRequest();

        $id = Yii::$app->request->get('id');
        $message = $this->getMessage($id);

        if ($message == null) {
            throw new HttpException(404, 'Could not find message!');
        }

        $message->leave(Yii::$app->user->id);

        if (Yii::$app->request->isAjax) {
            return $this->htmlRedirect(['index']);
        } else {
            return $this->redirect(['index']);
        }
    }

    /**
     * Edits Entry Id
     */
    public function actionEditEntry()
    {
        $messageEntryId = (int) Yii::$app->request->get('messageEntryId');
        $entry = MessageEntry::findOne(['id' => $messageEntryId]);

        // Check if message entry exists and it´s by this user
        if ($entry == null || $entry->user_id != Yii::$app->user->id) {
            throw new HttpException(404, 'Could not find message entry!');
        }
        if ($entry->load(Yii::$app->request->post()) && $entry->validate()) {
            // ?
            //$entry->content = $_POST['MessageEntry']['content'];
            $entry->save();
            File::attachPrecreated($entry, Yii::$app->request->get('fileUploaderHiddenGuidField'));

            return $this->htmlRedirect(['index', 'id' => $entry->message->id]);
        }

        return $this->renderAjax('editEntry', array('entry' => $entry));
    }

    /**
     * Delete Entry Id
     *
     * Users can delete the own message entries.
     */
    public function actionDeleteEntry()
    {
        $this->forcePostRequest();

        $messageEntryId = (int) Yii::$app->request->get('messageEntryId');
        $entry = MessageEntry::findOne(['id' => $messageEntryId]);

        // Check if message entry exists and it´s by this user
        if ($entry == null || $entry->user_id != Yii::$app->user->id) {
            throw new HttpException(404, 'Could not find message entry!');
        }

        $entry->message->deleteEntry($entry);

        if (Yii::$app->request->isAjax) {
            return $this->htmlRedirect(['index', 'id' => $entry->message_id]);
        } else {
            return $this->redirect(['index', 'id' => $entry->message_id]);
        }
    }

    /**
     * Returns the number of new messages as JSON
     */
    public function actionGetNewMessageCountJson()
    {
        Yii::$app->response->format = 'json';

        $json = array();
        $json['newMessages'] = UserMessage::getNewMessageCount();

        return $json;
    }

    /**
     * Returns the Message Model by given Id
     * Also an access check will be performed.
     *
     * If insufficed privileges or not found null will be returned.
     *
     * @param int $id
     */
    private function getMessage($id)
    {
        $message = Message::findOne(['id' => $id]);

        if ($message != null) {

            $userMessage = UserMessage::findOne([
                        'user_id' => Yii::$app->user->id,
                        'message_id' => $message->id
            ]);
            if ($userMessage != null) {
                return $message;
            }
        }

        return null;
    }

}
