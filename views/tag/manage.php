<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\forms\AddTag;
use humhub\modules\mail\models\MessageTag;
use humhub\modules\ui\view\components\View;
use humhub\widgets\Button;
use humhub\widgets\GridView;
use humhub\widgets\ModalButton;
use yii\bootstrap\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\helpers\Html;


/* @var $this View */
/* @var $model AddTag */

$dataProvider = new ActiveDataProvider([
    'query' => MessageTag::findByUser(Yii::$app->user->id)
])

?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div id="mail-conversation-header" class="panel-heading"
                     style="background-color:<?= $this->theme->variable('background-color-secondary') ?>">
                    <?= Yii::t('MailModule.base', '<strong>Manage</strong> conversation tags') ?>

                    <?= Button::back(Url::toMessenger())->right()->sm() ?>
                </div>

                <div class="panel-body">

                    <div class="help-block">
                        <?= Yii::t('MailModule.base', 'Here you can manage your private conversation tags.') ?><br>
                        <?= Yii::t('MailModule.base', 'Conversation tags can be used to filter conversations and are only visible to you.') ?>
                    </div>

                    <?php $form = ActiveForm::begin(['action' => Url::toAddTag()]); ?>
                    <div class="form-group<?= $model->tag->hasErrors() ? ' has-error' : ''?>" style="margin-bottom:0">
                        <div class="input-group">
                            <?= Html::activeTextInput($model->tag, 'name', ['style' => 'height:36px', 'class' => 'form-control', 'placeholder' => Yii::t('MailModule.base', 'Add Tag')]) ?>
                            <span class="input-group-btn">
                                <?= Button::defaultType()->icon('fa-plus')->loader()->submit() ?>
                            </span>
                        </div>
                        <span class="help-block help-block-error">
                                 <?= Html::error($model->tag, 'name') ?>
                            </span>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <?php $firstRow = true; ?>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'options' => ['class' => 'grid-view', 'style' => 'padding-top:0'],
                        'tableOptions' => ['class' => 'table table-hover'],
                        'showHeader' => false,
                        'summary' => false,
                        'columns' => [
                            'name',
                            [
                                'header' => Yii::t('base', 'Actions'),
                                'class' => ActionColumn::class,
                                'options' => ['width' => '80px'],
                                'contentOptions' => ['style' => 'text-align:right'],
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        /* @var $model \humhub\modules\topic\models\Topic */
                                        return ModalButton::primary()->load(Url::toEditTag($model->id))->icon('fa-pencil')->xs()->loader(false);
                                    },
                                    'view' => function() {
                                        return '';
                                    },
                                    'delete' => function ($url, $model) {
                                        /* @var $model \humhub\modules\topic\models\Topic */
                                        return Button::danger()->icon('fa-times')->action('client.post', Url::toDeleteTag($model->id))->confirm(
                                            Yii::t('MailModule.base', '<strong>Confirm</strong> tag deletion'),
                                            Yii::t('MailModule.base', 'Do you really want to delete this tag?'),
                                            Yii::t('base', 'Delete'))->xs()->loader(false);
                                    },
                                ],
                            ],
                        ]]) ?>
                </div>
            </div>
        </div>
    </div>
