<?php

use humhub\libs\Html;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\ParticipantUserList;

/* @var $message Message */
?>
<h1><?= Html::encode($message->title) . ' ' . $message->getPinIcon() ?></h1>

<div class="pull-right">
    <?= ConversationSettingsMenu::widget(['message' => $message]) ?>
</div>

<small><?= ParticipantUserList::widget(['message' => $message]) ?></small>
