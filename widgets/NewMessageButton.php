<?php

namespace humhub\modules\mail\widgets;

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
    public $icon = 'fa-plus';

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        $button = ModalButton::info($this->getLabel())->load(['/mail/mail/create', 'ajax' => 1, 'userGuid' => $this->guid]);

        if($this->icon) {
            $button->icon($this->icon);
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
        return ($this->guid)
            ? Yii::t('MailModule.widgets_views_newMessageButton', 'Send message')
            : Yii::t('MailModule.widgets_views_newMessageButton', 'New message');
    }
}

?>