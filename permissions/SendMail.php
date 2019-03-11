<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\permissions;

use humhub\modules\user\models\Group;
use humhub\modules\user\models\User;
use Yii;

/**
 * Send Mail Permission
 */
class SendMail extends \humhub\libs\BasePermission
{

    /**
     * @inheritdoc
     */
    protected $moduleId = 'mail';

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        User::USERGROUP_USER,
        User::USERGROUP_FRIEND
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_GUEST
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('MailModule.base', 'Receive private messages');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('MailModule.base', 'Allow others to send you private messages');
    }

    public function getDefaultState($groupId)
    {
        if ($groupId == Group::getAdminGroupId()) {
            return self::STATE_ALLOW;
        }

        return parent::getDefaultState($groupId);
    }

}
