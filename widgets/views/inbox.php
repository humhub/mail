<?php

use humhub\helpers\Html;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\InboxMessagePreview;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $options array */
/* @var $userMessages UserMessage[] */

?>

<?= Html::beginTag('div', $options) ?>
    <?php if (empty($userMessages)) : ?>
        <div><?= Yii::t('MailModule.base', 'There are no messages yet.') ?></div>
    <?php else: ?>
        <?php foreach ($userMessages as $userMessage) : ?>
            <?= InboxMessagePreview::widget(['userMessage' => $userMessage]) ?>
        <?php endforeach; ?>
    <?php endif; ?>
<?= Html::endTag('div') ?>
