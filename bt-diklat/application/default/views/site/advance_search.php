<style>
	input[type="checkbox"] {
        margin-top: -3px;
        margin-right: 5px;
    }

    @page {
		size: landscape;
		margin: 1.5cm;
	}

    @media print {
	    #form-search,
	    #footer,
	    .breadcrumb,
	    .navbar{
	        display: none;
	    }
	    
		body {
			font: 10pt Georgia, "Times New Roman", Times, serif;
			line-height: 1.3;
		}

		tbody  { 
			display: block; 
			page-break-before: auto;
		}
	}

	
	
</style>

<?php $title = l('Pencarian Terstruktur') ?>
<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
));
?>

<form id="form-search" action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
    	<legend><?php echo $title ?></legend>
		<div class="row-fluid">
			<div class="span6">
				<div class="row-fluid">
					<div>
						<label class="span4">Jenis Diklat</label>
						<?php echo form_dropdown('diklat_id',$diklats,'','class="span8" id="diklat_id"') ?>
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Jenis Peserta</label>
						<select name="participant_type" class="span8" id="participant_type">
			                <option value="" ><?php echo 'Semua Pegawai' ?></option>
			                <option value="0" ><?php echo 'Usulan Peserta Diklat' ?></option>
                            <option value="1" ><?php echo 'Calon Peserta Diklat' ?></option>
			                <option value="2" ><?php echo 'Peserta Diklat' ?></option>
			            </select>
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Tahun Diklat</label>
						<input class="span8" type="text" name="year" placeholder="Tahun Diklat" id="year">
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Nama Pegawai</label>
						<input class="span8" type="text" name="employee_name" placeholder="Nama Pegawai" id="employee_name">
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">NIP</label>
						<input class="span8" type="text" name="nip" placeholder="NIP" id="nip">
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Golongan</label>
						<?php echo form_dropdown('group_id',$groups,'','class="span8" id="group_id"') ?>
					</div>
					<div class="clearfix"></div>
                    <div>
                        <label class="span4">Jabatan</label>
                        <input class="span8" type="text" name="position" placeholder="Jabatan" id="position">
                    </div>
                </div>
            </div>
            <div class="span6">
                <div class="row-fluid">
					<div>
			            <label class="span4"><?php echo l('Unit Kerja') ?></label>
			            <input class="span8" id="unit_name" type="text" name="unit_of_work" placeholder="<?php echo l('Unit Kerja') ?>" />
			        </div>
			        <input id="unit_code" type="hidden" value="<?php echo set_value('unit_code') ?>" name="unit_code" />
			        <div class="clearfix"></div>
					<div>
						<label class="span4">Status Pensiun</label>
						<select name="pension_status"  class="span8" id="pension_status">
			                <option value="" ><?php echo 'Pilih Status Pensiun' ?></option>
			                <option value="1" ><?php echo 'Belum Pensiun' ?></option>
			                <option value="2" ><?php echo 'Sudah Pensiun' ?></option>
			            </select>
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Pendidikan Formal</label>
						<?php echo form_dropdown('level_of_education',$formal_education,'','class="span8" id="formal_education"') ?>
					</div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Ijazah Kelautan</label>
						<?php echo form_dropdown('certificate',$certificate,'','class="span8" id="certificate"') ?>
					</div>
                    <div class="clearfix"></div>
                    <div>
                        <label class="span4">Tahun Kelahiran</label>
                        <input class="span3" type="text" name="from_year" placeholder="Dari" id="from_year">
                        s/d
                        <input class="span3" type="text" name="until_year" placeholder="Sampai" id="until_year">
                    </div>
					<div class="clearfix"></div>
					<div>
						<label class="span4">Operator</label>
						<select name="operator"  class="span8" id="operator">
			                <option value="AND" ><?php echo 'AND' ?></option>
			                <option value="OR" ><?php echo 'OR' ?></option>
			            </select>
					</div>
				</div>
			</div>
		</div>
    </fieldset>
    <fieldset>
    	<legend>Format Hasil Pencarian</legend>
			<div class="row-fluid">
	    		<div class="span2"><input type="checkbox" name="e.employee_name" />Nama Pegawai</div>
	    		<div class="span2"><input type="checkbox" name="e.nip" />NIP</div>
	    		<div class="span2"><input type="checkbox" name="g.group_name" />Golongan</div>
	    		<div class="span2"><input type="checkbox" name="g.rank" />Pangkat</div>
	    		<div class="span2"><input type="checkbox" name="e.position" />Jabatan</div>
	    		<div class="span2"><input type="checkbox" name="e.born_date" />Tanggal Lahir</div>
			</div>
			<div class="row-fluid">
	    		<div class="span2"><input type="checkbox" name="w.unit_name" />Unit Kerja</div>
				<div class="span2"><input type="checkbox" name="e.pension_status" />Status Pensiun</div>
	    		<div class="span2"><input type="checkbox" name="d.name" />Jenis Diklat</div>
	    		<div class="span2"><input type="checkbox" name="year" />Tahun</div>
	    		<div class="span2"><input type="checkbox" name="mle.level_name" />Pendidikan Formal</div>
			</div>
    </fieldset>
    <div class="action-buttons btn-group">
        <a id="search" class="btn btn-primary"><?php echo l('Cari') ?></a>
        <a id="export" href="<?php echo site_url('site/export_result') ?>" class="btn cancel"><?php echo l('Eksport Ke Excel') ?></a>
        <a id="reset" class="btn btn-danger"><?php echo l('Hapus') ?></a>
    </div>
