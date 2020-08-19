<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\modules\mail\models\UserMessage;

/**
 * Mail notification dropdown inbox.
 */
class NotificationInbox extends Widget
{

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        return $this->render('notificationInbox', [
            'newMailMessageCount' => UserMessage::getNewMessageCount()
        ]);
    }
}