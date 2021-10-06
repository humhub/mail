<?php

namespace humhub\modules\mail\models\forms;

use humhub\modules\mail\models\Config;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

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
     * @var array
     */
    public $tags = [];

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['message', 'recipient', 'title'], 'required'],
            [['tags'], 'safe'],
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
            'tags' => Yii::t('MailModule.forms_CreateMessageForm', 'Tags'),
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
        if (empty($this->$attribute)) {
            return;
        }

        foreach ($this->recipient as $userGuid) {
            $user = User::findOne(['guid' => $userGuid]);
            if (!$user) {
                continue;
            }

            if ($user->isCurrentUser()) {
                $this->addError($attribute, Yii::t('MailModule.base', 'You cannot send a message to yourself!'));
            } else if(!$this->canSendToUser($user)) {
                $this->addError($attribute, Yii::t('MailModule.base', 'You are not allowed to start a conversation with {userName}!', [
                    'userName' => Html::encode($user->getDisplayName())
                ]));
            } else {
                $this->recipients[] = $user;
            }
        }
    }

    private function canSendToUser(User $user): bool
    {
        if ($user->isBlockedForUser()) {
            return false;
        }

        if (Yii::$app->user->isAdmin()) {
            return true;
        }

        return $user->getPermissionManager()->can(SendMail::class);
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

            if (!$this->saveRecipients()) {
                $transaction->rollBack();
                return false;
            }

            if (!$this->saveMessageEntry()) {
                $transaction->rollBack();
                return false;
            }

            if(!$this->saveOriginatorUserMessage()) {
                $transaction->rollBack();
                return false;
            }

            $this->saveTags();

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

    private function saveTags()
    {
        return MessageTag::attach(Yii::$app->user->id, $this->messageInstance, $this->tags);
    }

    private function saveRecipients()
    {
        // Attach also Recipients
        foreach ($this->getRecipients() as $recipient) {
            $this->messageInstance->addRecepient($recipient);
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
        $entry = MessageEntry::createForMessage($this->messageInstance, Yii::$app->user->getIdentity(), $this->message);
        $result = $entry->save();
        $entry->notify(true);
        return $result;
    }

    private function saveOriginatorUserMessage()
    {
        return $this->messageInstance->addRecepient(Yii::$app->user->getIdentity(), true);
    }

}
