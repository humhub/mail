<?php

use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\ConversationInbox;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\modules\mail\widgets\InboxFilter;
use yii\widgets\LinkPager;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

$filterModel = new InboxFilterForm();

?>

<div id="mail-conversation-overview" class="panel panel-default">
    <div class="panel-heading"  style="background-color:<?= $this->theme->variable('background-color-secondary')?>">
        <strong><?= Yii::t('MailModule.views_mail_index', 'Conversations') ?></strong>
        <?php if($canStartConversation) : ?>
            <?= NewMessageButton::widget(['label' => Yii::t('MailModule.base', '+ Message'), 'right' => true, 'icon' => false])?>
        <?php endif; ?>

        <?= InboxFilter::widget(['model' => $filterModel]) ?>

    </div>

    <hr style="margin:0">

    <?= ConversationInbox::widget(['filter' => $filterModel]) ?>

</div>
