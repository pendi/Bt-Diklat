<?php $title = l((empty($id) ? 'Tambah %s' : 'Ubah %s'), array(l('Data Diklat'))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<style>
    .requirement {
        border: 1px solid #cccccc;
        padding: 5px;
        border-radius: 5px;
        display: inline-block;
        margin-top: 5px;
        margin-right: 5px;
        cursor: pointer;
        /*background-color: #FF5C63;*/
    }
</style>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label><?php echo l('Nama Diklat') ?></label>
            <input type="text" value="<?php echo set_value('name') ?>" name="name" placeholder="<?php echo l('Nama Diklat') ?>" />
        </div>
        <div>
            <label><?php echo l('Penyelenggara') ?></label>
            <?php echo form_dropdown('organizer_id',$organizer) ?>
        </div>
        <div>
            <label><?php echo l('Persyaratan') ?></label>
            <input id="input-req" class="span4" type="text" name="q" autocomplete="off" placeholder="<?php echo l('Masukan Persyaratan') ?>">
            <div id="requirements"></div>
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
</form>


<script type="text/javascript">
    $(function(){
        var id = "<?php echo isset($id) ? $id : '' ?>";

        $('#input-req').on('keypress',function(e){
            if(e.which == 13){
                var req = $(this).val();
                var dom = '<div class="requirement">'+req+'<i class="icon-remove"></i><input type="hidden" value="'+req+'" name="requirement[]" /></div>';
                $('#requirements').append(dom);
                $(this).val('');
                return false;
            }
        });

        if(id){
            $.ajax({
                url: '<?php echo site_url("diklat/get_requirement") ?>',
                type: 'POST',
                data: {diklat_id:id}
            }).done(function(result){
                $.each(result, function(k,v){
                    var dom = '<div class="requirement" id="'+v.id+'">'+v.require_name+'<i class="icon-remove"></i></div>';
                    $('#requirements').append(dom);
                });
            });

            $('#requirements').on('click','.requirement',function(){
                var reqId = this.id;
                $.ajax({
                    url: '<?php echo site_url("diklat/update_requirement") ?>',
                    type: 'POST',
                    data: {req_id:reqId}
                });
                this.remove();
            });
        } else {
            $('#requirements').on('click','.requirement',function(){
                this.remove();
            });
        }
    });
</script>






