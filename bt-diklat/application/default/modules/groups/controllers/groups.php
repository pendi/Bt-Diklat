<?php

class groups extends app_crud_controller {

	function _config_grid() {
        $config = parent::_config_grid();
        $config['names'] = array('Nama Golongan', 'Pangkat');
        $config['formats'] = array();
        return $config;
    }

}