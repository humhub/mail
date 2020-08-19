humhub.module('mail.ConversationEntry', function (module, require, $) {

    var Widget = require('ui.widget').Widget;

    var ConversationEntry = Widget.extend();

    ConversationEntry.prototype.replace = function (dom) {
        var that = this;
        var $content = $(dom).hide();
        this.$.fadeOut(function () {
            $(this).replaceWith($content);
            that.$ = $content;
            that.$.fadeIn('slow');
        });
    };

    ConversationEntry.prototype.remove = function () {
        this.$.fadeToggle('slow', function () {
            $(this).remove();
        });
    };

    module.export = ConversationEntry;
});