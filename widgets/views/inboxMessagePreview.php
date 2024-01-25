<?php

/**
 * Shows a  preview of given $userMessage (UserMessage).
 *
 * This can be the notification list or the message navigation
 */

use humhub\modules\mail\models\Message;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\Image;
use yii\helpers\Html;

/* @var $message Message */
/* @var $messageTitle string */
/* @var $messageText string */
/* @var $messageTime string */
/* @var $lastParticipant User */
/* @var $options array */
?>
<?= Html::beginTag('li', $options) ?>
    <div class="mail-link">
        <div class="media">
            <div class="media-left">
                <?= $lastParticipant ? Image::widget([
                    'user' => $lastParticipant,
                    'width' => '32',
                    'link' => false,
                    'htmlOptions' => $lastParticipant->isBlockedForUser() ? ['class' => 'conversation-blocked-recipient'] : [],
                ]) : '' ?>
            </div>
            <div class="media-body text-break">
                <h4 class="media-heading">
                    <?= Html::encode($messageTitle) . ' ' . $message->getPinIcon() ?>
                    <time><?= $messageTime ?></time>
                </h4>
                <h5>
                    <span><?= Html::encode($message->title) ?></span>
                    <span class="new-message-badge"></span>
                </h5>
                <div class="mail-last-entry">
                    <?= $messageText ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('li') ?>
