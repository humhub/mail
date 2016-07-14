<?php

use yii\widgets\ActiveForm;


?>
<div class="modal-dialog">
    <div class="modal-content">
        <?php $form = ActiveForm::begin(['id' => 'add-user-form']); ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?php echo Yii::t("MailModule.views_mail_adduser", "Add more participants to your conversation..."); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-group">
                <?php echo $form->field($inviteForm, 'recipient', ['inputOptions' => ['id' => 'addUserFrom_mail']]); ?>
            </div>

            <?php
            if(version_compare(Yii::$app->version, '1.0.0-beta.5', 'lt')) {
                echo humhub\modules\user\widgets\UserPicker::widget(array(
                    'inputId' => 'addUserFrom_mail',
                    'model' => $inviteForm, // CForm Instanz
                    'attribute' => 'recipient',
                    'userGuid' => Yii::$app->user->guid,
                    'placeholderText' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
                    'focus' => true,
            )); 
            } else {
                echo humhub\modules\user\widgets\UserPicker::widget(array(
                    'inputId' => 'addUserFrom_mail',
                    'model' => $inviteForm, // CForm Instanz
                    'attribute' => 'recipient',
                    'userGuid' => Yii::$app->user->guid,
                    'data' => ['id' => $inviteForm->message->id],
                    'userSearchUrl' => \yii\helpers\Url::toRoute('/mail/mail/search-add-user'),
                    'placeholderText' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
                    'focus' => true,
                ));
            }
            
            ?>
        </div>
        <div class="modal-footer">
            <?php
            echo \humhub\widgets\AjaxButton::widget([
                'label' => Yii::t('MailModule.views_mail_adduser', 'Send'),
                'ajaxOptions' => [
                    'type' => 'POST',
                    'beforeSend' => '$.proxy(function() { $("#adduser-loader").removeClass("hidden"); $(this).prop("disabled",true); },this)',
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

        <?php ActiveForm::end(); ?>
    </div>
</div>


