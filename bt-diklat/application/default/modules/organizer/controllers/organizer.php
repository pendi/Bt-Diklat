<?php

class organizer extends app_crud_controller {

	function _config_grid(){
		$config = parent::_config_grid();
		$config['fields'] = array('organizer_name','address','phone');
		$config['names'] = array('Nama Penyelenggara','Alamat','Telepon');
		$config['formats'] = array();

		return $config;
	}
	
}