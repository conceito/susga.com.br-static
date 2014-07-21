$(document).ready(function () {
// limita caracteres ---------------------------------------------------------------
    $("textarea[name=resumo]").charLimit({
        limit: 300, // number
        speed: "normal", // nummber or string
        descending: true // boolean
    });
    $("[data-characters-limit]").charLimit();
});

(function ($, window, undefined) {

    // Create the defaults once
    var pluginName = 'charLimit',
        document = window.document,
        defaults = {
            limit: 30,
            speed: "normal",
            descending: true
        };

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = element;
        this.$element = $(element);

        this.limit = 0;

        // jQuery has an extend method which merges the contents of two or
        // more objects, storing the result in the first object. The first object
        // is generally empty as we don't want to alter the default options for
        // future instances of the plugin
        this.o = $.extend({}, defaults, options);

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype.init = function () {

        var obj = this.$element;
        var self = this;

        this.limit = (obj.data('characters-limit')) ? parseInt(obj.data('characters-limit'), 10) : this.o.limit;

        obj.wrap('<div class="count-box-wrapper blur"></div>');


        if (!obj.next().hasClass("countBox"))
            obj.after("<div class='count-box-bag'><span class='count-label'>limite: </span><span class='countBox'></span></div>");

        this.countChars();

        obj
            .keydown(function (e) {
                if (obj.val().length >= self.limit && e.keyCode != "8" && e.keyCode != "9" && e.keyCode != "46"){
                    e.preventDefault(); // cancel event
                }
                self.countChars();
            })
            .keyup(function (e) {
                if (obj.val().length >= self.limit) {
                    obj.val(obj.val().substr(0, self.limit))
                }
                self.countChars();
            })
            .focus(function () {
                obj.closest('.count-box-wrapper').removeClass('blur').addClass('focus');
                self.countChars();
            })
            .blur(function () {
                obj.closest('.count-box-wrapper').removeClass('focus').addClass('blur');
            });
    };

    Plugin.prototype.countChars = function () {
        var obj = this.$element;
        var value = (this.o.descending) ? this.limit - obj.val().length : obj.val().length;
        obj.parent().find(".countBox").text(value);
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function (options) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    }

}(jQuery, window));