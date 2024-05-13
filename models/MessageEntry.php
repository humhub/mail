<?php

namespace humhub\modules\mail\models;

use DateTime;
use humhub\modules\content\widgets\richtext\AbstractRichText;
use humhub\modules\content\widgets\richtext\converter\RichTextToEmailHtmlConverter;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\like\interfaces\LikeNotificationInterface;
use humhub\modules\mail\helpers\Url;
use humhub\modules\user\models\User;
use Yii;
use yii\base\InvalidConfigException;

/**
 * This class represents a message text within a conversation.
 *
 * @package humhub.modules.mail.models
 * @since 0.5
 */
class MessageEntry extends AbstractMessageEntry implements LikeNotificationInterface
{
    /**
     * @inheritdoc
     */
    public static function type(): int
    {
        return self::TYPE_MESSAGE;
    }

    /**
     * @inheritdoc
     */
    public function canEdit(?User $user = null): bool
    {
        if ($this->type !== self::type()) {
            return false;
        }

        if ($user === null) {
            if (Yii::$app->user->isGuest) {
                return false;
            }
            $user = Yii::$app->user;
        }

        return $this->created_by === $user->id;
    }

    /**
     * @inheritdoc
     */
    public function notify(bool $isNewConversation = false)
    {
        $messageNotification = new MessageNotification($this->message, $this);
        $messageNotification->isNewConversation = $isNewConversation;
        $messageNotification->notifyAll();
    }

    public function isFirstToday(): bool
    {
        $today = Yii::$app->formatter->asDatetime(new DateTime('today'), 'php:Y-m-d H:i:s');

        return !MessageEntry::find()
            ->where(['message_id' => $this->message_id])
            ->andWhere(['!=', 'id', $this->id])
            ->andWhere(['>=', 'created_at', $today])
            ->exists();
    }

    /**
     * @inerhitdoc
     * @throws InvalidConfigException
     */
    public function getLikeNotificationPlainTextPreview(): string
    {
        return Yii::t('MailModule.base', 'Message') . ' ' . RichText::convert($this->content, AbstractRichText::FORMAT_SHORTTEXT);
    }

    /**
     * @inerhitdoc
     * @throws InvalidConfigException
     */
    public function getLikeNotificationHtmlPreview(): string
    {
        return RichTextToEmailHtmlConverter::process($this->content);
    }

    public function getLikeNotificationUrl(bool $scheme = false): string
    {
        return Url::toMessenger($this->message, $scheme);
    }
}
