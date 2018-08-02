<?php

use humhub\modules\mail\assets\MailAsset;
use humhub\modules\mail\permissions\StartConversation;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \humhub\modules\ui\view\components\View */

MailAsset::register($this);

$canStartConversation = Yii::$app->user->can(StartConversation::class);
?>
<div class="btn-group">
    <a href="#" id="icon-messages" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i></a>
    <span id="badge-messages" style="display:none;" class="label label-danger label-notification"></span>
    <ul id="dropdown-messages" class="dropdown-menu">
    </ul>
</div>

<script type="text/javascript">



    // V1.2 force update when pjax load
    $(document).on('humhub:ready', function () {
        jQuery.getJSON("<?php echo Url::to(['/mail/mail/get-new-message-count-json']); ?>", function (json) {
            humhub.modules.mail.wall.setMailMessageCount(parseInt(json.newMessages));
        });
    });



    // open the messages menu
    $('#icon-messages').click(function () {

        // remove all <li> entries from dropdown
        $('#dropdown-messages').find('li').remove();
        $('#dropdown-messages').find('ul').remove();

        // append title and loader to dropdown
        $('#dropdown-messages').append('<li class="dropdown-header"><div class="arrow"></div><?= Yii::t('MailModule.widgets_views_mailNotification', 'Messages'); ?> <?= ($canStartConversation) ? Html::a(Yii::t('MailModule.widgets_views_mailNotification', 'New message'), Url::to(['/mail/mail/create', 'ajax' => 1]), ['class' => 'btn btn-info btn-xs', 'id' => 'create-message-button', 'data-target' => '#globalModal']) : '' ?></li> <ul class="media-list"><li id="loader_messages"><div class="loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div></li></ul><li><div class="dropdown-footer"><a class="btn btn-default col-md-12" href="<?php echo Url::to(['/mail/mail/index']); ?>"><?php echo Yii::t('MailModule.widgets_views_mailNotification', 'Show all messages'); ?></a></div></li>');

        $.ajax({
            'type': 'GET',
            'url': '<?php echo Url::to(['/mail/mail/notification-list']); ?>',
            'cache': false,
            'data': jQuery(this).parents("form").serialize(),
            'success': function (html) {
                jQuery("#loader_messages").replaceWith(html);
            }});
    })
</script>

