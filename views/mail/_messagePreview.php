<?php

/**
 * Shows a  preview of given $userMessage (UserMessage).
 * 
 * This can be the notification list or the message navigation
 */
use yii\helpers\Html;
use humhub\widgets\TimeAgo;
use humhub\libs\Helpers;
use humhub\widgets\MarkdownView;

$message = $userMessage->message;
?>

<?php if ($message->getLastEntry() != null) : ?>
    <li class="messagePreviewEntry_<?php echo $message->id; ?> messagePreviewEntry entry">
        <a href="javascript:loadMessage('<?php echo $message->id; ?>');">
            <div>
                <h5><b><?php echo Html::encode(Helpers::truncateText($message->title, 75)); ?></b></h5>
                <p>
                    <?php foreach ($message->users as $user) : ?>
                        <?php if ($user->id == Yii::$app->user->id) { continue; } ?>
                        <img src="<?php echo $user->getProfileImage()->getUrl(); ?>"
                             class="img-rounded tt img_margin"
                             data-src="holder.js/32x32" alt="32x32" style="width: 32px; height: 32px;"
                             data-toggle="tooltip" data-placement="top" title=""
                             data-original-title="<?php echo Html::encode($user->displayName); ?>">
                         <?php endforeach; ?>
                </p>
                <div class="media-body text-break">
                    <h5 class="media-heading"><?php echo Yii::t('MailModule.views_mail_index', 'Last reply by: ') . Html::encode($message->originator->displayName); ?> <small><?php echo TimeAgo::widget(['timestamp' => $message->updated_at]); ?></small></h5>
                    <?php echo Helpers::truncateText(MarkdownView::widget(['markdown' => $message->getLastEntry()->content, 'parserClass' => '\humhub\libs\MarkdownPreview', 'returnPlain' => true]), 200); ?>
                    <?php
                    // show the new badge, if this message is still unread
                    if ($message->updated_at > $userMessage->last_viewed && $message->getLastEntry()->user->id != Yii::$app->user->id) {
                        echo '<span class="label label-danger">' . Yii::t('MailModule.views_mail_index', 'New') . '</span>';
                    }
                    ?>
                </div>
            </div>
        </a>
    </li>
<?php endif; ?>
