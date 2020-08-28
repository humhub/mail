<?php

use humhub\modules\mail\widgets\MailRichtextEditor;
use yii\bootstrap\ActiveForm;
use humhub\widgets\ModalDialog;
use humhub\widgets\Button;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;

/* @var $entry \humhub\modules\mail\models\MessageEntry */

?>

<?php ModalDialog::begin(['header' => Yii::t("MailModule.views_mail_edit", "Edit message entry"), 'size' => 'large']) ?>

    <?php $form = ActiveForm::begin() ?>
        <div class="modal-body mail-edit-message">
            <?= $form->field($entry, 'content')->widget(
                MailRichtextEditor::class, [
                'placeholder' => Yii::t('MailModule.base', 'Edit message...')])->label(false) ?>
        </div>
        <div class="modal-footer">

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <?= Button::save(Yii::t('base', 'Save'))->submit()->action('mail.conversation.submitEditEntry')->options(['data-entry-id' => $entry->id]) ?>
                    <?= ModalButton::cancel() ?>
                </div>
                <div class="col-md-3">
                    <?= Button::danger(Yii::t('base', 'Delete'))->right()->options(['data-entry-id' => $entry->id])
                        ->action('mail.conversation.deleteEntry')
                        ->confirm(Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> message deletion'),
                            Yii::t('MailModule.views_mail_show', 'Do you really want to delete this message?'),
                            Yii::t('MailModule.views_mail_show', 'Delete'),
                            Yii::t('MailModule.views_mail_show', 'Cancel')) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end() ?>

<?php ModalDialog::end() ?>