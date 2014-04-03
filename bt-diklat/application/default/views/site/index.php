<style>
    .span6 {
        border: 1px solid #cccccc;
        font-family: "Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
        text-align: center;
        font-size: 16px;
        color: #274b6d;
        margin: 0px 10px 0px 10px;
    }
    .cd {
        height: 400px;
        position: relative;
    }
    .title {
        border-bottom: 1px solid #cccccc;
        margin-bottom: 5px;
        position: absolute; 
        top: 0;
        width: 100%;       
    }
    .title .wrapper { padding: 10px; }
    
    .code {
        text-align: left;
        font-size: 12px;
        padding-left: 10px;
        padding-right: 10px;
    }
    .desc { 
        margin-top: 40px;
        padding-top: 5px;
        padding-bottom: 20px;
        overflow-x: hidden;
        overflow-y: auto;
        height: 335px; 
    }
    .outside { display: table; margin: 0 auto; }
    #pie-chart a{
        font-family: "Lucida Grande", "Lucida Sans Unicode", Verdana, Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #4d759e;
        cursor: pointer;
        border-right: 1px solid #cccccc;
        padding-right: 5px;
        padding-left: 5px;
    }
    #pie-chart a:last-child{
        border: none;
    }
</style>

<div class="outside">
    <div class="span6" id="pie-chart"></div>
    <div class="span6 cd">
        <div class="title">
            <div class="wrapper">
                KODE DIKLAT
            </div>
        </div>
        <div class="desc">
            <?php foreach($diklats as $key => $val): ?>
                <div class="code">
                    <span class="label"><?php echo $val['code'].' :' ?></span>
                    <span class="desc"><?php echo strtoupper($val['name']) ?></span>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>

<br/>
<br/>

<div class="outside">
    <div class="span6" id="chart2"></div>
    <div class="span6" id="chart4"></div>
</div>

<br/>
<br/>

<div class="outside">
    <div class="span12" id="chart3"></div>
</div>

<script type="text/javascript">
    $(function () {
        /******************* Chart 1 *******************/
        var diklatData =   <?php echo $diklat_data ?>;
        var getYear = new Date();
        var now = getYear.getFullYear();
        var colors = Highcharts.getOptions().colors,
            categories = <?php echo $diklat_code ?>,
            data = <?php echo $data_participant ?>;
            

        pieChart(diklatData, now);

        function pieChart(pieData,yearParam){
            // Build the chart
            $('#pie-chart').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    height: 350
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'KEMENTERIAN PERHUBUNGAN<br>DIREKTORAT JENDERAL PERHUBUNGAN LAUT'
                },
                subtitle: {
                    text: 'DATA STATISTIK PENGUSULAN PESERTA DIKLAT TAHUN '+yearParam
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            formatter: function() {
                                return '<b>'+ this.point.name +'</b>: '+ this.y + ' Orang' ;
                            }
                        }
                    }                
                },
                series: [{
                    type: 'pie',
                    name: 'Persentase',
                    data: pieData
                }]
            });
            
            $('#pie-chart').append('<input placeholder="Tahun" style="text-align: center; margin-bottom: 20px;" id="year-pie1" class="span1" type="text" />');

            // Change pie data when year clicked
            $('#year-pie1').on('keypress',function(e){
                if(e.which == 13){
                    var year = $(this).val();
                    $.ajax({
                        url: '<?php echo site_url("site/get_data") ?>'+'/'+year,
                        success: function(result){
                            pieChart(result, year);
                        }
                    });
                }
            });
        }
        
        /******************* Chart 1 *******************/


        /******************* Chart 2 *******************/
        
        function setChart(name, categories, data, color) {
            chart.xAxis[0].setCategories(categories, false);
            chart.series[0].remove(false);
            chart.addSeries({
                name: name,
                data: data,
                color: color || 'white'
            }, false);
            chart.redraw();
        }
        
        var chart = $('#chart2').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'KEMENTERIAN PERHUBUNGAN<br>DIREKTORAT JENDERAL PERHUBUNGAN LAUT'
            },
            subtitle: {
                text: 'DATA STATISTIK TOTAL PESERTA DIKLAT BERDASARKAN KODE DIKLAT'
            },
            xAxis: {
                categories: categories,
                labels: {
                    rotation: -65,
                    align: 'right',
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'QUANTITY PESERTA'
                }
            },
            plotOptions: {
                column: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function() {
                                var drilldown = this.drilldown;
                                if (drilldown) { // drill down
                                    setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
                                } else { // restore
                                    setChart(name, categories, data);
                                }
                            }
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        color: colors[0],
                        formatter: function() {
                            return this.y;
                        }
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    var point = this.point,
                        s = 'Total Peserta '+this.x +':<b>'+ this.y +' Orang</b><br/>';
                    if (point.drilldown) {
                        s += 'Klik untuk melihat data '+ point.category +' per tahun';
                    } else {
                        s += 'Klik untuk melihat data peserta diklat secara keseluruhan';
                    }
                    return s;
                }
            },
            series: [{
                name: name,
                data: data,
                color: 'white'
            }],
            exporting: {
                enabled: true
            },
            legend: {
                enabled: false
            }, 
            credits: {
                enabled: false
            }
        }).highcharts(); // return chart
        /******************* Chart 2 *******************/

        /******************* Chart 3 *******************/
        $('#chart3').highcharts({
            chart: {
                type: 'column'
            },
            title: {
                text: 'KEMENTERIAN PERHUBUNGAN<br>DIREKTORAT JENDERAL PERHUBUNGAN LAUT'
            },
            subtitle: {
                text: 'DATA STATISTIK TOTAL PESERTA DIKLAT BERDASARKAN TAHUN'
            },
            xAxis: {
                categories: categories,
                labels: {
                    rotation: -65,
                    align: 'right',
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'QUANTITY PESERTA'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                align: 'right',
                x: -70,
                verticalAlign: 'top',
                y: 20,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
            plotOptions: {
                series: {
                    dataLabels:{
                        enabled:false,
                        formatter:function(){
                            if(this.y > 0)
                                return this.y;
                        }
                    }
                },
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                    }
                }
            },
            series: <?php echo $data_participant2 ?>,
            credits: {
                enabled: false
            }
        });
        /******************* Chart 3 *******************/

        /******************* Chart 4 *******************/
        $('#chart4').highcharts({
            chart: {
                type: 'column'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'KEMENTERIAN PERHUBUNGAN<br>DIREKTORAT JENDERAL PERHUBUNGAN LAUT'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y} Orang</b>'
            },
            subtitle: {
                text: 'DATA STATISTIK MINAT PESERTA TERHADAP DIKLAT YANG DISELENGGARAKAN PENYELENGGARA'
            },
            xAxis: {
                categories: [
                    'TOTAL PEGAWAI YANG PERNAH MENGIKUTI PELATIHAN YANG DISELENGGARAKAN OLEH '
                ],
                labels: {
                    enabled: false
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'QUANTITY DIKLAT'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        color: colors[0],
                        formatter: function() {
                            return this.y;
                        }
                    }
                }
            },
            series: <?php echo $data_organizer; ?>,
                    dataLabels:{
                        enabled:true,
                        formatter:function(){
                            if(this.y > 0)
                                return this.y;
                        }
                    }
        });
        /******************* Chart 4 *******************/
    });
</script>

















