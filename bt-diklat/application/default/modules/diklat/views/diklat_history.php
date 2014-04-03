<style>
    .action {
        text-align: center;
    }
    
</style>

<script type="text/javascript">
    $(function(){
        id = '<?php echo $id ?>';
        limit = 15;

        function getChartHistory(){
            $.ajax({
                url: '<?php echo site_url("diklat/chart_diklat_history") ?>'+'/'+id
            }).done(function(result){
                chartHistory(result);
            });
        }

        getChartHistory();

        function chartHistory(history){
            $('#pie-chart').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    height: 378
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'KEMENTERIAN PERHUBUNGAN<br>DIREKTORAT JENDERAL PERHUBUNGAN LAUT'
                },
                subtitle: {
                    text: 'DATA STATISTIK HISTORY DIKLAT <?php echo strtoupper($title["name"]) ?>'
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
                    name: 'Persentase Peserta',
                    data: history
                    
                }]
            });
        }

        function loadData(offset){
            $.ajax({
                url: '<?php echo site_url("diklat/get_diklat_employee") ?>'+'/'+id+'/'+offset
            }).done(function(result){
                if(result.length == 0){
                    $('tbody.first .empty').fadeIn();
                    return false;
                }

                var row = '';
                $.each(result, function(k,v){
                    var dom =   '<tr>'+
                                    '<td style="text-align: center;">'+v.nip+'</td>'+
                                    '<td style="text-align: center;">'+v.employee_name+'</td>'+
                                    '<td style="text-align: center;">'+v.group_name+'</td>'+
                                    '<td style="text-align: center;">'+v.unit_name+'</td>'+
                                    '<td style="text-align: center;">'+v.year+'</td>'+
                                    '<td style="text-align: center;">'+v.certificate_no+'</td>'+
                                    '<td style="text-align: center;">'+v.place+'</td>'+
                                    '<td style="text-align: center;">'+
                                        '<a data="'+v.id+'" class="icon-edit"></a>'+
                                        '<a data="'+v.id+'" class="icon-delete"></a>'+
                                    '</td>'+
                                '</tr>';
                    row += dom;
                });
                $('tbody.second').html(row);
            });    
        }

        /** Show input data **/
        $('.btn-input').on('click',function(){
            $('tbody.first .empty').hide();
            $('tbody.first .input').fadeIn();
        });

        /** Autocomplete **/
        $("#nip").autocomplete('<?php echo site_url('diklat/get_data_history?') ?>', {
            minChars:1,
            max:100,
            delay:10,
            selectFirst: false
        });
        
        /** Change value after select NIP on autocomplete **/
        $("#nip").result(function(evt, row, value) {
            $('#employee_name').val(row[1]);
            $('#group').val(row[2]);
            $('#unit_of_work').val(row[3]);
        });

        /** Send new data **/
        $('#send').on('click',function(){
            var nip = $('#nip').val();
            var employee_name = $('#employee_name').val();
            var group = $('#group').val();
            var unit_of_work = $('#unit_of_work').val();
            var year = $('#year').val();
            var certificate_no = $('#certificate_no').val();
            var place = $('#place').val();

            if(!nip) return false;
            $('.empty').hide();
            
            $.ajax({
                url: '<?php echo site_url("diklat/input_history") ?>',
                type: 'POST',
                data: {diklat_id:id, nip:nip, year:year, certificate_no: certificate_no, place: place}
            }).done(function(result){
                $('#nip').val('');
                $('#employee_name').val('');
                $('#group').val('');
                $('#unit_of_work').val('');
                $('#year').val('');
                $('#certificate_no').val('');
                $('#place').val('');
                loadData(limit);
                getChartHistory();
            });
        });

        $('#search').on('keypress',function(e){
            if(e.which == 13){
                var keyword = $(this).val();
                
                if(keyword.length == 0) {
                    loadData(limit);
                    return false;
                }

                $('#ajax-loading').hide().ajaxStart(function() {
                    $('.overlay-ajax').show();
                    $(this).show();
                }).ajaxStop(function() {
                    $('.overlay-ajax').hide();
                    $(this).hide();
                });
                

                $.ajax({
                    url: '<?php echo site_url("diklat/get_search") ?>',
                    type: 'POST',
                    data: {keyword: keyword, diklat_id: id}
                }).done(function(res){
                    if(res.length == 0){
                        $('tbody.second').css('display','none');
                        $('tbody.first .empty').fadeIn();

                        setTimeout(function(){
                            $('tbody.first .empty').fadeOut();
                        }, 2000);
                        setTimeout(function(){
                            $('tbody.second').css('display','');
                            $('#search').val('');
                        }, 3000);
                    } else {
                        var row = '';
                        $.each(res, function(k,v){
                            var dom =   '<tr>'+
                                            '<td style="text-align: center;">'+v.nip+'</td>'+
                                            '<td style="text-align: center;">'+v.employee_name+'</td>'+
                                            '<td style="text-align: center;">'+v.group_name+'</td>'+
                                            '<td style="text-align: center;">'+v.unit_name+'</td>'+
                                            '<td style="text-align: center;">'+v.year+'</td>'+
                                            '<td style="text-align: center;">'+v.certificate_no+'</td>'+
                                            '<td style="text-align: center;">'+v.place+'</td>'+
                                            '<td style="text-align: center;">'+
                                                '<a data="'+v.id+'" class="icon-edit"></a>'+
                                                '<a data="'+v.id+'" class="icon-delete"></a>'+
                                            '</td>'+
                                        '</tr>';
                            row += dom;
                        });
                        $('tbody.second').html(row);
                    }
                });
            }
        });

        $('tbody.second').on('click','.icon-edit',function(){
            var historyId = $(this).attr('data');
            
            var year = $('tr:hover td:nth-child(5)').html();
            var inputYear = '<input class="span2" style="text-align: center;" id="update-year" type="text" value="'+year+'">';
            $('tr:hover td:nth-child(5)').html(inputYear);

            var certificate_no = $('tr:hover td:nth-child(6)').html();
            var inputCertificate = '<input class="span2" style="text-align: center;" id="update-certificate-no" type="text" value="'+certificate_no+'">';
            $('tr:hover td:nth-child(6)').html(inputCertificate);

            var place = $('tr:hover td:nth-child(7)').html();
            var inputPlace = '<input class="span2" style="text-align: center;" id="update-place" type="text" value="'+place+'">';
            $('tr:hover td:nth-child(7)').html(inputPlace);

            var action = $('tr:hover td:nth-child(8)').html();
            var inputAction = '<a class="btn btn-success update-btn">UPDATE</a>';
            $('tr:hover td:nth-child(8)').html(inputAction);


            $('.update-btn').on('click',function(){
                var year = $('#update-year').val();
                var certificate_no = $('#update-certificate-no').val();
                var place = $('#update-place').val();

                $.ajax({
                    url: '<?php echo site_url("diklat/update_history") ?>',
                    type: 'POST',
                    data: {id: historyId, year: year, certificate_no: certificate_no, place: place}
                }).done(function(result){
                    loadData(limit);
                });
            });
        });

        $('tbody.second').on('click','.icon-delete',function(){
            historyId = $(this).attr('data');
            $('#confirm').modal('show');
        });

        $('#confirm').on('click','.delete-data',function(){
            $.ajax({
                url: '<?php echo site_url("diklat/delete_history") ?>',
                type: 'POST',
                data: {id: historyId}
            }).done(function(result){
                $('#confirm').modal('hide');
                loadData(limit);
                getChartHistory();
            });
        });

        loadData(limit);

        $(window).on('scroll',function() {
            var checkSearch = $('#search').val();
            if(checkSearch == 0){
                if($(window).scrollTop() + $(window).height() == $(document).height()) {
                   var count = $('tbody.second tr').length;
                   var limits = count + 10;
                   loadData(limits);
                }
            }
        });
    });
