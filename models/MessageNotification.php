<?php


namespace humhub\modules\mail\models;


use humhub\modules\content\widgets\richtext\converter\RichTextToEmailHtmlConverter;
use humhub\modules\content\widgets\richtext\converter\RichTextToHtmlConverter;
use humhub\modules\mail\live\NewUserMessage;
use humhub\modules\mail\notifications\ConversationNotificationCategory;
use humhub\modules\notification\targets\BaseTarget;
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

            if ($this->isSendMail($user)) {
                $this->sendMail($user);
            }
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

    private function isSendMail(User $user)
    {
        if($user->is($this->getEntrySender())) {
            return false;
        }

        /* @var $mailTarget BaseTarget */
        $mailTarget = Yii::$app->notification->getTarget(MailTarget::class);

        return $mailTarget && $mailTarget->isCategoryEnabled(new ConversationNotificationCategory(), $user);
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
        $mail->setSubject($this->getSubject($user));
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

    protected function getHeadline()
    {
        return Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> message');
    }

    protected function getSubHeadline()
    {
        $result = '<strong>'.Html::encode($this->getEntrySender()->displayName).'</strong> ';
        $result .= Yii::t('MailModule.views_emails_NewMessageEntry', 'sent you a new message in');
        $result .= ' <strong>'.Html::encode($this->message->title).'</strong>';
        return $result;
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

    protected function getSubject(User $user)
    {
        return Yii::t('MailModule.models_Message', 'New message from {senderName}', ["{senderName}" => Html::encode($this->getEntrySender()->displayName)]);
    }

}