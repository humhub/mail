<?php

use humhub\modules\mail\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\MarkdownView;

/* @var $sender \humhub\modules\user\models\User */
/* @var $message \humhub\modules\mail\models\Message */
?>
<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> message')) ?>


<?= Html::encode($sender->displayName) ?> <?= strip_tags(Yii::t('MailModule.views_emails_NewMessageEntry', 'sent you a new message in')) ?>: <?= Html::encode($message->title) ?>


<?= strip_tags(MarkdownView::widget(['markdown' => $entry->content, 'returnPlain' => true])) ?>


<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', 'Reply now')) ?>: <?= urldecode(Url::toMessenger($message, true)) ?>