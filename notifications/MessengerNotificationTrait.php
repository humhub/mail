<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2026 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\notifications;

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\components\MessageParam;
use humhub\modules\notification\components\NotificationBlock;
use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\components\NotificationContext;
use humhub\modules\user\models\User;
use Yii;

/**
 * The common part of the messenger notifications: the source is the conversation
 * ({@see Message}), the payload's `entryId` the {@see MessageEntry} the notification is about.
 *
 * - one category, "Messenger", for both classes;
 * - not listed in the web list, the messenger's inbox lists the conversations;
 * - only for participants of the conversation;
 * - links to the conversation;
 * - a mail shows the message text as a quote - plain text, shortened to {@see $excerptLength} -
 *   and a "Reply now" button;
 * - adds the `conversationTitle` message parameter - quoted in plain text, strong in HTML - and
 *   `''` for a conversation without title.
 *
 * @mixin BaseNotification
 * @internal
 * @since 3.5
 */
trait MessengerNotificationTrait
{
    /**
     * @var int max length of the message text in the quote
     */
    public int $excerptLength = 500;

    /**
     * @var string|false|null the memoized {@see getQuoteText()}, `false` before the first call
     */
    private string|false|null $_quoteText = false;

    /**
     * @inheritdoc
     */
    public static function category(): NotificationCategory
    {
        return NotificationCategory::ofModule(MailNotification::class, 'ti-message');
    }

    /**
     * @inheritdoc
     *
     * Conversations are listed in the messenger's own inbox.
     */
    public static function listed(): bool
    {
        return false;
    }

    /**
     * The conversation.
     */
    public function getConversation(): ?Message
    {
        return $this->sourceRecord instanceof Message ? $this->sourceRecord : null;
    }

    /**
     * The message the notification is about, `null` when it is gone.
     */
    public function getMessageEntry(): ?MessageEntry
    {
        $conversation = $this->getConversation();
        if ($conversation === null || empty($this->payload['entryId'])) {
            return null;
        }

        return MessageEntry::findOne(['id' => (int)$this->payload['entryId'], 'message_id' => $conversation->id]);
    }

    /**
     * Only for participants of the conversation.
     *
     * @inheritdoc
     */
    public function canReceive(User $user): bool
    {
        return $this->getConversation()?->isParticipant($user) ?? false;
    }

    /**
     * The conversation in the messenger.
     *
     * @inheritdoc
     */
    public function getUrl(bool $scheme = false): ?string
    {
        $conversation = $this->getConversation();

        return $conversation !== null ? Url::toMessenger($conversation, $scheme) : null;
    }

    /**
     * The message as a quote of its author, and a "Reply now" button.
     *
     * @inheritdoc
     */
    public function getBlocks(NotificationContext $context): array
    {
        $blocks = [];
        $text = $this->getQuoteText();
        if ($text !== null) {
            $blocks[] = NotificationBlock::quote($text, $this->originator, $this->getMessageEntry()?->created_at);
        }
        $blocks[] = NotificationBlock::button(Yii::t('MailModule.base', 'Reply now'), $this->getEntryUrl());

        return $blocks;
    }

    /**
     * The text of the message, plain and shortened to {@see $excerptLength}; `null` when the
     * message is gone or empty.
     */
    private function getQuoteText(): ?string
    {
        if ($this->_quoteText !== false) {
            return $this->_quoteText;
        }

        $content = (string)$this->getMessageEntry()?->content;
        $text = $content !== ''
            ? trim(RichText::convert($content, RichText::FORMAT_PLAINTEXT, ['maxLength' => $this->excerptLength]))
            : '';

        return $this->_quoteText = $text !== '' ? $text : null;
    }

    /**
     * @inheritdoc
     */
    protected function getMessageParams(): array
    {
        $title = (string)$this->getConversation()?->title;

        return [
            'conversationTitle' => $title !== '' ? MessageParam::emphasis($title) : '',
        ];
    }
}
