<?php

namespace humhub\modules\mail\models;

use humhub\components\ActiveRecord;
use humhub\modules\mail\models\states\MessageUserJoined;
use humhub\modules\mail\models\states\MessageUserLeft;
use humhub\modules\user\models\User;
use Yii;

/**
 * This class represents the relation between users and conversations.
 *
 * This is the model class for table "user_message".
 *
 * The followings are the available columns in table 'user_message':
 * @property int $message_id
 * @property int $user_id
 * @property int $is_originator
 * @property string $last_viewed
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 * @property int $pinned
 *
 * @property-read Message $message
 * @property-read User $user
 *
 * @package humhub.modules.mail.models
 * @since 0.5
 */
class UserMessage extends ActiveRecord
{
    public bool $informAfterAdd = true;

    private const NEW_MESSAGE_COUNT_CACHE_KEY = 'mail.newMessageCount.';

    /**
     * TTL for the new-message-count cache, as a safety net on top of the explicit invalidation
     * below (see getNewMessageCount()) - core's FileCache is configured without a default
     * duration (the old cacheExpireTime setting was removed, see migration
     * m250807_194741_remove_cache_settings), so without this a value would otherwise never expire.
     */
    private const NEW_MESSAGE_COUNT_CACHE_DURATION = 60;

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'user_message';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            [['message_id', 'user_id'], 'required'],
            [['message_id', 'user_id', 'is_originator', 'created_by', 'updated_by'], 'integer'],
            [['last_viewed', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getMessage()
    {
        return $this->hasOne(Message::class, ['id' => 'message_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'message_id' => Yii::t('MailModule.base', 'Message'),
            'user_id' => Yii::t('MailModule.base', 'User'),
            'is_originator' => Yii::t('MailModule.base', 'Is Originator'),
            'last_viewed' => Yii::t('MailModule.base', 'Last Viewed'),
            'created_at' => Yii::t('MailModule.base', 'Created At'),
            'created_by' => Yii::t('MailModule.base', 'Created By'),
            'updated_at' => Yii::t('MailModule.base', 'Updated At'),
            'updated_by' => Yii::t('MailModule.base', 'Updated By'),
        ];
    }

    /**
     * Returns the new message count for given User Id
     *
     * This is polled frequently by the frontend, so the result is cached per user. The cache is
     * invalidated explicitly whenever something that could change the count happens (a new message
     * entry, a conversation being seen, a participant joining/leaving - see afterSave()/afterDelete()
     * here and in AbstractMessageEntry). A short TTL (NEW_MESSAGE_COUNT_CACHE_DURATION) is set on
     * top of that as a safety net against the classic read-then-set race (a poll recomputes the
     * count between another request's invalidation and its own cache write) and against any writes
     * to message/user_message that happen outside this module's AR events.
     *
     * @param User|int|string|null $userId
     * @return int
     */
    public static function getNewMessageCount($userId = null)
    {
        $userId = $userId instanceof User
            ? $userId->id
            : (is_scalar($userId) ? (int) $userId : Yii::$app->user->id);

        if (!$userId) {
            return 0;
        }

        return Yii::$app->cache->getOrSet(
            self::NEW_MESSAGE_COUNT_CACHE_KEY . $userId,
            fn() => static::findByUser($userId)
                ->andWhere(['!=', 'message.updated_by', $userId])
                ->andWhere('message.updated_at > user_message.last_viewed OR user_message.last_viewed IS NULL')
                ->count(),
            self::NEW_MESSAGE_COUNT_CACHE_DURATION,
        );
    }

    /**
     * @param int $userId
     */
    public static function invalidateNewMessageCountCache($userId): void
    {
        Yii::$app->cache->delete(self::NEW_MESSAGE_COUNT_CACHE_KEY . $userId);
    }

    public static function findByUser($userId = null)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if ($userId instanceof User) {
            $userId = $userId->id;
        }

        return static::find()->joinWith('message')
            ->where(['user_message.user_id' => $userId])
            ->orderBy([
                'user_message.pinned' => SORT_DESC,
                'message.updated_at' => SORT_DESC,
            ]);
    }

    public function isUnread(): bool
    {
        return $this->message->updated_at > $this->last_viewed;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // A new participant may already have unread history, and a `last_viewed` change (see
        // Message::seen()) directly changes this user's own count.
        if ($insert || array_key_exists('last_viewed', $changedAttributes)) {
            static::invalidateNewMessageCountCache($this->user_id);
        }

        if ($insert && $this->informAfterAdd) {
            MessageUserJoined::inform($this->message, $this->user);
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();
        static::invalidateNewMessageCountCache($this->user_id);
        MessageUserLeft::inform($this->message, $this->user);
    }
}
