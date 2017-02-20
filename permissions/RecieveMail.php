<?php
/**
 * Created by PhpStorm.
 * User: jerem
 * Date: 2/19/2017
 * Time: 3:14 PM
 */

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\permissions;

use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use Yii;

/**
 * Send Mail Permission
 */
class RecieveMail extends \humhub\libs\BasePermission
{
    //protected $defaultState = self::STATE_ALLOW;
    /**
     * @inheritdoc
     */
    protected $moduleId = 'mail';

    protected $defaultState = self::STATE_DENY;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->title = 'Receive Mail';
        $this->description = 'Receive messages from users with mail privileges. ';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultState($groupId)
    {
        return parent::getDefaultState($groupId);

    }

    /**
     * A list of groupIds which allowed per default.
     *
     * @var array default allowed groups
     */
    protected $defaultAllowedGroups = [
        Space::USERGROUP_ADMIN,
    ];

}
