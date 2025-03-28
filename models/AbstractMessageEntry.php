<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\models;

use humhub\components\ActiveRecord;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\live\UserMessageDeleted;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This class represents abstract class for normal message and state entries within a conversation.
 *
 * The followings are the available columns in table 'message_entry':
 * @property int $id
 * @property int $message_id
 * @property int $user_id
 * @property string $content
 * @property int $type
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * The followings are the available model relations:
 * @property Message $message
 * @property User $user
 *
 * @package humhub.modules.mail.models
 * @since 2.1
 */
abstract class AbstractMessageEntry extends ActiveRecord
{
    public const TYPE_MESSAGE = 0;
    public const TYPE_USER_JOINED = 1;
    public const TYPE_USER_LEFT = 2;

    /**
     * Get type of the message entry
     *
     * @return int
     */
    abstract public static function type(): int;

    /**
     * Check if the given or current User can edit the message entry
     *
     * @param User|null $user
     * @return bool
     */
    abstract public function canEdit(?User $user = null): bool;

    /**
     * Notify Users about this message entry
     *
     * @var bool $isNewConversation
     */
    abstract public function notify(bool $isNewConversation = false);

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'message_entry';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['message_id', 'user_id'], 'required'],
            [['message_id', 'user_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->type = $this->type();
    }

    /**
     * @param Message $message
     * @param User $user
     * @param string|null $content
     * @return self
     */
    public static function createForMessage(Message $message, User $user, ?string $content = null): self
    {
        // Attach Message Entry
        return new static([
            'message_id' => $message->id,
            'user_id' => $user->id,
            'content' => $content,
            'type' => static::type(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord && $this->type != self::TYPE_USER_LEFT) {
            // Updates the updated_at attribute
            $this->message->save();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        RichText::postProcess($this->content, $this);
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        if ($this->message instanceof Message) {
            foreach ($this->message->users as $user) {
                Yii::$app->live->send(new UserMessageDeleted([
                    'contentContainerId' => $user->contentcontainer_id,
                    'message_id' => $this->message_id,
                    'entry_id' => $this->id,
                    'user_id' => $user->id,
                ]));
            }
        }

        parent::afterDelete();
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getMessage(): ActiveQuery
    {
        return $this->hasOne(Message::class, ['id' => 'message_id']);
    }
}
