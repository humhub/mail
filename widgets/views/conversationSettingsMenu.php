<?php

use humhub\modules\mail\helpers\Url;
use humhub\modules\mail\models\Message;
use humhub\modules\mail\widgets\PinLink;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\Link;
use humhub\widgets\ModalButton;

/* @var $message Message */
/* @var $isSingleParticipant bool */
/* @var $canAddParticipant bool */

if (!$isSingleParticipant) {
    $leaveLinkText = Yii::t('MailModule.base', 'Leave conversation');
    $leaveConfirmTitle =  Yii::t('MailModule.base', '<strong>Confirm</strong> leaving conversation');
    $leaveConfirmText =  Yii::t('MailModule.base', 'Do you really want to leave this conversation?');
    $leaveConfirmButtonText = Yii::t('MailModule.base', 'Leave');
} else {
    $leaveLinkText = Yii::t('MailModule.base', 'Delete conversation');
    $leaveConfirmTitle = Yii::t('MailModule.base', '<strong>Confirm</strong> deleting conversation');
    $leaveConfirmText = Yii::t('MailModule.base', 'Do you really want to delete this conversation?');
    $leaveConfirmButtonText = Yii::t('MailModule.base', 'Delete');
}
?>
<div class="dropdown" style="display:inline-block">
    <?= Icon::get('chevron-down', [
        'htmlOptions' => [
            'id' => 'conversation-settings-button',
            'data-toggle' => 'dropdown',
            'aria-haspopup' => 'true',
            'aria-expanded' => 'false'
        ]
    ]) ?>
    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="conversation-settings-button">
        <li>
            <?= ModalButton::none(Yii::t('MailModule.base', 'Tags'))->icon('star')
                ->load(Url::toEditConversationTags($message))->link()->loader(false) ?>
        </li>

        <?php if ($canAddParticipant) : ?>
            <li>
                <?= ModalButton::none(Yii::t('MailModule.base', 'Add user'))->icon('user-plus')
                    ->load(Url::toAddParticipant($message))->link()->loader(false) ?>
            </li>
        <?php endif; ?>

        <li>
            <?= Link::none(Yii::t('MailModule.base', 'Mark Unread'))
                ->action('mail.conversation.linkAction', Url::toMarkUnreadConversation($message))
                ->icon('eye-slash') ?>
        </li>

        <li>
            <?= PinLink::widget(['message' => $message]) ?>
        </li>

        <li>
            <?= Link::none($leaveLinkText)
                ->action('mail.conversation.linkAction', Url::toLeaveConversation($message))
                ->confirm($leaveConfirmTitle, $leaveConfirmText, $leaveConfirmButtonText)
                ->icon('sign-out')
                ->loader(false) ?>
        </li>
    </ul>
</div>
