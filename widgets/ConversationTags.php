<?php


namespace humhub\modules\mail\widgets;


use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\models\MessageTag;
use Yii;

class ConversationTags extends Widget
{
    const ID = 'conversation-tags-root';

    public $message;

    public function run()
    {   $tags = MessageTag::findByMessage(Yii::$app->user->id, $this->message)->all();

        $result = Html::beginTag('div', ['id' => static::ID, 'class' => 'panel-body', 'style' => ['display' => count($tags) ? 'block' : 'none']]);

        $result .= '<span class="my-tags-label">'.Yii::t('MailModule.base', 'My Tags').'</span>';

        foreach ($tags as $tag) {
            $result .= ConversationTagBadge::get($tag).'&nbsp;';
        }

        $result .= ConversationTagBadge::getEditConversationTagBadge($this->message, (empty($tags) ? 'plus' : 'pencil'));
        $result .= Html::endTag('div');

        return $result;
    }

}