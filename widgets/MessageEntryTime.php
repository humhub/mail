<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2023 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\mail\widgets;

use humhub\components\Widget;
use humhub\libs\Html;
use humhub\modules\mail\models\MessageEntry;
use Yii;

class MessageEntryTime extends Widget
{
    public MessageEntry $entry;
    public array $options = ['class' => 'conversation-entry-time'];
    public array $timeOptions = [];
    public array $statusOptions = [];
    public string $statusSeparator = ' - ';

    /**
     * @inheritdoc
     */
    public function run()
    {
        return Html::tag('div', $this->renderStatus() . $this->renderTime(), $this->options);
    }

    protected function renderTime(): string
    {
        return Html::tag('time', Yii::$app->formatter->asTime($this->entry->created_at, 'short'), $this->timeOptions);
    }

    protected function renderStatus(): string
    {
        if ($this->entry->created_at == $this->entry->updated_at) {
            return '';
        }

        return Html::tag('span', Yii::t('MailModule.base', 'edited') . $this->statusSeparator, $this->statusOptions);
    }
}
