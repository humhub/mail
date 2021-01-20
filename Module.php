<?php

namespace humhub\modules\mail;

use humhub\modules\mail\notifications\MailNotificationDummy;
use humhub\modules\mail\notifications\MailNotificationDummy2;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\permissions\SendMail;
use humhub\modules\user\models\User;
use humhub\modules\mail\helpers\Url;
use Yii;

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
     * @var int Defines the initial page size of conversation overview inbox sidebar in messenger view. Should be > 6
     */
    public $inboxInitPageSize = 30;

    /**
     * @var int Defines the page size when loading more conversation page entries (autoscroller)
     */
    public $inboxUpdatePageSize = 5;

    /**
     * @var int Defines the initial message amount loaded for a conversation
     */
    public $conversationInitPageSize = 50;

    /**
     * @var int Defines the amount of messages loaded when loading more messages
     */
    public $conversationUpdatePageSize = 50;

    /**
     * @return static
     */
    public static function getModuleInstance()
    {
        return Yii::$app->getModule('mail');
    }

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::toConfig();
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
    public function hideInTopNav()
    {
        return !$this->settings->get('showInTopNav', false);
    }

}
