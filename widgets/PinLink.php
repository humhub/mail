<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\widgets\Link;
use Yii;

class PinLink extends Widget
{
    public Message $message;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->message->isPinned()) {
            return Link::none(Yii::t('MailModule.base', 'Unpin'))
                ->action('mail.conversation.linkAction', Url::toUnpinConversation($this->message))
                ->icon('map-pin');
        }

        return Link::none(Yii::t('MailModule.base', 'Pin'))
            ->action('mail.conversation.linkAction', Url::toPinConversation($this->message))
            ->icon('map-pin');
    }
}
