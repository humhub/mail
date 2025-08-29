<?php

namespace humhub\modules\mail\controllers;

use humhub\components\access\ControllerAccess;
use humhub\components\Controller;
use humhub\modules\file\handler\FileHandlerCollection;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\models\forms\InviteParticipantForm;
use humhub\modules\mail\models\forms\ReplyForm;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\Module;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\ConversationEntry;
use humhub\modules\mail\widgets\ConversationHeader;
use humhub\modules\mail\widgets\Messages;
use humhub\modules\User\models\User;
use humhub\modules\user\models\UserFilter;
use humhub\modules\user\models\UserPicker;
use humhub\modules\user\widgets\UserListBox;
use Yii;
use yii\helpers\Html;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * MailController provides messaging actions.
 *
 * @package humhub.modules.mail.controllers
 * @since 0.5
 */
class MailController extends Controller
{
    /**
     * @inheritdoc
     */
    protected $doNotInterceptActionIds = ['get-new-message-count-json'];

    public $pageSize = 30;

    /**
     * @inheritdoc
     */
    protected function getAccessRules()
    {
        return [
            [ControllerAccess::RULE_LOGGED_IN_ONLY],
            [ControllerAccess::RULE_PERMISSION => StartConversation::class, 'actions' => ['create', 'add-user']],
        ];
    }

    /**
     * Overview of all messages
     * @param null $id
     * @return string
     */
    public function actionIndex($id = null)
    {
        return $this->render('index', [
            'messageId' => $id,
        ]);
    }

    /**
     * Shows a Message Thread
     */
    public function actionShow($id)
    {
        $message = ($id instanceof Message) ? $id : $this->getMessage($id);

        $this->checkMessagePermissions($message);

        // Marks message as seen
        $message->seen(Yii::$app->user->id);

        return $this->renderAjax('conversation', [
            'message' => $message,
            'messageCount' => UserMessage::getNewMessageCount(),
            'replyForm' => new ReplyForm(['model' => $message]),
            'fileHandlers' => FileHandlerCollection::getByType([FileHandlerCollection::TYPE_IMPORT, FileHandlerCollection::TYPE_CREATE]),
        ]);
    }

    public function actionSeen()
    {
        $id = Yii::$app->request->post('id');

        if ($id) {
            $message = ($id instanceof Message) ? $id : $this->getMessage($id);
            $this->checkMessagePermissions($message);
            $message->seen(Yii::$app->user->id);
        }

        return $this->asJson([
            'messageCount' => UserMessage::getNewMessageCount(),
        ]);
    }

    public function actionUpdate($id, $from = null)
    {
        $message = ($id instanceof Message) ? $id : $this->getMessage($id);

        $this->checkMessagePermissions($message);

        return $this->renderAjaxContent(Messages::widget([
            'message' => $message,
            'entries' => $message->getEntryUpdates($from)->all(),
            'showDateBadge' => false,
        ]));
    }

    public function actionLoadMore($id, $from)
    {
        $message = ($id instanceof Message) ? $id : $this->getMessage($id);

        $this->checkMessagePermissions($message);

        $entries = $message->getEntryPage($from);

        $result = Messages::widget(['message' => $message, 'from' => $from]);

        return $this->asJson([
            'result' => $result,
            'isLast' => (count($entries) < Module::getModuleInstance()->conversationUpdatePageSize),
        ]);
    }

    public function actionReply($id)
    {
        $message = $this->getMessage($id, true);

        $this->checkMessagePermissions($message);

        // Reply Form
        $replyForm = new ReplyForm(['model' => $message]);

        if (!empty(Yii::$app->request->post('fileList'))) {
            $replyForm->scenario = ReplyForm::SCENARIO_HAS_FILES;
        }

        if ($replyForm->load(Yii::$app->request->post()) && $replyForm->save()) {
            return $this->asJson([
                'success' => true,
                'content' => ConversationEntry::widget(['entry' => $replyForm->reply, 'showDateBadge' => $replyForm->reply->isFirstToday()]),
            ]);
        }

        return $this->asJson([
            'success' => false,
            'error' => [
                'message' => $replyForm->getFirstError('message'),
            ],
        ]);
    }

    /**
     * @param $id
     * @return
     * @throws HttpException
     */
    public function actionUserList($id)
    {
        return $this->renderAjaxContent(UserListBox::widget([
            'query' => $this->getMessage($id, true)->getUsers(),
            'title' => '<strong>' . Yii::t('MailModule.base', 'Participants') . '</strong>',
        ]));
    }

