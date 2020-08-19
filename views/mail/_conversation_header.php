<?php
use humhub\libs\Html;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\user\widgets\Image;
use humhub\widgets\ModalButton;
use humhub\modules\mail\helpers\Url;

/* @var $message \humhub\modules\mail\models\Message */

// Max items (including show more button) to display, should be > 2
$maxUserImageEntries = 3;

$users = $message->users;
$userCount = count($users);

// We only display the show more button if we have more than one overlapping user
$maxUserImages = ($userCount === $maxUserImageEntries) ? $maxUserImageEntries : $maxUserImageEntries - 1;


$userList = '';
?>

<?= Html::encode($message->title) ?>
<br>
<div class="pull-right">
    <?php if (!empty($users)) : ?>

        <?php foreach ($users as $index => $user) : ?>
            <?php if($index < $maxUserImages) : ?>
                <?= Image::widget(['user' => $user, 'width' => '25', 'showTooltip' => true, 'link' => true])?>
            <?php else: ?>
                <?php $userList .= Html::encode($user->getDisplayName())?>
                <?php $userList .= ($index < $userCount - 1) ? '<br>' : ''?>
            <?php endif ?>
        <?php endforeach; ?>

        <?php if($userCount > $maxUserImageEntries) : ?>
            <?= ModalButton::defaultType('+'.(count($message->users) - $maxUserImages))
                ->load(Url::toConversationUserList($message))
                ->cssClass('conversation-head-button')
                ->tooltip($userList)
                ->options(['data-html' => 'true', 'data-placement' => 'bottom'])
                ->sm()->loader(false) ?>
        <?php endif; ?>

        <?= ConversationSettingsMenu::widget(['message' => $message])?>

    <?php /*
        <?php if (count($message->users) != 1) : ?>
            <?= Button::warning( )
                ->action('mail.conversation.leave', Url::to(["/mail/mail/leave", 'id' => $message->id]))->icon('fa-sign-out')->sm()
                ->confirm( Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation'),
                    Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?'),
                    Yii::t('MailModule.views_mail_show', 'Leave'))->tooltip(Yii::t('MailModule.views_mail_show', 'Leave conversation'))?>
        <?php elseif (count($message->users) == 1) : ?>
            <?= Button::warning( )
                ->action('mail.conversation.leave', Url::to(["/mail/mail/leave", 'id' => $message->id]))->icon('fa-sign-out')->sm()
                ->confirm( Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation'),
                    Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?'),
                    Yii::t('MailModule.views_mail_show', 'Delete'))->tooltip(Yii::t('MailModule.views_mail_show', 'Delete conversation'))?>
        <?php endif; ?>
    */ ?>
    <?php endif; ?>
</div>

<div id="conversation-head-info">
    <small>
    <?= Yii::t('MailModule.base', 'crated by {name}', ['name' => '<strong>'.Html::encode($message->originator->displayName).'</strong>'])?>
    </small>
</div>
