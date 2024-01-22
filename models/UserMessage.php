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
 * @property integer $message_id
 * @property integer $user_id
 * @property integer $is_originator
 * @property string $last_viewed
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
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
     * @param int $userId
     * @return int
     */
    public static function getNewMessageCount($userId = null)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if($userId instanceof User) {
            $userId = $userId->id;
        }

        return static::findByUser($userId)
            ->andWhere("message.updated_at > user_message.last_viewed OR user_message.last_viewed IS NULL")
            ->andWhere(["<>", 'message.updated_by', $userId])->count();
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
            ->orderBy('message.updated_at DESC');
    }

    public function isUnread($userId = null)
    {
        if ($userId === null) {
            $userId = Yii::$app->user->id;
        }

        if($this->message->lastEntry && ($this->message->lastEntry->user_id === $userId)) {
            return false;
        }

        return $this->message->updated_at > $this->last_viewed;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

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
        MessageUserLeft::inform($this->message, $this->user);
    }
}
