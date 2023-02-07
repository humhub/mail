<?php
/**
 * Created by PhpStorm.
 * User: kingb
 * Date: 29.07.2018
 * Time: 09:29
 */

namespace humhub\modules\mail\widgets;

use humhub\libs\Html;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\widgets\JsWidget;
use Imagine\Image\Palette\RGB;
use Yii;

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

    public array $userColors = ['#34568B', '#FF6F61', '#6B5B95', '#88B04B', '#92A8D1', '#955251', '#B565A7', '#009B77',
        '#DD4124', '#D65076', '#45B8AC', '#EFC050', '#5B5EA6', '#9B2335', '#55B4B0', '#E15D44', '#BC243C', '#C3447A'
    ];

    public function run()
    {
        $showUser = $this->showUser();

        return $this->render('conversationEntry', [
            'entry' => $this->entry,
            'contentClass' => $this->getContentClass(),
            'contentColor' => $this->getContentColor(),
            'showUser' => $showUser,
            'userColor' => $showUser ? $this->getUserColor() : null,
            'showDateBadge' => $this->showDateBadge(),
            'options' => $this->getOptions()
        ]);
    }

    private function getContentClass(): string
    {
        $result = 'conversation-entry-content';

        if ($this->isOwnMessage()) {
            $result .= ' own';
        }

        return $result;
    }

    private function getContentColor(): ?string
    {
        if (!$this->isOwnMessage()) {
            return null;
        }

        $rgb = new RGB();
        $color = $rgb->color($this->view->theme->variable('info'), 12);

        return sprintf('rgba(%s, %s, %s, %s)', $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha()/100);
    }

    private function isOwnMessage(): bool
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

        if ($this->isOwnMessage()) {
            Html::addCssClass($result, 'own');
        }

        if ($this->isPrevEntryFromSameUser()) {
            Html::addCssClass($result, 'hideUserInfo');
        }

        return $result;
    }

    private function isPrevEntryFromSameUser(): bool
    {
        return $this->prevEntry && $this->prevEntry->created_by === $this->entry->created_by;
    }

    private function showUser(): bool
    {
        return !$this->isOwnMessage() && $this->entry->message->getUsers()->count() > 2;
    }

    private function getUserColor(): string
    {
        return $this->userColors[$this->entry->created_by % count($this->userColors)];
    }

    private function showDateBadge(): bool
    {
        return !$this->prevEntry || substr($this->prevEntry->created_at, 0, 10) !== substr($this->entry->created_at, 0, 10);
    }

}