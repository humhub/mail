<?php


use humhub\libs\Html;
use humhub\modules\mail\models\forms\InboxFilterForm;
use humhub\modules\mail\widgets\ConversationTagPicker;
use humhub\modules\ui\filter\widgets\PickerFilterInput;
use humhub\modules\ui\filter\widgets\TextFilterInput;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\ui\view\components\View;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\Link;


/* @var $this View */
/* @var $options array */
/* @var $model InboxFilterForm# */
?>

<?= Html::beginTag('div', $options) ?>
<?= Link::none(Yii::t('MailModule.base', 'Filters') . ' <b class="caret"></b>')
    ->id('conversation-filter-link')
    ->href('#mail-filter-menu')
    ->icon('filter')
    ->options(['data-toggle' => "collapse"])
    ->sm() ?>

<div id="mail-filter-menu" class="collapse">
    <hr>
    <?php $filterForm = ActiveForm::begin() ?>

    <?= TextFilterInput::widget(['id' => 'term', 'category' => 'term', 'options' => ['placeholder' => Yii::t('MailModule.base', 'Search')]]) ?>

    <div class="form-group">
        <?= PickerFilterInput::widget([
            'id' => 'participants', 'category' => 'participants',
            'picker' => UserPickerField::class,
            'pickerOptions' => ['name' => 'participants', 'placeholder' => Yii::t('MailModule.base', 'Participants')]]) ?>
    </div>

    <div class="form-group">
        <?= PickerFilterInput::widget([
            'id' => 'tags', 'category' => 'tags',
            'picker' => ConversationTagPicker::class,
            'pickerOptions' => ['id' => 'inbox-tag-picker', 'name' => 'tags', 'placeholder' => Yii::t('MailModule.base', 'Tags'), 'placeholderMore' => Yii::t('MailModule.base', 'Tags')]]) ?>
    </div>

    <?php /* $filterForm->field($model, 'term')->textInput([
            'placeholder' => Yii::t('MailModule.base', 'Search')])->label(false) ?>

        <?= $filterForm->field($model, 'participants')->widget(UserPickerField::class, [
            'placeholder' => Yii::t('MailModule.base', 'Participants')])->label(false) ?>

        <?= $filterForm->field($model, 'tags')->widget(ConversationTagPicker::class, [
            'placeholderMore' => Yii::t('MailModule.base', 'Tags'),
            'addOptions' => false])
            ->label(false) ?>
        */ ?>
    <?php ActiveForm::end() ?>
</div>
<?= Html::endTag('div') ?>
