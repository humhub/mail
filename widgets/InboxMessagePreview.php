<?php

namespace humhub\modules\mail\widgets;

use DateTime;
use DateTimeZone;
use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\AbstractMessageEntry;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\user\models\User;
use Yii;

class InboxMessagePreview extends Widget
{
    public ?UserMessage $userMessage = null;
    private ?Message $_message = null;

    public function run()
    {
        if ($this->getLastEntry() === null) {
            return '';
        }

        return $this->render('inboxMessagePreview', [
            'message' => $this->userMessage->message,
            'messageTitle' => $this->getMessageTitle(),
            'messageText' => $this->getMessagePreview(),
            'messageTime' => $this->getMessageTime(),
            'lastParticipant' => $this->lastParticipant(),
            'options' => $this->getOptions(),
        ]);
    }

    private function getOptions(): array
    {
        $message = $this->getMessage();

        return [
            'class' => 'messagePreviewEntry entry' . ($this->userMessage->isUnread() ? ' unread' : ''),
            'data' => [
                'message-id' => $message->id,
                'action-click' => 'mail.notification.loadMessage',
                'action-url' => Url::toMessenger($message),
            ],
        ];
    }

    public function getMessage(): Message
    {
        if ($this->_message === null) {
            $this->_message = $this->userMessage->message;
        }

        return $this->_message;
    }

    public function lastParticipant(): ?User
    {
        return $this->isGroupChat()
            ? $this->getLastEntry()->user
            : $this->getMessage()->getLastActiveParticipant();
    }

    private function getUsername(): string
    {
        $user = $this->lastParticipant();
        $profile = $user->profile;

        if ($profile === null || Yii::$app->settings->get('displayNameFormat') != '{profile.firstname} {profile.lastname}') {
            return $user->displayName;
        }

        $lastname = $this->isGroupChat()
            ? mb_substr($profile->lastname, 0, 1)
            : $profile->lastname;

        return $profile->firstname . ' ' . $lastname;
    }

    private function getMessageTitle(): string
    {
        if ($this->isGroupChat()) {
            $suffix = ', ' . Yii::t('MailModule.base', '{n,plural,=1{# other} other{# others}}', [
                'n' => $this->getMessage()->getUsersCount() - 2,
            ]);
        } else {
            $suffix = '';
        }

        return $this->getUsername() . $suffix;
    }

    public function getMessagePreview(): string
    {
        switch ($this->getLastEntry()->type) {
            case AbstractMessageEntry::TYPE_USER_JOINED:
                return $this->isOwnLastEntry()
                    ? Yii::t('MailModule.base', 'You joined the conversation.')
                    : Yii::t('MailModule.base', '{username} joined the conversation.', ['username' => $this->getUsername()]);

            case AbstractMessageEntry::TYPE_USER_LEFT:
                return $this->isOwnLastEntry()
                    ? Yii::t('MailModule.base', 'You left the conversation.')
                    : Yii::t('MailModule.base', '{username} left the conversation.', ['username' => $this->getUsername()]);
        }

        if ($this->isGroupChat()) {
            $prefix = $this->isOwnLastEntry()
                ? Yii::t('MailModule.base', 'You')
                : Html::encode($this->getUsername());
            $prefix .= ': ';
        } else {
            $prefix = '';
        }

        return $prefix . RichText::preview($this->getLastEntry()->content, 70);
    }

    private function getMessageTime(): string
    {
        $datetime = $this->getMessage()->updated_at ?? $this->getMessage()->created_at;
        $datetime = new DateTime($datetime, new DateTimeZone(Yii::$app->timeZone));

        if ($datetime->format('Y-m-d') === date('Y-m-d')) {
            // Show time for today
            return Yii::$app->formatter->asTime($datetime, 'short');
        } elseif (time() - $datetime->getTimestamp() < 3600 * 24 * 7) {
            // Show week day for week ago messages
            switch ($datetime->format('w')) {
                case 0: return Yii::t('MailModule.base', 'Sunday');
                case 1: return Yii::t('MailModule.base', 'Monday');
                case 2: return Yii::t('MailModule.base', 'Tuesday');
                case 3: return Yii::t('MailModule.base', 'Wednesday');
                case 4: return Yii::t('MailModule.base', 'Thursday');
                case 5: return Yii::t('MailModule.base', 'Friday');
                case 6: return Yii::t('MailModule.base', 'Saturday');
            }
        }

        // Show date for older messages
        return Yii::$app->formatter->asDate($datetime, 'short');
    }

    public function getLastEntry(): ?MessageEntry
    {
        return $this->getMessage()->getLastEntry();
    }

    private function isGroupChat(): bool
    {
        return $this->getMessage()->getUsersCount() > 2;
    }

    private function isOwnLastEntry(): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $lastEntryUser = $this->getLastEntry()->user;

        return $lastEntryUser instanceof User &&
            $lastEntryUser->is(Yii::$app->user->getIdentity());
    }
}
