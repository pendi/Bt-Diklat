<?php $title = l('Pencarian Global') ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<style type="text/css">
    #where .opt, #select-col .col {display: none}
    #where .display-it, #select-col .display-it {display: block;}
</style>
<div class="clearfix"></div>


<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label><?php echo l('Pilih Table') ?></label>
            <?php echo form_dropdown('tables',$tables,null,'id="tables"') ?>
        </div>
    </fieldset>
    <fieldset id="where">
        <legend>Pencairan Berdasarkan</legend>
        <!-- this for field of table -->
        <div id="diklat" class="row-fluid opt">
            <div class="span3">
                <div>
                    <label><?php echo l('Nama Diklat') ?></label>
                    <input type="text" value="" name="diklat_name" placeholder="<?php echo l('Nama Diklat') ?>" />
                </div>
                <div>
                    <label><?php echo l('Kode Diklat') ?></label>
                    <input type="text" value="" name="diklat_code" placeholder="<?php echo l('Kode Diklat') ?>" />
                </div>
                <div>
                    <label><?php echo l('Pilih Lembaga Diklat') ?></label>
                    <?php echo form_dropdown('organizer_id',$organizer) ?>
                </div>
            </div>
        </div>
        <div id="employee" class="row-fluid opt">
            <div class="span3">
                <div>
                    <label><?php echo l('Nama Pegawai') ?></label>
                    <input type="text" value="" name="employee_name" placeholder="<?php echo l('Nama Pegawai') ?>" />
                </div>
                <div>
                    <label><?php echo l('NIP') ?></label>
                    <input type="text" value="" name="employee_nip" placeholder="<?php echo l('NIP') ?>" />
                </div>
            </div>
                    
            <div class="span3">
                <div>
                    <label><?php echo l('JABATAN') ?></label>
                    <input type="text" value="" name="employee_jabatan" placeholder="<?php echo l('Jabatan') ?>" />
                </div>
                <div>
                    <label><?php echo l('Pilih Golongan') ?></label>
                    <?php echo form_dropdown('group',$group) ?>
                </div>
            </div>
        </div>
        <div id="diklat_order" class="row-fluid opt">
            <div class="span3">
                <div>
                    <label><?php echo l('Nama Diklat') ?></label>
                    <input type="text" value="" name="diklat_name" placeholder="<?php echo l('Nama Diklat') ?>" />
                </div>
                <div>
                    <label><?php echo l('Kode Diklat') ?></label>
                    <input type="text" value="" name="diklat_code" placeholder="<?php echo l('Kode Diklat') ?>" />
                </div>
                <div>
                    <label><?php echo l('Pilih Lembaga Diklat') ?></label>
                    <?php echo form_dropdown('organizer_id',$organizer) ?>
                </div>
            </div>
            <div class="span3">
                <div>
                    <label><?php echo l('Tahun Diklat') ?></label>
                    <input type="text" value="" name="diklat_year" placeholder="<?php echo l('Tahun Diklat') ?>" />
                </div>
                <div>
                    <label><?php echo l('Nama Pegawai') ?></label>
                    <input type="text" value="" name="employee_name" placeholder="<?php echo l('Nama Pegawai') ?>" />
                </div>
                <div>
                    <label><?php echo l('NIP') ?></label>
                    <input type="text" value="" name="employee_nip" placeholder="<?php echo l('NIP') ?>" />
                </div>
            </div>
                    
            <div class="span3">
                <div>
                    <label><?php echo l('JABATAN') ?></label>
                    <input type="text" value="" name="employee_nip" placeholder="<?php echo l('Jabatan') ?>" />
                </div>
                <div>
                    <label><?php echo l('Pilih Golongan') ?></label>
                    <?php echo form_dropdown('group',$group) ?>
                </div>
            </div>
        </div>
    </fieldset>
    <fieldset id="select-col">
        <legend><?php echo l("Pilih Kolom"); ?></legend>
        <div id="diklat" class="row-fluid col">
            <div class="span12">
                <select id='canselect_code' name='canselect_code' multiple class='fl'>
                    <option value='name'>Nama Diklat</option>
                    <option value='code'>Kode Diklat </option>
                    <option value='organizer_name'>Lembaga Diklat </option>
                </select>
                <input type='button' id='btnRight_code' value='  >  ' />
                <input type='button' id='btnLeft_code' value='  <  ' />
                <select id='isselect_code' name='isselect_code' multiple class='fr'>
                </select>
            </div>
        </div>
        <div id="employee" class="row-fluid col">
            <div class="span12">
                <select id='canselect_code' name='canselect_code' multiple class='fl'>
                    <option value='employee_name'>Nama Pegawai</option>
                    <option value='nip'>NIP</option>
                    <option value='group_name'>Golongan </option>
                    <option value='position'>Jabatan </option>
                </select>
                <input type='button' id='btnRight_code' value='  >  ' />
                <input type='button' id='btnLeft_code' value='  <  ' />
                <select id='isselect_code' name='isselect_code' multiple class='fr'>
                </select>
            </div>
        </div>
        <div id="diklat_order" class="row-fluid col">
            <div class="span12">
                <select id='canselect_code' name='canselect_code' multiple class='fl'>
                    <option value='name'>Nama Diklat</option>
                    <option value='code'>Kode Diklat </option>
                    <option value='organizer_name'>Lembaga Diklat </option>
                    <option value='year'>Tahun Diklat </option>
                    <option value='employee_name'>Nama Pegawai</option>
                    <option value='nip'>NIP</option>
                    <option value='group_name'>Golongan </option>
                    <option value='position'>Jabatan </option>
                </select>
                <input type='button' id='btnRight_code' value='  >  ' />
                <input type='button' id='btnLeft_code' value='  <  ' />
                <select id='isselect_code' name='isselect_code' multiple class='fr'>
                </select>
            </div>
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
</form>

<script type="text/javascript">
      $(function(){
          $('#where div.opt').removeClass('display-it');
          $('#select-col div.col').removeClass('display-it');
          $('[id^=\"btnRight\"]').click(function (e) {
              $(this).prev('select').find('option:selected').remove().appendTo('#isselect_code');
          });

          $('[id^=\"btnLeft\"]').click(function (e) {
              $(this).next('select').find('option:selected').remove().appendTo('#canselect_code');
          });

            function displayVals() {
              var optValues = $( "#tables" ).val();
              // if(!optValues){
                  $('#where div.opt').removeClass('display-it');
                  $('#select-col div.col').removeClass('display-it');
                  $('#where #'+optValues+'.opt').addClass('display-it');
                  $('#select-col #'+optValues+'.col').addClass('display-it');
              // }
            }
             
              $( "select#tables" ).change( displayVals );
              // displayVals();
      });
</script>


