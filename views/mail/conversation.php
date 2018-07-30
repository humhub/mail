<?php

use humhub\modules\mail\permissions\StartConversation;
use yii\bootstrap\ActiveForm;
use humhub\widgets\ModalButton;
use yii\helpers\Url;
use humhub\modules\ui\form\widgets\Markdown;
use humhub\modules\mail\widgets\wall\ConversationEntry;
use humhub\widgets\Button;

$canStartConversation = Yii::$app->user->can(StartConversation::class);

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $replyForm \humhub\modules\mail\models\forms\ReplyForm */


?>
<div class="panel panel-default">

    <?php if ($message == null) : ?>

        <div class="panel-body">
            <?= Yii::t('MailModule.views_mail_show', 'There are no messages yet.'); ?>
        </div>

    <?php  else :?>

        <div class="panel-heading" style="background-color:<?= $this->theme->variable('background-color-secondary')?>">
            <strong> <?= $this->render('_conversation_header', ['message' => $message]) ?></strong>
        </div>

        <div class="panel-body">

            <div class="media-list conversation-entry-list">
                <?php foreach ($message->entries as $entry) : ?>
                    <?= ConversationEntry::widget(['entry' => $entry])?>
                <?php endforeach; ?>
            </div>


            <div class="row-fluid">
                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                    <?= $form->field($replyForm, 'message')->widget(Markdown::class, ['rows' => 5])->label(false) ?>

                    <hr>

                    <?= Button::primary(Yii::t('MailModule.views_mail_show', 'Send'))->submit()->action('reply', $replyForm->getUrl()) ?>

                    <div class="pull-right">

                        <!-- Button to trigger modal to add user to conversation -->
                        <?= ModalButton::info(Yii::t('MailModule.views_mail_show', 'Add user'))->icon('fa-plus')
                            ->load(['/mail/mail/add-user', 'id' => $message->id])->visible($canStartConversation) ?>

                        <?php if (count($message->users) > 2): ?>
                            <a class="btn btn-danger"
                               href="<?php echo Url::to(['leave', 'id' => $message->id]); ?>"><i
                                    class="fa fa-sign-out"></i> <?php echo Yii::t('MailModule.views_mail_show', "Leave discussion"); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    <?php endif; ?>

</div>
