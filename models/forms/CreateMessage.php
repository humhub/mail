<?php

namespace humhub\modules\mail\models\forms;

use Yii;
use yii\base\Model;
use humhub\modules\user\models\User;

/**
 * @package humhub.modules.mail.forms
 * @since 0.5
 */
class CreateMessage extends Model
{

    public $recipient;
    public $recipientUser;
    public $message;
    public $title;

    /**
     * Parsed recipients in array of user objects
     *
     * @var type
     */
    public $recipients = [];

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['message', 'recipient', 'title'], 'required'],
            ['recipient', 'checkRecipient']
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
            'recipient' => Yii::t('MailModule.forms_CreateMessageForm', 'Recipient'),
            'title' => Yii::t('MailModule.forms_CreateMessageForm', 'Subject'),
            'message' => Yii::t('MailModule.forms_CreateMessageForm', 'Message'),
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

        // Check if email field is not empty
        if ($this->$attribute != "") {

            foreach ($this->recipient as $userGuid) {
                // Try load user
                $user = User::findOne(['guid' => $userGuid]);
                if ($user != null) {

                    if ($user->id == Yii::$app->user->id) {
                        $this->addError($attribute, Yii::t('MailModule.forms_CreateMessageForm', "You cannot send a email to yourself!"));
                    } else {
                        $this->recipients[] = $user;
                    }
                }
            }
        }
    }

    /**
     * Returns an Array with selected recipients
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

}
