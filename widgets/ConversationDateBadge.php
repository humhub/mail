<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\widgets;

use DateTime;
use DateTimeZone;
use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\models\MessageEntry;
use Yii;

class ConversationDateBadge extends Widget
{
    public MessageEntry $entry;
    public array $options = ['class' => 'conversation-entry-badge conversation-date-badge'];
    public string $format = 'long';

    /**
     * @inheritdoc
     */
    public function run()
    {
        return Html::tag('div', Html::tag('span', $this->renderDate()), $this->options);
    }

    protected function renderDate(): string
    {
        if ($this->isDate('today')) {
            return Yii::t('MailModule.base', 'Today');
        }

        if ($this->isDate('yesterday')) {
            return Yii::t('MailModule.base', 'Yesterday');
        }

        return $this->getFormattedEntryDate();
    }

    private function getFormattedEntryDate(): string
    {
        return Yii::$app->formatter->asDate($this->entry->created_at, $this->format);
    }

    private function isDate(string $date): bool
    {
        $datetime = new DateTime($date, new DateTimeZone(Yii::$app->formatter->timeZone));

        return $this->getFormattedEntryDate() === Yii::$app->formatter->asDate($datetime, $this->format);
    }
}
