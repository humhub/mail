<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $this View */
/* @var $model MessageTag */
?>

<?php ModalDialog::begin(['header' => Yii::t('MailModule.base', '<strong>Edit</strong> tag')])?>
<?php $form = ActiveForm::begin() ?>
    <div class="modal-body">
        <?= $form->field($model, 'name') ?>
    </div>
    <div class="modal-footer">
        <?= ModalButton::submitModal(Url::toEditTag($model->id)) ?>
        <?= ModalButton::cancel()?>
    </div>

<?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>
