


<form action="<?php echo site_url('diklat/delete_order_history') ?>" method="post" class="ajaxform">
    <fieldset>
        <legend>Penghapusan Data</legend>
        <div class="clearfix"></div>
        <div>
            <label>History Pengusulan Diklat</label>
            <?php echo form_dropdown('diklat_id',$diklats) ?>
        </div>
    
        <div>
            <label class="mandatory">Tahun</label>
            <input type="text" name="year" value="<?php echo set_value('year') ?>" />
        </div>
        <div class="action-buttons btn-group">
            <a id="send" class="btn btn-info">Hapus Data</a>
        </div>
    </fieldset>
    <div id="confirm" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h4 id="myModalLabel">KONFIRMASI</h4>
        </div>
        <div class="modal-body">
            Anda yakin ingin menghapus data ini ?
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Batal</button>
            <input type="submit" value="Kirim">
        </div>
    </div>
</form>

<script>
    $(function(){
        $('#send').on('click',function(evt){
            evt.preventDefault();
            $('#confirm').modal('show');
        });
    });
</script>