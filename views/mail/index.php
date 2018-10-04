<?php

use humhub\modules\mail\widgets\wall\ConversationView;

/* @var $messageId int */
/* @var $userMessages \humhub\modules\mail\models\UserMessage[] */
/* @var $pagination \yii\data\Pagination */

?>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <?= $this->render('_conversation_chooser', [
                'userMessages' => $userMessages,
                'pagination' => $pagination,
                'activeMessageId' => $messageId
            ]) ?>
        </div>

        <div class="col-md-8 messages">
            <?= ConversationView::widget(['messageId' => $messageId]) ?>
        </div>
    </div>
</div>
