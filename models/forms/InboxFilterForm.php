<?php


namespace humhub\modules\mail\models\forms;


use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\mail\models\UserMessageTag;
use humhub\modules\mail\Module;
use humhub\modules\ui\filter\models\QueryFilter;
use Yii;
use yii\base\InvalidCallException;
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

    /**
     * @inheritDoc
     */
    public $autoLoad = self::AUTO_LOAD_ALL;

    /**
     * @inheritDoc
     */
    public $formName = '';

    /**
     * @var int
     */
    public $from;

    /**
     * @var int
     */
    public $ids;

    /**
     * @var
     */
    private $wasLastPage;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['term'], 'trim'],
            [['participants'], 'safe'],
            [['tags'], 'safe'],
            [['from'], 'integer'],
            [['ids'], 'integer'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->query = UserMessage::findByUser();
    }

    /**
     * @inheritDoc
     */
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
                    ->andWhere('user_message.user_id = user_message_tag.user_id')
                    ->andWhere(['user_message_tag.tag_id' => $tag]);
                $this->query->andWhere(new ExistsCondition('EXISTS', $participantsExistsSubQuery));
            }
        }

        if(!empty($this->from)) {
            $message = Message::findOne(['id' => $this->from]);
            if(!$message) {
                throw new InvalidCallException();
            }
            $this->query->andWhere(['<=', 'message.updated_at', $message->updated_at]);
            $this->query->andWhere(['<>', 'message.id', $message->id]);
        }

        if(!empty($this->ids)) {
            $this->query->andWhere(['IN', 'user_message.message_id', $this->ids]);
        }
    }

    private function createTermLikeCondition($column)
    {
        return new LikeCondition($column, 'LIKE', $this->term);
    }

    /**
     * @return UserMessage[]
     */
    public function getPage()
    {
        $this->apply();
        $module = Module::getModuleInstance();
        $pageSize = $this->from ? $module->inboxUpdatePageSize : $module->inboxInitPageSize;
        $result = $this->query->limit($pageSize)->all();
        $this->wasLastPage = count($result) < $pageSize;
        return $result;
    }

    public function wasLastPage()
    {
        if($this->wasLastPage === null) {
            throw new InvalidCallException();
        }

        return (int) $this->wasLastPage;
    }

    public function formName()
    {
        return '';
    }
}