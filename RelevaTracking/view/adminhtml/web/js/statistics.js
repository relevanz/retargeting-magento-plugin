define([
    "jquery",
    'mageUtils',
    "moment",
    "relevanz/tracking/chart",
    "relevanz/tracking/tablesorter",
    'mage/validation'
], function($, $utils, $moment, $chart) {
    "use strict";

    $.widget('extensions.relevaStatistics', {

        statisticsChart     :   null,
        statisticsTableSort :   null,
        dateFormat          :   null,

        options : {
            dataUrl             : '',
            keyValidationUrl    : '',
            graphTitle          : '',
            dateFormat          : 'M/d/Y',
            selectors           :   {
                chart               :   '',
                statisticsTable     :   '',
                reportFrom          :   '',
                reportTo            :   '',
                refreshButton       :   '',
                submitKeyForm       :   '',
                submitKeyMessages   :   '',
                submitKeyInput      :   '',
                submitKeyButton     :   ''
            },
            datasets : {
                label : {
                    clicks : '',
                    turnover : '',
                    costs : '',
                    conversions : '',
                    impressions : ''
                },
                backgroundColor : {
                    clicks      : '#00cc85',
                    turnover    : '#cc0049',
                    costs       : '#cc00aa',
                    conversions : '#0085cc',
                    impressions : '#cc3c00'
                },
                borderColor : {
                    clicks      : '#000',
                    turnover    : '#000',
                    costs       : '#000',
                    conversions : '#000',
                    impressions : '#000'
                }
            }
        },

        _formatDate : function(date){
            //response date format is YYYY-MM-DD
            return $moment(date, 'YYYY-MM-DD').format(this.dateFormat);
        },

        _getPeriod : function(){
            var period = {};
            period.report_from  = $(this.options.selectors.reportFrom).val();
            period.report_to    = $(this.options.selectors.reportTo).val();
            return period;
        },

        _createChart : function(values){
            var $this = this;
            var ctx = $(this.options.selectors.chart);
            var conversionsObj = [],
                impressionsObj = [],
                clicksObj = [],
                turnoverObj = [],
                costsObj = [],
                ddObj = [];
            if(values.length){
                for (var i = 0; i < values.length; i++) {
                    conversionsObj.push(values[i].conversions);
                    ddObj.push($this._formatDate(values[i].dd));
                    impressionsObj.push(values[i].impressions);
                    clicksObj.push(values[i].clicks);
                    turnoverObj.push(values[i].turnover);
                    costsObj.push(values[i].costs);
                }
            }
            if(this.statisticsChart !== null){
                this.statisticsChart.destroy();
                this.statisticsChart = null;
            }
            this.statisticsChart = new $chart(ctx, {
                type: 'line',
                data: {
                    labels: ddObj,
                    datasets: [
                        {
                            label: $this.options.datasets.label.clicks,
                            data: clicksObj,
                            backgroundColor: $this.options.datasets.backgroundColor.clicks,
                            borderColor: $this.options.datasets.borderColor.clicks,
                            borderWidth: 1
                        },
                        {
                            label: $this.options.datasets.label.turnover,
                            data: turnoverObj,
                            backgroundColor: $this.options.datasets.backgroundColor.turnover,
                            borderColor: $this.options.datasets.borderColor.turnover,
                            borderWidth: 1
                        },
                        {
                            label: $this.options.datasets.label.costs,
                            data: costsObj,
                            backgroundColor: $this.options.datasets.backgroundColor.costs,
                            borderColor: $this.options.datasets.borderColor.costs,
                            borderWidth: 1
                        },
                        {
                            label: $this.options.datasets.label.conversions,
                            data: conversionsObj,
                            backgroundColor: $this.options.datasets.backgroundColor.conversions,
                            borderColor: $this.options.datasets.borderColor.conversions,
                            borderWidth: 1
                        },
                        {
                            label: $this.options.datasets.label.impressions,
                            data: impressionsObj,
                            backgroundColor: $this.options.datasets.backgroundColor.impressions,
                            borderColor: $this.options.datasets.borderColor.impressions,
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                userCallback: function(label, index) {
                                    if (Math.floor(label) === label) {
                                        return label;
                                    }
                                }
                            }
                        }]
                    },
                    elements: {
                        line: {
                            tension: 0
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: $this.options.graphTitle
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    }
                }
            });
        },

        _createTable : function(values){
            var $table = $(this.options.selectors.statisticsTable);
            var $tableContent = $table.find('tbody');
            $tableContent.empty(); //clear table tbody
            if(values.length){
                for (var i = 0; i < values.length; i++) {
                    var $tr = $('<tr></tr>');
                    var data = values[i];
                    for (var k in data){
                        if (typeof data[k] !== 'function') {
                            var val = data[k];
                            if(k == 'dd'){
                                val = this._formatDate(val);
                            }
                            var $td = $('<td></td>').text(val);
                            $tr.append($td);
                        }
                    }
                    $table.append($tr);
                }
            }
            if(this.statisticsTableSort == null) {
                this.statisticsTableSort = $table.tablesorter();
            }
            else{
                $tableContent.trigger("update");
                var sorting = [[0,0]];
                $tableContent.trigger("sorton",[sorting]);
            }
        },

        _validateApiKey : function(apiKey){
            var $this = this;
            var $messages = $(this.options.selectors.submitKeyMessages);
            $messages.html('').hide();
            $.ajax({
                url: this.options.keyValidationUrl,
                type: 'POST',
                crossDomain: true,
                dataType: 'json',
                cache : false,
                showLoader: true,
                data : {api_key : apiKey},
                success: function (data) {
                    if(data.status == 'success'){
                        $($this.options.selectors.submitKeyForm).submit();
                    }
                    else{
                        $messages.show().html($('<div class="message message-error"></div>').html(data.message));
                    }
                },
                error: function (error) {
                    $messages.show().html($('<div class="message message-error"></div>').html(error.message));
                }
            });
        },

        _requestStatsData : function(){
            var $this   = this;
            var period  = this._getPeriod();
            $.ajax({
                url: this.options.dataUrl,
                type: 'POST',
                crossDomain: true,
                dataType: 'json',
                cache : false,
                showLoader: true,
                data : {report_from : period.report_from, report_to : period.report_to},
                success: function (data) {
                    if(data.status == 'success'){
                        $this._createChart(data.values);
                        $this._createTable(data.values);
                    }
                    else{
                        alert(data.message);
                    }
                },
                error: function (error) {
                    alert(error.message);
                }
            });
        },

        _bind: function () {
            var $this   = this;
            $(this.options.selectors.refreshButton).on('click', function(e){
                e.preventDefault();
                $this._proceed();
            });
            $(this.options.selectors.submitKeyButton).on('click', function(e){
                e.preventDefault();
                var enteredApiKey = $($this.options.selectors.submitKeyInput).val();
                if($($this.options.selectors.submitKeyForm).validation()
                    && $($this.options.selectors.submitKeyForm).validation('isValid')){
                    $this._validateApiKey(enteredApiKey);
                    return false;
                }
                return false;
            });
        },

        _proceed: function () {
            if($(this.options.selectors.chart).length
                && $(this.options.selectors.statisticsTable)) {
                this._requestStatsData();
            }
        },

        _create: function() {
            this.dateFormat = $utils.normalizeDate(this.options.dateFormat);
            this._bind();
            this._proceed();
        }
    });

    return $.extensions.relevaStatistics;
});