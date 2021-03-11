<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail;

use humhub\modules\mail\models\UserMessageTag;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\user\models\User;
use Yii;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\NotificationInbox;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\mail\models\Config;
use humhub\modules\rest\Module as RestModule;

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
     * @return bool
     */
    public static function onUserDelete($event)
    {

        foreach (MessageEntry::findAll(['user_id' => $event->sender->id]) as $messageEntry) {
            $messageEntry->delete();
        }

        foreach (UserMessage::findAll(['user_id' => $event->sender->id]) as $userMessage) {
            $userMessage->message->leave($event->sender->id);
        }

        foreach (UserMessageTag::findAll(['user_id' => $event->sender->id]) as $userMessageTag) {
            $userMessageTag->message->leave($event->sender->id);
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

        if(!Config::getModule()->hideInTopNav()){
            $event->sender->addItem([
                'label' => Yii::t('MailModule.base', 'Messages'),
                'url' => Url::toMessenger(),
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

        $event->sender->addWidget(NotificationInbox::className(), [], ['sortOrder' => 90]);
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

    public static function onRestApiAddRules()
    {
        /* @var RestModule $restModule */
        $restModule = Yii::$app->getModule('rest');
        $restModule->addRules([

            // Conversations
            ['pattern' => 'mail', 'route' => 'mail/rest/message/index', 'verb' => 'GET'],
            ['pattern' => 'mail/<id:\d+>', 'route' => 'mail/rest/message/view', 'verb' => 'GET'],
            ['pattern' => 'mail', 'route' => 'mail/rest/message/create', 'verb' => 'POST'],

            // Participants
            ['pattern' => 'mail/<messageId:\d+>/users', 'route' => 'mail/rest/user/index', 'verb' => 'GET'],
            ['pattern' => 'mail/<messageId:\d+>/user/<userId:\d+>', 'route' => 'mail/rest/user/add', 'verb' => 'POST'],
            ['pattern' => 'mail/<messageId:\d+>/user/<userId:\d+>', 'route' => 'mail/rest/user/leave', 'verb' => 'DELETE'],

            // Entries
            ['pattern' => 'mail/<messageId:\d+>/entries', 'route' => 'mail/rest/entry/index', 'verb' => 'GET'],
            ['pattern' => 'mail/<messageId:\d+>/entry', 'route' => 'mail/rest/entry/add', 'verb' => 'POST'],
            ['pattern' => 'mail/<messageId:\d+>/entry/<entryId:\d+>', 'route' => 'mail/rest/entry/view', 'verb' => 'GET'],
            ['pattern' => 'mail/<messageId:\d+>/entry/<entryId:\d+>', 'route' => 'mail/rest/entry/update', 'verb' => 'PUT'],
            ['pattern' => 'mail/<messageId:\d+>/entry/<entryId:\d+>', 'route' => 'mail/rest/entry/delete', 'verb' => 'DELETE'],

            // Tags
            ['pattern' => 'mail/<messageId:\d+>/tags', 'route' => 'mail/rest/tag/index', 'verb' => 'GET'],
            ['pattern' => 'mail/<messageId:\d+>/tags', 'route' => 'mail/rest/tag/update', 'verb' => 'PUT'],

        ], 'mail');
    }

}
