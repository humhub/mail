<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail;

use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\Notifications;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\mail\models\Config;

/**
 * Description of Events
 *
 * @author luke
 */
class Events
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
     * When enabled add a menu item
     *
     * @param type $event
     */
    public static function onTopMenuInit($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $showInTopNav = false;

        // Workaround for module update problem
        if (method_exists(Config::getModule(), 'showInTopNav')) {
            $showInTopNav = Config::getModule()->showInTopNav();
        }

        if(!Config::getModule()->showInTopNav()){
            $event->sender->addItem([
                'label' => Yii::t('MailModule.base', 'Messages'),
                'url' => Url::to(['/mail/mail/index']),
                'icon' => '<i class="fa fa-envelope"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'mail'),
                'sortOrder' => 300,
            ]);
        }
    }

    public static function onNotificationAddonInit($event)
    {
        if (Yii::$app->user->isGuest) {
            return;
        }

        $event->sender->addWidget(Notifications::className(), [], ['sortOrder' => 90]);
    }

    public static function onProfileHeaderControlsInit($event)
    {
        /** @var User $profileContainer */
        $profileContainer = $event->sender->user;

        if($profileContainer->isCurrentUser() || !Yii::$app->user->can(StartConversation::class)) {
            return;
        }

        // Is the current logged user allowed to send mails to profile user?
        if(!Yii::$app->user->isAdmin() && !$profileContainer->can(SendMail::class)) {
            return;
        }

        $event->sender->addWidget(NewMessageButton::class, ['guid' => $event->sender->user->guid, 'size' => null, 'icon' => null], ['sortOrder' => 90]);
    }

}
