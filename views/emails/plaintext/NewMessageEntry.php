<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\MarkdownView;
?>
<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> message')); ?>

<?= Html::encode($sender->displayName); ?> <?= strip_tags(Yii::t('MailModule.views_emails_NewMessageEntry', 'sent you a new message in')); ?>: <?= Html::encode($message->title); ?>

<?= strip_tags(MarkdownView::widget(array('markdown' => $entry->content, 'returnPlain' => true))); ?>

<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', 'Reply now')); ?>: <?= urldecode(Url::to(['/mail/mail/index', 'id' => $message->id], true)); ?>