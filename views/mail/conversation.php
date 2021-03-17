<?php

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\mail\models\forms\ReplyForm;
use humhub\modules\mail\widgets\ConversationHeader;
use humhub\modules\mail\widgets\ConversationTags;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\modules\mail\widgets\Messages;
use humhub\modules\ui\view\components\View;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;

/* @var $this View */
/* @var $replyForm ReplyForm */
/* @var $messageCount integer */

?>
<div class="panel panel-default">

    <?php if ($message === null) : ?>

        <div class="panel-body">
            <?= Yii::t('MailModule.views_mail_show', 'There are no messages yet.'); ?>
        </div>

    <?php  else :?>

        <div id="mail-conversation-header" class="panel-heading" style="background-color:<?= $this->theme->variable('background-color-secondary')?>">
             <?= ConversationHeader::widget(['message' => $message]) ?>
        </div>

        <?= ConversationTags::widget(['message' => $message])?>

        <div class="panel-body">

            <div class="media-list conversation-entry-list">
                <?= Messages::widget(['message' => $message])?>
            </div>

        </div>

        <div class="mail-message-form">
            <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

            <?= $form->field($replyForm, 'message')->widget(MailRichtextEditor::class, ['id' => 'reply-'.time()])->label(false) ?>

            <div class="clearfix">
                <?= Button::defaultType()->cssClass('reply-button')->submit()->action('reply', $replyForm->getUrl())->right()->icon('paper-plane-o')->sm() ?>
            </div>


            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>

    <script <?= Html::nonce() ?>>
        humhub.modules.mail.notification.setMailMessageCount(<?= $messageCount ?>);
    </script>
</div>
