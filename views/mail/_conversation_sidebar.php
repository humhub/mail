<?php

use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\InboxFilter;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Button;
use yii\widgets\LinkPager;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

$filterModel = new InboxFilterForm();

?>

<div id="mail-conversation-overview" class="panel panel-default">
    <div class="panel-heading"  style="background-color:<?= $this->theme->variable('background-color-secondary')?>">
        <a data-action-click="mail.inbox.toggleInbox">
            <strong><span class="visible-xs-inline"><?=Icon::get('bars')?></span> <?= Yii::t('MailModule.views_mail_index', 'Conversations') ?></strong>
        </a>
        <?php if($canStartConversation) : ?>
            <?= NewMessageButton::widget(['label' => Yii::t('MailModule.base', '+ Message'), 'right' => true, 'icon' => false, 'cssClass' => 'hidden-xs'])?>

            <?= NewMessageButton::widget(['right' => true, 'label' => '+', 'cssClass' => 'visible-xs'])?>
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
