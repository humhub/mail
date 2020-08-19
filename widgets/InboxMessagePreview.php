<?php


namespace humhub\modules\mail\widgets;


use humhub\components\Widget;
use humhub\modules\mail\models\UserMessage;

class InboxMessagePreview extends Widget
{
    /**
     * @var UserMessage
     */
    public $userMessage;

    public function run()
    {
        return $this->render('inboxMessagePreview', ['userMessage' => $this->userMessage]);
    }
}