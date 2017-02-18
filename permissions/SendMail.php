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
    protected $moduleId = 'mail';

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->title = 'Send Mail';
        $this->description = 'Can send and receive messages from users with mail previlieges. ';
    }

    /**
     * @inheritdoc
     */
    public function getDefaultState($groupId)
    {
        return parent::getDefaultState($groupId);

    }

}
