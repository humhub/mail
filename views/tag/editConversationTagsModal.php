<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\ConversationTagsForm;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\ui\view\components\View;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $this View */
/* @var $model ConversationTagsForm */

?>

<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('MailModule.base', '<strong>Edit</strong> conversation tags'),
    'footer' => ModalButton::cancel() . ' ' . ModalButton::save(null, Url::toEditConversationTags($model->message)),
]) ?>
    <div class="form-text">
            <?= Yii::t('MailModule.base', 'Conversation tags can be used to filter conversations and are only visible to you.') ?>
    </div>
    <?= $form->field($model, 'tags')->widget(ConversationTagPicker::class)->label(false) ?>
<?php Modal::endFormDialog(); ?>
