<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\live;

use humhub\modules\live\components\LiveEvent;
use humhub\modules\content\models\Content;
use humhub\modules\mail\models\UserMessage;

/**
 * Live event for new notifications
 *
 * @since 1.2
 */
class UserMessageDeleted extends LiveEvent
{
    /**
     * @var int
     */
    public $message_id;

    /**
     * @var int the id of the new notification
     */
    public $user_id;

    /**
     * @var int
     */
    public $entry_id;

    /**
     * @var int
     */
    public $count;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->visibility = Content::VISIBILITY_OWNER;
        $this->count = UserMessage::getNewMessageCount($this->user_id);
    }

}
