<?php


namespace humhub\modules\mail\widgets;


use humhub\components\Widget;
use humhub\modules\mail\models\Message;

class ConversationHeader extends Widget
{
    /**
     * @var Message
     */
    public $message;

    public function run()
    {
        return $this->render('conversationHeader', ['message' => $this->message]);
    }

}