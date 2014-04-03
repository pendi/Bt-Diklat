<div class="header">
    <div class="pull-left">
        <?php echo $this->admin_panel->breadcrumb() ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="grid-top">
    <div class="pull-left btn-group">
        <?php echo xform_anchor($CI->_get_uri('trash'), l('Trash'), 'class="btn btn-danger mass-action"') ?>
    </div>
    <div class="pull-right">
        <?php echo xform_anchor($CI->_get_uri('diklat_schedule'), l('Tambah Data'), 'class="btn"') ?>
    </div>
    <div class="clearfix"></div>
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
