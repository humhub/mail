<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\modules\mail\models\UserMessage;

/**
 * @package humhub.modules.mail
 * @since 0.5
 */
class Notifications extends Widget
{

    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        return $this->render('notifications', array(
                    'newMailMessageCount' => UserMessage::getNewMessageCount()
        ));
    }

}

?>