</form>
<br>
<div class="result">
	<div class="grid-container table-bordered">
		<table class="table table-bordered">
			<tr class="thead"></tr>
			<tbody class="search-result"></tbody>
		</table>
	</div>
</div>


<script type="text/javascript">
    $(function(){

    	function returnResult(resultAjax){
            $('.search-result').append(resultAjax);
    	}

    	function getSearch(offset){
            var params = {};
            params['diklat_id'] = $('#diklat_id :selected').val();
            params['year'] = $('#year').val();
            params['e-employee_name'] = $('#employee_name').val();
            params['e-nip'] = $('#nip').val();
            params['e-group_id'] = $('#group_id :selected').val();
            params['e-position'] = $('#position').val();
            params['w-unit_code'] = $('#unit_code').val();
            params['e-pension_status'] = $('#pension_status :selected').val();
            params['f-level_of_education'] = $('#formal_education :selected').val();
            params['c-certificate'] = $('#certificate :selected').val();
            params['operator'] = $('#operator').val();
            params['participant_type'] = $('#participant_type :selected').val();
            params['e-born_date'] = Array($('#from_year').val(),$('#until_year').val());
            params['operator'] = $('#operator').val();

            var select = $('input[type="checkbox"]:checked');
            var selected = new Array();
            $.each(select, function(k,v){
                selected.push(v['name']);
            });
            params['select'] = selected;

            $.ajax({
                url: '<?php echo site_url("site/get_advance_search") ?>'+'/'+offset,
                type: 'POST',
                data: params
            }).done(function(data){

                td = '';
                $.each(data, function(k, v){
                    keys = Object.keys(v);
                    convert = json2array(v);

                    if(convert[0].length == 0){
                        td = '<tr><td style="text-align: center;" colspan="8">Data Tidak Tersedia</td></tr>'
                    } else {
                        for (var i = 0; i < convert.length; i++) {
                            if(i == 0){
                                td += '<tr><td>'+convert[i]+'</td>';
                            } else if(i == (convert.length - 1)){
                                td += '<td>'+convert[i]+'</td></tr>';
                            } else {
                                td += '<td>'+convert[i]+'</td>';
                            }
                        };
                    }
                });

                var th = '';
                for (var i = 0; i < keys.length; i++) {
                    keys[i] = keys[i].replace('nip','NIP');
                    keys[i] = keys[i].replace('employee_name','Nama Karyawan');
                    keys[i] = keys[i].replace('group_name','Golongan');
                    keys[i] = keys[i].replace('rank','Pangkat');
                    keys[i] = keys[i].replace('position','Jabatan');
                    keys[i] = keys[i].replace('born_date','Tanggal Lahir');
                    keys[i] = keys[i].replace('unit_name','Unit Kerja');
                    keys[i] = keys[i].replace('pension_status','Status Pensiun');
                    keys[i] = keys[i].replace('level_name','Pendidikan Terakhir');
                    keys[i] = keys[i].replace('name','Jenis Diklat');
                    keys[i] = keys[i].replace('year','Tahun');
                    keys[i] = keys[i].replace('born_date','Tahun Kelahiran');
                    th += '<th>'+keys[i]+'</th>';
                };

                $('tr.thead').html(th);
                returnResult(td);
            });
    	}

        $("#unit_name").autocomplete('<?php echo site_url('employee/unit_work_options?') ?>', {
            minChars:1,
            max:100,
            delay:10,
            selectFirst: false
        });
        
        $("#unit_name").result(function(evt, row, value) {
             $('#unit_name').val(row[0]);
             $('#unit_code').val(row[1]);
        });

        $('#reset').on('click', function(){
        	$('#form-search')[0].reset();
        });

        function json2array(json){
    		var result = [];
		    var keys = Object.keys(json);
		    keys.forEach(function(key){
		        result.push(json[key]);
		    });
		    return result;
		}

        $('#search').on('click', function(){
        	var count = $('tbody.search-result tr').length;

        	if(count !== 0){
           		$('tbody.search-result tr').replaceWith(null);
        	}
        	getSearch(0);
        });


        var count = 0;
        $(window).on('scroll',function(){
            var checkResultArea = $('tbody.search-result tr').length;
            if($(window).scrollTop() + $(window).height() == $(document).height()) {

                if(checkResultArea > 1){
                    getSearch(count);
                    count += 20;
                }
            }
        });
    });
</script>

























