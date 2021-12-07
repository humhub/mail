<?php


namespace humhub\modules\mail\models;


use humhub\modules\content\widgets\richtext\converter\RichTextToEmailHtmlConverter;
use humhub\modules\content\widgets\richtext\converter\RichTextToHtmlConverter;
use humhub\modules\mail\live\NewUserMessage;
use humhub\modules\mail\notifications\ConversationNotificationCategory;
use humhub\modules\mail\notifications\MailNotificationCategory;
use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\targets\MailTarget;
use humhub\modules\user\models\User;
use Yii;
use yii\base\Model;use yii\helpers\Html;

class MessageNotification extends Model
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @var MessageEntry
     */
    public $entry;

    /**
     * @var bool $isNewConversation Flag for notification type: Conversation vs Message
     */
    public $isNewConversation = false;

    public function __construct(Message $message, MessageEntry $entry = null)
    {
        $this->message = $message;
        $this->entry = $entry ?: $this->message->lastEntry;
        parent::__construct([]);
    }

    public function notifyAll()
    {
        foreach ($this->message->users as $user) {
            $this->notify($user);
        }
    }

    public function notify(User $user)
    {
        try {
            $this->sendLiveEvent($user);

            // Backup the flag because it may be forced per user in order to select a proper notification type
            $isNewConversation = $this->isNewConversation;

            if ($this->isSendMail($user)) {
                $this->sendMail($user);
            }

            // Restore the flag
            $this->isNewConversation = $isNewConversation;
        } catch (\Exception $e) {
            Yii::error('Could not send notification e-mail to: ' . $user->username . ". Error:" . $e->getMessage());
        }
    }

    private function sendLiveEvent(User $user)
    {
        Yii::$app->live->send(new NewUserMessage([
            'contentContainerId' => $user->contentcontainer_id,
            'message_id' => $this->message->id,
            'user_guid' => $this->entry->user->guid
        ]));
    }

    private function isSendMail(User $user): bool
    {
        if ($user->is($this->getEntrySender())) {
            return false;
        }

        if (!($mailTarget = Yii::$app->notification->getTarget(MailTarget::class))) {
            return false;
        }

        if ($mailTarget->isCategoryEnabled($this->getNotificationCategory(), $user)) {
            return true;
        }

        // Try to send notification as "New message" when notification "New conversation" is disabled for the user
        if ($this->isNewConversation && $mailTarget->isCategoryEnabled(new MailNotificationCategory(), $user)) {
            $this->isNewConversation = false;
            return true;
        }

        return false;
    }

    private function getNotificationCategory(): NotificationCategory
    {
        return $this->isNewConversation
            ? new ConversationNotificationCategory()
            : new MailNotificationCategory();
    }

    private function sendMail(User $user)
    {
        Yii::$app->i18n->setUserLocale($user);

        $mail = Yii::$app->mailer->compose([
            'html' => '@mail/views/emails/NewMessage',
            'text' => '@mail/views/emails/plaintext/NewMessage'
        ], [
            'user' => $user,
            'headline' => $this->getHeadline(),
            'senderUrl' => $this->getEntrySender()->createUrl(null, [], true),
            'subHeadline' => $this->getSubHeadline(),
            'content' => $this->getContent($user),
            'message' => $this->message,
            'originator' => $this->getMessageOriginator(),
            'entry' => $this->entry,
            'sender' => $this->getEntrySender(),
        ]);

        $mail->setFrom([Yii::$app->settings->get('mailer.systemEmailAddress') => Yii::$app->settings->get('mailer.systemEmailName')]);
        $mail->setTo($user->email);
        $mail->setSubject($this->getSubject());
        $mail->send();

        Yii::$app->i18n->autosetLocale();
    }

    protected function getContent(User $user)
    {
        return RichTextToEmailHtmlConverter::process($this->entry->content, [
            RichTextToEmailHtmlConverter::OPTION_RECEIVER_USER => $user,
            RichTextToHtmlConverter::OPTION_LINK_AS_TEXT => true,
            RichTextToHtmlConverter::OPTION_CACHE_KEY => 'mail_entry_message_' . $this->entry->id,
        ]);
    }

    protected function getHeadline(): string
    {
        return $this->isNewConversation
            ? Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> conversation')
            : Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> message');
    }

    protected function getSubHeadline(): string
    {
        $params = [
            'senderName' => Html::encode($this->getEntrySender()->displayName),
            'conversationTitle' => '"' . Html::encode($this->message->title) . '"',
        ];

        return $this->isNewConversation
            ? Yii::t('MailModule.views_emails_NewMessageEntry', '{senderName} created a new conversation {conversationTitle}', $params)
            : Yii::t('MailModule.views_emails_NewMessageEntry', '{senderName} sent you a new message in {conversationTitle}',  $params);
    }

    /**
     * @return User
     */
    protected function getMessageOriginator()
    {
        return $this->message->originator;
    }

    /**
     * @return User
     */
    protected function getEntrySender()
    {
        return $this->entry->user;
    }

    protected function getSubject(): string
    {
        $params = ['{senderName}' => $this->getEntrySender()->displayName];

        return $this->isNewConversation
            ? Yii::t('MailModule.models_Message', 'New conversation from {senderName}', $params)
            : Yii::t('MailModule.models_Message', 'New message from {senderName}', $params);
    }

}