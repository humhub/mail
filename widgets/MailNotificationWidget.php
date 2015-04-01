<?php

/**
 * @package humhub.modules.mail
 * @since 0.5
 */
class MailNotificationWidget extends HWidget
{

    public function init()
    {
        $assetPrefix = Yii::app()->assetManager->publish(dirname(__FILE__) . '/../assets', true, 0, defined('YII_DEBUG'));
        Yii::app()->clientScript->registerCssFile($assetPrefix . '/mail.css');
        Yii::app()->clientScript->registerScriptFile($assetPrefix . '/mail.js');

        Yii::app()->clientScript->setJavascriptVariable('mail_loadMessageUrl', $this->createUrl('//mail/mail/show', array('id' => '-messageId-')));
        Yii::app()->clientScript->setJavascriptVariable('mail_viewMessageUrl', $this->createUrl('//mail/mail/index', array('id' => '-messageId-')));
    }

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        $this->render('mailNotifications', array());
    }

}

?>