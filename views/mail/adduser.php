<?php

use humhub\compat\CActiveForm;
?>
<div class="modal-dialog">
    <div class="modal-content">
        <?php $form = CActiveForm::begin(); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t("MailModule.views_mail_adduser", "Add more participants to your conversation..."); ?></h4>
        </div>
        <div class="modal-body">
            <?php echo $form->textField($inviteForm, 'recipient', array('id' => 'addUserFrom_mail')); ?>
            <?php echo $form->error($inviteForm, 'recipient'); ?>
            <?php
            echo humhub\modules\user\widgets\UserPicker::widget(array(
                'inputId' => 'addUserFrom_mail',
                'model' => $inviteForm, // CForm Instanz
                'attribute' => 'recipient',
                'userGuid' => Yii::$app->user->guid,
                'focus' => true,
            ));
            ?>
        </div>
        <div class="modal-footer">
            <hr/>
            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('MailModule.views_mail_adduser', 'Send'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'beforeSend' => 'function(){ $("#adduser-loader").removeClass("hidden"); }',
                    'success' => 'function(html){ $("#globalModal").html(html); }',
                    'url' => ['/mail/mail/add-user', 'id'=>$inviteForm->message->id]
                ],
                'htmlOptions' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
            ?>
            <button type="button" class="btn btn-primary"
                    data-dismiss="modal"><?php echo Yii::t('MailModule.views_mail_adduser', 'Close'); ?></button>

        </div>

        <?php CActiveForm::end(); ?>
    </div>
</div>