    /**
     * Shows the invite user form
     *
     * This method invite new people to the conversation.
     */
    public function actionAddUser($id)
    {
        $message = $this->getMessage($id);

        $this->checkMessagePermissions($message);

        // Invite Form
        $inviteForm = new InviteParticipantForm(['message' => $message]);

        if ($inviteForm->load(Yii::$app->request->post())) {
            if ($inviteForm->save()) {
                return $this->asJson([
                    'result' => ConversationHeader::widget(['message' => $message]),
                ]);
            }

            return $this->asJson([
                'success' => false,
                'error' => [
                    'message' => $inviteForm->getFirstError('recipients'),
                ],
            ]);
        }

        return $this->renderAjax('adduser', ['inviteForm' => $inviteForm]);
    }

    /**
     * Overview of all messages
     * Used by MailNotificationWidget to display all recent messages
     */
    public function actionNotificationList()
    {
        $query = UserMessage::findByUser()->limit(5);
        return $this->renderAjax('notificationList', ['userMessages' => $query->all()]);
    }

    /**
     * Used by user picker, searches user which are allwed messaging permissions
     * for the current user (v1.1).
     *
     * @param null $id
     * @param $keyword
     * @return string
     * @throws HttpException
     * @throws \Throwable
     */
    public function actionSearchUser($keyword, $id = null)
    {
        $message = $this->getMessage($id);

        if ($message) {
            $this->checkMessagePermissions($message);
        }

        $result = UserPicker::filter([
            'query' => UserFilter::find()->available(),
            'keyword' => $keyword,
            'permission' => (!Yii::$app->user->isAdmin()) ? new SendMail() : null,
            'disableFillUser' => true,
            'disabledText' => Yii::t('MailModule.base', 'You are not allowed to start a conversation with this user.'),
        ]);

        // Disable already participating users
        if ($message) {
            foreach ($result as $i => $user) {
                if ($this->isParticipant($message, $user)) {
                    $index = $i++;
                    $result[$index]['disabled'] = true;
                    $result[$index]['disabledText'] = Yii::t('MailModule.base', 'This user is already participating in this conversation.');
                }
            }
        }

        return $this->asJson($result);
    }

    private function checkMessagePermissions($message)
    {
        if ($message == null) {
            throw new HttpException(404, 'Could not find message!');
        }

        if (!$message->isParticipant(Yii::$app->user->getIdentity())) {
            throw new HttpException(403, 'Access denied!');
        }
    }

    /**
     * Checks if a user (user json representation) is participant of a given
     * message.
     *
     * @param type $message
     * @param type $user
     * @return bool
     */
    private function isParticipant($message, $user)
    {
        foreach ($message->users as $participant) {
            if ($participant->guid === $user['guid']) {
                return true;
            }
        }
        return false;
    }

    /*
     * @deprecated
     */
    private function findUserByFilter($keyword, $maxResult)
    {
        $query = User::find()->limit($maxResult)->joinWith('profile');

        foreach (explode(" ", $keyword) as $part) {
            $query->orFilterWhere(['like', 'user.email', $part]);
            $query->orFilterWhere(['like', 'user.username', $part]);
            $query->orFilterWhere(['like', 'profile.firstname', $part]);
            $query->orFilterWhere(['like', 'profile.lastname', $part]);
            $query->orFilterWhere(['like', 'profile.title', $part]);
        }

        $query->active();

        $results = [];
        foreach ($query->all() as $user) {
            if ($user != null) {
                $userInfo = [];
                $userInfo['guid'] = $user->guid;
                $userInfo['displayName'] = Html::encode($user->displayName);
                $userInfo['image'] = $user->getProfileImage()->getUrl();
                $userInfo['link'] = $user->getUrl();
                $results[] = $userInfo;
            }
        }
        return $results;
    }

    /**
     * Creates a new Message
     * and redirects to it.
     */
    public function actionCreate($userGuid = null, ?string $title = null, ?string $message = null)
    {
        $model = new CreateMessage(['recipient' => [$userGuid], 'title' => $title, 'message' => $message]);

        // Preselect user if userGuid is given
        if ($userGuid) {
            /* @var User $user */
            $user = User::find()->where(['guid' => $userGuid])->available()->one();

            if (!$user) {
                throw new NotFoundHttpException();
            }

            if (!$user->getPermissionManager()->can(SendMail::class) && !Yii::$app->user->isAdmin()) {
                throw new ForbiddenHttpException();
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->htmlRedirect(['index', 'id' => $model->messageInstance->id]);
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'fileHandlers' => FileHandlerCollection::getByType([FileHandlerCollection::TYPE_IMPORT, FileHandlerCollection::TYPE_CREATE]),
        ]);
    }

