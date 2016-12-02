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
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->title = \Yii::t('MailModule.permissions', 'Send Mail');
        $this->description = \Yii::t('MailModule.permissions', 'Allows the user to send mails');
    }
    
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
    protected $title;

    /**
     * @inheritdoc
     */
    protected $description;

    /**
     * @inheritdoc
     */
    protected $moduleId = 'mail';

}
