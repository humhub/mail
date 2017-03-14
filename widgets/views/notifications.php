<?php

use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\mail\Assets;

$this->registerjsVar('mail_loadMessageUrl', Url::to(['/mail/mail/show', 'id' => '-messageId-']));
$this->registerjsVar('mail_viewMessageUrl', Url::to(['/mail/mail/index', 'id' => '-messageId-']));

Assets::register($this);
?>
<div class="btn-group">
    <a href="#" id="icon-messages" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i></a>
    <span id="badge-messages" style="display:none;"
          class="label label-danger label-notification">1</span>
    <ul id="dropdown-messages" class="dropdown-menu">
    </ul>
</div>

<script type="text/javascript">

    /**
     * Refresh New Mail Message Count (Badge)
     */
    reloadMessageCountInterval = 60000;
    setInterval(function () {
        jQuery.getJSON("<?php echo Url::to(['/mail/mail/get-new-message-count-json']); ?>", function (json) {
            setMailMessageCount(parseInt(json.newMessages));
        });
    }, reloadMessageCountInterval);

    setMailMessageCount(<?php echo $newMailMessageCount; ?>);


    /**
     * Sets current message count
     */
    function setMailMessageCount(count) {
        // show or hide the badge for new messages
        if (count == 0) {
            $('#badge-messages').css('display', 'none');
        } else {
            $('#badge-messages').empty();
            $('#badge-messages').append(count);
            $('#badge-messages').fadeIn('fast');
        }
    }

    // V1.2 force update when pjax load
    $(document).on('humhub:ready', function () {
        jQuery.getJSON("<?php echo Url::to(['/mail/mail/get-new-message-count-json']); ?>", function (json) {
            setMailMessageCount(parseInt(json.newMessages));
        });
    });



    // open the messages menu
    $('#icon-messages').click(function () {

        // remove all <li> entries from dropdown
        $('#dropdown-messages').find('li').remove();
        $('#dropdown-messages').find('ul').remove();

        // append title and loader to dropdown
        $('#dropdown-messages').append('<li class="dropdown-header"><div class="arrow"></div><?php echo Yii::t('MailModule.widgets_views_mailNotification', 'Messages'); ?> <?php echo Html::a(Yii::t('MailModule.widgets_views_mailNotification', 'New message'), Url::to(['/mail/mail/create', 'ajax' => 1]), array('class' => 'btn btn-info btn-xs', 'id' => 'create-message-button', 'data-target' => '#globalModal')); ?></li> <ul class="media-list"><li id="loader_messages"><div class="loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div></li></ul><li><div class="dropdown-footer"><a class="btn btn-default col-md-12" href="<?php echo Url::to(['/mail/mail/index']); ?>"><?php echo Yii::t('MailModule.widgets_views_mailNotification', 'Show all messages'); ?></a></div></li>');

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

