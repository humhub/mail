<div class="modal-dialog">
    <div class="modal-content">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'edit-message-entry-form',
            'enableAjaxValidation' => false,
        ));
        ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t("MailModule.views_mail_edit", "Edit message entry"); ?></h4>
        </div>
        <div class="modal-body">

            <?php echo $form->errorSummary($entry); ?>

            <div class="form-group">
                <?php echo $form->textArea($entry, 'content', array('class' => 'form-control', 'rows' => '7', 'id' => 'newMessageEntryText')); ?>
                <?php $this->widget('application.widgets.MarkdownEditorWidget', array('fieldId' => 'newMessageEntryText')); ?>
                <?php echo $form->error($entry, 'content'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <hr/>
            <?php
            echo HHtml::ajaxSubmitButton(Yii::t('MailModule.views_mail_edit', 'Save'), $this->createUrl('//mail/mail/editEntry', array('messageEntryId' => $entry->id)), array(
                'type' => 'POST',
                'beforeSend' => 'function(){ $("#create-message-loader").removeClass("hidden"); }',
                'success' => 'function(html){ $("#globalModal").html(html); }',
                    ), array('class' => 'btn btn-primary'));
            ?>

            <?php
            $this->widget('application.widgets.ModalConfirmWidget', array(
                'uniqueID' => 'modal_maildelete_' . $entry->id,
                'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> message deletion'),
                'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to delete this message?'),
                'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Delete'),
                'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                'linkContent' => Yii::t('MailModule.views_mail_show', 'Delete'),
                'class' => 'btn btn-danger',
                'linkHref' => $this->createUrl("//mail/mail/deleteEntry", array('messageEntryId' => $entry->id))
            ));
            ?>            

            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('MailModule.views_mail_create', 'Close'); ?></button>


            <div class="col-md-1 modal-loader">
                <div id="create-message-loader" class="loader loader-small hidden"></div>
            </div>
        </div>

        <?php $this->endWidget(); ?>
    </div>

</div>

