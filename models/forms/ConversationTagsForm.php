<?php


namespace humhub\modules\mail\models\forms;


use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageTag;
use Yii;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

class ConversationTagsForm extends Model
{
    public $tags;

    /**
     * @var Message
     */
    public $message;

    public function init()
    {
        parent::init();
        if(empty($this->tags)) {
            $this->tags = MessageTag::findByMessage(Yii::$app->user->id, $this->message)->all();
        }
    }

    public function rules()
    {
        return [
            ['tags', 'safe']
        ];
    }

    public function save()
    {
        MessageTag::attach(Yii::$app->user->id, $this->message, $this->tags);

        return true;
    }
}