<?php

use humhub\helpers\Html;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\ParticipantUserList;

/* @var $message Message */
?>

<div class="float-end">
    <?= ConversationSettingsMenu::widget(['message' => $message]) ?>
</div>

<?php if ($message->title): ?>
    <h1 class="mb-0"><?= Html::encode($message->title) . ' ' . $message->getPinIcon() ?></h1>
<?php endif; ?>

<small class="py-2"><?= ParticipantUserList::widget(['message' => $message]) ?></small>
