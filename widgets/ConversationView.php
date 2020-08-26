<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 09:29
 */

namespace humhub\modules\mail\widgets;

use humhub\widgets\JsWidget;
use humhub\modules\mail\helpers\Url;

class ConversationView extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'mail.ConversationView';

    /**
     * @inheritdoc
     */
    public $id = 'mail-conversation-root';

    /**
     * @inheritdoc
     */
    public $init = true;

    /**
     * @var int
     */
    public $messageId;

    public function getData()
    {
        return [
            'message-id' => $this->messageId,
            'load-message-url' => Url::toLoadMessage(),
            'load-update-url' => Url::toUpdateMessage(),
            'load-more-url' => Url::toLoadMoreMessages(),
            'mark-seen-url' => Url::toNotificationSeen()
        ];
    }
}