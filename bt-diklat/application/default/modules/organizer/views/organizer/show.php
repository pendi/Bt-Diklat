<?php $title = l((empty($id) ? 'Tambah %s' : 'Ubah %s'), array(l('Data Penyelenggara'))) ?>

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
            <label><?php echo l('Nama Penyelenggara') ?></label>
            <input type="text" value="<?php echo set_value('organizer_name') ?>" name="organizer_name" placeholder="<?php echo l('Nama Penyelenggara') ?>" />
        </div>
        <div>
            <label><?php echo l('Alamat') ?></label>
            <input type="text" value="<?php echo set_value('address') ?>" name="address" placeholder="<?php echo l('Alamat') ?>" />
        </div>
        <div>
            <label><?php echo l('Telepon') ?></label>
            <input type="text" value="<?php echo set_value('phone') ?>" name="phone" placeholder="<?php echo l('Telepon') ?>" />
        </div>
    </fieldset>
    <div class="action-buttons btn-group">
        <input type="submit" />
        <a href="<?php echo site_url($CI->_get_uri('listing')) ?>" class="btn cancel"><?php echo l('Batal') ?></a>
    </div>
</form>