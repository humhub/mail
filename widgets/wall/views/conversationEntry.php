<?php

use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\widgets\TimeAgo;
use humhub\libs\Html;

/* @var $entry \humhub\modules\mail\models\MessageEntry */
/* @var $options array */

$isOwnMessage = $entry->user->is(Yii::$app->user->getIdentity());
$authorLabel = $isOwnMessage ? Yii::t('MailModule.base', 'You') : Html::encode($entry->user->displayName);
$options['id'] = 'message_'.$entry->id;

?>

<?= Html::beginTag('div', $options) ?>

<div class="media">

    <?php if(!$isOwnMessage) : ?>
    <span class="author-image pull-left">
        <?= Image::widget(['user' => $entry->user, 'width' => 30]) ?>
    </span>
    <?php endif; ?>

    <?php if(!$isOwnMessage) : ?>
        <div class="media-body">
            <strong class="media-heading" style="font-size: 10px"><?= $authorLabel  ?></strong>
            <?php /*<small class="message-time" style="float:right;margin-top:2px;visibility: hidden"><?= TimeAgo::widget(['timestamp' => $entry->created_at]) ?></small>*/ ?>
        </div>
    <?php endif; ?>



    <?php // <?= TimeAgo::widget(['timestamp' => $entry->created_at]) ?>
    <div class="conversation-entry-content<?= $isOwnMessage ? ' own' : ''?>"
         style="<?= $isOwnMessage ? 'float:right' : ''?>">
        <span class="content">
            <?= RichText::output($entry->content) ?>
        </span>
    </div>


    <div class="conversation-menu">

        <?php if($isOwnMessage) : ?>
            <div class="conversation-menu-item" style="display: inline-block">
                &middot; <?= ModalButton::none(Yii::t('MailModule.base', 'edit'))->cssClass('conversation-edit-button')
                    ->load( ['/mail/mail/edit-entry', 'id' => $entry->id])->link()->cssClass('time') ?>
            </div>
        <?php endif ?>

        <div class="conversation-menu-item" style="display: inline-block">
            <?=  TimeAgo::widget(['timestamp' => $entry->created_at])  ?>
        </div>
    </div>


</div>

<?= Html::endTag('div') ?>


