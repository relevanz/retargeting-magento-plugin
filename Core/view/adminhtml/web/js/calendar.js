(function (factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define([
            'jquery',
            'jquery/ui',
            'jquery/jquery-ui-timepicker-addon',
            'mage/calendar'
        ], factory);
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    'use strict';

    $.widget('extensions.dateRangePicker', $.mage.calendar, {

        /**
         * creates two instances of datetimepicker for date range selection
         * @protected
         */
        _initPicker: function () {
            var from,
                to;

            if (this.options.from && this.options.to) {

                from = this.element.find('#' + this.options.from.id);
                to = this.element.find('#' + this.options.to.id);

                this.options.onSelect = $.proxy(function (selectedDate, inst) {
                    var selectedDateValue = $.datepicker.parseDate($.datepicker._get(inst, "dateFormat"),
                        selectedDate, $.datepicker._getFormatConfig(inst));
                    var newToDate = new Date(selectedDateValue);
                    newToDate.setDate(newToDate.getDate() + 1);
                    to[this._picker()]('option', 'minDate', newToDate);
                }, this);

                this.options.minDate = ((this.options.from.minDate !== undefined) ? this.options.from.minDate : null);
                this.options.maxDate = ((this.options.from.maxDate !== undefined) ? this.options.from.maxDate : null);

                $.mage.calendar.prototype._initPicker.call(this, from);

                from.on('change', $.proxy(function () {
                    var fromDate = from[this._picker()]('getDate');
                    var newToDate = new Date(fromDate);
                    newToDate.setDate(newToDate.getDate() + 1);
                    to[this._picker()]('option', 'minDate', newToDate);
                }, this));

                this.options.onSelect = $.proxy(function (selectedDate, inst) {
                    var selectedDateValue = $.datepicker.parseDate($.datepicker._get(inst, "dateFormat"),
                        selectedDate, $.datepicker._getFormatConfig(inst));
                    var newFromDate = new Date(selectedDateValue);
                    newFromDate.setDate(newFromDate.getDate() - 1);
                    from[this._picker()]('option', 'maxDate', newFromDate);
                }, this);

                this.options.minDate = ((this.options.to.minDate !== undefined) ? this.options.to.minDate : null);
                this.options.maxDate = ((this.options.to.maxDate !== undefined) ? this.options.to.maxDate : null);

                $.mage.calendar.prototype._initPicker.call(this, to);

                to.on('change', $.proxy(function () {
                    var toDate = to[this._picker()]('getDate');
                    var newFromDate = new Date(toDate);
                    newFromDate.setDate(newFromDate.getDate() - 1);
                    from[this._picker()]('option', 'maxDate', newFromDate);
                }, this));
            }
        },

        /**
         * destroy two instances of datetimepicker
         */
        _destroy: function () {
            if (this.options.from) {
                this.element.find('#' + this.options.from.id)[this._picker()]('destroy');
            }

            if (this.options.to) {
                this.element.find('#' + this.options.to.id)[this._picker()]('destroy');
            }
            this._super();
        }
    });

    return {dateRangePicker: $.extensions.dateRangePicker}

}));
