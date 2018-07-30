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
    public $size;

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        return ModalButton::info($this->getLabel())->icon('fa-plus')->load(['/mail/mail/create', 'ajax' => 1, 'userGuid' => $this->guid])->sm()->right();
    }

    public function getLabel()
    {
        return ($this->guid)
            ? Yii::t('MailModule.widgets_views_newMessageButton', 'Send message')
            : Yii::t('MailModule.widgets_views_newMessageButton', 'New message');
    }
}

?>