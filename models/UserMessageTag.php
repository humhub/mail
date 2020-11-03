<?php


namespace humhub\modules\mail\models;


use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * Class ConversationTag maps user tags to conversations.
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $message_id
 * @property integer $tag_id
 */
class UserMessageTag extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_message_tag';
    }


    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['message_id', 'unique', 'targetAttribute' => ['message_id', 'user_id', 'tag_id']]
        ];
    }

    /**
     * @param UserMessage $message
     * @param MessageTag $userTag
     */
    public static function create(UserMessage $message, MessageTag $userTag)
    {
        (new static(['message_id' => $message->message_id, 'user_id' => $message->user_id, 'tag_id' => $userTag->id]))->save();
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class,['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(MessageTag::class,['id' => 'tag_id']);
    }

    /**
     * @param $userId
     * @param UserMessage $message
     * @return ActiveQuery
     */
    public static function findAllByUserMessage(UserMessage $message)
    {
        return static::find()
            ->where(['message_id' => $message->message_id])
            ->andWhere(['user_id' => $message->user_id]);
    }
}