<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\models\MessageEntry;
use humhub\modules\user\models\User;
use yii\helpers\Html;
use humhub\widgets\MarkdownView;

/* @var $user User */
/* @var $sender User */
/* @var $message Message */
/* @var $entry MessageEntry */
/* @var $headline string */
/* @var $senderUrl string */
/* @var $content string */
/* @var $subHeadline string */
?>
<?= strip_tags($headline) ?>


<?= strip_tags($subHeadline) ?>


<?= strip_tags($content) ?>


<?= strip_tags(Yii::t('MailModule.views_emails_NewMessage', 'Reply now')) ?>: <?= urldecode(Url::toMessenger($message, true)) ?>
