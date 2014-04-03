<?php $title = l('Pengusulan Peserta Diklat') ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<style>
    input[type="radio"],
    input[type="checkbox"] {
        margin-top: -3px;
        margin-right: 5px;
    }
    .sub-req {
        position: relative;
        background: rgba(228, 71, 71, 0.6);
        padding: 5px;
        border-radius: 5px;
        border: 1px solid rgba(228, 71, 71, 0.6);
        margin-bottom: 2px;
        cursor: pointer;
    }
    .minus {
        position: relative;
        background: rgba(71, 228, 165, 0.6);
        padding: 5px;
        border-radius: 5px;
        border: 1px solid rgba(71, 228, 165, 0.6);
        margin-bottom: 2px;
        cursor: pointer;
    }
    .check {
        position: absolute;
        height: 100%;
        bottom: 0;
        right: 0;
    }
    .modal-body p,
    #checking,
    #checking-age {
        text-align: justify;
        position: relative;
        background: rgba(71, 228, 165, 0.6);
        padding: 5px;
        border-radius: 5px;
        border: 1px solid rgba(71, 228, 165, 0.6);
        margin-bottom: 2px;
        cursor: pointer;
        margin-left: 0px;
    }
    .checking,
    .label-age,
    .image-order {
        display: none;
    }
    .checking-age {
        display: none;
        margin-top: -10px;
    }
    #scan_age {
        margin-left: 0;
    }
</style>

