<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\widgets\TimeAgo;
use humhub\libs\Html;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $entry \humhub\modules\mail\models\MessageEntry */
/* @var $options array */
/* @var $contentClass string */
/* @var $showUserInfo boolean */
/* @var $isOwnMessage boolean */

$isOwnMessage = $entry->user->is(Yii::$app->user->getIdentity());

?>

<?= Html::beginTag('div', $options) ?>

<div class="media">

    <?php if(!$isOwnMessage) : ?>
    <span class="author-image pull-left">
        <?= Image::widget(['user' => $entry->user, 'width' => 30]) ?>
    </span>
    <?php endif; ?>

    <?php if(!$isOwnMessage) : ?>
        <div class="media-body author-label">
            <strong class="media-heading" style="font-size: 10px">
                <?= Html::encode($entry->user->displayName)  ?>
            </strong>
        </div>
    <?php endif; ?>

    <div class="<?= $contentClass ?>" style="<?= $isOwnMessage ? 'float:right' : ''?>">
        <div style="display: table-cell">
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


