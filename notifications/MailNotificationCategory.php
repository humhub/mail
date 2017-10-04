<?php

namespace humhub\modules\mail\notifications;

use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\targets\MobileTarget;
use humhub\modules\notification\targets\WebTarget;
use Yii;

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

class MailNotificationCategory extends NotificationCategory
{
    public $id = 'mail';

    public function getFixedSettings()
    {
        $webTarget = Yii::createObject(WebTarget::class);
        $mobileTarget = Yii::createObject(MobileTarget::class);
        return [
            $webTarget->id,
            $mobileTarget->id
        ];
    }

    /**
     * Returns a human readable title of this  category
     */
    public function getTitle()
    {
        return Yii::t('MailModule.base', 'Message');
    }

    /**
     * Returns a group description
     */
    public function getDescription()
    {
        return Yii::t('MailModule.base', 'Receive Notifications when someone sends you a message.');
    }
}