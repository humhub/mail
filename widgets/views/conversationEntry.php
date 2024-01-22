<?php

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\file\widgets\ShowFiles;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\ConversationDateBadge;
use humhub\modules\mail\widgets\ConversationEntryMenu;
use humhub\modules\mail\widgets\MessageEntryTime;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\Image;

/* @var $this View */
/* @var $entry MessageEntry */
/* @var $options array */
/* @var $contentClass string */
/* @var $showUser bool */
/* @var $userColor string */
/* @var $showDateBadge bool */
?>
<?php if ($showDateBadge) : ?>
    <?= ConversationDateBadge::widget(['entry' => $entry]) ?>
<?php endif; ?>

<?= Html::beginTag('div', $options) ?>

<div class="media">

    <?php if ($showUser) : ?>
        <span class="author-image pull-left">
            <?= Image::widget([
                'user' => $entry->user,
                'width' => 30,
            ]) ?>
        </span>
    <?php endif; ?>

    <div class="<?= $contentClass ?>">
        <div class="markdown-render">
            <?php if ($showUser) : ?>
                <div class="author-label"
                     style="color:<?= $userColor ?>"><?= Html::encode($entry->user->displayName) ?></div>
            <?php endif; ?>
            <?= RichText::output($entry->content) ?>
            <?= ShowFiles::widget(['object' => $entry]) ?>
        </div>
        <?= MessageEntryTime::widget(['entry' => $entry]) ?>
    </div>

    <?= ConversationEntryMenu::widget(['entry' => $entry]) ?>
</div>

<?= Html::endTag('div') ?>
