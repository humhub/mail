<?php

namespace humhub\modules\mail\models\forms;

use humhub\modules\mail\models\Config;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
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
     * @var Message new message
     */
    public $messageInstance;

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
     * @param string $attribute
     * @param array $params
     */
    public function checkRecipient($attribute, $params)
    {
        // Check if email field is not empty
        if ($this->$attribute) {
            foreach ($this->recipient as $userGuid) {
                // Try load user
                $user = User::findOne(['guid' => $userGuid]);
                if ($user != null) {
                    if ($user->isCurrentUser()) {
                        $this->addError($attribute, Yii::t('MailModule.forms_CreateMessageForm', "You cannot send a email to yourself!"));
                    } else {
                        $this->recipients[] = $user;
                    }
                }
            }
        }
    }

    public function save()
    {
        $transaction = Message::getDb()->beginTransaction();

        try {
            if (!$this->validate()) {
                $transaction->rollBack();
                return false;
            }

            if (!$this->saveMessage()) {
                $transaction->rollBack();
                return false;
            }

            if (!$this->saveMessageEntry()) {
                $transaction->rollBack();
                return false;
            }

            if (!$this->saveRecipients()) {
                $transaction->rollBack();
                return false;
            }

            if(!$this->saveOriginatorUserMessage()) {
                $transaction->rollBack();
                return false;
            }

            (new Config())->incrementConversationCount(Yii::$app->user->getIdentity());

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    private function saveRecipients()
    {
        $recepients = [];
        // Attach also Recipients
        foreach ($this->getRecipients() as $recipient) {
            if ($this->messageInstance->addRecepient($recipient)) {
                $recepients[] = $recipient;
            }
        }

        // Inform recipients (We need to add all before)
        foreach ($recepients as $recipient) {
            try {
                $this->messageInstance->notify($recipient);
            } catch (\Exception $e) {
                Yii::error('Could not send notification e-mail to: ' . $recipient->username . ". Error:" . $e->getMessage());
            }
        }

        return true;
    }

    private function saveMessage()
    {
        $this->messageInstance = new Message([
            'title' => $this->title
        ]);

        if(!(new Config())->canCreateConversation(Yii::$app->user->getIdentity())) {
            $this->addError('message', Yii::t('MailModule.base', 'You\'ve exceeded your daily amount of new conversations.'));
            return false;
        }

        return $this->messageInstance->save();
    }

    /**
     * Returns an Array with selected recipients
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    private function saveMessageEntry()
    {
        return MessageEntry::createForMessage($this->messageInstance, Yii::$app->user->getIdentity(), $this->message)->save();
    }

    private function saveOriginatorUserMessage()
    {
        return $this->messageInstance->addRecepient(Yii::$app->user->getIdentity(), true);
    }

}
