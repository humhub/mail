<?php

/**
 * @package humhub.modules.mail
 * @since   0.5.9
 */
class NewMessageButtonWidget extends HWidget
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

        $assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__) . '/../resources', true, 0, defined('YII_DEBUG'));
        Yii::app()->clientScript->registerCssFile($assetPrefix . '/mail.css');

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

        $this->render('newMessageButton', $params);
    }

}

?>