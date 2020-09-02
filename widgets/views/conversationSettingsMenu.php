<?php

use humhub\modules\mail\models\Message;
use humhub\modules\mail\permissions\StartConversation;
use humhub\widgets\Button;
use humhub\widgets\Link;
use humhub\modules\mail\helpers\Url;
use humhub\widgets\ModalButton;

/* @var $this \humhub\modules\ui\view\components\View */
/* @var $message Message */

if((count($message->users) != 1)) {
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

$canStartConversation = Yii::$app->user->can(StartConversation::class);
$isOwn = $message->createdBy->is(Yii::$app->user->getIdentity());
?>

<div class="dropdown" style="display:inline-block">
    <?= Button::primary()->icon('fa-ellipsis-v')
        ->id('conversationSettingsButton')
        ->cssClass('conversation-head-button')
        ->options([
                'data-toggle' => "dropdown",
                'aria-haspopup' => "true",
                'aria-expanded' => "false"
        ])->loader(false)->sm() ?>
    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="conversationSettingsButton">

        <li>
            <?= ModalButton::none(Yii::t('MailModule.base', 'Tags'))->icon('star')
                ->load(Url::toEditConversationTags($message))->link()->loader(false) ?>
        </li>

        <?php if($canStartConversation) : ?>

            <li>
                <?= ModalButton::none(Yii::t('MailModule.views_mail_show', 'Add user'))->icon('user-plus')
                    ->load(Url::toAddParticipant($message))->link()->loader(false) ?>
            </li>

        <?php endif; ?>

        <li>
            <?= Link::none($leaveLinkText)
                ->action('mail.conversation.leave', Url::toLeaveConversation($message))
                ->confirm($leaveConfirmTitle,
                    $leaveConfirmText,
                    $leaveConfirmButtonText)->icon('sign-out')->loader(false) ?>
        </li>
    </ul>
</div>
