<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 09:29
 */

namespace humhub\modules\mail\widgets\wall;


use Yii;
use humhub\modules\mail\models\MessageEntry;
use humhub\widgets\JsWidget;
use yii\helpers\Url;

class ConversationEntry extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'mail.wall.ConversationEntry';

    /**
     * @var MessageEntry
     */
    public $entry;

    public function run()
    {
        return $this->render('conversationEntry', [
            'entry' => $this->entry,
            'options' => $this->getOptions()
        ]);
    }

    public function getData()
    {
        return [
            'entry-id' => $this->entry->id,
            'delete-url' => Url::to(['/mail/mail/delete-entry', 'id' => $this->entry->id]),
        ];
    }

    public function getAttributes()
    {
        return [
            'class' => 'media mail-conversation-entry'
        ];
    }
}