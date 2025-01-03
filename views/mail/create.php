<?php

use humhub\helpers\Html;
use humhub\modules\file\handler\BaseFileHandler;
use humhub\modules\file\widgets\FileHandlerButtonDropdown;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model CreateMessage */
/* @var $fileHandlers BaseFileHandler[] */
?>

<?php $form = Modal::beginFormDialog([
    'id' => 'mail-create',
    'title' => Yii::t('MailModule.base', '<strong>New</strong> message'),
    'closable' => false,
    'form' => ['enableClientValidation' => false, 'acknowledge' => true],
    'footer' => ModalButton::cancel() . ' ' . ModalButton::save(Yii::t('MailModule.base', 'Send'), Url::toCreateConversation()),
]) ?>
    <?= $form->field($model, 'recipient')->widget(UserPickerField::class,
        [
            'url' => Url::toSearchNewParticipants(),
            'placeholder' => Yii::t('MailModule.base', 'Add recipients'),
        ]
    )->label(false) ?>

    <?= $form->field($model, 'title')->textInput(['placeholder' => Yii::t('MailModule.base', 'Subject')])->label(false) ?>

    <?= $form->field($model, 'message')->widget(MailRichtextEditor::class)->label(false) ?>

    <?php $uploadButton = UploadButton::widget([
        'id' => 'mail-upload',
        'model' => $model,
        'label' => Yii::t('ContentModule.base', 'Attach Files'),
        'tooltip' => false,
        'cssButtonClass' => 'btn-light',
        'attribute' => 'files',
        'progress' => '#mail-progress',
        'preview' => '#mail-preview',
        'dropZone' => '#mail-create',
        'max' => Yii::$app->getModule('content')->maxAttachedFiles,
    ]) ?>
    <?= FileHandlerButtonDropdown::widget([
        'primaryButton' => $uploadButton,
        'handlers' => $fileHandlers,
        'cssButtonClass' => 'btn-light',
        'pullRight' => true,
    ]) ?>
    <?= UploadProgress::widget(['id' => 'mail-progress']) ?>
    <?= FilePreview::widget([
        'id' => 'mail-preview',
        'model' => $model,
        'edit' => true,
        'options' => ['style' => 'margin-top:10px;'],
    ]) ?>

    <?php /* $form->field($model, 'tags')->widget(ConversationTagPicker::class, ['addOptions' => true]) */ ?>
<?php Modal::endFormDialog(); ?>

<?= Html::script(' $(\'#recipient\').focus();') ?>
