humhub.module('mail.inbox', function (module, require, $) {

    var Widget = require('ui.widget').Widget;
    var Filter = require('ui.filter').Filter;
    var view = require('ui.view');
    var loader = require('ui.loader');
    var client = require('client');

    var ConversationFilter = Filter.extend();

    ConversationFilter.prototype.triggerChange = function() {
        this.super('triggerChange');
        this.updateFilterCount();
    };

    ConversationFilter.prototype.updateFilterCount = function () {
        var count = this.getActiveFilterCount();

        var $filterToggle = this.$.find('#conversation-filter-link');
        var $filterCount = $filterToggle.find('.filterCount');

        if(count) {
            if(!$filterCount.length) {
                $filterCount = $('<small class="filterCount"></small>').insertBefore($filterToggle.find('.caret'));
            }
            $filterCount.html(' <b>('+count+')</b> ');
        } else if($filterCount.length) {
            $filterCount.remove();
        }
    };

    var ConversationList = Widget.extend();

    ConversationList.prototype.init = function () {
        this.filter = Widget.instance('#mail-filter-root');

        this.initScroll();

        var that = this;
        this.filter.off('afterChange.inbox').on('afterChange.inbox', function () {
            that.reload().then(function() {
                that.updateActiveItem();
            });
        });

        if(view.isLarge()) {
            this.$.niceScroll({
                cursorwidth: "7",
                cursorborder: "",
                cursorcolor: "#555",
                cursoropacitymax: "0.2",
                nativeparentscrolling: false,
                railpadding: {top: 0, right: 3, left: 0, bottom: 0}
            });
        }

        this.$.on('click', '.entry', function() {
            that.$.find('.entry').removeClass('selected');
            $(this).addClass('selected');
        })

    };

    ConversationList.prototype.updateEntries = function(ids) {
        var that = this;

        if(!ids.length) {
            return;
        }

        client.get(this.options.updateEntriesUrl, {data: {ids: ids}}).then(function(response) {
            if(!response.result)  {
                return;
            }

            $.each(response.result, function(id, html) {
                var $entry = that.getEntry(id);
                if(!$entry.length) {
                    $(html).prependTo(that.$) ;
                } else {
                   $entry.replaceWith(html);
                }
            });

            that.updateActiveItem();
        }).catch(function(e) {
            module.log.error(e);
        });
    };

    ConversationList.prototype.getEntry = function(id) {
        return this.$.find('[data-message-preview="'+id+'"]');
    };

    ConversationList.prototype.initScroll = function() {
        if (window.IntersectionObserver) {

            var $streamEnd = $('<div class="inbox-stream-end"></div>');
            this.$.append($streamEnd);

            var that = this;
            var observer = new IntersectionObserver(function (entries) {
                if (that.preventScrollLoading()) {
                    return;
                }

                if (entries.length && entries[0].isIntersecting) {
                    loader.append(that.$);
                    that.loadMore().finally(function() {
                        loader.reset(that.$);
                    });
                }

            }, {root: this.$[0], rootMargin: "50px"});

            // Assure the conversation list is scrollable by loading more entries until overflow
            this.assureScroll().then(function() {
                observer.observe($streamEnd[0]);
            });
        }
    };

    ConversationList.prototype.assureScroll = function () {
        var that = this;

        if(this.$[0].offsetHeight >= this.$[0].scrollHeight && this.canLoadMore()) {
            return this.loadMore().then(function() {
                return that.assureScroll();
            }).catch(function () {
                return Promise.resolve();
            })
        }

        return Promise.resolve();
    };

    ConversationList.prototype.loadMore = function () {
        var that = this;
        return new Promise(function(resolve, reject) {
            var data = that.filter.getFilterMap();
            data.from = that.getLastMessageId();
            client.get(that.options.loadMoreUrl, {data: data}).then(function(response) {
                if(response.result) {
                    $(response.result).insertBefore('.inbox-stream-end');
                    that.$.find('.inbox-stream-end').append();
                }

                that.options.isLast = !response.result || response.isLast;
                that.updateActiveItem();

                resolve();
            }).catch(function(err) {
                module.log.error(err, true);
                reject();
            }).finally(function() {
                that.scrollLock = false;
            });
        });

    };

    ConversationList.prototype.preventScrollLoading = function () {
        return this.scrollLock || !this.canLoadMore();
    };

    ConversationList.prototype.canLoadMore = function () {
        return !this.options.isLast;
    };

    ConversationList.prototype.getReloadOptions = function () {
        return {data: this.filter.getFilterMap()};
    };

    ConversationList.prototype.updateActiveItem = function() {

        var activeMessageId = Widget.instance('#mail-conversation-root').getActiveMessageId();

        this.$.find('.entry').removeClass('selected');

        // Remove New badge from current selection
        this.$.find('.entry.selected').find('.new-message-badge').hide();

        // Set new selection
        this.$.find('.entry').removeClass('selected');
        var $selected = this.$.find('[data-message-preview="' + activeMessageId + '"]');

        if($selected.length) {
            $selected.removeClass('unread').addClass('selected').find('.new-message-badge').hide();
        }
    };


    ConversationList.prototype.getFirstMessageId = function() {
        return this.$.find('.entry:first').data('messagePreview');
    };

    ConversationList.prototype.getLastMessageId = function() {
        return this.$.find('.entry:last').data('messagePreview');
    };

    ConversationList.prototype.hide = function() {
        return new Promise(function (resolve) {
            if(view.isSmall()) {
                $('.inbox-wrapper').slideUp(function() {
                    if($('#mail-conversation-root').length) {
                        Widget.instance('#mail-conversation-root').updateSize();
                    }
                    resolve();
                });
            }
            resolve();
        });
    };

    ConversationList.prototype.show = function() {
        return new Promise(function (resolve) {
            if(view.isSmall()) {
                $('.inbox-wrapper').slideDown(function() {
                    if($('#mail-conversation-root').length) {
                        Widget.instance('#mail-conversation-root').updateSize();
                    }

                    resolve();
                });
            }
            resolve();
        });
    };

    var toggleInbox = function() {
        if(view.isSmall()) {
            $('.inbox-wrapper').slideToggle(function() {
                Widget.instance('#mail-conversation-root').updateSize();
            });
        }
    };

    var setTagFilter = function (evt) {
        Widget.instance('#inbox').show().then(function() {
            $('#mail-filter-menu').collapse('show');
            Widget.instance('#inbox-tag-picker').setSelection([{
                id: evt.$trigger.data('tagId'),
                text: evt.$trigger.data('tagName'),
                image: evt.$trigger.data('tagImage'),
            }]);
        });
    };

    module.export({
        ConversationList: ConversationList,
        Filter: ConversationFilter,
        setTagFilter: setTagFilter,
        toggleInbox: toggleInbox
    });
});