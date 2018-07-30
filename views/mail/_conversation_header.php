<?php
use humhub\libs\Html;
use humhub\widgets\ModalConfirm;
use humhub\modules\user\widgets\Image;
use yii\helpers\Url;

/* @var $message \humhub\modules\mail\models\Message */
?>

<?= Html::encode($message->title); ?>

<div class="pull-right">
    <?php if (count($message->users)) : ?>
        <?php if (count($message->users) != 1) : ?>
            <?php
            echo ModalConfirm::widget(array(
                'uniqueID' => 'modal_leave_conversation_' . $message->id,
                'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> leaving conversation'),
                'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to leave this conversation?'),
                'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Leave'),
                'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                'linkContent' => '<i class="fa fa-sign-out"></i> ',
                'linkTooltipText' => Yii::t('MailModule.views_mail_show', 'Leave conversation'),
                'cssClass' => 'btn btn-primary btn-sm',
                'linkHref' => Url::to(["/mail/mail/leave", 'id' => $message->id])
            ));
            ?>
        <?php endif; ?>
        <?php if (count($message->users) == 1) : ?>
            <?php
            echo ModalConfirm::widget(array(
                'uniqueID' => 'modal_leave_conversation_' . $message->id,
                'title' => Yii::t('MailModule.views_mail_show', '<strong>Confirm</strong> deleting conversation'),
                'message' => Yii::t('MailModule.views_mail_show', 'Do you really want to delete this conversation?'),
                'buttonTrue' => Yii::t('MailModule.views_mail_show', 'Delete'),
                'buttonFalse' => Yii::t('MailModule.views_mail_show', 'Cancel'),
                'linkContent' => '<i class="fa fa-times"></i> ',
                'linkTooltipText' => Yii::t('MailModule.views_mail_show', 'Delete conversation'),
                'cssClass' => 'btn btn-primary btn-sm',
                'linkHref' => Url::to(["/mail/mail/leave", 'id' => $message->id])
            ));
            ?>
        <?php endif; ?>

        <?php foreach ($message->users as $user) : ?>
            <a href="<?= $user->getUrl(); ?>">
                <?= Image::widget(['user' => $user, 'width' => '25', 'showTooltip' => true])?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
