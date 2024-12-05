<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\models\AbstractMessageEntry;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\user\models\User;
use Yii;

/**
 * @property-read string $username
 */
class ConversationStateBadge extends Widget
{
    public MessageEntry $entry;
    public array $options = ['class' => 'conversation-entry-badge conversation-state-badge'];

    /**
     * @inheritdoc
     */
    public function run()
    {
        $text = $this->renderInfoText();
        if ($text === null) {
            return '';
        }

        return Html::tag('div', Html::tag('span', Html::encode($text)), $this->getOptions());
    }

    protected function getOptions(): array
    {
        $this->options['data-entry-id'] = $this->entry->id;

        return $this->options;
    }

    protected function renderInfoText(): ?string
    {
        switch ($this->entry->type) {
            case AbstractMessageEntry::TYPE_USER_JOINED:
                return $this->isOwn()
                    ? Yii::t('MailModule.base', 'You joined the conversation.')
                    : Yii::t('MailModule.base', '{username} joined the conversation.', ['username' => $this->username]);

            case AbstractMessageEntry::TYPE_USER_LEFT:
                return $this->isOwn()
                    ? Yii::t('MailModule.base', 'You left the conversation.')
                    : Yii::t('MailModule.base', '{username} left the conversation.', ['username' => $this->username]);
        }

        return null;
    }

    public function getUsername(): string
    {
        return $this->entry->user->displayName;
    }

    protected function isOwn(): bool
    {
        return !Yii::$app->user->isGuest &&
            $this->entry->user->is(Yii::$app->user->getIdentity());
    }
}
