<?php

namespace humhub\modules\mail;

use humhub\modules\mail\notifications\ConversationNotificationCategory;
use humhub\modules\mail\notifications\MailNotificationDummy;
use humhub\modules\mail\notifications\MailNotificationDummy2;

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
        if ($contentContainer !== null && $contentContainer instanceof \humhub\modules\user\models\User) {
            return [
                new permissions\SendMail()
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
