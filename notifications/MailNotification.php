<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\mail\notifications;

use humhub\modules\notification\components\BaseNotification;

class MailNotification extends BaseNotification
{
    public function category()
    {
        return new MailNotificationCategory();
    }
    
    public function html()
    {
        return 'Someone has sent a message'; //WIP
    }
    
    public function getUrl()
    {
        return Yii::$app->homeUrl . '/mail/mail';// To do: specific conversation
    }
}