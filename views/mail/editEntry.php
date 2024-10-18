<?php

use humhub\modules\file\handler\BaseFileHandler;
use humhub\modules\file\widgets\FileHandlerButtonDropdown;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/* @var $entry MessageEntry */
/* @var $fileHandlers BaseFileHandler[] */
?>

<?php ModalDialog::begin([
    'id' => 'mail-edit',
    'header' => Yii::t("MailModule.base", "Edit message entry"),
    'size' => 'large',
]) ?>

<?php $form = ActiveForm::begin() ?>
<div class="modal-body mail-edit-message">
    <?= $form->field($entry, 'content')->widget(
        MailRichtextEditor::class, [
        'placeholder' => Yii::t('MailModule.base', 'Edit message...')])->label(false) ?>

    <?php $uploadButton = UploadButton::widget([
        'id' => 'mail-edit-upload',
        'model' => $entry,
        'label' => Yii::t('ContentModule.base', 'Attach Files'),
        'tooltip' => false,
        'cssButtonClass' => 'btn-default',
        'attribute' => 'files',
        'progress' => '#mail-edit-progress',
        'preview' => '#mail-edit-preview',
        'dropZone' => '#mail-edit',
        'max' => Yii::$app->getModule('content')->maxAttachedFiles,
    ]); ?>
    <?= FileHandlerButtonDropdown::widget([
        'primaryButton' => $uploadButton,
        'handlers' => $fileHandlers,
        'cssButtonClass' => 'btn-default',
        'pullRight' => true,
    ]) ?>
    <?= UploadProgress::widget(['id' => 'mail-edit-progress']) ?>
    <?= FilePreview::widget([
        'id' => 'mail-edit-preview',
        'model' => $entry,
        'edit' => true,
        'options' => ['style' => 'margin-top:10px;']
    ]) ?>
</div>
<div class="modal-footer">

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            
            <?= ModalButton::cancel() ?>
            <?= Button::save(Yii::t('base', 'Save'))->submit()->action('mail.conversation.submitEditEntry')->options(['data-entry-id' => $entry->id]) ?>

        </div>
        <div class="col-md-3">
            <?= Button::danger(Yii::t('base', 'Delete'))->right()->options(['data-entry-id' => $entry->id])
                ->action('mail.conversation.deleteEntry')
                ->confirm(Yii::t('MailModule.base', '<strong>Confirm</strong> message deletion'),
                    Yii::t('MailModule.base', 'Do you really want to delete this message?'),
                    Yii::t('MailModule.base', 'Delete'),
                    Yii::t('MailModule.base', 'Cancel')) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>