<script type="text/javascript">
    $(function(){
        
        function checking(diklat, nip){

            $.ajax({
                url: '<?php echo site_url("diklat/checking") ?>'+'/'+nip+'/'+diklat,
                success: function(resultCheck){

                    if(resultCheck !== 0){
                        $('.checking').show();
                        $('span#checking').html('Pegawai dengan NIP '+nip+' sudah pernah mengikuti diklat ini');
                    } else {
                        $('.checking').hide();
                    }
                }
            });
        }

        $("#scan_name").autocomplete('<?php echo site_url('employee/order_employee_option?') ?>', {
            minChars:1,
            max:100,
            delay:1,
            selectFirst: false
        });
        
        $("#scan_name").result(function(evt, row, value) {
            $('#scan_id').val(row[1]);
            $('#scan_nip').val(row[2]);
            $('#scan_jabatan').val(row[3]);
            $('#scan_name').val(row[4]);
            $('#scan_golongan').val(row[5]);
            $('#scan_pangkat').val(row[6]);
            $('#scan_group').val(row[7]);
            $('#scan_unit_of_work').val(row[9]);
            $('#scan_majors').val(row[11]);
            $('#scan_year_pass').val(row[12]);
            $('#scan_school').val(row[13]);
            $('#scan_desc_education').val(row[14]);
            $('#scan_age').html(row[15]+' Tahun');
            $('#scan_born_date').val(row[16]);

            if(row[15] >= 50){
                $('.checking-age').show();
                $('.label-age').show();
                $('span#checking-age').html('Umur pegawai sudah mencapai 50 Tahun');
            } else {
                $('.label-age').show();
                $('.checking-age').hide();
            }

            checking($('#option').val(),row[2]);

            if(row[1]){
                $.ajax({
                    url: '<?php echo site_url("employee/get_certificate") ?>'+'/'+row[1]
                }).done(function(result){
                    if(result.length !== 0){
                        $.each(result, function(k,v){
                            $('input[type=text][group=input'+v.certificate+']').css('display','table');
                            $('input[type=text][group=input'+v.certificate+']').val(v.certificate_no);
                            $('input[type=checkbox][group=input'+v.certificate+']').attr('checked', true);
                        });
                    } else {
                        $('input[type=checkbox]').each(function () { $(this).attr('checked', false); });
                        $('input[name="cer_no[]"]').each(function(){
                            $(this).val('');
                            $(this).css('display','none');
                        });
                    }
                });
            }

            if(row[8]){
                var baseUrl = '<?php echo base_url("data/employee/image") ?>';
                var dom =   '<label class="span2"><?php echo l("Foto") ?></label>'+
                            '<div class="thumbs thumbnail">'+
                                '<img src="'+baseUrl+'/'+row[8]+'" />'+
                            '</div>';
                $('.image-order').show();
                $('.image-order').html(dom);
            } else {
                var dom = '<label class="span2"><?php echo l("Foto") ?></label><input type="file" value="<?php echo set_value('image') ?>" name="image" />';
                $('.image-order').show();
                $('.image-order').html(dom);
            }

            if(row[10]){
                $('input[type=radio][value='+row[10]+']').attr('checked', true);
            } else {
                $('input[type=radio]').each(function () { $(this).attr('checked', false); });
            }

            var nip = row[2];
            $.ajax({
                url: '<?php echo site_url("diklat/get_history") ?>' + '/' + nip
            }).done(function(history){
                var allHistory = '';
                $.each(history, function(k,his){
                    if(his.place == undefined) his.place = '-';
                    if(his.description == undefined) his.description = '-';
                    if(his.year == undefined) his.year = '-';

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

        $('#option').on('change', function(){
            var id = $(this).val();

            if(id){
                $.ajax({
                    url: '<?php echo site_url("diklat/get_data") ?>'+'/'+id
                }).done(function(data){
                    if(data.length !=0){
                        var req = '';
                        $.each(data, function(k,v){
                            if(v.require_name == ''){
                                $('#requirement').html('');
                                $('.require').css('display','none');
                            } else {
                                req += '<div class="row-fluid"><div class="span12 sub-req">'+v.require_name+'<input class="check" type="checkbox" name="requirement_id[]" value="'+v.id+'" data="'+v.require_name+'" /></div></div> ';
                                $('.require').css('display','');
                            }
                        });
                        $('#requirement').html(req);
                    } else {
                        $('#requirement').html('');
                        $('.require').css('display','none');
                    }
                });
            } else {
                $('#requirement').html('');
                $('.require').css('display','none');
            }
        });

        $('.certificate .span2').on('click','input[type=checkbox]:checked',function(){
            var getGroup = $(this).attr('group');
            var getInput = $('input[group="'+getGroup+'"][type=text]');
            getInput.css('display','table');
        });
        
        $('.certificate .span2').on('click','input[type=checkbox]:not(:checked)',function(){
            var getGroup = $(this).attr('group');
            var getInput = $('input[group="'+getGroup+'"][type=text]');
            getInput.css('display','none');
            getInput.val('');
        });

        $('a.btn-danger').on('click',function(){
            var minData = '';
            var minRequire = $('.check:not(:checked)');
            var min = '';

            if(minRequire.length){
                $.each(minRequire, function(k,v){
                    var point = $(v).attr('data');
                    var dom = '<p>'+point+'</p>';
                    minData += dom;
                });
                $('.modal-header').css('display','');
            } else {
                $('.modal-header').css('display','none');
                minData += '<p style="text-align: center">SEMUA PERSYARATAN SUDAH TERPENUHI</p>';
            }
            $('.modal-body').html(minData);
            $('#myModal').modal('show');

            $('#min .remove-req').replaceWith(null);
            var unselected = $('#requirement input[type=checkbox]:not(:checked)').clone();
            unselected.each(function(k,v){
                $(v).removeClass();
                $(v).addClass('remove-req');
                v.name = 'minus[]';
                v.type = 'text';
                $('#min').append(v);
            });
        });

        $('#scan_group').on('change', function(){
            var group_id = $(this).val();
            var rank = '<?php echo $rank ?>';
            var rank = $.parseJSON(rank);
            $('#scan_pangkat').val(rank[group_id]);   
        });

        $('#option').on('change', function(){
            checking($('#option').val(),$('#scan_nip').val());
        });

        $('#year').on('change',function() {
            $.getJSON("<?php echo site_url('diklat/get_diklat') ?>/" + $(this).val(), '', function(data) {

                $('#option option').remove();
                $('<option value="">Pilih Diklat</option>').appendTo($('#option'));
                
                $.each(data, function(i, v) {
                    $('<option value="'+v.id+'">'+v.name+'</option>').appendTo($('#option'));
                });
            });
        });
    });
</script>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform" enctype="multipart/form-data">
    <fieldset>
        <legend><?php echo l('Jenis Diklat & Data Peserta') ?></legend>
        <div>
            <label><?php echo l('Tahun Diklat') ?></label>
            <?php echo form_dropdown('year',$year,'','id="year"') ?>
        </div>
        <div>
            <label class="mandatory">Nama Diklat</label>
            <?php echo form_dropdown('diklat_id',$diklats,'','id="option"') ?>
        </div>
        <div class="checking">
            <label></label>
            <span id="checking"></span>
        </div>
        <div>
            <label class="mandatory"><?php echo l('Nama Pegawai') ?></label>
            <input id="scan_name" type="text" value="<?php echo set_value('employee_name') ?>" name="employee_name" placeholder="<?php echo l('Nama Pegawai') ?>" style="width: 100%"/>
            <input id="scan_id" type="hidden" value="<?php echo set_value('employee_id') ?>" name="employee_id" placeholder="<?php echo l('ID Pegawai') ?>" />
        </div>
        <div>
            <label><?php echo l('Tanggal Lahir') ?></label>
            <?php echo xform_date('born_date',array('default_today' => false),'id="scan_born_date"') ?>
        </div>
        <div class="label-age">
            <label><?php echo l('Umur') ?></label>
            <span id="scan_age"></span>
        </div>
        <div class="checking-age">
            <label></label>
            <span id="checking-age"></span>
        </div>
        <div class="image-order"></div>
        <div>
            <label><?php echo l('NIP') ?></label>
            <input id="scan_nip" type="text" value="<?php echo set_value('nip') ?>" name="nip" placeholder="<?php echo l('NIP') ?>" />
        </div>
        <div>
            <label><?php echo l('Jabatan') ?></label>
            <input id="scan_jabatan" type="text" value="<?php echo set_value('position') ?>" name="position" placeholder="<?php echo l('Jabatan') ?>" />
        </div>

        <div>
            <label><?php echo l('Golongan') ?></label>
            <select name="group_id" id="scan_group">
                <option value="" ><?php echo 'Pilih Golongan' ?></option>
                <?php foreach($groups as $key => $value): ?>
                    <option value="<?php echo $key ?>" ><?php echo $value ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div>
            <label><?php echo l('Pangkat') ?></label>
            <input id="scan_pangkat" type="text" value="<?php echo set_value('rank') ?>" name="rank" placeholder="<?php echo l('Pangkat') ?>" />
        </div>

        <div>
            <label><?php echo l('Unit Kerja') ?></label>
            <select name="unit_of_work" id="scan_unit_of_work">
                <option value="" ><?php echo 'Pilih Unit Kerja' ?></option>
                <?php foreach($uow as $k => $v): ?>
                    <option value="<?php echo $k ?>" ><?php echo $v ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <?php foreach($level_education as $k => $v): ?>
            <div>
                <label><?php echo $k ? l('') : l('Pendidikan Formal') ?></label>
                <?php for($a=0;$a<count($v);$a++): ?>
                    <div class="span2">
                        <input type="radio" name="level_of_education" value="<?php echo $v[$a]['level_code'] ?>"> <?php echo $v[$a]['level_name'] ?>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endforeach; ?>

        <div>
            <label><?php echo l('Jurusan') ?></label>
            <input id="scan_majors" type="text" value="<?php echo set_value('majors') ?>" name="majors" placeholder="<?php echo l('Jurusan') ?>" />
        </div>
        <div>
            <label><?php echo l('Tahun Lulus') ?></label>
            <input id="scan_year_pass" type="text" value="<?php echo set_value('year_pass') ?>" name="year_pass" placeholder="<?php echo l('Tahun Lulus') ?>" />
        </div>
        <div>
            <label><?php echo l('Nama Sekolah') ?></label>
            <input id="scan_school" type="text" value="<?php echo set_value('name_of_school') ?>" name="name_of_school" placeholder="<?php echo l('Nama Sekolah') ?>" />
        </div>
        <div>
            <label><?php echo l('Deskripsi Pendidikan') ?></label>
            <input id="scan_desc_education" type="text" value="<?php echo set_value('desc_of_education') ?>" name="desc_of_education" placeholder="<?php echo l('Deskripsi Pendidikan') ?>" />
        </div>

        <?php foreach($certificate_marine as $key => $cm): ?>
            <div class="certificate">
                <label><?php echo $key ? l('') : l('Ijazah Kelautan') ?></label>
                <div class="row-fluid sub-cer">
                    <?php for($i=0;$i<count($cm);$i++): ?>
                        <div>
                            <div class="span2"><input type="checkbox" name="certificate[]" value="<?php echo $cm[$i]['id'] ?>" group="input<?php echo $cm[$i]['id'] ?>"><?php echo $cm[$i]['certificate_name'] ?></div>
                            <div class="span4"><input type="text" name="cer_no[]" value="<?php echo set_value('cer_no') ?>" group="input<?php echo $cm[$i]['id'] ?>" style="display:none" placeholder="No Seri Ijazah"></div>
                        </div>
                    <?php endfor ?>
                </div>
            </div>
        <?php endforeach ?>

        <div class="require" style="display: none;">
            <label>Persyaratan</label>
            <div id="requirement"></div>
        </div>

        <div id="min" style="display: none;"></div>
    </fieldset>

    <h4>Diklat Yang Pernah Diikuti</h4>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Nama Diklat</th>
            <th>Tempat</th>
            <th>Tahun</th>
            <th>Keterangan</th>
        </tr>
        <tbody class="history">
            <td colspan="4">&nbsp;</td>
        </tbody>
    </table>
    <br>
    <div class="action-buttons btn-group">
        <a class="btn btn-danger"><?php echo l('Kirim') ?></a>
        <a href="<?php echo site_url($CI->_get_uri('order_list')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
    <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 id="myModalLabel">PERSYARATAN YANG BELUM TERPENUHI</h4>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
            <input type="submit" value="Kirim">
        </div>
    </div>
</form>










