<?php $title = l((empty($id) ? 'Tambah %s' : 'Ubah %s'), array(l('Data Diklat'))) ?>

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
            <label><?php echo l('Golongan') ?></label>
            <input type="text" value="<?php echo set_value('class_name') ?>" name="class_name" placeholder="<?php echo l('Golongan') ?>" />
        </div>
        <div>
            <label><?php echo l('Pangkat') ?></label>
            <input type="text" value="<?php echo set_value('rank') ?>" name="rank" placeholder="<?php echo l('Pangkat') ?>" />
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
</form>