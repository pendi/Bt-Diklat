<?php $title = l((empty($id) ? 'Tambah %s' : 'Ubah %s'), array(l('Unit Kerja'))) ?>

<?php
echo $this->admin_panel->breadcrumb(array(
    array('uri' => $CI->_get_uri('listing'), 'title' => l(humanize(get_class($CI)))),
    array('uri' => $CI->uri->uri_string, 'title' => $title),
))
?>
<div class="clearfix"></div>

<form action="<?php echo current_url() ?>" method="post" class="ajaxform">
    <fieldset>
        <legend><?php echo $title ?></legend>
        <div>
            <label><?php echo l('Kode Unit Kerja') ?></label>
            <input type="text" value="<?php echo set_value('unit_code') ?>" name="unit_code" placeholder="<?php echo l('Kode Unit Kerja') ?>" />
        </div>
        <div>
            <label><?php echo l('Nama Unit Kerja') ?></label>
            <input type="text" value="<?php echo set_value('unit_name') ?>" name="unit_name" placeholder="<?php echo l('Nama Unit Kerja') ?>" />
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
</form>