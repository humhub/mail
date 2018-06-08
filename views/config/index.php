<?php

use humhub\modules\cfiles\Module;
use humhub\widgets\Button;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $model \humhub\modules\mail\models\ConfigureForm */
?>

<div class="panel panel-default">

    <div class="panel-heading"><?= Yii::t('MailModule.base', '<strong>Mail</strong> module configuration'); ?></div>

    <div class="panel-body">
        <?php $form = ActiveForm::begin(['id' => 'configure-form']); ?>

            <?= $form->field($model, 'showInTopNav')->checkbox(null, false); ?>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
