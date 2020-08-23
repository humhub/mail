humhub.module('mail.inbox', function (module, require, $) {

    var Widget = require('ui.widget').Widget;
    var Filter = require('ui.filter').Filter;

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

        var that = this;
        this.filter.off('afterChange.inbox').on('afterChange.inbox', function () {
            that.reload();
        });
    };

    ConversationList.prototype.getReloadOptions = function () {
        return {data: this.filter.getFilterMap()};
    };

    ConversationList.prototype.getFirstMessageId = function() {
        return this.$.find('.messagePreviewEntry:first').data('messagePreview');
    };

    var setTagFilter = function (evt) {
        $('#mail-filter-menu').collapse('show');
        Widget.instance('#inbox-tag-picker').setSelection([{
            id: evt.$trigger.data('tagId'),
            text: evt.$trigger.data('tagName'),
            image: evt.$trigger.data('tagImage'),
        }]);
    };

    module.export({
        ConversationList: ConversationList,
        Filter: ConversationFilter,
        setTagFilter: setTagFilter
    });
});