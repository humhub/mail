<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\MarkdownView;
?>
<?php echo strip_tags(Yii::t('MailModule.views_emails_NewMessage', '<strong>New</strong> message')); ?>


<?php echo Html::encode($sender->displayName); ?> <?php echo strip_tags(Yii::t('MailModule.views_emails_NewMessageEntry', 'sent you a new message in')); ?>: <?php echo Html::encode($message->title); ?>


<?php echo strip_tags(MarkdownView::widget(array('markdown' => $entry->content, 'returnPlain' => true))); ?>


<?php echo strip_tags(Yii::t('MailModule.views_emails_NewMessage', 'Reply now')); ?>: <?php echo urldecode(Url::to(['/mail/mail/index', 'id' => $message->id], true)); ?>