<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

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
        
        <?= ModalButton::cancel() ?>
        <?= ModalButton::submitModal(Url::toEditConversationTags($model->message)) ?>
        
    </div>
    <?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>
