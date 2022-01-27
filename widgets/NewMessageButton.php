<?php

namespace humhub\modules\mail\widgets;

use humhub\modules\mail\helpers\Url;
use humhub\widgets\ModalButton;
use Yii;
use humhub\components\Widget;

class NewMessageButton extends Widget
{

    /**
     * @var string
     */
    public $guid;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $size = 'sm';

    /**
     * @var string
     */
    public $icon = null;

    /**
     * @var string
     */
    public $label;

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        $button = ModalButton::info($this->getLabel())->load(Url::toCreateConversation($this->guid))->id($this->id)->cssClass('btn-secondary');

        return $button;
    }

    public function getLabel()
    {
        if($this->label !== null) {
            return $this->label;
        }

        return ($this->guid)
            ? Yii::t('MailModule.widgets_views_newMessageButton', 'Send message')
            : Yii::t('MailModule.base', '+ Message');
    }
}

?>