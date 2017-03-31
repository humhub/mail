<?php

use yii\helpers\Html;
use yii\helpers\Url;
use humhub\widgets\ModalConfirm;
use humhub\widgets\TimeAgo;
use humhub\compat\CActiveForm;
?>
<div class="panel panel-default">

    <?php if ($message == null) { ?>

        <div class="panel-body">
            <?= Yii::t('MailModule.views_mail_show', 'There are no messages yet.'); ?>
        </div>
    <?php } else { ?>

        <div class="panel-heading">
            <?= Html::encode($message->title); ?>

            <div class="pull-right">
                <?php if (count($message->users) != 0) : ?>
                    <?php if (count($message->users) != 1) : ?>
                        <?=
                        ModalConfirm::widget([
                            'uniqueID' => 'modal_leave_conversation_' . $message->id,
                            'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation'),
                            'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?'),
                            'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Leave'),
                            'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                            'linkContent' => '<i class="fa fa-sign-out"></i> ',
                            'linkTooltipText' => Yii::t('MailModule.views_mail_show', 'Leave conversation'),
                            'cssClass' => 'btn btn-primary btn-sm',
                            'linkHref' => Url::to(["/mail/mail/leave", 'id' => $message->id])
                        ]);
                        ?>
                    <?php endif; ?>
                    <?php if (count($message->users) == 1) : ?>
                        <?=
                        ModalConfirm::widget([
                            'uniqueID' => 'modal_leave_conversation_' . $message->id,
                            'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation'),
                            'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?'),
                            'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Delete'),
                            'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                            'linkContent' => '<i class="fa fa-times"></i> ',
                            'linkTooltipText' => Yii::t('MailModule.views_mail_show', 'Delete conversation'),
                            'cssClass' => 'btn btn-primary btn-sm',
                            'linkHref' => Url::to(["/mail/mail/leave", 'id' => $message->id])
                        ]);
                        ?>
                    <?php endif; ?>
                    <?php foreach ($message->users as $user) : ?>
                        <a href="<?= $user->getUrl(); ?>">
                            <img src="<?= $user->getProfileImage()->getUrl(); ?>"
                                 class="img-rounded tt img_margin" height="29" width="29"
                                 data-toggle="tooltip" data-placement="top" title=""
                                 data-original-title="<?= Html::encode($user->displayName); ?>">
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel-body">

            <hr style="margin-top: 0;">

            <ul class="media-list">
                <!-- BEGIN: Results -->
                <?php foreach ($message->entries as $entry) : ?>
                    <div class="media" style="margin-top: 0;">
                        <a class="pull-left" href="<?= $entry->user->getUrl(); ?>">
                            <img src="<?= $entry->user->getProfileImage()->getUrl(); ?>"
                                class="media-object img-rounded"
                                data-src="holder.js/50x50" alt="50x50"
                                style="width: 50px; height: 50px;">
                        </a>

                        <?php if ($entry->created_by == Yii::$app->user->id) : ?>
                            <div class="pull-right">
                                <?= Html::a('<i class="fa fa-pencil-square-o"></i>', ["/mail/mail/edit-entry", 'messageEntryId' => $entry->id], ['data-target' => '#globalModal', 'class' => '']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="media-body">
                            <h4 class="media-heading" style="font-size: 14px;"><?= Html::encode($entry->user->displayName); ?>
                                <small><?= TimeAgo::widget(['timestamp' => $entry->created_at]); ?></small>
                            </h4>

                            <span class="content">
                                <?= humhub\widgets\MarkdownView::widget(['markdown' => $entry->content]); ?>
                            </span>
                        </div>
                    </div>

                    <hr>

                <?php endforeach; ?>

            </ul>
            <!-- END: Results -->

            <div class="row-fluid">
                <?php $form = CActiveForm::begin(); ?>

                <?= $form->errorSummary($replyForm); ?>
                <div class="form-group">
                    <?= $form->textArea($replyForm, 'message', ['class' => 'form-control', 'id' => 'newMessage', 'rows' => '4', 'placeholder' => Yii::t('MailModule.views_mail_show', 'Write an answer...')]); ?>
                    <?= humhub\widgets\MarkdownEditor::widget(['fieldId' => 'newMessage']); ?>
                </div>
                <hr>

                <?=
                \humhub\widgets\AjaxButton::widget([
                    'label' => Yii::t('MailModule.views_mail_show', 'Send'),
                    'ajaxOptions' => [
                        'type' => 'POST',
                        'beforeSend' => '$.proxy(function() { $(this).prop("disabled",true); },this)',
                        'success' => 'function(html){ $("#mail_message_details").html(html); }',
                        'url' => Url::to(['/mail/mail/show', 'id' => $message->id]),
                    ],
                    'htmlOptions' => [
                        'class' => 'btn btn-primary'
                    ]
                ]);
                ?>

                <div class="pull-right">

                    <!-- Button to trigger modal to add user to conversation -->
                    <?=
                    Html::a('<i class="fa fa-plus"></i> ' . Yii::t('MailModule.views_mail_show', 'Add user'), ['/mail/mail/add-user', 'id' => $message->id, 'ajax' => 1], [
                        'class' => 'btn btn-info',
                        'data-target' => '#globalModal'
                    ]);
                    ?>

                    <?php if (count($message->users) > 2): ?>
                        <a class="btn btn-danger"
                           href="<?= Url::to(['leave', 'id' => $message->id]); ?>">
                           <i class="fa fa-sign-out"></i> <?= Yii::t('MailModule.views_mail_show', "Leave discussion"); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <?php CActiveForm::end(); ?>
            </div>
        <?php } ?>

    </div>
</div>
