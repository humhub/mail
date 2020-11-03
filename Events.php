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

/**
 * Description of Events
 *
 * @author luke
 */
class Events
{

    /**
     * @param $event
     */
    public static function onIntegrityCheck($event)
    {
        $integrityController = $event->sender;

        $integrityController->showTestHeadline("Mail Module (" . MessageEntry::find()->count() . " message entries)");

        foreach (MessageEntry::find()->each() as $messageEntry) {
            if(!$messageEntry->getUser()->count()) {
                if ($integrityController->showFix("Deleting message entry id " . $messageEntry->id . " without existing user!")) {
                    $messageEntry->delete();
                }
            }
        }

        $integrityController->showTestHeadline("Mail Module (" . UserMessage::find()->count() . " user message entries)");

        foreach (UserMessage::find()->each() as $userMessage) {
            if(!$userMessage->getUser()->count()) {
                if ($integrityController->showFix("Deleting user message id " . $userMessage->message_id . " without existing user!")) {
                    $userMessage->delete();
                }
            }
        }

        $integrityController->showTestHeadline("Mail Module (" . UserMessageTag::find()->count() . " user message tag entries)");

        foreach (UserMessageTag::find()->each() as $messageTag) {
            if(!$messageTag->getUser()->count()) {
                if ($integrityController->showFix("Deleting user tag id " . $messageTag->id . " without existing user!")) {
                    $messageTag->delete();
                }
            }
        }
    }

    /**
     * On User delete, also delete all comments
     *
     * @param type $event
     * @return bool
     */
    public static function onUserDelete($event)
    {
        try {
            foreach (MessageEntry::findAll(['user_id' => $event->sender->id]) as $messageEntry) {
                $messageEntry->delete();
            }

            foreach (UserMessage::findAll(['user_id' => $event->sender->id]) as $userMessage) {
                if($userMessage->message) {
                    $userMessage->message->leave($event->sender->id);
                }

                $userMessage->delete();
            }
            
            foreach (UserMessageTag::findAll(['user_id' => $event->sender->id]) as $userMessageTag) {
                $userMessageTag->delete();
            }
        } catch(\Exception $e) {
            Yii::error($e);
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
        try {
            if (Yii::$app->user->isGuest) {
                return;
            }

            $module = Config::getModule();
            // See https://github.com/humhub/humhub-modules-mail/issues/201
            if (method_exists($module, 'hideInTopNav') && !$module->hideInTopNav()) {
                $event->sender->addItem([
                    'label' => Yii::t('MailModule.base', 'Messages'),
                    'url' => Url::toMessenger(),
                    'icon' => '<i class="fa fa-envelope"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'mail'),
                    'sortOrder' => 300,
                ]);
            }
        } catch(\Exception $e) {
            Yii::error($e);
        }
    }

    public static function onNotificationAddonInit($event)
    {
        try {
            if (Yii::$app->user->isGuest) {
                return;
            }

            $event->sender->addWidget(NotificationInbox::className(), [], ['sortOrder' => 90]);
        } catch (\Exception $e) {
            Yii::error($e);
        }
    }

    public static function onProfileHeaderControlsInit($event)
    {
        try {
            /** @var User $profileContainer */
            $profileContainer = $event->sender->user;

            if ($profileContainer->isCurrentUser() || !Yii::$app->user->can(StartConversation::class)) {
                return;
            }

            // Is the current logged user allowed to send mails to profile user?
            if (!Yii::$app->user->isAdmin() && !$profileContainer->can(SendMail::class)) {
                return;
            }

            $event->sender->addWidget(NewMessageButton::class, ['guid' => $event->sender->user->guid, 'size' => null, 'icon' => null], ['sortOrder' => 90]);
        } catch (\Exception $e) {
            Yii::error($e);
        }
    }

}