    /**
     * Mark a Conversation as unread
     *
     * @param int $id
     */
    public function actionMarkUnread($id)
    {
        $this->forcePostRequest();
        $this->getMessage($id, true)->markUnread();

        $nextReadMessage = $this->getNextReadMessage($id);

        return $this->asJson([
            'success' => true,
            'redirect' => $nextReadMessage ? Url::toMessenger($nextReadMessage) : Url::to(['/dashboard']),
        ]);
    }

    /**
     * Pin a Conversation
     *
     * @param int $id
     */
    public function actionPin($id)
    {
        $this->forcePostRequest();
        $message = $this->getMessage($id, true);
        $message->pin();

        return $this->asJson([
            'success' => true,
            'redirect' => Url::toMessenger($message),
        ]);
    }

    /**
     * Unpin a Conversation
     *
     * @param int $id
     */
    public function actionUnpin($id)
    {
        $this->forcePostRequest();
        $message = $this->getMessage($id, true);
        $message->unpin();

        return $this->asJson([
            'success' => true,
            'redirect' => Url::toMessenger($message),
        ]);
    }

    /**
     * Leave Message / Conversation
     *
     * Leave is only possible when at least two people are in the
     * conversation.
     * @param $id
     * @return \yii\web\Response
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionLeave($id)
    {
        $this->forcePostRequest();
        $this->getMessage($id, true)->leave();

        return $this->asJson([
            'success' => true,
            'redirect' => Url::toMessenger(),
        ]);
    }

    /**
     * Edits Entry Id
     * @param $id
     * @return string|\yii\web\Response
     * @throws HttpException
     */
    public function actionEditEntry($id)
    {
        $entry = MessageEntry::findOne(['id' => $id]);

        if (!$entry) {
            throw new HttpException(404);
        }

        if (!$entry->canEdit()) {
            throw new HttpException(403);
        }

        if ($entry->load(Yii::$app->request->post()) && $entry->save()) {
            $entry->fileManager->attach(Yii::$app->request->post('MessageEntry')['files'] ?? null);
            return $this->asJson([
                'success' => true,
                'content' => ConversationEntry::widget([
                    'entry' => $entry,
                    'showDateBadge' => false,
                ]),
            ]);
        }

        return $this->renderAjax('editEntry', [
            'entry' => $entry,
            'fileHandlers' => FileHandlerCollection::getByType([FileHandlerCollection::TYPE_IMPORT, FileHandlerCollection::TYPE_CREATE]),
        ]);
    }

    /**
     * Delete Entry Id
     *
     * Users can delete the own message entries.
     */
    public function actionDeleteEntry($id)
    {
        $this->forcePostRequest();
        $entry = MessageEntry::findOne(['id' => $id]);

        if (!$entry) {
            throw new HttpException(404);
        }

        // Check if message entry exists and it´s by this user
        if (!$entry->canEdit()) {
            throw new HttpException(403);
        }

        $entry->message->deleteEntry($entry);

        return $this->asJson([
            'success' => true,
        ]);
    }

    /**
     * Returns the number of new messages as JSON
     */
    public function actionGetNewMessageCountJson()
    {
        $json = ['newMessages' => UserMessage::getNewMessageCount()];
        return $this->asJson($json);
    }

    /**
     * Returns the Message Model by given Id
     * Also an access check will be performed.
     *
     * If insufficed privileges or not found null will be returned.
     *
     * @param int $id
     * @param bool $throw
     * @return Message|null
     * @throws HttpException
     */
    private function getMessage($id, $throw = false): ?Message
    {
        $message = Message::findOne(['id' => $id]);

        if ($message && $message->getUserMessage() !== null) {
            return $message;
        }

        if ($throw) {
            throw new HttpException(404, 'Could not find message!');
        }

        return null;
    }

    private function getNextReadMessage($id): ?Message
    {
        return Message::find()
            ->leftJoin('user_message', 'user_message.message_id = message.id')
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere(['!=', 'message_id', $id])
            ->andWhere('user_message.last_viewed >= message.updated_at')
            ->orderBy([
                'user_message.pinned' => SORT_DESC,
                'message.updated_at' => SORT_DESC,
            ])
            ->one();
    }
}
