(function($) {

    $.fn.chart = function(data, options) {
        var that = this;

        var o = {
            '$o': this,
            options: options,
            data: data,

            render: function() {
                var self = this;
                var i, j, values, row;

                var nvdata = [];
                if( Object.prototype.toString.call(this.data) === '[object Array]' ) {
                    values = [];
                    for(i in this.data) {
                        row = {};
                        for(j in this.data[i]) {
                            row[j] = this.data[i][j];
                        }
                        row.x = i;
                        row.y = this.data[i][this.options.value_field];

                        values.push(row);
                    }
                    nvdata.push({ key: this.options.title, values: values });
                } else {
                    for(i in this.data) {
                        values = [];
                        for(j in this.data[i]) {
                            row = this.data[i][j];
                            row.x = j;
                            row.y = this.data[i][j][this.options.value_field];
                            values.push(row);

                        }
                        nvdata.push({key: i, values: values });
                    }
                }

                this.nvdata = nvdata;

                if (!self.chart) {
                    // nv.addGraph(function() {
                        var chart = self.chart = nv.models.multiBarChart();
                        chart.xAxis.tickFormat(function(d,i,a) {
                            return self.nvdata[0].values[d][self.options.key_field];
                        });
                        chart.yAxis.tickFormat(d3.format(',f'));
                        // chart.stacked(true);

                        d3.select(self.$o[0]).append('svg')
                            .datum(nvdata)
                          .transition().duration(500).call(chart);

                        nv.utils.windowResize(chart.update);

                        // return chart;
                    // });
                } else {
                    
                    d3.select(self.$o[0]).select('svg')
                        .datum(nvdata)
                      .transition().duration(500)
                        .call(self.chart);
                }
            },

            init: function() {
                var self = this;

                this.options.title = this.options.title || 'Generic Chart';
                this.options.key_field = this.options.key_field || 'key';
                this.options.value_field = this.options.value_field || 'value';

                if (typeof data == 'string') {
                    this.options.remote_url = data;
                }
                this.options.remote_mode = this.options.remote_mode || 'http';
                this.options.remote_interval = this.options.remote_interval || 3000;

                if (this.options.remote_url) {
                    var fnInterval = function() {
                        // console.log(new Date(), 'a');
                        $.get(self.options.remote_url, function(data) {
                            if (typeof data == 'string') {
                                data = JSON.parse(data);
                            }
                            delete data['_execution_profile'];
                            self.data = data;
                            self.render();
                            // console.log(new Date(), 'b');
                        });
                    };
                    this._interval = setInterval(fnInterval, this.options.remote_interval);
                    fnInterval();
                } else {
                    this.render();
                }

            }
        };

        o.init();

        return o;
    };
})(jQuery);