<style>
    .select-participant,
    .select-participant:hover,
    .full-pop-desc,
    .full-pop-desc:hover {
        border: 1px solid rgb(9, 149, 184);
        padding: 5px;
        margin: 5px;
        border-radius: 2px;
        background: rgb(9, 149, 184);
        color: #fff;
    }

    .delete-participant,
    .delete-participant:hover,
    .min-pop-desc,
    .min-pop-desc:hover {
        border: 1px solid rgb(223, 99, 99);
        padding: 5px;
        margin: 5px;
        border-radius: 2px;
        background: rgb(223, 99, 99);
        color: #fff;
    }
    th,
    td {
        text-align: center;
    }
</style>

<div class="header">
    <div class="pull-left">
        <?php echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="clearfix"></div>
</div>

<script type="text/javascript">
    $(function(){
        $("#scan_name").autocomplete('<?php echo site_url('employee/employee_option?') ?>', {
            minChars:1,
            max:100,
            delay:10,
            selectFirst: false
        });
        $("#scan_name").result(function(evt, row, value) {
             $('#scan_name').val(row[4]);
             $('#scan_id').val(row[1]);
             $('#scan_nip').val(row[2]);
             $('#scan_unit_kerja').val(row[3]);
             $('#scan_gol').val(row[4]);
             $('#scan_jabatan').val(row[5]);
        });

        $(".pop-employee").on("click", function(evt){
            evt.preventDefault();
            var nip = $(this).attr('data-employee');

            $.ajax({
                url: '<?php echo site_url("diklat/get_history") ?>' + '/' + nip
            }).done(function(history){
                var allHistory = '';
                $.each(history, function(k,his){
                    var domHis = '  <tr>'+
                                        '<td>'+his.course_name+'</td>'+
                                        '<td>'+his.place+'</td>'+
                                        '<td>'+his.year+'</td>'+
                                        '<td>'+his.description+'</td>'+
                                    '</tr>';
                    allHistory += domHis;
                });
                $('tbody.history').html(allHistory);
            });
        });

        $(".min-pop-desc").on("click", function(evt){
            evt.preventDefault();
            var diklat = $(this).attr('diklat');
            var diklatorder = $(this).attr('diklatorder');
            var employee = $(this).attr('employee');

            $.ajax({
                url: '<?php echo site_url("diklat/get_minus") ?>' + '/' + diklat + '/' + diklatorder + '/' + employee
            }).done(function(resultMinus){
                var allMinus = '';
                $.each(resultMinus, function(k,min){
                    var dom =   '<tr>'+
                                    '<td>'+min.require_name+'</td>'+
                                '</tr>';
                    allMinus += dom;
                });
                $('tbody.desc-min').html(allMinus);
            });
        });

        function selectParticipant(id){
            $.ajax({
                url: '<?php echo site_url("diklat/select_participant") ?>',
                type: 'POST',
                data: {data_id: id}
            }).done(function(report){
                if(report !== 0){
                    $('td:hover span:first-child').remove();
                    $('td:hover').append('<span class="select-participant">Peserta Terpilih</span>');

                    var calculate = parseInt($('#count').html()) + 1;
                    $('#count').html(calculate);
                } else {
                    var dom = '<p>Kuota sudah mencapai 40 Orang</p>';
                    $('.new-alert').html(dom);
                    $('.new-alert').show();
                    
                    setTimeout(function(){
                        $('.new-alert').hide();
                    },2000);

                }
            });
        }

        function deleteParticipant(id){
            $.ajax({
                url: '<?php echo site_url("diklat/delete_participant") ?>',
                type: 'POST',
                data: {data_id: id},
                success: function(report){
                    $('tr.selected').each(function(k,v){
                        $(v).remove();
                    });
                }
            });
        }

        $('.select-participant').on('click',function(){
            var dataID = [$(this).attr('dataid')];
            selectParticipant(dataID);
        });

        $('.delete-participant').on('click',function(){
            var dataID = [$(this).attr('dataid')];
            deleteParticipant(dataID);
            $('tr:hover').remove();
        });

        $('.delete-all').on('click',function(){
            var Data = new Array();
            $('tr.selected').each(function(k,v){
                Data.push($(v).attr('data-ref'));
            });
            deleteParticipant(Data);
        });

        <?php 
        if(!empty($filter)){
            $id = $filter['diklat_id']; 
            $year = $filter['year']; 
        ?>

        $('#diklat option[value=<?php echo $id ?>]').attr('selected','selected');
        $('#year option[value=<?php echo $year ?>]').attr('selected','selected');
        
        <?php } ?>
    });
</script>
<style type="text/css">
    #myModal {width: auto;}
</style>


<form action="<?php echo site_url('diklat/order_list') ?>" method="post" class="ajaxform">
    <fieldset>
        <div class="clearfix"></div>
        <div>
            <label class="mandatory"><?php echo l('Nama Pegawai') ?></label>
            <input id="scan_name" type="text" value="<?php echo set_value('employee_name') ?>" name="employee_name" placeholder="<?php echo l('Nama Pegawai') ?>" style="width: 100%"/>
            <input id="scan_id" type="hidden" value="<?php echo set_value('employee_id') ?>" name="employee_id" placeholder="<?php echo l('Employee ID') ?>" />
        </div>
        <div>
            <label class="mandatory">Nama Diklat</label>
            <?php echo form_dropdown('diklat_id',$diklats,'','id=diklat') ?>
        </div>
        <div>
            <label>Tahun</label>
            <?php echo form_dropdown('year',$years,'','id=year') ?>
        </div>
        <div class="action-buttons btn-group">
            <input type="submit" value="Cari" />
            <a href="<?php echo site_url($CI->_get_uri('order_list')) ?>" class="btn cancel"><?php echo l('Hapus') ?></a>
        </div>
    </fieldset>
</form>
<div class="grid-top">
    <div class="pull-left btn-group">
        <a class="btn btn-danger delete-all">Hapus Data</a>
    </div>
    <div class="alert alert-danger new-alert"></div>
    <style>
        .new-alert{
            right: 0;
            left: 0;
            margin: 0 auto;
            display: none;
        }
    </style>
    <div class="pull-right">
        <span style="font-weight: bold">Jenis Diklat : </span><span><?php echo strtoupper($diklat_name) ?></span>    
    </div>
    <div class="clearfix"></div>
    <?php if(isset($count_participant)): ?>
        <div class="pull-right">
            <span style="font-weight: bold">Total Calon Peserta : </span><span id="count"><?php echo $count_participant ?></span>
        </div>
    <?php endif ?>
</div>
<?php echo $this->listing_grid->show($data) ?>

<?php if (!$this->input->is_ajax_request()): ?>
    <div class="row-fluid grid-bottom">
        <div class="span6 left">
            <?php echo $this->pagination->per_page_changer() ?>
        </div>
        <div class="span6 right">
            <?php echo $this->pagination->create_links() ?>
        </div>
    </div>
<?php endif ?>

<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 id="myModalLabel">DAFTAR DIKLAT YANG TELAH DIIKUTI</h4>
    </div>
    <div class="modal-body">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Nama Diklat</th>
                <th>Tahun</th>
                <th>Tempat</th>
                <th>Keterangan</th>
            </tr>
            <tbody class="history">
            
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
    </div>
</div>

<div id="minusdesc" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="desLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h4 id="desLabel">PERSYARATAN YANG KURANG</h4>
    </div>
    <div class="modal-body">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Jenis Persyaratan</th>
            </tr>
            <tbody class="desc-min">
            
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
    </div>
</div>















