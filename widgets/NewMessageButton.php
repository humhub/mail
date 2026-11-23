<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\modules\mail\helpers\Url;
use humhub\widgets\modal\ModalButton;
use Yii;

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
     * @var string an empty string shows only the icon, named by its tooltip
     */
    public $label;

    /**
     * @var bool
     */
    public $right = true;

    /**
     * @var string
     */
    public $cssClass;

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        $iconOnly = $this->icon && $this->label === '';
        $button = ModalButton::light($iconOnly ? null : $this->getLabel())
            ->load(Url::toCreateConversation($this->guid))
            ->id($this->id);

        if ($this->icon) {
            $button->icon($this->icon);
        }

        if ($iconOnly) {
            // The core's icon-only button takes its aria-label from the tooltip
            $button->tooltip($this->guid
                ? Yii::t('MailModule.base', 'Send message')
                : Yii::t('MailModule.base', 'New message'));
        }

        if ($this->right) {
            $button->right();
        }

        if ($this->cssClass) {
            $button->cssClass($this->cssClass);
        }

        match ($this->size) {
            'sm', 'small' => $button->sm(),
            'lg', 'large' => $button->lg(),
            default => $button,
        };

        return $button;
    }

    public function getLabel()
    {
        if ($this->label !== null) {
            return $this->label;
        }

        return ($this->guid)
            ? Yii::t('MailModule.base', 'Send message')
            : Yii::t('MailModule.base', 'Message');
    }
}
