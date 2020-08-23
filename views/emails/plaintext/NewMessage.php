<?php

use humhub\modules\mail\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\MarkdownView;

/* @var $originator \humhub\modules\user\models\User */
/* @var $message \humhub\modules\mail\models\Message */
?>
<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> message')) ?>


<?= Html::encode($originator->displayName) ?> <?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', 'sent you a new message:')) ?> <?= Html::encode($message->title) ?>


<?= strip_tags(MarkdownView::widget(['markdown' => $entry->content, 'returnPlain' => true])) ?>


<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', 'Reply now')) ?>: <?= urldecode(Url::toMessenger($message, true)) ?>
