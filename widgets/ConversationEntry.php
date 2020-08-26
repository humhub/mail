<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 09:29
 */

namespace humhub\modules\mail\widgets;


use humhub\libs\Html;
use Yii;
use humhub\modules\mail\models\MessageEntry;
use humhub\widgets\JsWidget;
use humhub\modules\mail\helpers\Url;

class ConversationEntry extends JsWidget
{
    /**
     * @inheritdoc
     */
    public $jsWidget = 'mail.ConversationEntry';

    /**
     * @var MessageEntry
     */
    public $entry;

    /**
     * @var MessageEntry
     */
    public $prevEntry;

    /**
     * @var MessageEntry
     */
    public $nextEntry;

    public function run()
    {
        return $this->render('conversationEntry', [
            'entry' => $this->entry,
            'contentClass' => $this->getContentClass(),
            'showUserInfo' => $this->isShowUserInfo(),
            'isOwnMessage' => $this->isOwnMessage(),
            'options' => $this->getOptions()
        ]);
    }

    private function getContentClass()
    {
        $result = 'conversation-entry-content';

        if($this->isPrevEntryFromSameUser()) {
            $result .= ' seq-top';
        }

        if($this->isNextEntryFromSameUser()) {
            $result .= ' seq-bottom';
        }

        if($this->isOwnMessage()) {
            $result .= ' own';
        }

        return $result;
    }

    private function isOwnMessage()
    {
        return $this->entry->user->is(Yii::$app->user->getIdentity());
    }

    public function getData()
    {
        return [
            'entry-id' => $this->entry->id,
            'delete-url' => Url::toDeleteMessageEntry($this->entry)
        ];
    }

    public function getAttributes()
    {
        $result =  [
            'class' => 'media mail-conversation-entry'
        ];

        if($this->isOwnMessage()) {
            Html::addCssClass($result, 'own');
        }

        if($this->isPrevEntryFromSameUser()) {
            Html::addCssClass($result, 'hideUserInfo');
        }

        return $result;
    }

    private function isPrevEntryFromSameUser()
    {
        return $this->prevEntry && $this->prevEntry->created_by === $this->entry->created_by;
    }

    private function isNextEntryFromSameUser()
    {
        return $this->nextEntry && $this->nextEntry->created_by === $this->entry->created_by;
    }



    private function isShowUserInfo()
    {
        return !$this->prevEntry || $this->prevEntry->created_by !== $this->entry->created_by;
    }



}