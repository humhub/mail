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
    $leaveLinkText = Yii::t('MailModule.views_mail_show', 'Leave conversation');
    $leaveConfirmTitle =  Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation');
    $leaveConfirmText =  Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?');
    $leaveConfirmButtonText = Yii::t('MailModule.views_mail_show', 'Leave');
} else {
    $leaveLinkText = Yii::t('MailModule.views_mail_show', 'Delete conversation');
    $leaveConfirmTitle = Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation');
    $leaveConfirmText = Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?');
    $leaveConfirmButtonText = Yii::t('MailModule.views_mail_show', 'Delete');
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
                <?= ModalButton::none(Yii::t('MailModule.views_mail_show', 'Add user'))->icon('user-plus')
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
