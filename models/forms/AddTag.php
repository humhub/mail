<?php


namespace humhub\modules\mail\models\forms;


use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageTag;
use Yii;
use yii\base\Model;
use yii\web\ForbiddenHttpException;

class AddTag extends Model
{
    /**
     * @var MessageTag
     */
    public $tag;

    public function init()
    {
        $this->tag = new MessageTag(['user_id' => Yii::$app->user->id]);
        parent::init();
    }

    public function load($data, $formName = null)
    {
        return $this->tag->load($data, $formName);
    }

    public function save()
    {
        return $this->tag->save();
    }
}