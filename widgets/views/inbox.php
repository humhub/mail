<?php

use humhub\libs\Html;
use humhub\modules\mail\models\UserMessage;
use humhub\modules\mail\widgets\InboxMessagePreview;
use humhub\modules\ui\view\components\View;

/* @var $this View */
/* @var $options array */
/* @var $userMessages UserMessage[] */

?>

<?= Html::beginTag('ul', $options) ?>
    <?php if (empty($userMessages)) : ?>
        <li class="placeholder"><?= Yii::t('MailModule.views_mail_index', 'There are no messages yet.') ?></li>
    <?php else: ?>
        <?php foreach ($userMessages as $userMessage) : ?>
            <?= InboxMessagePreview::widget(['userMessage' => $userMessage]) ?>
        <?php endforeach; ?>
    <?php endif; ?>
<?= Html::endTag('ul') ?>
