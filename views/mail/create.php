<?php

use humhub\libs\Html;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\mail\widgets\MailRichtextEditor;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\ModalDialog;
use humhub\modules\mail\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;

/* @var $model CreateMessage */
?>

<?php ModalDialog::begin(['closable' => false]) ?>
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

            <?= $form->field($model, 'title')->textInput( ['placeholder' => Yii::t('MailModule.base', 'Subject')])->label(false) ?>

            <?= $form->field($model, 'message')->widget(
                MailRichtextEditor::class)->label(false) ?>

            <?php /* $form->field($model, 'tags')->widget(ConversationTagPicker::class, ['addOptions' => true]) */?>

        </div>
        <div class="modal-footer">

            <?= ModalButton::submitModal(Url::toCreateConversation(), Yii::t('MailModule.views_mail_create', 'Send'))?>
            <?= ModalButton::cancel()?>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php ModalDialog::end() ?>


<?= Html::script(' $(\'#recipient\').focus();') ?>