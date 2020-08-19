humhub.module('mail.ConversationView', function (module, require, $) {

    var Widget = require('ui.widget').Widget;
    var loader = require('ui.loader');
    var client = require('client');
    var additions = require('ui.additions');
    var object = require('util.object');
    var mail = require('mail.notification');

    var ConversationView = Widget.extend();

    ConversationView.prototype.init = function () {
        additions.observe(this.$);

        var that = this;
        window.onresize = function (evt) {
            that.updateSize();
        };
        this.reload();

        this.$.on('mouseenter', '.mail-conversation-entry', function () {
            $(this).find('.conversation-menu').fadeIn('fast');
        }).on('mouseleave', '.mail-conversation-entry', function () {
            $(this).find('.conversation-menu').hide();
        });
    };

    ConversationView.prototype.loader = function (load) {
        if (load !== false) {
            loader.set(this.$);
        } else {
            loader.reset(this.$);
        }
    };

    ConversationView.prototype.markSeen = function (id) {
        client.post(module.config.url.seen, {data: {id: id}}).then(function (response) {
            if (object.isDefined(response.messageCount)) {
                mail.setMailMessageCount(response.messageCount);
            }
        }).catch(function (e) {
            module.log.error(e);
        });
    };

    ConversationView.prototype.loadUpdate = function () {
        var $lastEntry = this.$.find('.mail-conversation-entry:last');
        var lastEntryId = $lastEntry.data('entry-id');
        var data = {id: this.options.messageId, from: lastEntryId};

        var that = this;
        client.get(this.options.loadUpdateUrl, {data: data}).then(function (response) {
            if (response.html) {
                that.appendEntry(response.html);
            }
        })
    };

    ConversationView.prototype.reply = function (evt) {
        var that = this;
        client.submit(evt).then(function (response) {
            if (response.success) {
                that.appendEntry(response.content);
                that.$.find(".time").timeago(); // somehow this is not triggered after reply
                $('#replyform-message').trigger('clear');
            } else {
                module.log.error(response, true);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        });
    };

    ConversationView.prototype.updateContent = function (html) {
        this.$.hide().html(html).fadeIn();
        this.getListNode().niceScroll({
            cursorwidth: "7",
            cursorborder: "",
            cursorcolor: "#555",
            cursoropacitymax: "0.2",
            nativeparentscrolling: false,
            railpadding: {top: 0, right: 0, left: 0, bottom: 0}
        });
        var that = this;
        setTimeout(function () {
            that.scrollToBottom()
        });
    };

    ConversationView.prototype.reload = function () {
        if (this.options.messageId) {
            this.loadMessage(this.options.messageId);
        }
    };

    ConversationView.prototype.addUser = function (evt) {
        var that = this;

        that.loader(true);
        client.submit(evt).then(function (response) {
            if (response.error) {
                module.log.error(response, true);
                that.reload();
            } else {
                that.updateContent(response.html);
            }
        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function () {
            that.loader(false);
        });
    };

    ConversationView.prototype.appendEntry = function (html) {
        var that = this;
        var $html = $(html);

        // Filter out all script/links and text nodes
        var $elements = $html.not('script, link').filter(function () {
            return this.nodeType === 1; // filter out text nodes
        });

        // We use opacity because some additions require the actual size of the elements.
        $elements.css('opacity', 0);

        // call insert callback
        this.getListNode().append($html);

        $elements.hide().css('opacity', 1).fadeIn('fast', function () {
            that.scrollToBottom();
            that.onUpdate();
        });

    };

    ConversationView.prototype.loadMessage = function (evt) {
        var messageId = object.isNumber(evt) ? evt : evt.$trigger.data('message-id');
        var that = this;
        this.loader();
        client.get(this.options.loadMessageUrl, {data: {id: messageId}}).then(function (response) {
            that.options.messageId = messageId;

            // Remove New badge from current selection
            $('#mail-conversation-overview').find('.selected[data-message-preview]').find('.new-message-badge').hide();

            // Set new selection
            $('#mail-conversation-overview').find('[data-message-preview]').removeClass('selected');
            $('#mail-conversation-overview').find('[data-message-preview="' + messageId + '"]').addClass('selected').find('.new-message-badge').hide();

            // Replace history state only if triggered by message preview item
            if (evt.$trigger && history && history.replaceState) {
                var url = evt.$trigger.data('action-url');
                if (url) {
                    history.replaceState(null, null, url);
                }
            }

            that.updateContent(response.html);

        }).catch(function (e) {
            module.log.error(e, true);
        }).finally(function () {
            that.loader(false);
        });
    };

    ConversationView.prototype.scrollToBottom = function () {
        var $list = this.getListNode();
        $list.animate({scrollTop: $list[0].scrollHeight});
        this.updateSize();

    };

    ConversationView.prototype.updateSize = function () {
        var that = this;
        setTimeout(function () {
            if (!$('.conversation-entry-list').length) {
                return;
            }

            var formHeight = $('.mail-message-form').height();
            var max_height = (window.innerHeight - that.$.position().top - formHeight - 160) + 'px';
            that.$.find('.conversation-entry-list').css('max-height', max_height);
        }, 100);
    };

    ConversationView.prototype.getListNode = function () {
        return this.$.find('.conversation-entry-list');
    };

    ConversationView.prototype.onUpdate = function () {
        this.getListNode().getNiceScroll().resize();
    };

    module.export = ConversationView;
});