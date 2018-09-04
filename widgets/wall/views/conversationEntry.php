<?php

use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\widgets\TimeAgo;
use humhub\libs\Html;

/* @var $entry \humhub\modules\mail\models\MessageEntry */
/* @var $options array */

?>

<?= Html::beginTag('div', $options) ?>

<div class="media">

    <span class="pull-left">
        <?= Image::widget(['user' => $entry->user]) ?>
    </span>

    <?php if ($entry->canEdit()): ?>
        <div class="pull-right">
            <?= ModalButton::asLink()->icon('fa-pencil-square-o')->load( ["/mail/mail/edit-entry", 'id' => $entry->id]) ?>
        </div>
    <?php endif; ?>

    <div class="media-body">
        <h4 class="media-heading" style="font-size: 14px;"><?= Html::encode($entry->user->displayName); ?>
            <small><?= TimeAgo::widget(['timestamp' => $entry->created_at]); ?></small>
        </h4>
    </div>
    <div style="margin-left:50px">
        <span class="content">
            <?= RichText::output($entry->content); ?>
        </span>
    </div>

</div>

<hr>

<?= Html::endTag('div') ?>


