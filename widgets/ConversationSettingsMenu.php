<?php

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\permissions\StartConversation;
use Yii;

class ConversationSettingsMenu extends Widget
{
    /**
     * @var Message
     */
    public $message;

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('conversationSettingsMenu', [
            'message' => $this->message,
            'isSingleParticipant' => $this->message->getUsers()->count() === 1,
            'canAddParticipant' => Yii::$app->user->can(StartConversation::class),
        ]);
    }

}
