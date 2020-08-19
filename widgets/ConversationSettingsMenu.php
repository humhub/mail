<?php


namespace humhub\modules\mail\widgets;


use humhub\components\mail\Message;
use humhub\components\Widget;

class ConversationSettingsMenu extends Widget
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @return string|void
     */
    public function run()
    {
        return $this->render('conversationSettingsMenu', ['message' => $this->message]);
    }

}