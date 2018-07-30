<?php

namespace humhub\modules\mail;

use humhub\modules\mail\notifications\MailNotificationDummy;
use humhub\modules\mail\notifications\MailNotificationDummy2;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\User;
use yii\helpers\Url;

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
    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/mail/config']);
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if (!$contentContainer) {
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

    /**
     * Determines showInTopNav is enabled or not
     *
     * @return boolean is showInTopNav enabled
     */
    public function showInTopNav()
    {
        return !$this->settings->get('showInTopNav', false);
    }

}
