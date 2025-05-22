<?php

use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $inviteForm \humhub\modules\mail\models\forms\InviteParticipantForm */
?>

<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('MailModule.base', 'Add participants'),
    'form' => ['enableClientValidation' => false],
    'footer' => ModalButton::cancel() . ' ' . ModalButton::save(Yii::t('MailModule.base', 'Confirm'))->submit()->action('addUser', $inviteForm->getUrl(), '#mail-conversation-root')->close(),
])?>
    <div class="mb-3">
        <?= $form->field($inviteForm, 'recipients')->widget(UserPickerField::class, [
            'url' => $inviteForm->getPickerUrl(),
            'focus' => true,
        ])->label(false) ?>
    </div>
<?php Modal::endFormDialog(); ?>
