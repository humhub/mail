<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\models\states;

/**
 * This class informs about new joined user within a conversation.
 *
 * @package humhub.modules.mail.models.states
 * @since 2.1
 */
class MessageUserJoined extends AbstractMessageState
{
    /**
     * @inheritdoc
     */
    public static function type(): int
    {
        return self::TYPE_USER_JOINED;
    }
}
