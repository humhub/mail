<?php

use humhub\libs\Html;
use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;

/* @var $model \humhub\modules\mail\models\Config */
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('MailModule.base', '<strong>Mail</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>

            <?= $form->field($model, 'showInTopNav')->checkbox(); ?>

            <hr>
            <?= $form->field($model, 'userConversationRestriction')->textInput(['type' => 'number']); ?>
            <?php // $form->field($model, 'userMessageRestriction')->textInput(['type' => 'number']); ?>

            <hr>
            <?= $form->field($model, 'newUserRestrictionEnabled')->checkbox(['id' => 'newUserCheckbox']); ?>
            <div id="newUserRestriction">
                <?= $form->field($model, 'newUserSinceDays')->textInput(['type' => 'number']); ?>
                <?= $form->field($model, 'newUserConversationRestriction')->textInput(['type' => 'number']); ?>
                <?php // $form->field($model, 'newUserMessageRestriction')->textInput(['type' => 'number']); ?>
            </div>

            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> <?= Yii::t('MailModule.base', 'Leave fields blank in order to disable a restriction.') ?>
            </div>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?= Html::script(<<<JS
    function checkNewUserFields()
    {
        var disabled = !$('#newUserCheckbox').is(':checked');
        if(!$('#newUserCheckbox').is(':checked')) {
            $('#newUserRestriction').hide();
        } else {
            $('#newUserRestriction').show();
        }
    }

    checkNewUserFields();

    $('#newUserCheckbox').on('change', function() {
        checkNewUserFields();
    })
JS
); ?>
