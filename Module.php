<?php

namespace humhub\modules\mail;

use humhub\modules\mail\notifications\MailNotificationDummy;
use humhub\modules\mail\notifications\MailNotificationDummy2;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\User;

/**
 * MailModule provides messaging functions inside the application.
 *
 * @package humhub.modules.mail
 * @since 0.5
 */
class Module extends \humhub\components\Module
{

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if(!$contentContainer) {
            return [
                new StartConversation()
            ];
        } else if ($contentContainer instanceof User) {
            return [
                new SendMail()
            ];
        }

        return [];
    }

    public function getNotifications()
    {
        return [
            MailNotificationDummy::class,
            MailNotificationDummy2::class
        ];
    }

}
