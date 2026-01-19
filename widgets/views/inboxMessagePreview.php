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
<?= Html::beginTag('div', $options) ?>
    <div class="mail-link">
        <div class="d-flex">
            <div class="flex-shrink-0 me-2">
                <?= $lastParticipant ? Image::widget([
                    'user' => $lastParticipant,
                    'width' => '32',
                    'link' => false,
                    'htmlOptions' => $lastParticipant->isBlockedForUser() ? ['class' => 'conversation-blocked-recipient'] : [],
                ]) : '' ?>
            </div>
            <div class="text-break flex-grow-1">
                <h4 class="mt-0">
                    <?= Html::encode($messageTitle) . ' ' . $message->getPinIcon() ?>
                    <time><?= $messageTime ?></time>
                </h4>
                <span class="new-message-badge"></span>
                <?php if ($message->title): ?>
                    <h5>
                        <?= Html::encode($message->title) ?>
                    </h5>
                <?php endif; ?>
                <div class="mail-last-entry">
                    <?= $messageText ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
