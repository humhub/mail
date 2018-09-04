humhub.module('mail', function(module, require, $) {
    var client = require('client');
    var wall = require('mail.wall');
    var loader = require('ui.loader');

    var init = function() {
        // open the messages menu
        $('#icon-messages').click(function () {
            // remove all <li> entries from dropdown
            $('#loader_messages').parent().find(':not(#loader_messages)').remove();
            loader.set($('#loader_messages').show());
            client.get(module.config.url.list).then(function (response) {
                $('#loader_messages').parent().prepend($(response.html));
                $('#loader_messages').hide();
            });
        });

        updateCount();
    };

    var updateCount = function() {
        client.get(module.config.url.count).then(function(response) {
            setMailMessageCount(parseInt(response.newMessages));
        });
    };

    var setMailMessageCount = function(count) {
        // show or hide the badge for new messages
        var $badge = $('#badge-messages');
        if (!count || count == '0') {
            $badge.css('display', 'none');
        } else {
            $badge.empty();
            $badge.append(count);
            $badge.fadeIn('fast');
        }
    }

    module.export({
        init: init,
        setMailMessageCount: setMailMessageCount,
        updateCount: updateCount
    })
});