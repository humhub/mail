<?php

/**
 * MailController provides messaging actions.
 *
 * @package humhub.modules.mail.controllers
 * @since 0.5
 */
class MailController extends Controller
{

    /**
     *
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl'
                ) // perform access control for CRUD operations
        ;
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * 
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'users' => array('@')
            ),
            array(
                'deny', // deny all users
                'users' => array('*')
            )
        );
    }

    /**
     * Overview of all messages
     */
    public function actionIndex()
    {
        // Initially displayed message
        $messageId = (int) Yii::app()->request->getParam('id');

        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN message on message.id = t.message_id";
        $criteria->condition = "t.user_id=:userId";
        $criteria->params = array(':userId' => Yii::app()->user->id);
        $criteria->order = "message.updated_at DESC";

        $pagination = new CPagination(UserMessage::model()->count($criteria));
        $pagination->setPageSize(10);
        $pagination->applyLimit($criteria);

        $userMessages = UserMessage::model()->findAll($criteria);

        // If no messageId is given, use first 
        if (($messageId == "" || $this->getMessage($messageId) === null) && count($userMessages) != 0) {
            $messageId = $userMessages[0]->message->id;
        }

        if ($messageId != "") {
            Yii::app()->clientScript->registerScript("loadInitialMessage", 'loadMessage(' . CHtml::encode($messageId) . ');');
        }

        $this->render('/mail/index', array(
            'userMessages' => $userMessages,
            'pagination' => $pagination
        ));
    }

    /**
     * Overview of all messages
     * Used by MailNotificationWidget to display all recent messages
     */
    public function actionNotificationList()
    {
        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN message on message.id = t.message_id";
        $criteria->condition = "t.user_id=:userId";
        $criteria->params = array(':userId' => Yii::app()->user->id);
        $criteria->order = "message.updated_at DESC";
        $criteria->limit = 5;

        $userMessages = UserMessage::model()->findAll($criteria);

        $this->renderPartial('notificationList', array('userMessages' => $userMessages), false, true);
    }

    /**
     * Shows a Message Thread
     */
    public function actionShow()
    {
        // Load Message
        $id = (int) Yii::app()->request->getQuery('id');
        $message = $this->getMessage($id);

        if ($message == null) {
            throw new CHttpException(404, 'Could not find message!');
        }

        // Reply Form
        $replyForm = new ReplyMessageForm();
        if (isset($_POST['ReplyMessageForm'])) {
            $replyForm->attributes = $_POST['ReplyMessageForm'];

            if ($replyForm->validate()) {

                // Attach Message Entry
                $messageEntry = new MessageEntry();
                $messageEntry->message_id = $message->id;
                $messageEntry->user_id = Yii::app()->user->id;
                $messageEntry->content = $replyForm->message;
                $messageEntry->save();
                $messageEntry->notify();
                File::attachPrecreated($messageEntry, Yii::app()->request->getParam('fileUploaderHiddenGuidField'));

                $this->redirect($this->createUrl('index', array(
                            'id' => $message->id
                )));
            }
        }

        // Marks message as seen
        $message->seen(Yii::app()->user->id);

        $this->renderPartial('/mail/show', array(
            'message' => $message,
            'replyForm' => $replyForm,
                ), false, true);
    }

    /**
     * Shows the invite user form
     *
     * This method invite new people to the conversation.
     */
    public function actionAddUser()
    {
        $id = Yii::app()->request->getQuery('id');
        $message = $this->getMessage($id);

        if ($message == null) {
            throw new CHttpException(404, 'Could not find message!');
        }

        // Invite Form
        $inviteForm = new InviteRecipientForm();
        $inviteForm->message = $message;
        if (isset($_POST['InviteRecipientForm'])) {
            $inviteForm->attributes = Yii::app()->input->stripClean($_POST['InviteRecipientForm']);
            if ($inviteForm->validate()) {
                foreach ($inviteForm->getRecipients() as $user) {
                    // Attach User Message
                    $userMessage = new UserMessage();
                    $userMessage->message_id = $message->id;
                    $userMessage->user_id = $user->id;
                    $userMessage->is_originator = 0;
                    $userMessage->save();
                    $message->notify($user);
                }
                $this->htmlRedirect($this->createUrl('index', array('id' => $message->id)));
            }
        }
        $output = $this->renderPartial('/mail/adduser', array('inviteForm' => $inviteForm));
        Yii::app()->clientScript->render($output);
        echo $output;
    }

