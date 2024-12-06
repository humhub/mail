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
class MessageUserLeft extends AbstractMessageState
{
    /**
     * The default value for the 'enableMessageUserLeft' setting.
     */
    public const DEFAULT_ENABLE_MESSAGE_USER_LEFT = true;

    /**
     * The key for the 'enableMessageUserLeft' setting.
     */
    public const SETTING_ENABLE_MESSAGE_USER_LEFT = 'enableMessageUserLeft';

    /**
     * @inheritdoc
     */
    public static function type(): int
    {
        return self::TYPE_USER_LEFT;
    }

    /**
     * Checks if the MessageUserLeft state is enabled.
     *
     * @param array $settings The settings array, where the 'enableMessageUserLeft' setting is stored.
     * @return bool
     */
    public static function isEnabled(array $settings): bool
    {
        return $settings[self::SETTING_ENABLE_MESSAGE_USER_LEFT] ?? self::DEFAULT_ENABLE_MESSAGE_USER_LEFT;
    }

    /**
     * Checks if the MessageUserLeft state is disabled.
     *
     * @param array $settings The settings array, where the 'enableMessageUserLeft' setting is stored.
     * @return bool
     */
    public static function isDisabled(array $settings): bool
    {
        return !self::isEnabled($settings);
    }
}
