<?php

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\file\widgets\ShowFiles;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\ConversationDateBadge;
use humhub\modules\mail\widgets\ConversationEntryMenu;
use humhub\modules\mail\widgets\MessageEntryTime;
use humhub\modules\user\widgets\Image;

/* @var $this View */
/* @var $entry MessageEntry */
/* @var $options array */
/* @var $contentClass string */
/* @var $showUser bool */
/* @var $userColor string */
/* @var $showDateBadge bool */
/* @var $isOwnMessage bool */

?>
<?php if ($showDateBadge) : ?>
    <?= ConversationDateBadge::widget(['entry' => $entry]) ?>
<?php endif; ?>

<?= Html::beginTag('div', $options) ?>

<div class="d-flex pe-2 gap-2<?= $isOwnMessage ? ' justify-content-end' : '' ?>">

    <?= ConversationEntryMenu::widget(['entry' => $entry]) ?>

    <?php if ($showUser) : ?>
        <span class="author-image float-start">
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
</div>

<?= Html::endTag('div') ?>
