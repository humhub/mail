<?php

namespace humhub\modules\mail\widgets;

use Yii;
use humhub\components\Widget;

/**
 * @package humhub.modules.mail
 * @since   0.5.9
 */
class NewMessageButton extends Widget
{

    public $guid = null;
    public $id = null;
    public $type = 'default';
    public $size = null;

    /**
     * Creates the Wall Widget
     */
    public function run()
    {

        $class = 'btn btn-' . $this->type;
        if (!empty($this->size)) {
            $class .= ' btn-' . $this->size;
        }

        $params = array(
            'guid' => $this->guid,
            'id' => $this->id,
            'class' => $class,
        );

        // if guid is set, then change button label to "Send message"
        if (!empty($this->guid)) {
            $params['buttonLabel'] = Yii::t('MailModule.widgets_views_newMessageButton', 'Send message');
        } else {
            $params['buttonLabel'] = Yii::t('MailModule.widgets_views_newMessageButton', 'New message');
        }

        return $this->render('newMessageButton', $params);
    }

}

?>