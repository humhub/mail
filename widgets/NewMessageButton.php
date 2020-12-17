<?php

namespace humhub\modules\mail\widgets;

use humhub\modules\ui\icon\widgets\Icon;
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
     * @var boolean
     */
    public $right = false;

    /**
     * @var string
     */
    public $cssClass;

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        $button = ModalButton::info($this->getLabel())->load(Url::toCreateConversation($this->guid))->id($this->id);

        if($this->icon) {
            $button->icon($this->icon);
        }

        if($this->right) {
            $button->right();
        }

        if($this->cssClass) {
            $button->cssClass($this->cssClass);
        }

        switch ($this->size) {
            case 'sm':
            case 'small':
                $button->sm();
                break;
            case 'lg':
            case 'large':
                $button->lg();
                break;
            case 'xs':
            case 'extraSmall':
                $button->xs();
                break;
        }

        return $button;
    }

    public function getLabel()
    {
        if($this->label !== null) {
            return $this->label;
        }

        return ($this->guid)
            ? Icon::get('envelope')->tooltip(Yii::t('MailModule.widgets_views_newMessageButton', 'Send message'))	
            : Icon::get('plus')->right()->tooltip(Yii::t('MailModule.base', '+ Message'));
    }
}

?>
