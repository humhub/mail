<?php

use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\InboxFilter;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\widgets\Icon;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

$filterModel = new InboxFilterForm();
?>
<div id="mail-conversation-overview" class="card content-card mail-inbox-messages">
    <div class="card-header">
        <div class="card-title">
            <a data-action-click="mail.inbox.toggleInbox">
                <span class="d-sm-none"><?=Icon::get('menu-2')?></span> <?= Yii::t('MailModule.base', 'Conversations') ?>
            </a>
        </div>
        <?php if ($canStartConversation) : ?>
            <div class="card-header-actions">
                <?= NewMessageButton::widget(['icon' => 'plus', 'label' => '', 'id' => 'mail-conversation-create-button'])?>
            </div>
        <?php endif; ?>

        <div class="inbox-wrapper">
         <?= InboxFilter::widget(['model' => $filterModel]) ?>
        </div>

    </div>

    <div class="inbox-wrapper">
        <hr style="margin:0">
        <?= ConversationInbox::widget(['filter' => $filterModel]) ?>
    </div>
</div>
