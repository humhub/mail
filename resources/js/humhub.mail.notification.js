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

                const messageLoader = $('#loader_messages');
                const messageList = messageLoader.parent();

                // remove all entries from dropdown
                messageLoader.parent().find(':not(#loader_messages)').remove();
                loader.set(messageLoader.removeClass('d-none'));

                client.get(module.config.url.list, {
                    beforeSend: function (xhr) {
                        currentXhr = xhr;
                    }
                }).then(function (response) {
                    currentXhr = undefined;
                    messageList.prepend($(response.html));
                    messageLoader.addClass('d-none');
                    messageList.niceScroll({
                        cursorwidth: '7',
                        cursorborder: '',
                        cursorcolor: '#555',
                        cursoropacitymax: '0.2',
                        nativeparentscrolling: false,
                        railpadding: {top: 0, right: 3, left: 0, bottom: 0}
                    });
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
            $badge.addClass('d-none');
            newMessageCount = 0;
        } else {
            $badge.removeClass('d-none');
            newMessageCount = count;
            $badge.empty();
            $badge.append(count);
            $badge.fadeIn('fast');
        }

        event.trigger('humhub:modules:notification:UpdateTitleNotificationCount');
    };

    var loadMessage = function (evt) {
        var root = Widget.instance('#mail-conversation-root');
        if (root && typeof(root.loadMessage) === 'function') {
            root.loadMessage(evt);
            root.$.closest('.container').addClass('mail-conversation-single-message');
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
