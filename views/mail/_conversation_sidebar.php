<?php

use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\InboxFilter;
use humhub\modules\ui\icon\widgets\Icon;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

$filterModel = new InboxFilterForm();
?>
<div id="mail-conversation-overview" class="panel panel-default mail-inbox-messages">
    <div class="panel-heading">
        <a data-action-click="mail.inbox.toggleInbox">
            <span class="visible-xs-inline"><?=Icon::get('bars')?></span> <?= Yii::t('MailModule.base', 'Conversations') ?>
        </a>
        <?php if ($canStartConversation) : ?>
            <?= NewMessageButton::widget(['icon' => 'plus', 'label' => '', 'id' => 'mail-conversation-create-button'])?>
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
