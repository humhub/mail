<?php

use humhub\helpers\Html;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\ParticipantUserList;

/* @var $message Message */
?>
<h1><?= Html::encode($message->title) . ' ' . $message->getPinIcon() ?></h1>

<div class="float-end">
    <?= ConversationSettingsMenu::widget(['message' => $message]) ?>
</div>

<small><?= ParticipantUserList::widget(['message' => $message]) ?></small>
