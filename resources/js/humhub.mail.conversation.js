humhub.module('mail.conversation', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var modal = require('ui.modal');
    var client = require('client');
    var event = require('event');
    var mail = require('mail.notification');

    var submitEditEntry = function (evt) {
        modal.submit(evt).then(function (response) {
            if (response.success) {
                var entry = getEntry(evt.$trigger.data('entry-id'));
                if (entry) {
                    setTimeout(function () {
                        entry.replace(response.content);
                    }, 300)
                }

                return;
            }

            module.log.error(null, true);
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    var deleteEntry = function (evt) {
        var entry = getEntry(evt.$trigger.data('entry-id'));

        if (!entry) {
            module.log.error(null, true);
            return;
        }

        client.post(entry.options.deleteUrl).then(function (response) {
            modal.global.close();

            if (response.success) {
                setTimeout(function () {
                    entry.remove();
                }, 1000);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    var getEntry = function (id) {
        return Widget.instance('.mail-conversation-entry[data-entry-id="' + id + '"]');
    };

    var getRootView = function () {
        return Widget.instance('#mail-conversation-root');
    };

    var init = function () {
        event.on('humhub:modules:mail:live:NewUserMessage', function (evt, events) {
            if(!$('#inbox').length) {
                return;
            }

            var root = getRootView();
            var updated = false;
            var updatedMessages = [];
            events.forEach(function (event) {
                updatedMessages.push(event.data.message_id);
                if (!updated && root && root.options.messageId == event.data.message_id) {
                    root.loadUpdate();
                    updated = true;
                    root.markSeen(event.data.message_id);
                }
            });

            Widget.instance('#inbox').updateEntries(updatedMessages);
        }).on('humhub:modules:mail:live:UserMessageDeleted', function (evt, events, update) {
            if(!$('#inbox').length) {
                return;
            }

            events.forEach(function (event) {
                var entry = getEntry(event.data.entry_id);
                if (entry) {
                    entry.remove();
                }
                mail.setMailMessageCount(event.data.count);
            });
        });
    };

    var linkAction = function (evt) {
        client.post(evt).then(function (response) {
            if (response.redirect) {
                client.pjax.redirect(response.redirect);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    module.export({
        init,
        linkAction,
        submitEditEntry,
        deleteEntry,
    });
});
