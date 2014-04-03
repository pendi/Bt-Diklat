<?php

class work_unit extends app_crud_controller {

	function _config_grid() {
        $config = parent::_config_grid();
        $config['names'] = array('Kode Unit Kerja', 'Nama Unit Kerja');
        $config['formats'] = array();
        return $config;
    }

}