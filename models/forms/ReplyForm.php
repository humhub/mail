<?php

namespace humhub\modules\mail\models\forms;

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use Yii;
use yii\base\Model;

/**
 * @package humhub.modules.mail.forms
 * @since 0.5
 */
class ReplyForm extends Model
{
    /**
     * Scenario - when related content has attached files
     */
    public const SCENARIO_HAS_FILES = 'hasFiles';

    /**
     * @var Message
     */
    public $model;

    /**
     * @var string
     */
    public $message;

    /**
     * @var MessageEntry
     */
    public $reply;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            ['message', 'required', 'except' => [self::SCENARIO_HAS_FILES]],
            ['message', 'validateRecipients'],
        ];
    }

    public function validateRecipients($attribute)
    {
        if ($this->model->isBlocked()) {
            $this->addError($attribute, Yii::t('MailModule.base', 'You are not allowed to reply to users {userNames}!', [
                'userNames' => implode(', ', $this->model->getBlockerNames()),
            ]));
        }
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'message' => Yii::t('MailModule.base', 'Message'),
        ];
    }

    public function getUrl()
    {
        return Url::toReply($this->model);
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->reply = new MessageEntry([
            'message_id' => $this->model->id,
            'user_id' => Yii::$app->user->id,
            'content' => $this->message,
        ]);
        if ($this->scenario === self::SCENARIO_HAS_FILES) {
            $this->reply->scenario = MessageEntry::SCENARIO_HAS_FILES;
        }

        if ($this->reply->save()) {
            $this->reply->refresh(); // Update created_by date, otherwise db expression is set...
            $this->reply->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->reply->notify();

            // Update last viewed date to avoid marking the conversation as unread
            $userMessage = $this->model->getUserMessage($this->reply->user_id);
            if ($userMessage) {
                $userMessage->last_viewed = date('Y-m-d G:i:s');
                $userMessage->save();
            }

            return true;
        }

        return false;
    }

}
