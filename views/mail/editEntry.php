<?php

use humhub\modules\file\handler\BaseFileHandler;
use humhub\modules\file\widgets\FileHandlerButtonDropdown;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $entry MessageEntry */
/* @var $fileHandlers BaseFileHandler[] */
?>

<?php $form = Modal::beginFormDialog([
    'id' => 'mail-edit',
    'title' => Yii::t("MailModule.base", "Edit message entry"),
    'size' => Modal::SIZE_LARGE,
    'footer' =>
        ModalButton::cancel() . ' ' .
        Button::save(Yii::t('base', 'Save'))->submit()->action('mail.conversation.submitEditEntry')->options(['data-entry-id' => $entry->id]) . ' ' .
        Button::danger(Yii::t('base', 'Delete'))->right()->options(['data-entry-id' => $entry->id])
            ->action('mail.conversation.deleteEntry')
            ->confirm(Yii::t('MailModule.base', '<strong>Confirm</strong> message deletion'),
                Yii::t('MailModule.base', 'Do you really want to delete this message?'),
                Yii::t('MailModule.base', 'Delete'),
                Yii::t('MailModule.base', 'Cancel')),
]) ?>
    <?= $form->field($entry, 'content')->widget(
        MailRichtextEditor::class, [
        'placeholder' => Yii::t('MailModule.base', 'Edit message...')])->label(false) ?>

    <?php $uploadButton = UploadButton::widget([
        'id' => 'mail-edit-upload',
        'model' => $entry,
        'label' => Yii::t('ContentModule.base', 'Attach Files'),
        'tooltip' => false,
        'cssButtonClass' => 'btn-light',
        'attribute' => 'files',
        'progress' => '#mail-edit-progress',
        'preview' => '#mail-edit-preview',
        'dropZone' => '#mail-edit',
        'max' => Yii::$app->getModule('content')->maxAttachedFiles,
    ]) ?>
    <?= FileHandlerButtonDropdown::widget([
        'primaryButton' => $uploadButton,
        'handlers' => $fileHandlers,
        'cssButtonClass' => 'btn-light',
        'pullRight' => true,
    ]) ?>
    <?= UploadProgress::widget(['id' => 'mail-edit-progress']) ?>
    <?= FilePreview::widget([
        'id' => 'mail-edit-preview',
        'model' => $entry,
        'edit' => true,
        'options' => ['style' => 'margin-top:10px;']
    ]) ?>
<?php Modal::endFormDialog(); ?>
