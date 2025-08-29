<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\models\states;

use humhub\modules\mail\models\AbstractMessageEntry;
use humhub\modules\mail\models\Message;
use humhub\modules\user\models\User;

/**
 * This class informs about a message state within a conversation.
 *
 * @package humhub.modules.mail.models.states
 * @since 2.1
 */
abstract class AbstractMessageState extends AbstractMessageEntry
{
    /**
     * @inheritdoc
     */
    public function canEdit(?User $user = null): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function notify(bool $isNewConversation = false)
    {
    }

    /**
     * Insert a new message state
     *
     * @param Message $message
     * @param User $user
     * @return bool
     */
    public static function inform(Message $message, User $user): bool
    {
        return static::createForMessage($message, $user)->save();
    }
}
