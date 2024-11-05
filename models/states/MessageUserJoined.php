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
     * The default value for the 'enableMessageUserJoined' setting.
     */
    const DEFAULT_ENABLE_MESSAGE_USER_JOINED = true;

    /**
     * The key for the 'enableMessageUserJoined' setting.
     */
    const SETTING_ENABLE_MESSAGE_USER_JOINED = 'enableMessageUserJoined';

    /**
     * @inheritdoc
     */
    public static function type(): int
    {
        return self::TYPE_USER_JOINED;
    }

    /**
     * Checks if the MessageUserJoined state is enabled.
     *
     * @param array $settings The settings array, where the 'enableMessageUserJoined' setting is stored.
     * @return bool
     */
    public static function isEnabled(array $settings): bool
    {
        return $settings[self::SETTING_ENABLE_MESSAGE_USER_JOINED] ?? self::DEFAULT_ENABLE_MESSAGE_USER_JOINED;
    }

    /**
     * Checks if the MessageUserJoined state is disabled.
     *
     * @param array $settings The settings array, where the 'enableMessageUserJoined' setting is stored.
     * @return bool
     */
    public static function isDisabled(array $settings): bool
    {
        return !self::isEnabled($settings);
    }
}
