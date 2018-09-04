<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 09:29
 */

namespace humhub\modules\mail\widgets\wall;


use humhub\widgets\JsWidget;
use yii\helpers\Url;

class ConversationView extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'mail.wall.ConversationView';

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
            'load-message-url' => Url::to(['/mail/mail/show']),
            'load-update-url' => Url::to(['/mail/mail/update'])
        ];
    }
}