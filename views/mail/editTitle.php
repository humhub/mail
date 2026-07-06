<?php
use humhub\widgets\bootstrap\Button;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $message \humhub\modules\mail\models\Message */
?>

<?php $form = Modal::beginFormDialog([
    'id' => 'mail-edit-message-title',
    'title' => Yii::t("MailModule.base", "Edit conversation subject"),
    'footer'
        => ModalButton::cancel() . ' '
        . Button::save(Yii::t('base', 'Save'))->submit()->action('mail.conversation.submitEditMessage')->options(['data-message-id' => $message->id]),
]) ?>
    <?= $form->field($message, 'title')->textInput()->label(false) ?>
<?php Modal::endFormDialog(); ?>
