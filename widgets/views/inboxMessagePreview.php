<?php

/**
 * Shows a  preview of given $userMessage (UserMessage).
 *
 * This can be the notification list or the message navigation
 */

use yii\helpers\Html;
use humhub\widgets\TimeAgo;
use humhub\libs\Helpers;
use humhub\modules\mail\helpers\Url;
use humhub\modules\user\widgets\Image;
use humhub\widgets\Label;


/* @var $userMessage \humhub\modules\mail\models\UserMessage */
/* @var $active bool */

$message = $userMessage->message;
$userCount = $message->getUsers()->count();
$participant = $message->getLastActiveParticipant();
$lastEntry = $message->getLastEntry();
$users = $message->users;
?>

<?php if ($lastEntry) : ?>
    <li data-message-preview="<?= $message->id ?>" class="messagePreviewEntry entry">
        <div class="mail-link" data-action-click="mail.conversation.loadMessage" data-action-url="<?= Url::toMessenger($message)?>" data-message-id="<?= $message->id ?>">
            <div class="media">
                <div class="media-left pull-left">
                    <?= Image::widget(['user' => $participant, 'width' => '32', 'link' => false])?>
                </div>

                <div class="media-body text-break">
                    <h4 class="media-heading">
                        <a href="#" style="color:<?= $this->theme->variable('info') ?>"><strong><?= Html::encode(Helpers::truncateText($message->title, 75)) ?></strong></a>
                    </h4>
                    <h5>
                        <small>
                            <?= Yii::t('MailModule.base','with')?> <a href="#" style="color:<?= $this->theme->variable('info') ?>">
                                <?= Html::encode($participant->displayName) . (($userCount > 2)
                                    ? ', '. Yii::t('MailModule.base', '{n,plural,=1{# other} other{# others}}', ['n' => $userCount - 2])
                                    : '') ?>
                            </a> &middot; <?= TimeAgo::widget(['timestamp' => $message->updated_at]) ?>
                        </small>
                    </h5>

                    <?= $lastEntry->user->is(Yii::$app->user->getIdentity()) ? Yii::t('MailModule.base', 'You') : Html::encode($lastEntry->user->profile->firstname) ?>:

                        <?= Html::encode($message->getPreview()) ?>

                    <?= Label::danger(Yii::t('MailModule.views_mail_index', 'New'))
                        ->cssClass('new-message-badge')->style((!$userMessage->isUnread() ? 'display:none' : '')); ?>
                </div>
            </div>
        </div>
    </li>
<?php endif; ?>
