<?php

use humhub\libs\Html;
use humhub\modules\user\widgets\UserPickerField;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\modules\content\widgets\richtext\ProsemirrorRichTextEditor;


/* @var $model \humhub\modules\mail\models\forms\CreateMessage */
?>



<div class="modal-dialog">
    <div class="modal-content">
        <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"
                id="myModalLabel"><?= Yii::t('MailModule.views_mail_create', 'New message'); ?></h4>
        </div>

        <div class="modal-body">

            <?= $form->field($model, 'recipient')->widget(UserPickerField::class,
                [
                    'url' => Url::toRoute(['/mail/mail/search-user']),
                    'placeholder' => Yii::t('MailModule.views_mail_create', 'Add recipients'),
                    'focus' => true
                ]
            );?>

            <?= $form->field($model, 'title'); ?>

            <?= $form->field($model, 'message')->widget(
                ProsemirrorRichTextEditor::class, [
                'menuClass' => 'plainMenu',
                'placeholder' => Yii::t('MailModule.base', 'Write a message...'),
                'pluginOptions' => ['maxHeight' => '300px'],
            ])->label(false) ?>

        </div>
        <div class="modal-footer">

            <?= ModalButton::submitModal(Url::to(['/mail/mail/create']), Yii::t('MailModule.views_mail_create', 'Send'))?>
            <?= ModalButton::cancel()?>

        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>


<?= Html::script(' $(\'#recipient\').focus();') ?>