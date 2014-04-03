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
             $('#scan_name').val(row[0]);
             $('#scan_id').val(row[1]);
             $('#scan_nip').val(row[2]);
             $('#scan_unit_kerja').val(row[3]);
             $('#scan_gol').val(row[4]);
             $('#scan_jabatan').val(row[5]);
        });
        $("#export").click(function(evt){
            $("#form").attr('action', '<?php echo site_url() ?>/diklat/export_training_participant');
            $("#form").attr('target', '_blank');
            $("#form").submit();
            $("#form").attr('target', '');
            $("#form").attr('action', '');
        });

        <?php 
        if(!empty($filter)){
            $id = $filter['diklat_id']; 
            $year = $filter['year']; 
        ?>

        $('#form option[value=<?php echo $id ?>]').attr('selected','selected');
        $('#year option[value=<?php echo $year ?>]').attr('selected','selected');
        
        <?php } ?>
        
    });
</script>

<form id="form" action="<?php echo site_url('diklat/training_participant') ?>" method="post" class="ajaxform" name="diklat_form" onsubmit="return validasi_input(this)">
    <fieldset>
        <div class="clearfix"></div>
        <div>
            <label class="mandatory">Nama Diklat</label>
            <?php echo form_dropdown('diklat_id',$diklats) ?>
        </div>
        <div>
            <label>Tahun</label>
            <?php echo form_dropdown('year',$years,'','id=year') ?>
        </div>
        <div class="action-buttons btn-group">
            <input type="submit" value="Cari" />
            <input type="button" id="export" href="" class="button" value="<?php echo l('Eksport Ke Excel')?>"></input>
        </div>
    </fieldset>
</form>

<div class="row-fluid">
    <span style="font-weight: bold">Jenis Diklat : </span><span><?php echo $diklat_name ?></span>
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

<script type="text/javascript">
    function validasi_input(form){
        if(document.diklat_form.diklat_id.value=='' && document.diklat_form.year.value==''){
            alert ('Mohon Pilih Diklat atau Tahun dahulu !');
            return false;
        }
        return (true);
    }
</script>
