<?php $title = l((empty($id) ? 'Tambah %s' : 'Ubah %s'), array(l('Data Pegawai'))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform" enctype="multipart/form-data">
    <fieldset>
        <legend><?php echo l('Data Utama Pegawai') ?></legend>
        <div>
            <label><?php echo l('Nama Pegawai') ?></label>
            <input type="text" value="<?php echo set_value('employee_name') ?>" name="employee_name" placeholder="<?php echo l('Nama Pegawai') ?>" />
        </div>
        <div>
            <label><?php echo l('NIP') ?></label>
            <input type="text" value="<?php echo set_value('nip') ?>" name="nip" placeholder="<?php echo l('NIP') ?>" />
        </div>
        <div>
            <label><?php echo l('Lahir') ?></label>
            <?php echo xform_date('born_date',array('default_today' => false)) ?>
        </div>
        <div>
            <label><?php echo l('Jabatan') ?></label>
            <input type="text" value="<?php echo set_value('position') ?>" name="position" placeholder="<?php echo l('Jabatan') ?>" />
        </div>
        <div>
            <label><?php echo l('Golongan') ?></label>
            <?php echo form_dropdown('group_id', $group_name) ?>
        </div>
        <div>
            <label><?php echo l('Unit Kerja') ?></label>
            <input id="unit_name" type="text" value="<?php echo set_value('unit_of_work') ?>" name="unit_of_work" placeholder="<?php echo l('Unit Kerja') ?>" />
            <input id="unit_code" type="hidden" value="<?php echo set_value('unit_code') ?>" name="unit_code" />
        </div>
        <div>
            <label><?php echo l('Foto') ?></label>
            <?php if(empty($img)): ?>
                <input type="file" value="<?php echo set_value('image') ?>" name="image" />
            <?php else: ?>
                <?php if(isset($id)): ?>
                        <div class="thumbs thumbnail span2">
                            <img src="<?php echo base_url('timthumb.php?src=')?><?php echo base_url('data/employee/image') . '/' . $img ?>&w=100&h=100" alt=""/>
                            <a class="btn" href="<?php echo site_url($CI->_get_uri('delete_image') . '/' . $id ) ?>" >
                                <?php echo l('Delete') ?>
                            </a>
                        </div>
                <?php endif ?>
            <?php endif ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?php echo l('Data Pendidikan Formal') ?></legend>
        <div>
            <label><?php echo l('Pendidikan Terakhir') ?></label>
            <?php echo form_dropdown('level_of_education',$level_of_education) ?>
        </div>
        <div>
            <label><?php echo l('Nama Sekolah / Universitas') ?></label>
            <input type="text" value="<?php echo set_value('name_of_school') ?>" name="name_of_school" placeholder="<?php echo l('Nama Sekolah / Universitas') ?>" />
        </div>
        <div>
            <label><?php echo l('Jurusan') ?></label>
            <input type="text" value="<?php echo set_value('majors') ?>" name="majors" placeholder="<?php echo l('Jurusan') ?>" />
        </div>
        <div>
            <label><?php echo l('Tahun Lulus') ?></label>
            <input type="text" value="<?php echo set_value('year_pass') ?>" name="year_pass" placeholder="<?php echo l('Tahun Lulus') ?>" />
        </div>
        <div>
            <label><?php echo l('Keterangan') ?></label>
            <input type="text" value="<?php echo set_value('desc_of_education') ?>" name="desc_of_education" placeholder="<?php echo l('Keterangan') ?>" />
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
</form>

<script type="text/javascript">
    $(function(){
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
    });
</script>






















