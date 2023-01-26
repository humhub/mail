<?php

use humhub\libs\Html;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\Image;
use humhub\modules\content\widgets\richtext\RichText;

/* @var $this View */
/* @var $entry MessageEntry */
/* @var $options array */
/* @var $contentClass string */
/* @var $contentColor string */
/* @var $showUser bool */
?>

<?= Html::beginTag('div', $options) ?>

<div class="media">

    <?php if ($showUser) : ?>
        <span class="author-image pull-left">
            <?= Image::widget(['user' => $entry->user, 'width' => 30]) ?>
        </span>
    <?php endif; ?>

    <div class="<?= $contentClass ?>"<?= $contentColor ? 'style="background:' . $contentColor . '"' : '' ?>>
        <div class="markdown-render">
            <?php if ($showUser) : ?>
                <div class="author-label"><?= Html::encode($entry->user->displayName) ?></div>
            <?php endif; ?>
            <?= RichText::output($entry->content) ?>
        </div>
    </div>

    <div class="hidden-xs">
        <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => true]) ?>
    </div>

</div>

<div class="visible-xs">
    <?= $this->render('_conversationEntryMenu', ['entry' => $entry, 'badge' => false]) ?>
</div>

<?= Html::endTag('div') ?>
