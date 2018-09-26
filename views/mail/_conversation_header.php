<?php
use humhub\libs\Html;
use humhub\modules\user\widgets\Image;
use yii\helpers\Url;
use humhub\widgets\Button;

/* @var $message \humhub\modules\mail\models\Message */
?>

<?= Html::encode($message->title); ?>

<div class="pull-right">
    <?php if (count($message->users)) : ?>
        <?php if (count($message->users) != 1) : ?>
            <?= Button::primary( )
                ->action('mail.wall.leave', Url::to(["/mail/mail/leave", 'id' => $message->id]))->icon('fa-sign-out')->sm()
                ->confirm( Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation'),
                    Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?'),
                    Yii::t('MailModule.views_mail_show', 'Leave'))->tooltip(Yii::t('MailModule.views_mail_show', 'Leave conversation'))?>
        <?php elseif (count($message->users) == 1) : ?>
            <?= Button::primary( )
                ->action('mail.wall.leave', Url::to(["/mail/mail/leave", 'id' => $message->id]))->icon('fa-sign-out')->sm()
                ->confirm( Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation'),
                    Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?'),
                    Yii::t('MailModule.views_mail_show', 'Delete'))->tooltip(Yii::t('MailModule.views_mail_show', 'Delete conversation'))?>
            ?>
        <?php endif; ?>

        <?php foreach ($message->users as $user) : ?>
            <a href="<?= $user->getUrl(); ?>">
                <?= Image::widget(['user' => $user, 'width' => '25', 'showTooltip' => true])?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
