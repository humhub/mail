<?php

use humhub\libs\Html;
use humhub\modules\mail\permissions\StartConversation;
use yii\bootstrap\ActiveForm;
use humhub\widgets\ModalButton;
use yii\helpers\Url;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;
use humhub\modules\mail\widgets\wall\ConversationEntry;
use humhub\widgets\Button;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $replyForm \humhub\modules\mail\models\forms\ReplyForm */
/* @var $messageCount integer */


?>
<div class="panel panel-default">

    <?php if ($message == null) : ?>

        <div class="panel-body">
            <?= Yii::t('MailModule.views_mail_show', 'There are no messages yet.'); ?>
        </div>

    <?php  else :?>

        <div id="mail-conversation-header" class="panel-heading" style="background-color:<?= $this->theme->variable('background-color-secondary')?>">
            <strong> <?= $this->render('_conversation_header', ['message' => $message]) ?></strong>
        </div>

        <div class="panel-body">

            <div class="media-list conversation-entry-list">
                <?php foreach ($message->entries as $entry) : ?>
                    <?= ConversationEntry::widget(['entry' => $entry])?>
                <?php endforeach; ?>
            </div>


            <div class="mail-message-form row-fluid">
                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                    <?= $form->field($replyForm, 'message')->widget(
                        ProsemirrorRichTextEditor::class, [
                        'menuClass' => 'plainMenu',
                        'placeholder' => Yii::t('MailModule.base', 'Write a message...'),
                        'pluginOptions' => ['maxHeight' => '300px'],
                    ])->label(false); ?>

                    <?= Button::primary(Yii::t('MailModule.views_mail_show', 'Send'))->submit()->action('reply', $replyForm->getUrl()) ?>

                    <div class="pull-right">

                        <!-- Button to trigger modal to add user to conversation -->
                        <?= ModalButton::info(Yii::t('MailModule.views_mail_show', 'Add user'))->icon('fa-plus')
                            ->load(['/mail/mail/add-user', 'id' => $message->id])->visible($canStartConversation) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif; ?>

    <?= Html::beginTag('script')?>
        humhub.modules.mail.setMailMessageCount(<?= $messageCount ?>);
    <?= Html::endTag('script')?>
</div>
