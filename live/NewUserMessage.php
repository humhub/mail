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
use humhub\modules\user\models\User;

/**
 * Live event for new notifications
 *
 * @since 1.2
 */
class NewUserMessage extends LiveEvent
{
    /**
     * @var int the id of the new notification
     * @deprecated since v2.0 only in use for compatibility, can be removed in v2.0.1
     */
    public $user_id;

    /**
     * @var int the id of the new notification
     */
    public $user_guid;
    
    /**
     * @var string text representation used for frotnend desktop notifications 
     */
    public $message_id;

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
        $this->count = UserMessage::getNewMessageCount(User::findOne(['guid' => $this->user_guid]));
    }

}
