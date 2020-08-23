<?php

use humhub\libs\Html;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\ModalDialog;
use humhub\modules\mail\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;

/* @var $model \humhub\modules\mail\models\forms\CreateMessage */
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
            ) ?>

            <?= $form->field($model, 'title') ?>

            <?= $form->field($model, 'message')->widget(
                ProsemirrorRichTextEditor::class, [
                'menuClass' => 'plainMenu',
                'placeholder' => Yii::t('MailModule.base', 'Write a message...'),
                'pluginOptions' => ['maxHeight' => '300px'],
            ])->label(false) ?>

            <?= $form->field($model, 'tags')->widget(ConversationTagPicker::class, ['addOptions' => true])?>

        </div>
        <div class="modal-footer">

            <?= ModalButton::submitModal(Url::toCreateConversation(), Yii::t('MailModule.views_mail_create', 'Send'))?>
            <?= ModalButton::cancel()?>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

<?php ModalDialog::end() ?>


<?= Html::script(' $(\'#recipient\').focus();') ?>