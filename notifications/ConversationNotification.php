<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\mail\notifications;

use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\components\NotificationPriority;
use Yii;

/**
 * A conversation the user was added to: a new conversation, dispatched with its first message
 * by {@see \humhub\modules\mail\models\MessageEntry::notify()}, or an existing one the user was
 * invited to ({@see \humhub\modules\mail\models\forms\InviteParticipantForm}), marked by the
 * payload's `invited`. The source is the conversation ({@see \humhub\modules\mail\models\Message}),
 * the originator its creator or the inviting user, the payload's `entryId` the first message.
 *
 * High priority: the first message of a conversation goes out at once. Shares the module's category
 * with {@see MailNotification}, so the user has one switch for the messenger.
 *
 * Since 3.5 built on the record-bound {@see BaseNotification}.
 */
final class ConversationNotification extends BaseNotification
{
    use MessengerNotificationTrait;

    /**
     * @inheritdoc
     */
    public static function priority(): NotificationPriority
    {
        return NotificationPriority::High;
    }

    /**
     * @inheritdoc
     */
    protected function getMessage(array $params): string
    {
        $args = [
            'senderName' => $params['displayName'],
            'conversationTitle' => $params['conversationTitle'],
        ];
        $hasTitle = $params['conversationTitle'] !== '';

        if (!empty($this->payload['invited'])) {
            return $hasTitle
                ? Yii::t('MailModule.base', '{senderName} added you to the conversation {conversationTitle}', $args)
                : Yii::t('MailModule.base', '{senderName} added you to a conversation', $args);
        }

        return $hasTitle
            ? Yii::t('MailModule.base', '{senderName} created a new conversation {conversationTitle}', $args)
            : Yii::t('MailModule.base', '{senderName} created a new conversation', $args);
    }

    /**
     * @inheritdoc
     */
    protected function getRedactedMessage(array $params): ?string
    {
        return !empty($this->payload['invited'])
            ? Yii::t('MailModule.base', '{displayName} added you to a conversation.', $params)
            : Yii::t('MailModule.base', '{displayName} started a conversation with you.', $params);
    }
}
