<?php

namespace humhub\modules\mail\models;

use DateTime;
use humhub\interfaces\ViewableInterface;
use humhub\modules\user\models\User;
use Yii;

/**
 * This class represents a message text within a conversation.
 *
 * @package humhub.modules.mail.models
 * @since 0.5
 */
class MessageEntry extends AbstractMessageEntry implements ViewableInterface
{
    /**
     * Scenario - when related content has attached files
     */
    public const SCENARIO_HAS_FILES = 'hasFiles';

    /**
     * @inheritdoc
     */
    public static function type(): int
    {
        return self::TYPE_MESSAGE;
    }

    /**
     * @inerhitdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['content'], 'required', 'except' => [self::SCENARIO_HAS_FILES]],
        ]);
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
     * @inheritdoc
     */
    public function canView($user = null): bool
    {
        $message = $this->message;

        return $message instanceof Message && $message->isParticipant($user);
    }
}
