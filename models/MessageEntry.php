<?php

namespace humhub\modules\mail\models;

use humhub\interfaces\ViewableInterface;
use humhub\components\db\AfterCommit;
use humhub\modules\mail\notifications\ConversationNotification;
use humhub\modules\mail\notifications\MailNotification;
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
     * Updates the inbox of all participants and notifies the other participants: of a new
     * conversation by {@see ConversationNotification}, of a further message by {@see MailNotification}.
     *
     * @inheritdoc
     */
    public function notify(bool $isNewConversation = false)
    {
        $message = $this->message;

        // After the commit: the clients fetch the message on the live event
        AfterCommit::run(fn() => $message->sendLiveEvents(), static::getDb());

        // The sender is left out by the dispatch, as the originator.
        $recipients = UserMessage::find()
            ->select('user_id')
            ->where(['message_id' => $message->id])
            ->column();

        try {
            // Each message is an event of its own: no dedupe by conversation and sender
            $notificationClass = $isNewConversation ? ConversationNotification::class : MailNotification::class;
            $notificationClass::send(
                array_map('intval', $recipients),
                source: $message,
                originator: $this->user,
                payload: ['entryId' => (int)$this->id],
                dedupe: false,
            );
        } catch (\Throwable $e) {
            Yii::error('Could not dispatch the notification of message entry #' . $this->id . ': ' . $e, 'mail');
        }
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
