<?php

use humhub\libs\Html;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\ParticipantUserList;

/* @var $message Message */
?>
<strong><?= Html::encode($message->title) ?></strong>

<div class="pull-right">
    <?= ConversationSettingsMenu::widget(['message' => $message]) ?>
</div>

<div id="conversation-head-info">
    <small><?= ParticipantUserList::widget(['message' => $message]) ?></small>
</div>