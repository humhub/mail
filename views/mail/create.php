<?php

use humhub\libs\Html;
use humhub\modules\file\widgets\FilePreview;
use humhub\modules\file\widgets\UploadButton;
use humhub\modules\file\widgets\UploadProgress;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\widgets\ActiveForm;

/* @var $model CreateMessage */
?>

<?php ModalDialog::begin([
    'id' => 'mail-create',
    'closable' => false,
]) ?>
<div class="modal-content">
    <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 id="myModalLabel" class="modal-title">
            <?= Yii::t('MailModule.views_mail_create', '<strong>New</strong> message') ?>
        </h4>
    </div>

    <div class="modal-body">

        <?= $form->field($model, 'recipient')->widget(UserPickerField::class,
            [
                'url' => Url::toSearchNewParticipants(),
                'placeholder' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
            ]
        )->label(false) ?>

        <?= $form->field($model, 'title')->textInput(['placeholder' => Yii::t('MailModule.base', 'Subject')])->label(false) ?>

        <?= $form->field($model, 'message')->widget(
            MailRichtextEditor::class)->label(false) ?>

        <?= UploadButton::widget([
            'id' => 'mail-upload',
            'model' => $model,
            'label' => Yii::t('ContentModule.base', 'Attach Files'),
            'tooltip' => false,
            'cssButtonClass' => 'btn-default',
            'attribute' => 'files',
            'progress' => '#mail-progress',
            'preview' => '#mail-preview',
            'dropZone' => '#mail-create',
            'max' => Yii::$app->getModule('content')->maxAttachedFiles,
        ]) ?>
        <?= UploadProgress::widget(['id' => 'mail-progress']) ?>
        <?= FilePreview::widget([
            'id' => 'mail-preview',
            'model' => $model,
            'edit' => true,
            'options' => ['style' => 'margin-top:10px;']
        ]) ?>

        <?php /* $form->field($model, 'tags')->widget(ConversationTagPicker::class, ['addOptions' => true]) */ ?>

    </div>
    <div class="modal-footer">

        <?= ModalButton::submitModal(Url::toCreateConversation(), Yii::t('MailModule.views_mail_create', 'Send')) ?>
        <?= ModalButton::cancel() ?>

    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php ModalDialog::end() ?>


<?= Html::script(' $(\'#recipient\').focus();') ?>
