<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\mail\widgets\TimeAgo;
use humhub\libs\Html;

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

    <div class="<?= $contentClass ?>"
         style="<?= $isOwnMessage ? 'float:right' : ''?>">
        <span class="content">
            <?= RichText::output($entry->content) ?>
        </span>
    </div>

    <div class="conversation-menu">

        <?php if($isOwnMessage) : ?>
            <div class="conversation-menu-item" style="display: inline-block">
                <span class="hidden-xs time">|</span> <?= ModalButton::none(Yii::t('MailModule.base', 'edit'))
                    ->cssClass('conversation-edit-button time')
                    ->load(Url::toEditMessageEntry($entry))->link() ?>
            </div>
        <?php endif ?>

        <div class="conversation-menu-item" style="display: inline-block">
            <?=  TimeAgo::widget(['timestamp' => $entry->created_at])  ?>
        </div>
    </div>

</div>

<?= Html::endTag('div') ?>


