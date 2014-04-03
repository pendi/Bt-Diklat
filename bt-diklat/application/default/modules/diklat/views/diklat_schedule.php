<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend>Penjadwalan Diklat</legend>
        <div class="clearfix"></div>
        <div>
            <label class="mandatory">Tahun</label>
            <input type="text" name="year" value="<?php echo set_value('year') ?>" />
        </div>
        <div>
            <label><?php echo l('Hak Akses') ?></label>
            <?php echo form_multiselect('diklat_id[]', $diklats, @$_POST['diklat_id'],'style="height: 150px;"') ?>
        </div>
    
        <div class="action-buttons btn-group">
            <input type="submit" value="Kirim">
            <a href="<?php echo site_url('diklat/diklat_schedule_listing') ?>" class="btn btn-info">Batal</a>
        </div>
    </fieldset>
</form>