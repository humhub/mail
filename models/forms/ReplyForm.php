<?php

namespace humhub\modules\mail\models\forms;

use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\helpers\Url;
use Yii;
use yii\base\Model;

/**
 * @package humhub.modules.mail.forms
 * @since 0.5
 */
class ReplyForm extends Model
{

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
            ['message', 'required'],
            ['message', 'validateRecipients'],
        ];
    }

    public function validateRecipients($attribute)
    {
        if ($this->model->isBlocked()) {
            $this->addError($attribute, Yii::t('MailModule.base', 'You are not allowed to reply to users {userNames}!', [
                'userNames' => implode(', ', $this->model->getBlockerNames())
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
            'message' => Yii::t('MailModule.forms_ReplyMessageForm', 'Message'),
        ];
    }

    public function getUrl()
    {
        return Url::toReply($this->model);
    }

    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        $this->reply = new MessageEntry([
            'message_id' => $this->model->id,
            'user_id' => Yii::$app->user->id,
            'content' => $this->message
        ]);

        if($this->reply->save()) {
            $this->reply->refresh(); // Update created_by date, otherwise db expression is set...
            $this->reply->notify();
            $this->reply->fileManager->attach(Yii::$app->request->post('fileUploaderHiddenGuidField'));
            return true;
        }

        return false;
    }

}
