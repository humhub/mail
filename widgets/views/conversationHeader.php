<?php

use humhub\libs\Html;
use humhub\modules\mail\widgets\ConversationSettingsMenu;
use humhub\modules\mail\widgets\ParticipantUserList;
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

<strong>
<?= Html::encode($message->title) ?>
</strong>
<br>
<div class="pull-right">
    <?php if (!empty($users)) : ?>

        <?php foreach ($users as $index => $user) : ?>
            <?php if ($index < $maxUserImages) : ?>
                <?= Image::widget([
                    'user' => $user,
                    'width' => '25',
                    'showTooltip' => true,
                    'link' => true,
                    'linkOptions' => ['class' => 'hidden-xs' . ($user->isBlockedForUser() ? ' conversation-blocked-recipient' : '')]
                ]) ?>
            <?php else: ?>
                <?php $userList .= Html::encode($user->getDisplayName()) ?>
                <?php $userList .= ($index < $userCount - 1) ? '<br>' : '' ?>
            <?php endif ?>
        <?php endforeach; ?>

        <?php if ($userCount > $maxUserImageEntries) : ?>
            <?= ModalButton::defaultType('+' . (count($message->users) - $maxUserImages))
                ->load(Url::toConversationUserList($message))
                ->cssClass('conversation-head-button')
                ->tooltip($userList)
                ->cssClass('hidden-xs')
                ->options(['data-html' => 'true', 'data-placement' => 'bottom'])
                ->sm()->loader(false) ?>
        <?php endif; ?>

        <?= ConversationSettingsMenu::widget(['message' => $message]) ?>
    <?php endif; ?>
</div>

<div id="conversation-head-info">
    <small>
        <?php $link = Html::beginTag('strong') . Html::containerLink($message->originator, ['style' => ['color' => $this->theme->variable('info')]]) . Html::endTag('strong'); ?>
        <?= Yii::t('MailModule.base', 'created by {name}', ['name' => $link]) ?>

        <?= ParticipantUserList::widget(['message' => $message, 'options' => ['class' => 'visible-xs-inline']]) ?>
    </small>
</div>
