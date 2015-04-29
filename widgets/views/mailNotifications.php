<div class="btn-group">
    <a href="#" id="icon-messages" class="dropdown-toggle" data-toggle="dropdown"><i
            class="fa fa-envelope"></i></a>
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
        jQuery.getJSON("<?php echo $this->createUrl('//mail/mail/GetNewMessageCountJson'); ?>", function (json) {
            setMailMessageCount(parseInt(json.newMessages));
        });
    }, reloadMessageCountInterval);

    setMailMessageCount(<?php echo $newMailMessageCount;?>);


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



    // open the messages menu
    $('#icon-messages').click(function () {

        // remove all <li> entries from dropdown
        $('#dropdown-messages').find('li').remove();
        $('#dropdown-messages').find('ul').remove();

        // append title and loader to dropdown
        $('#dropdown-messages').append('<li class="dropdown-header"><div class="arrow"></div><?php echo Yii::t('MailModule.widgets_views_mailNotification', 'Messages'); ?> <?php echo CHtml::link(Yii::t('MailModule.widgets_views_mailNotification', 'New message'), $this->createUrl('//mail/mail/create', array('ajax' => 1)), array('class' => 'btn btn-info btn-xs', 'id' => 'create-message-button', 'data-toggle' => 'modal', 'data-target' => '#globalModal')); ?></li> <ul class="media-list"><li id="loader_messages"><div class="loader"></div></li></ul><li><div class="dropdown-footer"><a class="btn btn-default col-md-12" href="<?php echo Yii::app()->createUrl('//mail/mail/index'); ?>"><?php echo Yii::t('MailModule.widgets_views_mailNotification', 'Show all messages'); ?></a></div></li>');

        // load newest notifications
        $.ajax({
            'type': 'GET',
            'url': '<?php echo $this->createUrl('//mail/mail/notificationList'); ?>',
            'cache': false,
            'data': jQuery(this).parents("form").serialize(),
            'success': function (html) {
                jQuery("#loader_messages").replaceWith(html)
            }});

    })
</script>

