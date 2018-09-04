<?php

use yii\widgets\ActiveForm;
use humhub\widgets\ModalDialog;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\ModalButton;
use humhub\widgets\Button;

/* @var $inviteForm \humhub\modules\mail\models\forms\InviteParticipantForm */
?>


<?php ModalDialog::begin(['header' => Yii::t("MailModule.views_mail_adduser", "Add more participants to your conversation...")])?>

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]) ?>
        <div class="modal-body">
            <div class="form-group">
                <?= $form->field($inviteForm, 'recipients')->widget(UserPickerField::class, [
                        'url' => $inviteForm->getPickerUrl(), 'focus' => true
                ])->label(false); ?>
            </div>
        </div>
        <div class="modal-footer">
            <?= ModalButton::save()->submit()->action('addUser', $inviteForm->getUrl(), '#mail-conversation-root')->close() ?>
            <?= ModalButton::cancel() ?>
        </div>
    <?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>
