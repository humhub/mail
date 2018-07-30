<?php

use humhub\modules\mail\permissions\StartConversation;
use humhub\modules\mail\widgets\NewMessageButton;
use humhub\widgets\LinkPager;
use humhub\modules\mail\widgets\wall\ConversationView;

/* @var $messageId int */
/* @var $userMessages \humhub\modules\mail\models\UserMessage[] */
/* @var $pagination \yii\data\Pagination */
/* @var $canStartConversation boolean */

$canStartConversation = Yii::$app->user->can(StartConversation::class);
?>
<div class="container">
    <div class="row">
        <div class="col-md-4">

            <div class="panel panel-default">
                <div class="panel-heading"  style="background-color:<?= $this->theme->variable('background-color-secondary')?>">
                    <strong><?= Yii::t('MailModule.views_mail_index', 'Conversations') ?></strong>
                    <?php if($canStartConversation) : ?>
                        <?= NewMessageButton::widget()?>
                    <?php endif; ?>
                </div>

                <hr style="margin-top:0px">

                <ul id="inbox" class="media-list">
                    <?php if (empty($userMessages)) : ?>
                        <li class="placeholder"><?= Yii::t('MailModule.views_mail_index', 'There are no messages yet.'); ?></li>
                    <?php else: ?>
                        <?php foreach ($userMessages as $userMessage) : ?>
                            <?= $this->render('_messagePreview', ['userMessage' => $userMessage]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="pagination-container">
                <?= LinkPager::widget(['pagination' => $pagination]); ?>
            </div>
        </div>

        <div class="col-md-8 messages">
            <?= ConversationView::widget(['messageId' => $messageId])?>
        </div>
    </div>
</div>
