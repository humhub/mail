<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail;


use Yii;
use yii\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\Notifications;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\mail\permissions\RecieveMail;

/**
 * Description of Events
 *
 * @author luke
 */
class Events extends \yii\base\Object
{

    /**
     * On User delete, also delete all comments
     *
     * @param type $event
     */
    public static function onUserDelete($event)
    {

        foreach (MessageEntry::findAll(array('user_id' => $event->sender->id)) as $messageEntry) {
            $messageEntry->delete();
        }
        foreach (UserMessage::findAll(array('user_id' => $event->sender->id)) as $userMessage) {
            $userMessage->message->leave($event->sender->id);
        }

        return true;
    }

    /**
     * On build of the TopMenu, check if module is enabled
     * When enabled and the user or the users group has access to
     * mail privileges add a menu item.
     *
     * @param type $event
     */
    public static function onTopMenuInit($event)
    {

        if (Yii::$app->user->can(new SendMail())) {
            $event->sender->addItem(array(
                'label' => Yii::t('MailModule.base', 'Messages'),
                'url' => Url::to(['/mail/mail/index']),
                'icon' => '<i class="fa fa-envelope"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'mail'),
                'sortOrder' => 300,
            ));
        }

       return;
    }

    public static function onNotificationAddonInit($event)
    {
        if (Yii::$app->user->isGuest || (!Yii::$app->user->can(new permissions\SendMail()))) {
            return;
        }

        $event->sender->addWidget(Notifications::className(), array(), array('sortOrder' => 90));
    }

    public static function onProfileHeaderControlsInit($event)
    {
        $profileUser = $event->sender->user;


        $recieveMail = $profileUser->can(new permissions\RecieveMail());
        $permitted = true;
        if(version_compare(Yii::$app->version, '1.1', '>=')) {


            if (!(Yii::$app->user->can(new permissions\SendMail()) && $profileUser->can(new permissions\RecieveMail()))){
               $permitted = false;
            }
        }
        
        if (Yii::$app->user->isGuest || $profileUser->id == Yii::$app->user->id || !$permitted) {
            return;
        }

        $event->sender->addWidget(NewMessageButton::className(), array('guid' => $event->sender->user->guid, 'type' => 'info'), array('sortOrder' => 90));
    }

}
