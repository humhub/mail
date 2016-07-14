<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\permissions;

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
    public $defaultAllowedGroups = [
        User::USERGROUP_USER
    ];
    
    /**
     * @inheritdoc
     */
    public function getDefaultState($groupId)
    {
        if(version_compare(Yii::$app->version, '1.1', 'lt')) {
            return parent::getDefaultState($groupId);
        } else if(Yii::$app->getModule('friendship')->getIsEnabled()) {
            if($groupId === User::USERGROUP_FRIEND) {
                return self::STATE_ALLOW;
            } else {
                return self::STATE_DENY;
            }
        }
        
        return parent::getDefaultState($groupId);
    }

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_GUEST
    ];

    /**
     * @inheritdoc
     */
    protected $title = "Send Mail";

    /**
     * @inheritdoc
     */
    protected $description = "Allows the user to send mails";

    /**
     * @inheritdoc
     */
    protected $moduleId = 'mail';

}