</script>

<div class="overlay-ajax"></div>
<div id="ajax-loading">
    <div class="progress progress-info progress-striped active" style="width: 30%"><div class="bar" style="width: 100%;"></div></div>
</div>    

<div class="row-fluid">
    <div id="pie-chart" style="min-width: 310px; height: 300px; margin: 0 auto;"></div>
    <hr>
</div>


<div class="header" style="margin-top: 50px;">
    <div class="pull-left">
        <a class="btn btn-danger btn-input">Tambah Data</a>
    </div>
    <div class="pull-right">
        <input id="search" type="text" name="keyword" placeholder="Cari">
    </div>
    <div class="clearfix"></div>
</div>
<div class="grid-container table-bordered">
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="text-align: center;">NIP</th>
                <th style="text-align: center;">Nama</th>
                <th style="text-align: center;">Golongan</th>
                <th style="text-align: center;">Unit Kerja</th>
                <th style="text-align: center;">Tahun</th>
                <th style="text-align: center;">No. Sertifikat</th>
                <th style="text-align: center;">Tempat</th>
                <th style="text-align: center;"></th>
            </tr>
        </thead>
        <tbody class="first">
            <tr class="input" style="display: none;">
                <td style="text-align: center;"><input class="span2" style="text-align: center;" id="nip" type="text"></td>
                <td style="text-align: center;"><input disabled class="span3" style="text-align: center;" id="employee_name" type="text"></td>
                <td style="text-align: center;"><input disabled class="span1" style="text-align: center;" id="group" type="text"></td>
                <td style="text-align: center;"><input disabled class="span5" style="text-align: center;" id="unit_of_work" type="text"></td>
                <td style="text-align: center;"><input class="span1" style="text-align: center;" id="year" type="text"></td>
                <td style="text-align: center;"><input class="span2" style="text-align: center;" id="certificate_no" type="text"></td>
                <td style="text-align: center;"><input class="span2" style="text-align: center;" id="place" type="text"></td>
                <td style="text-align: center;"><a id="send" class="btn btn-success">KIRIM</a></td>
            </tr>
            <tr class="empty" style="display: none;">
                <td style="text-align: center;" colspan="8">
                    Tidak ada data
                </td>
            </tr>
        </tbody>
        <tbody class="second">
            
        </tbody>
    </table>
</div>

<div id="confirm" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="myModalLabel">Konfirmasi</h3>
    </div>
    <div class="modal-body">
        <p>Anda yakin ingin menghapus data ini ?</p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
        <button class="btn btn-primary delete-data">Hapus</button>
    </div>
</div>























