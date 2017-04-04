function loadMessage(messageId) {
    
    if ($("#mail_message_details").length) {

        // Inside message module
        $.ajax({
            'type': 'GET',
            'url': mail_loadMessageUrl.replace('-messageId-', messageId),
            'cache': false,
            'data': jQuery(this).parents("form").serialize(),
            'beforeSend': function() {
                window.scrollTo(0, 0);
                $("#mail_message_details").html('<div class="loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div></div></div></div>');
                $('.messagePreviewEntry').removeClass('selected');
                $(".messagePreviewEntry_"+messageId).addClass('selected');
            },
            'success': function(html) {
                $("#mail_message_details").html(html);
                
                scrollToLastMsg();                
            }});
    } else {
        // Somewhere outside
        window.location.replace(mail_viewMessageUrl.replace('-messageId-', messageId));
    }
    

}

function scrollToLastMsg(recursion = false) {
    var msgContainer = $('.panel.panel-default');
    var msgContainerTop = msgContainer.offset().top;

    for (i = -2; i < 1; i++) {
        var lastMsg = $('#mail_message_details .media-list div.media').eq(i);
        if (lastMsg.size()) {
            var lastMsgTop = lastMsg.offset().top;
            //console.log('scroll to: ' + (lastMsgTop ));
            $('html, body').animate({
                scrollTop: lastMsgTop - msgContainerTop
            }, (recursion) ? 0 : 1000, function() {
                if ((!recursion) && (lastMsgTop !== lastMsg.offset().top)) {
                    //console.log('scroll not finished. missing: ' + (lastMsg.offset().top - lastMsgTop) );
                    scrollToLastMsg(true);
                }
            });
            break;
        }
    }
}

$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 120) {
            //
            $('#back-to-top').fadeIn();
        } else {
            $('#back-to-top').fadeOut();
        }
    });
    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#back-to-top').tooltip('hide');
        $('html, body').animate({
            scrollTop: 0
        }, 1000);
        return false;
    });

    $('#back-to-top').tooltip('show');

});
