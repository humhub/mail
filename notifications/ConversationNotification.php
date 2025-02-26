<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\mail\notifications;

use humhub\modules\notification\components\BaseNotification;

class ConversationNotification extends BaseNotification
{
    public function category()
    {
        return new ConversationNotificationCategory();
    }

}
