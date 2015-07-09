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
                id="myModalLabel"><?php echo Yii::t("MailModule.views_mail_create", "New message"); ?></h4>
        </div>
        <div class="modal-body">

            <?php echo $form->errorSummary($model); ?>

            <?php echo $form->labelEx($model, 'recipient'); ?>
            <?php echo $form->textField($model, 'recipient', array('class' => 'form-control', 'id' => 'recipient')); ?>

            <?php
            echo \humhub\modules\user\widgets\UserPicker::widget(array(
                'inputId' => 'recipient',
                'model' => $model,
                'attribute' => 'recipient',
                'userGuid' => Yii::$app->user->guid,
                'focus' => true,
            ));
            ?>

            <div class="form-group">
                <?php echo $form->labelEx($model, 'title'); ?>
                <?php echo $form->textField($model, 'title', array('class' => 'form-control')); ?>
                <?php echo $form->error($model, 'title'); ?>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model, 'message'); ?>
                <?php echo $form->textArea($model, 'message', array('class' => 'form-control', 'rows' => '7', 'id' => 'newMessageText')); ?>
                <?php echo \humhub\widgets\MarkdownEditor::widget(array('fieldId' => 'newMessageText')); ?>
                <?php echo $form->error($model, 'message'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <hr/>
            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('MailModule.views_mail_create', 'Send'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'beforeSend' => 'function(){ $("#create-message-loader").removeClass("hidden"); }',
                    'success' => 'function(html){ $("#globalModal").html(html); }',
                    'url' => Url::to(['/mail/mail/create']),
                ],
                'htmlOptions' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
            ?>

            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('MailModule.views_mail_create', 'Close'); ?></button>

            <div class="col-md-1 modal-loader">
                <div id="create-message-loader" class="loader loader-small hidden"></div>
            </div>
        </div>

        <?php CActiveForm::end(); ?>
    </div>

</div>


<script type="text/javascript">
    // set focus to input for space name
    $('#recipient').focus();
</script>