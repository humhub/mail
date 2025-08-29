<?php

use humhub\components\View;
use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\MessageTag;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $this View */
/* @var $model MessageTag */
?>

<?php $form = Modal::beginFormDialog([
    'title' => Yii::t('MailModule.base', '<strong>Edit</strong> tag'),
    'footer' => ModalButton::cancel() . ' ' . ModalButton::save()->submit(Url::toEditTag($model->id)),
])?>
    <?= $form->field($model, 'name') ?>
<?php Modal::endFormDialog(); ?>