    /**
     * Creates a new Message
     * and redirects to it.
     */
    public function actionCreate()
    {
        $userGuid = Yii::app()->request->getParam('userGuid');
        $model = new CreateMessageForm();

        // Preselect user if userGuid is given
        if ($userGuid != "") {
            $user = User::model()->findByAttributes(array('guid' => $userGuid));
            if (isset($user)) {
                $model->recipient = $user->guid;
            }
        }

        if (isset($_POST['CreateMessageForm'])) {
            $model->attributes = $_POST['CreateMessageForm'];

            if ($model->validate()) {

                // Create new Message
                $message = new Message();
                $message->title = $model->title;
                $message->save();

                // Attach Message Entry
                $messageEntry = new MessageEntry();
                $messageEntry->message_id = $message->id;
                $messageEntry->user_id = Yii::app()->user->id;
                $messageEntry->content = $model->message;
                $messageEntry->save();
                File::attachPrecreated($messageEntry, Yii::app()->request->getParam('fileUploaderHiddenGuidField'));

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
                $userMessage->user_id = Yii::app()->user->id;
                $userMessage->is_originator = 1;
                $userMessage->last_viewed = new CDbExpression('NOW()');
                $userMessage->save();

                $this->htmlRedirect($this->createUrl('index', array('id' => $message->id)));
            }
        }

        $output = $this->renderPartial('create', array('model' => $model));
        Yii::app()->clientScript->render($output);
        echo $output;
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

        $id = Yii::app()->request->getQuery('id');
        $message = $this->getMessage($id);

        if ($message == null) {
            throw new CHttpException(404, 'Could not find message!');
        }

        $message->leave(Yii::app()->user->id);

        if (Yii::app()->request->isAjaxRequest) {
            $this->htmlRedirect($this->createUrl('index'));
        } else {
            $this->redirect($this->createUrl('index'));
        }
    }

    /**
     * Edits Entry Id
     */
    public function actionEditEntry()
    {
        $messageEntryId = (int) Yii::app()->request->getQuery('messageEntryId');
        $entry = MessageEntry::model()->findByPk($messageEntryId);

        // Check if message entry exists and it´s by this user
        if ($entry == null || $entry->user_id != Yii::app()->user->id) {
            throw new CHttpException(404, 'Could not find message entry!');
        }
        // Reply Form
        if (isset($_POST['MessageEntry'])) {
            $entry->content = $_POST['MessageEntry']['content'];

            if ($entry->validate()) {
                $entry->save();
                File::attachPrecreated($entry, Yii::app()->request->getParam('fileUploaderHiddenGuidField'));

                $this->htmlRedirect($this->createUrl('index', array(
                            'id' => $entry->message->id
                )));
            }
        }
        
        $this->renderPartial('editEntry', array('entry'=>$entry), false, true);
    }

    /**
     * Delete Entry Id
     *
     * Users can delete the own message entries.
     */
    public function actionDeleteEntry()
    {
        $this->forcePostRequest();

        $messageEntryId = (int) Yii::app()->request->getQuery('messageEntryId');
        $entry = MessageEntry::model()->findByPk($messageEntryId);

        // Check if message entry exists and it´s by this user
        if ($entry == null || $entry->user_id != Yii::app()->user->id) {
            throw new CHttpException(404, 'Could not find message entry!');
        }

        $entry->message->deleteEntry($entry);

        if (Yii::app()->request->isAjaxRequest) {
            $this->htmlRedirect($this->createUrl('index', array('id' => $entry->message_id)));
        } else {
            $this->redirect($this->createUrl('index', array('id' => $entry->message_id)));
        }
    }

    /**
     * Returns the number of new messages as JSON 
     */
    public function actionGetNewMessageCountJson()
    {
        $json = array();

        // New message count
        $sql = "SELECT count(message_id)
                FROM user_message
                LEFT JOIN message on message.id = user_message.message_id
                WHERE user_message.user_id = :user_id AND (message.updated_at > user_message.last_viewed OR user_message.last_viewed IS NULL) AND message.updated_by <> :user_id";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $userId = Yii::app()->user->id;
        $command->bindParam(":user_id", $userId);
        $json['newMessages'] = $command->queryScalar();

        echo CJSON::encode($json);
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
        $message = Message::model()->findByAttributes(array(
            'id' => $id
        ));

        if ($message != null) {

            $userMessage = UserMessage::model()->findByAttributes(array(
                'user_id' => Yii::app()->user->id,
                'message_id' => $message->id
            ));
            if ($userMessage != null) {
                return $message;
            }
        }

        return null;
    }

}
