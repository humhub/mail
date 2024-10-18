<?php

namespace humhub\modules\mail\models\forms;

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageNotification;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * @package humhub.modules.mail.forms
 * @since 0.5
 */
class InviteParticipantForm extends Model
{
    /**
     * @var Message
     */
    public $message; // message

    /**
     * Parsed recipients in array of user objects
     *
     * @var array
     */
    public $recipients = [];

    /**
     * @var User[]
     */
    public $recipientUsers = [];

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            ['recipients', 'required'],
            ['recipients', 'checkRecipient'],
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'recipient' => Yii::t('MailModule.base', 'Recipient'),
        ];
    }

    /**
     * Form Validator which checks the recipient field
     *
     * @param type $attribute
     * @param type $params
     */
    public function checkRecipient($attribute, $params)
    {
        foreach ($this->recipients as $userGuid) {
            $user = User::findOne(['guid' => $userGuid]);
            if ($user) {
                $name = Html::encode($user->getDisplayName());
                if (Yii::$app->user->identity->is($user)) {
                    $this->addError($attribute, Yii::t('MailModule.base', "You cannot send a email to yourself!"));
                } elseif ($this->message->isParticipant($user)) {
                    $this->addError($attribute, Yii::t('MailModule.base', "User {name} is already participating!", ['name' => $name]));
                } elseif (!$user->can(SendMail::class) && !Yii::$app->user->isAdmin()) {
                    $this->addError($attribute, Yii::t('MailModule.base', "You are not allowed to send user {name} is already!", ['name' => $name]));
                } else {
                    $this->recipientUsers[] = $user;
                }
            }
        }
    }

    public function getPickerUrl()
    {
        return Url::toSearchNewParticipants($this->message);
    }

    public function getUrl()
    {
        return Url::toAddParticipant($this->message);
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        foreach ($this->recipientUsers as $user) {
            $userMessage = new UserMessage([
                'message_id' => $this->message->id,
                'user_id' => $user->id,
                'is_originator' => 0,
            ]);

            if ($userMessage->save()) {
                $this->message->refresh();
                (new MessageNotification($this->message))
                    ->setEntrySender(Yii::$app->user->getIdentity())
                    ->notifyAll();
            }
        }

        unset($this->message->users);
        return true;
    }

    /**
     * Returns an Array with selected recipients
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

}
