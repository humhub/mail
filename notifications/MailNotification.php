<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\mail\notifications;

use humhub\modules\notification\components\BaseNotification;
use humhub\modules\notification\components\Grouping;
use humhub\modules\notification\components\NotificationPriority;
use Yii;

/**
 * A new message in a conversation, dispatched by {@see \humhub\modules\mail\models\MessageEntry::notify()}
 * to the other participants. The source is the conversation ({@see \humhub\modules\mail\models\Message}),
 * the originator the sender, the payload's `entryId` the message.
 *
 * The messages of one conversation are grouped ("Anna sent you 3 messages in …"). The priority is
 * normal, so a burst of messages is collected by the delay of the mail and push channels; opening
 * the conversation marks them seen, which cancels their pending deliveries.
 *
 * Since 3.5 built on the record-bound {@see BaseNotification}.
 */
final class MailNotification extends BaseNotification
{
    use MessengerNotificationTrait;

    /**
     * @inheritdoc
     */
    public static function priority(): NotificationPriority
    {
        return NotificationPriority::Normal;
    }

    /**
     * The unseen messages of the same conversation: messages the user has read do not count.
     *
     * @inheritdoc
     */
    public static function grouping(): ?Grouping
    {
        return Grouping::bySource()->unseenOnly();
    }

    /**
     * @inheritdoc
     */
    protected function getMessage(array $params): string
    {
        $hasTitle = $params['conversationTitle'] !== '';

        if ($this->groupCount > 1 && $params['displayNames'] !== '') {
            $grouped = [
                'displayNames' => $params['displayNames'],
                'count' => $params['groupCount'],
                'conversationTitle' => $params['conversationTitle'],
            ];

            return $hasTitle
                ? Yii::t('MailModule.base', '{displayNames} sent you {count} messages in {conversationTitle}', $grouped)
                : Yii::t('MailModule.base', '{displayNames} sent you {count} messages', $grouped);
        }

        $single = [
            'senderName' => $params['displayName'],
            'conversationTitle' => $params['conversationTitle'],
        ];

        return $hasTitle
            ? Yii::t('MailModule.base', '{senderName} sent you a new message in {conversationTitle}', $single)
            : Yii::t('MailModule.base', '{senderName} sent you a new message', $single);
    }

    /**
     * @inheritdoc
     */
    protected function getRedactedMessage(array $params): ?string
    {
        if ($this->groupCount > 1 && $params['displayNames'] !== '') {
            return Yii::t('MailModule.base', '{displayNames} sent you {groupCount} messages.', $params);
        }

        return Yii::t('MailModule.base', '{displayName} sent you a new message.', $params);
    }
}
