<?php


namespace humhub\modules\mail\models\forms;


use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\mail\models\UserMessageTag;
use humhub\modules\ui\filter\models\QueryFilter;
use Yii;
use yii\db\conditions\ExistsCondition;
use yii\db\conditions\LikeCondition;
use yii\db\conditions\OrCondition;
use yii\db\Expression;

class InboxFilterForm extends QueryFilter
{

    /**
     * @var string
     */
    public $term;

    /**
     * @var array
     */
    public $participants;

    /**
     * @var array
     */
    public $tags;

    public $autoLoad = self::AUTO_LOAD_ALL;

    public $formName = '';

    public function rules()
    {
        return [
            [['term'], 'trim'],
            [['participants'], 'safe'],
            [['tags'], 'safe'],
        ];
    }

    public function init()
    {
        parent::init();
        $this->query = UserMessage::findByUser();
    }

    public function apply()
    {
        if(!empty($this->term)) {
            $messageEntryContentSubQuery = MessageEntry::find()->where('message_entry.message_id = message.id')
                ->andWhere($this->createTermLikeCondition('message_entry.content'));

            $this->query->andWhere(new OrCondition([
                new ExistsCondition('EXISTS', $messageEntryContentSubQuery),
                $this->createTermLikeCondition('message.title')
            ]));
        }

        if(!empty($this->participants)) {
            foreach ($this->participants as $userGuid) {
                $participantsExistsSubQuery = UserMessage::find()->joinWith('user')->where('user_message.message_id = message.id')
                    ->andWhere(['user.guid' => $userGuid]);
                $this->query->andWhere(new ExistsCondition('EXISTS', $participantsExistsSubQuery));
            }

        }

        if(!empty($this->tags)) {
            foreach ($this->tags as $tag) {
                $participantsExistsSubQuery = UserMessageTag::find()
                    ->where('user_message.message_id = user_message_tag.message_id')
                    ->andWhere('user_message.user_id = user_message_tag.user_id');
                $this->query->andWhere(new ExistsCondition('EXISTS', $participantsExistsSubQuery));
            }
        }
    }

    private function createTermLikeCondition($column)
    {
        return new LikeCondition($column, 'LIKE', $this->term);
    }

    public function formName()
    {
        return '';
    }
}