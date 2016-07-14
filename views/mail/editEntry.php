<?php

use yii\helpers\Url;
use humhub\compat\CActiveForm;
?>

<div class="modal-dialog">
    <div class="modal-content">
        <?php $form = CActiveForm::begin(); ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t("MailModule.views_mail_edit", "Edit message entry"); ?></h4>
        </div>
        <div class="modal-body">

            <?php echo $form->errorSummary($entry); ?>

            <div class="form-group">
                <?php echo $form->textArea($entry, 'content', array('class' => 'form-control', 'rows' => '7', 'id' => 'newMessageEntryText')); ?>
                <?php echo humhub\widgets\MarkdownEditor::widget(array('fieldId' => 'newMessageEntryText')); ?>
                <?php echo $form->error($entry, 'content'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-md-6 text-left">
                    <?php
                    echo \humhub\widgets\AjaxButton::widget([
                        'label' => Yii::t('MailModule.views_mail_edit', 'Save'),
                        'ajaxOptions' => [
                            'type' => 'POST',
                            'beforeSend' => '$.proxy(function() { $("#create-message-loader").removeClass("hidden"); $(this).prop("disabled",true);},this)',
                            'success' => 'function(html){ $("#globalModal").html(html); }',
                            'url' => Url::to(['/mail/mail/edit-entry', 'messageEntryId' => $entry->id]),
                        ],
                        'htmlOptions' => [
                            'class' => 'btn btn-primary'
                        ]
                    ]);
                    ?>

                    <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('MailModule.views_mail_create', 'Close'); ?></button>

                </div>
                <div class="col-md-6 text-right">
                    <?php
                    echo humhub\widgets\ModalConfirm::widget(array(
                        'uniqueID' => 'modal_maildelete_' . $entry->id,
                        'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> message deletion'),
                        'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to delete this message?'),
                        'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Delete'),
                        'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                        'linkContent' => Yii::t('MailModule.views_mail_show', 'Delete'),
                        'cssClass' => 'btn btn-danger',
                        'linkHref' => Url::to(["/mail/mail/delete-entry", 'messageEntryId' => $entry->id])
                    ));
                    ?>
                </div>
            </div>

            <div class="col-md-1 modal-loader">
                <div id="create-message-loader" class="loader loader-small hidden"></div>
            </div>
        </div>

        <?php CActiveForm::end(); ?>
    </div>

</div>

