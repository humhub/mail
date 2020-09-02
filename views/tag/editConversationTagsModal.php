<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\ui\view\components\View;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\bootstrap\ActiveForm;

/* @var $this View */
/* @var $model ConversationTagsForm */

?>

<?php ModalDialog::begin(['header' => Yii::t('MailModule.base', '<strong>Edit</strong> conversation tags')]) ?>
    <?php $form = ActiveForm::begin() ?>
        <div class="modal-body">

        <div class="help-block">
                <?= Yii::t('MailModule.base', 'Conversation tags can be used to filter conversations and are only visible to you.') ?>
        </div>

            <?= $form->field($model, 'tags')->widget(ConversationTagPicker::class)->label(false) ?>
        </div>
    <div class="modal-footer">
        <?= ModalButton::submitModal(Url::toEditConversationTags($model->message)) ?>
        <?= ModalButton::cancel() ?>
    </div>
    <?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>