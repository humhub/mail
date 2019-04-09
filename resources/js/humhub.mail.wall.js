humhub.module('mail.wall', function(module, require, $) {

   var Widget = require('ui.widget').Widget;
   var loader = require('ui.loader');
   var modal = require('ui.modal');
   var client = require('client');
   var additions = require('ui.additions');
   var object = require('util.object');
   var event = require('event');
   var mail = require('mail');

    /* from https://github.com/Wizcorp/locks */
    function Mutex() {
        this._isLocked = false;
        this._waiting = [];
    }
    Mutex.prototype.lock = function (cb) {
        if (this._isLocked) {
            this._waiting.push(cb);
        } else {
            this._isLocked = true;
            cb.call(this);
        }
    };
    Mutex.prototype.unlock = function () {
        if (!this._isLocked) {
            throw new Error('Mutex is not locked');
        }
        var waiter = this._waiting.shift();
        if (waiter) {
            waiter.call(this);
        } else {
           this._isLocked = false;
        }
    };
    var mutex = new Mutex();

   var ConversationView = Widget.extend();

    ConversationView.prototype.init = function() {
        additions.observe(this.$);
        var that = this;
        window.onresize = function (evt) {
            that.updateSize();
        };
        this.reload();
    };

    ConversationView.prototype.loader = function(load) {
        if(load !== false) {
            loader.set(this.$);
        } else {
            loader.reset(this.$);
        }
    };

    ConversationView.prototype.markSeen = function(id) {
        client.post(module.config.url.seen, {data: {id: id}}).then(function(response) {
            if(object.isDefined(response.messageCount)) {
                mail.setMailMessageCount(response.messageCount);
            }
        }).catch(function(e) {
            module.log.error(e);
        });
    };

    ConversationView.prototype.loadUpdate = function() {
        var  $lastEntry = this.$.find('.mail-conversation-entry:last');
        var lastEntryId = $lastEntry.data('entry-id');
        var data = {id: this.options.messageId, from: lastEntryId};

        var that = this;
        client.get(this.options.loadUpdateUrl, {data:data}).then(function(response) {
            if(response.html) {
                that.appendEntry(response.html);
            }
        })
    };

    ConversationView.prototype.reply = function(evt) {
        var that = this;
        mutex.lock(function () {
        client.submit(evt).then(function(response) {
            if(response.success) {
                that.appendEntry(response.content);
                $('#replyform-message').trigger('clear');
            } else {
                module.log.error(response, true);
            }
            mutex.unlock();
        }).catch(function(e) {
            module.log.error(e, true);
            mutex.unlock();
        });
        });
    };

    ConversationView.prototype.updateContent = function(html) {
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
        setTimeout(function() {that.scrollToBottom()});
    };

    ConversationView.prototype.reload = function() {
        if(this.options.messageId) {
            this.loadMessage(this.options.messageId);
        }
    };

    ConversationView.prototype.addUser = function(evt) {
        var that = this;

        that.loader(true);
        client.submit(evt).then(function(response) {
            if(response.error) {
                module.log.error(response, true);
                that.reload();
            } else {
                that.updateContent(response.html);
            }
        }).catch(function(e) {
           module.log.error(e, true);
        }).finally(function() {
            that.loader(false);
        });
    }

    ConversationView.prototype.appendEntry = function(html) {
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

    ConversationView.prototype.loadMessage = function(evt) {
        var messageId = object.isNumber(evt) ? evt : evt.$trigger.data('message-id');
        var that = this;
        this.loader();
        client.get(this.options.loadMessageUrl, {data: {id:messageId}}).then(function(response) {
            that.options.messageId = messageId;

            // Remove New badge from current selection
            $('#mail-conversation-overview').find('.selected[data-message-preview]').find('.new-message-badge').hide();

            // Set new selection
            $('#mail-conversation-overview').find('[data-message-preview]').removeClass('selected');
            $('#mail-conversation-overview').find('[data-message-preview="'+messageId+'"]').addClass('selected').find('.new-message-badge').hide();

            // Replace history state only if triggered by message preview item
            if(evt.$trigger && history && history.replaceState) {
                var url = evt.$trigger.data('action-url');
                if(url) { history.replaceState(null, null, url); }
            }

            that.updateContent(response.html);

        }).catch(function(e) {
            module.log.error(e, true);
        }).finally(function() {
            that.loader(false);
        });
    };

    ConversationView.prototype.scrollToBottom = function() {
        var $list = this.getListNode();
        $list.animate({scrollTop : $list[0].scrollHeight});
        this.updateSize();

    };

    ConversationView.prototype.updateSize = function() {
        if(!$('.conversation-entry-list').length) {
            return;
        }

        var formHeight = $('.mail-message-form').height();
        var max_height = (window.innerHeight - this.$.position().top - formHeight - 95) + 'px';
        this.$.find('.conversation-entry-list').css('max-height', max_height);
    };

    ConversationView.prototype.getListNode = function() {
        return this.$.find('.conversation-entry-list');
    };

    ConversationView.prototype.onUpdate = function() {
        this.getListNode().getNiceScroll().resize();
    };

    var ConversationEntry = Widget.extend();

    ConversationEntry.prototype.replace = function(dom) {
        var that = this;
        $content = $(dom).hide();
        this.$.fadeOut(function() {
            $(this).replaceWith($content);
            that.$ = $content;
            that.$.fadeIn('slow');
        });
    };

    ConversationEntry.prototype.remove = function() {
        this.$.fadeToggle('slow', function() {
            $(this).remove();
        });
    };

    var submitEditEntry = function(evt) {
        modal.submit(evt).then(function(response) {
            if(response.success) {
                var entry = getEntry(evt.$trigger.data('entry-id'));
                if(entry) {
                    setTimeout(function() {
                        entry.replace(response.content);
                    }, 300)
                }

                return;
            }

            module.log.error(null, true);
        }).catch(function(e) {
            module.log.error(e, true);
        });
    };

    var deleteEntry = function(evt) {
        var entry = getEntry(evt.$trigger.data('entry-id'));

        if(!entry) {
            module.log.error(null, true);
            return;
        }

        client.post(entry.options.deleteUrl).then(function(response) {
            modal.global.close();

            if(response.success) {
                setTimeout(function() { entry.remove(); }, 1000);
            }
        }).catch(function(e) {
            module.log.error(e, true);
        });
    };

    var getEntry = function(id) {
        return Widget.instance('.mail-conversation-entry[data-entry-id="'+id+'"]');
    };

    var getRootView = function() {
        return Widget.instance('mail-conversation-root');
    }

    var init = function() {
       event.on('humhub:modules:mail:live:NewUserMessage', function (evt, events, update) {
           var root = getRootView();

           events.forEach(function(event) {
               if(root && root.options.messageId == event.data.message_id) {
                   mutex.lock(function () {
                   root.loadUpdate();
                   root.markSeen(event.data.message_id);
                   mutex.unlock();
                   });
               } else if(root) {
                   getOverViewEntry(event.data.message_id).find('.new-message-badge').show();
                  // messageIds[event.data.message_id] = messageIds[event.data.message_id] ? messageIds[event.data.message_id] ++ : 1;
               }
               mail.setMailMessageCount(event.data.count);
           });

           //TODO: update notification count
       }).on('humhub:modules:mail:live:UserMessageDeleted', function (evt, events, update) {
           events.forEach(function(event) {
               var entry = getEntry(event.data.entry_id);
               if(entry) {
                   entry.remove();
               }
               mail.setMailMessageCount(event.data.count);
           });
       });
    };

    var getOverViewEntry = function(id) {
        return $('#mail-conversation-overview').find('[data-message-preview="'+id+'"]');
    };



    var loadMessage = function(evt) {
        if($('#mail-conversation-root').length) {
            getRootView().loadMessage(evt);
        } else {
            client.pjax.redirect(evt.url);
        }

        evt.finish();
    };

    var leave = function(evt) {
        client.post(evt).then(function(response) {
            if(response.redirect) {
                client.pjax.redirect(response.redirect);
            }
        }).catch(function(e) {
            module.log.error(e, true);
        });
    };

   module.export({
       init: init,
       ConversationView: ConversationView,
       ConversationEntry: ConversationEntry,
       leave: leave,
       loadMessage: loadMessage,
       submitEditEntry: submitEditEntry,
       deleteEntry: deleteEntry,
   });
});
