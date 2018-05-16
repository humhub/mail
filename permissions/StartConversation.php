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
 * Group permission restricting users from creating new conversations
 */
class StartConversation extends \humhub\libs\BasePermission
{

    /**
     * @inheritdoc
     */
    protected $moduleId = 'mail';

    /**
     * @inheritdoc
     */
    protected $defaultState = self::STATE_ALLOW;

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('MailModule.base', 'Start new conversations');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('MailModule.base', 'Allow users to start new conversations');
    }

}
