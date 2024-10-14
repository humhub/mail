<?php

use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalDialog;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\ModalButton;

/* @var $inviteForm \humhub\modules\mail\models\forms\InviteParticipantForm */
?>
<?php ModalDialog::begin(['header' => Yii::t('MailModule.base', 'Add participants')])?>

    <?php $form = ActiveForm::begin(['enableClientValidation' => false]) ?>
        <div class="modal-body">
            <div class="form-group">
                <?= $form->field($inviteForm, 'recipients')->widget(UserPickerField::class, [
                        'url' => $inviteForm->getPickerUrl(), 'focus' => true
                ])->label(false); ?>
            </div>
        </div>
        <div class="modal-footer">
            <?= ModalButton::cancel() ?>
            <?= ModalButton::save(Yii::t('MailModule.base', 'Confirm'))->submit()->action('addUser', $inviteForm->getUrl(), '#mail-conversation-root')->close() ?>
        </div>
    <?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>
