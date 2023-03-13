humhub.module('mail.notification', function (module, require, $) {
    var client = require('client');
    var loader = require('ui.loader');
    var event = require('event');
    var Widget = require('ui.widget').Widget;
    var currentXhr;
    var newMessageCount = 0;

    module.initOnPjaxLoad = true;

    var init = function (isPjax) {
        // open the messages menu
        if (!isPjax) {
            event.on('humhub:modules:mail:live:NewUserMessage', function (evt, events) {
                var evtx = events[events.length - 1];
                setMailMessageCount(evtx.data.count);
            }).on('humhub:modules:mail:live:UserMessageDeleted', function (evt, events) {
                var evtx = events[events.length - 1];
                setMailMessageCount(evtx.data.count);
            });


            $('#icon-messages').click(function () {
                if (currentXhr) {
                    currentXhr.abort();
                }

                // remove all <li> entries from dropdown
                $('#loader_messages').parent().find(':not(#loader_messages)').remove();
                loader.set($('#loader_messages').show());

                client.get(module.config.url.list, {
                    beforeSend: function (xhr) {
                        currentXhr = xhr;
                    }
                }).then(function (response) {
                    currentXhr = undefined;
                    $('#loader_messages').parent().prepend($(response.html));
                    $('#loader_messages').hide();
                });
            });
        }

        updateCount();
    };

    var updateCount = function () {
        client.get(module.config.url.count).then(function (response) {
            setMailMessageCount(parseInt(response.newMessages));
        });
    };

    var setMailMessageCount = function (count) {
        // show or hide the badge for new messages
        var $badge = $('#badge-messages');
        if (!count || parseInt(count) === 0) {
            $badge.css('display', 'none');
            newMessageCount = 0;
        } else {
            newMessageCount = count;

            $badge.empty();
            $badge.append(count);
            $badge.fadeIn('fast');
        }

        event.trigger('humhub:modules:notification:UpdateTitleNotificationCount');
    };

    var loadMessage = function (evt) {
        if ($('#mail-conversation-root').length) {
            Widget.instance('#mail-conversation-root').loadMessage(evt);
            $('#mail-conversation-root').closest('.container').addClass('mail-conversation-single-message');
        } else {
            client.redirect(evt.url);
        }
        evt.finish();
    };

    var getNewMessageCount = function () {
        return newMessageCount;
    };

    module.export({
        init: init,
        loadMessage: loadMessage,
        setMailMessageCount: setMailMessageCount,
        updateCount: updateCount,
        getNewMessageCount: getNewMessageCount,
    });
});