humhub.module('mail', function(module, require, $) {
    var client = require('client');
    var loader = require('ui.loader');
    var currentXhr;

    module.initOnPjaxLoad = true;

    var init = function(isPjax) {
        // open the messages menu

        if(!isPjax) {
            $('#icon-messages').click(function () {

                if(currentXhr) {
                    currentXhr.abort();
                }

                // remove all <li> entries from dropdown
                $('#loader_messages').parent().find(':not(#loader_messages)').remove();
                loader.set($('#loader_messages').show());

                client.get(module.config.url.list, {beforeSend: function(xhr) {
                    currentXhr = xhr;
                }}).then(function (response) {
                    currentXhr = undefined;
                    $('#loader_messages').parent().prepend($(response.html));
                    $('#loader_messages').hide();
                });
            });
        }

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
    };

    module.export({
        init: init,
        setMailMessageCount: setMailMessageCount,
        updateCount: updateCount
    });
});

// Hotfix for IE11, this will be included in HumHub v1.3.5
if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, 'includes', {
        value: function(searchElement, fromIndex) {

            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }

            // 1. Let O be ? ToObject(this value).
            var o = Object(this);

            // 2. Let len be ? ToLength(? Get(O, "length")).
            var len = o.length >>> 0;

            // 3. If len is 0, return false.
            if (len === 0) {
                return false;
            }

            // 4. Let n be ? ToInteger(fromIndex).
            //    (If fromIndex is undefined, this step produces the value 0.)
            var n = fromIndex | 0;

            // 5. If n ≥ 0, then
            //  a. Let k be n.
            // 6. Else n < 0,
            //  a. Let k be len + n.
            //  b. If k < 0, let k be 0.
            var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

            function sameValueZero(x, y) {
                return x === y || (typeof x === 'number' && typeof y === 'number' && isNaN(x) && isNaN(y));
            }

            // 7. Repeat, while k < len
            while (k < len) {
                // a. Let elementK be the result of ? Get(O, ! ToString(k)).
                // b. If SameValueZero(searchElement, elementK) is true, return true.
                if (sameValueZero(o[k], searchElement)) {
                    return true;
                }
                // c. Increase k by 1.
                k++;
            }

            // 8. Return false
            return false;
        }
    });
}