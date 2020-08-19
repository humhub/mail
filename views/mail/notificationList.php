<?php
 /* @var $userMessages \humhub\modules\mail\models\UserMessage[] */

use humhub\modules\mail\widgets\InboxMessagePreview;

?>

<?php if (count($userMessages) != 0) : ?>
    <?php foreach ($userMessages as $userMessage) : ?>
        <?= InboxMessagePreview::widget(['userMessage' => $userMessage]) ?>
    <?php endforeach; ?>
<?php else: ?>
    <li class="placeholder"> <?= Yii::t('MailModule.views_mail_list', 'There are no messages yet.'); ?></li>
<?php endif; ?>