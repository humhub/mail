<?php

use humhub\modules\user\widgets\UserPickerField;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $model \humhub\modules\mail\models\forms\CreateMessage */
?>



<div class="modal-dialog">
    <div class="modal-content">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?= Yii::t('MailModule.views_mail_create', 'New message'); ?></h4>
        </div>

        <div class="modal-body">

            <?= $form->field($model, 'recipient')->widget(UserPickerField::class,
                [
                    'url' => Url::toRoute(['/mail/mail/search-user']),
                    'placeholder' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
                    'focus' => true
                ]
            );?>

            <?= $form->field($model, 'title'); ?>

            <div class="form-group">
                <?php echo $form->field($model, 'message', ['inputOptions' => ['class' => 'form-control', 'id' => 'newMessageText']])->textarea(); ?>
                <?php echo \humhub\widgets\MarkdownEditor::widget(array('fieldId' => 'newMessageText')); ?>
            </div>

        </div>
        <div class="modal-footer">
            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('MailModule.views_mail_create', 'Send'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'beforeSend' => '$.proxy(function() { $(this).prop("disabled",true); },this)',
                    'success' => 'function(html){ $("#globalModal").html(html); }',
                    'url' => Url::to(['/mail/mail/create']),
                ],
                'htmlOptions' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
            ?>

            <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo Yii::t('MailModule.views_mail_create', 'Close'); ?></button>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>


<script type="text/javascript">
    // set focus to input for space name
    $('#recipient').focus();
</script>