<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageEntry;
use humhub\widgets\ModalButton;
use humhub\modules\mail\widgets\TimeAgo;

/* @var $entry MessageEntry */
/* @var $badge boolean */

$isOwnMessage = $entry->user->is(Yii::$app->user->getIdentity());
?>
<div class="conversation-menu">

    <?php if ($isOwnMessage) : ?>
        <div class="conversation-menu-item" style="display: inline-block">
            <?= ModalButton::none()
                ->cssClass('conversation-edit-button time')->cssClass('badge')
                ->load(Url::toEditMessageEntry($entry))->link()->icon('pencil')->tooltip(Yii::t('MailModule.base', 'Edit')) ?>
        </div>
    <?php endif ?>

    <div class="conversation-menu-item" style="display: inline-block">
        <?= TimeAgo::widget(['timestamp' => $entry->created_at, 'badge' => $badge])  ?>
    </div>
</div>


