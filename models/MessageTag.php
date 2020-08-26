<?php


namespace humhub\modules\mail\models;


use humhub\components\ActiveRecord;
use humhub\modules\content\models\Content;
use Yii;
use yii\db\conditions\ExistsCondition;
use yii\db\conditions\LikeCondition;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class MessageTag
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $color
 * @property integer $sort_order
 */
class MessageTag extends ActiveRecord
{
    public static function tableName()
    {
        return 'message_tag';
    }

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['sort_order', 'integer'],
            ['name', 'unique', 'targetAttribute' => ['user_id', 'name'], 'when' => function(MessageTag $model) {
                return $model->isNewRecord || $model->isAttributeChanged('name');
            }, 'message' => Yii::t('MailModule.base', 'A tag with the same name already exists.')]
        ];
    }

    public static function search($userId, $keyword)
    {
        if(empty($userId)) {
            return [];
        }

        return static::find()
            ->where(['user_id' => $userId])
            ->andWhere(new LikeCondition('name', 'LIKE', $keyword))->orderBy('sort_order ASC')->limit(20)->all();
    }

    /**
     * Attaches the given topics to the given content instance.
     *
     * @param Content $content target content
     * @param int[]|int|MessageTag|MessageTag[] $topics either a single or array of topics or topic Ids to add.
     * @throws NotFoundHttpException
     */
    public static function attach($userId, Message $message, $tags = [])
    {
         /* @var $result static[] */
        $result = [];

        $userMessage = $message->getUserMessage();

        if(!$userMessage) {
            throw new NotFoundHttpException();
        }

        // Clear all relations and append them again
        static::deleteTagRelations($userId, $userMessage);

        if (empty($tags)) {
            return;
        }

        $tags = is_array($tags) ? $tags : [$tags];

        foreach ($tags as $tag) {
            if(is_string($tag) && strpos($tag, '_add:') === 0) {
                $newTag = new static([
                    'name' => substr($tag, strlen('_add:')),
                    'user_id' => $userId
                ]);

                if ($newTag->save()) {
                    $result[] = $newTag;
                }
            } elseif (is_numeric($tag)) {
                $tag = static::findOne(['id' => (int) $tag]);
                if ($tag && $tag->user_id === $userId) {
                    $result[] = $tag;
                }
            } elseif ($tag instanceof static && $tag->user_id === $userId) {
                $result[] = $tag;
            }
        }

        static::addTagsToConversation($userMessage, $result);
    }

    private static function deleteTagRelations($userId, UserMessage $message)
    {
        foreach (UserMessageTag::findAllByUserMessage($message)->all() as $userMessageTag)
        {
            $userMessageTag->delete();
        }
    }

    public function afterDelete()
    {
        foreach(UserMessageTag::find()->where(['tag_id' => $this->id])->all() as $messageTag) {
            $messageTag->delete();
        }
    }

    /**
     * @param Message $message
     * @param static[] $result
     */
    private static function addTagsToConversation(UserMessage $message, array $userTags)
    {
        foreach ($userTags as $userTag) {
           UserMessageTag::create($message, $userTag);
        }
    }

    /**
     * @param int $userId
     * @param Message $message
     * @return \yii\db\ActiveQuery
     */
    public static function findByMessage(int $userId, Message $message)
    {
        $existsQuery = UserMessageTag::find()
            ->where(['user_message_tag.message_id' => $message->id])
            ->andWhere('user_message_tag.tag_id = message_tag.id');
        return static::find()->where(['user_id' => $userId])->andWhere(new ExistsCondition('EXISTS', $existsQuery));
    }

    /**
     * @param int $userId
     * @return \yii\db\ActiveQuery
     */
    public static function findByUser(int $userId)
    {
        return static::find()->where(['user_id' => $userId])->orderBy('name');
    }
}