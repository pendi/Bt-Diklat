<?php

/**
 * xmenu.php
 *
 * @package     arch-php
 * @author      jafar <jafar@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   jafar <jafar@xinix.co.id>
 *
 *
 */

$config = array();
// $config['xmenu_source'] = 'model:menu:find_admin_panel';

$config['xmenu_source'] = 'inline';

$config['xmenu_items'][0]['title'] = 'Home';
$config['xmenu_items'][0]['uri'] = '/';

$config['xmenu_items'][1]['title'] = 'System';
$config['xmenu_items'][1]['children'][0]['title'] = 'Pengguna';
$config['xmenu_items'][1]['children'][0]['uri'] = 'user/listing';
$config['xmenu_items'][1]['children'][1]['title'] = 'Hak Akses';
$config['xmenu_items'][1]['children'][1]['uri'] = 'role/listing';
$config['xmenu_items'][1]['children'][2]['title'] = 'Penghapusan Data';
$config['xmenu_items'][1]['children'][2]['uri'] = 'diklat/delete_order_history';
$config['xmenu_items'][1]['children'][3]['title'] = 'Penjadwalan Diklat';
$config['xmenu_items'][1]['children'][3]['uri'] = 'diklat/diklat_schedule_listing';

$config['xmenu_items'][2]['title'] = 'Data Master';
$config['xmenu_items'][2]['children'][0]['title'] = 'Data Master Diklat';
$config['xmenu_items'][2]['children'][0]['uri'] = 'diklat/listing';
$config['xmenu_items'][2]['children'][1]['title'] = 'Data Master Pegawai';
$config['xmenu_items'][2]['children'][1]['uri'] = 'employee/listing';
$config['xmenu_items'][2]['children'][2]['title'] = 'Data Master Penyelenggara';
$config['xmenu_items'][2]['children'][2]['uri'] = 'organizer/listing';
$config['xmenu_items'][2]['children'][3]['title'] = 'Data Master Golongan';
$config['xmenu_items'][2]['children'][3]['uri'] = 'groups/listing';
$config['xmenu_items'][2]['children'][4]['title'] = 'Data Master Unit Kerja';
$config['xmenu_items'][2]['children'][4]['uri'] = 'work_unit/listing';
$config['xmenu_items'][2]['children'][5]['title'] = 'Data Master Ijazah Kepelautan';
$config['xmenu_items'][2]['children'][5]['uri'] = 'master_certificate/listing';
$config['xmenu_items'][2]['children'][6]['title'] = 'Data Pegawai Yang Sudah Dihapus';
$config['xmenu_items'][2]['children'][6]['uri'] = 'employee/roleback_employee';

$config['xmenu_items'][3]['title'] = 'Pengusulan Peserta Sementara';
$config['xmenu_items'][3]['uri'] = 'diklat/order';

$config['xmenu_items'][4]['title'] = 'Data Peserta Sementara';
$config['xmenu_items'][4]['uri'] = 'diklat/order_list';

$config['xmenu_items'][5]['title'] = 'Data Peserta Diklat';
$config['xmenu_items'][5]['uri'] = 'diklat/training_participant';

$config['xmenu_items'][6]['title'] = 'Pencarian';
$config['xmenu_items'][6]['uri'] = 'site/advance_search';


















