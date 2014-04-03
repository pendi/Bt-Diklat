<?php

class diklat extends app_crud_controller {

    function _config_grid_order() {
        $config = parent::_config_grid();
        $config['fields'] = array('nip','nip','position','unit_name','name','year','id','');
        $config['names'] = array('Nama Pegawai', 'NIP','Jabatan','Unit Kerja','Diklat Name','Tahun','Keterangan','');
        $config['formats'] = array('callback__popup_employee','','','','callback__uppercase','','callback__description','callback__approval_status');
        return $config;
    }

    function _config_grid_schedule() {
        $config = parent::_config_grid();
        $config['fields'] = array('year');
        $config['names'] = array('Tahun');
        $config['formats'] = array();
        $config['actions'] = array(
            'edit' => $this->_get_uri('diklat_schedule'),
            'delete' => $this->_get_uri('trash_diklat_schedule')
        );
        return $config;
    }

    function _config_grid_history() {
        $config = parent::_config_grid();
        $config['fields'] = array('employee_name','nip','group_name','unit_name','name','year');
        $config['names'] = array('Nama', 'NIP','Golongan','Unit Kerja','Diklat / Kursus','Tahun');
        $config['formats'] = array();
        $config['show_checkbox'] = false;
        return $config;
    }

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('name','code');
        $config['names'] = array('Nama Diklat','Kode Diklat');
        $config['formats'] = array('callback__diklat_history');
        return $config;
    }

    function _config_grid_training_participant() {
        $config = parent::_config_grid();
        $config['fields'] = array('employee_name','nip','position','unit_name','year');
        $config['names'] = array('Nama Pegawai', 'NIP','Jabatan','Unit Kerja','Tahun');
        $config['formats'] = array();
        return $config;
    }

    function _uppercase($v){
        return strtoupper($v);
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/show';

        $organizers = $this->db->query('SELECT * FROM organizer WHERE status !=0')->result_array();
        $this->_data['organizer'] = array(
            '' => 'Pilih Penyelenggara'
        );
        if($organizers){
            foreach ($organizers as $key => $value) {
                $organizer[0] = 'Pilih Penyelenggara';
                $organizer[$value['id']] = $value['organizer_name'];
            }
            $this->_data['organizer'] = $organizer;
        }

        if ($_POST) {
            if ($this->_validate()) {

                /** Validation Diklat Name **/
                $checking = $this->db->query('SELECT COUNT(*) count FROM diklat WHERE name = ?',$_POST['name'])->row()->count;
                if($checking > 0){
                    add_error('Diklat sudah tersedia');
                    return false;
                }
                /** Validation Diklat Name **/

                if(!$id){
                    $check = $this->db->query('SELECT MAX(code) max_code FROM diklat WHERE status !=0')->row_array();
                    if(empty($check['max_code'])){
                        $_POST['code'] = 'DIK-001';
                    } else {
                        $explode = explode('-', $check['max_code']);
                        $generate = 'DIK-'.str_pad($explode[1]+1, 3, "0", STR_PAD_LEFT);
                        $_POST['code'] = $generate;
                    }
                }

                $_POST['id'] = $id;
                try {
                    $id = $this->_model()->save($_POST, $id);

                    if(!empty($_POST['requirement'])){
                        foreach ($_POST['requirement'] as $key => $value) {
                            $requirement = array(
                                'diklat_id' => $id,
                                'require_name' => trim($value)
                            );
                            $this->_model()->before_save($requirement);
                            $this->db->insert('requirement',$requirement);
                        }
                    }

                    $referrer = $this->session->userdata('referrer');
                    if (empty($referrer)) {
                        $referrer = $this->_get_uri('listing');
                    }

                    add_info( ($id) ? l('Record updated') : l('Record added') );

                    if (!$this->input->is_ajax_request()) {
                        redirect($referrer);
                    }
                } catch (Exception $e) {
                    add_error(l($e->getMessage()));
                }
            }
        } else {
            if ($id !== null) {
                $this->_data['id'] = $id;
                $_POST = $this->_model()->get($id);
                if (empty($_POST)) {
                    show_404($this->uri->uri_string);
                }
            }
            $this->load->library('user_agent');
            $this->session->set_userdata('referrer', $this->agent->referrer());
        }
        $this->_data['fields'] = $this->_model()->list_fields(true);
    }

	function order(){
        $year = date('Y');

        /** Create Year Dropdown **/
        $get_year = $this->db->query('SELECT DISTINCT * FROM diklat_schedule')->result_array();
        $this->_data['year'] = array('' => 'Pilih Tahun');
        if(!empty($get_year)){
            foreach ($get_year as $key => $y) {
                $this->_data['year'][$y['year']] = $y['year'];
            }
        } else {
            $this->_data['year'][$year] = $year;
        }
        /** Create Year Dropdown **/


        $diklats = $this->_model('diklat')->find(null, array('id' => 'asc'));

        $this->_data['diklats'] = array('' => 'Pilih Diklat');
        foreach ($diklats as $diklat) {
            $this->_data['diklats'][$diklat['id']] = strtoupper($diklat['name']);
        }

        /** Create Golongan Dropdown **/
        $groups = $this->db->query('SELECT * FROM groups')->result_array();
        foreach ($groups as $group) {
            $this->_data['groups'][$group['id']] = $group['group_name'];
            $rank[$group['id']] = $group['rank'];
        }
        $this->_data['rank'] = json_encode($rank);

        /** Create Unit Kerja Dropdown **/
        $unit_of_work = $this->db->query('SELECT * FROM work_unit')->result_array();
        foreach ($unit_of_work as $units) {
            $this->_data['uow'][$units['unit_code']] = $units['unit_name'];
            $unit[$units['unit_code']] = $units['unit_name'];
        }
        $this->_data['unit_of_works'] = json_encode($unit);

        /** Create Checkbox Ijazah Kepelautan **/
        $certificate_marine = $this->db->query('SELECT * FROM master_certificate WHERE status !=0')->result_array();
        $this->_data['certificate_marine'] = array_chunk($certificate_marine, 2);

        /** Create Radio Button Pendidikan Terakhir **/
        $level_education = $this->db->query('SELECT * FROM master_level_education WHERE status !=0')->result_array();
        $this->_data['level_education'] = array_chunk($level_education, 6);

        if($_POST){

            /** Upload employee photo **/
            if(!empty($_FILES['image']['name'])){
                $config['upload_path'] = 'employee';
                $config['allowed_types'] = 'png|jpg|jpeg|gif';
                $config['field'] = 'image';
                $config['encrypt_name'] = 'TRUE';

                $this->load->library('upload');
                $this->upload->initialize($config);

                $this->upload->do_upload('image');
                $image = $this->upload->data();
                $_POST['image'] = $image[0]['file_name'];
            } else {
                $_POST['image'] = '';
            }

            /** Add new data if none selected data from autocomplete **/
            if(!empty($_POST['employee_id'])){
                $employee_id = $_POST['employee_id'];
                if($_POST['born_date']){
                    $data_employee = array(
                        'id' => $employee_id,
                        'nip' => $_POST['nip'],
                        'employee_name' => $_POST['employee_name'],
                        'position' => $_POST['position'],
                        'group_id' => $_POST['group_id'],
                        'image' => $_POST['image'],
                        'unit_of_work' => $_POST['unit_of_work'],
                        'born_date' => $_POST['born_date']
                    );
                } else {
                    $data_employee = array(
                        'id' => $employee_id,
                        'nip' => $_POST['nip'],
                        'employee_name' => $_POST['employee_name'],
                        'position' => $_POST['position'],
                        'group_id' => $_POST['group_id'],
                        'image' => $_POST['image'],
                        'unit_of_work' => $_POST['unit_of_work']
                    );
                }
                $this->_model('employee')->update_employee($data_employee);
            } else {
                $data_employee = array(
                    'nip' => $_POST['nip'],
                    'employee_name' => $_POST['employee_name'],
                    'position' => $_POST['position'],
                    'group_id' => $_POST['group_id'],
                    'image' => $_POST['image'],
                    'unit_of_work' => $_POST['unit_of_work'],
                    'born_date' => $_POST['born_date']
                );
                $this->_model('employee')->insert_employee($data_employee);
                $employee_id = $this->db->insert_id();
            }

            /** Proccess saving data into table diklat order **/
            $data_diklat = array(
                'diklat_id' => $_POST['diklat_id'],
                'employee_id' => $employee_id,
                'year' => $_POST['year']
            );
            $diklat_order_id = $this->_model('diklat')->insert_order_diklat($data_diklat);

            /** Insert data to table require complete **/
            if(!empty($_POST['requirement_id'])){
                foreach ($_POST['requirement_id'] as $k => $req) {
                    $req_complete = array(
                        'diklat_id' => $_POST['diklat_id'],
                        'employee_id' => $employee_id,
                        'diklat_order_id' => $diklat_order_id,
                        'requirement_id' => $req
                    );
                    $this->_model()->before_save($req_complete);
                    $this->db->trans_start();
                    $this->db->insert('require_compelete',$req_complete);
                    $this->db->trans_complete();
                }
            }

            /** Insert data to table require minus **/
            if(!empty($_POST['minus'])){
                foreach ($_POST['minus'] as $k => $min) {
                    $req_minus = array(
                        'diklat_id' => $_POST['diklat_id'],
                        'employee_id' => $employee_id,
                        'diklat_order_id' => $diklat_order_id,
                        'requirement_id' => $min
                    );
                    $this->_model()->before_save($req_minus);
                    $this->db->trans_start();
                    $this->db->insert('require_minus',$req_minus);
                    $this->db->trans_complete();
                }
            }

            /** Proccess certificate data & Saving into table certificate **/
            $certificate = array();
            if($_POST['certificate']){
                for($i=0;$i<count($_POST['certificate']);$i++){
                    foreach ($_POST['cer_no'] as $key => $value) {
                        if(($key+1) == $_POST['certificate'][$i]){
                            $certificate[] = array(
                                'certificate' => $_POST['certificate'][$i],
                                'certificate_no' => $value
                            );
                        }
                    }
                }

                foreach ($certificate as $key => $value) {
                    $check_certificate = $this->db->query('SELECT *
                                                           FROM certificate
                                                           WHERE employee_id = ?
                                                           AND certificate = ?',array($employee_id,$value['certificate']))->row_array();
                    $dc = array(
                        'employee_id' => $employee_id,
                        'certificate' => $value['certificate'],
                        'certificate_no' => $value['certificate_no']
                    );
                    $this->_model()->before_save($dc);

                    if(empty($check_certificate)){
                        $this->db->insert('certificate',$dc);
                    } else {
                        $this->db->where('id',$check_certificate['id']);
                        $this->db->update('certificate',$dc);
                    }
                }
            }

            /** Saving formal education **/
            if($_POST['nip']){
                $check_education = $this->db->query('SELECT * FROM formal_education WHERE nip = ?',array($_POST['nip']))->row_array();
                $fe = array(
                    'nip' => $_POST['nip'],
                    'level_of_education' => $_POST['level_of_education'],
                    'majors' => $_POST['majors'],
                    'year_pass' => $_POST['year_pass'],
                    'name_of_school' => $_POST['name_of_school'],
                    'desc_of_education' => $_POST['desc_of_education']
                );
                $this->_model()->before_save($fe);

                if($check_education){
                    $this->db->where('id',$check_education['id']);
                    $this->db->update('formal_education',$fe);
                } else {
                    $this->db->insert('formal_education',$fe);
                }
            }

            redirect(site_url('diklat/order_list'));
        }
    }

    function _approval_status($v,$r,$a){
    	if($a['diklat_approval'] == 0){
    		return '<a class="select-participant" dataid="'.$a['id'].'">Pilih</a>
                    <a class="delete-participant" dataid="'.$a['id'].'">Hapus</a>';
    	} else {
    		return '<span class="select-participant">Peserta Terpilih</span>';
    	}
    }

    function select_participant(){
        header('content-type:application/json');

        $data = array(
            'diklat_approval' => 1
        );

        $get_diklat = $this->db->query('SELECT * FROM diklat_order WHERE id = ?',array($_POST['data_id'][0]))->row_array();
        $get_count = $this->db->query('SELECT COUNT(*) count FROM diklat_order WHERE diklat_approval = 1 AND diklat_id = ?',array($get_diklat['diklat_id']))->row()->count;

        if($get_count <= 40){
            $this->db->trans_start();
            foreach ($_POST['data_id'] as $key => $value) {
                $this->_model()->before_save($data);
                $this->db->where('id',$value);
                $this->db->update('diklat_order',$data);
            }
            $this->db->trans_complete();
            $res = 1;
        } else {
            $res = 0;
        }

        echo json_encode($res);
        exit;
    }

    function delete_participant(){
        header('content-type:application/json');

        $data = array(
            'status' => 0
        );

        $this->db->trans_start();
        foreach ($_POST['data_id'] as $key => $value) {
            $this->_model()->before_save($data);

            $this->_model()->_db()->where_in('diklat_order_id', $value)->update('require_minus', $data);
            $this->_model()->_db()->where_in('diklat_order_id', $value)->update('require_compelete', $data);

            $this->db->where('id',$value);
            $this->db->update('diklat_order',$data);
        }
        $this->db->trans_complete();

        echo json_encode('success');
        exit;
    }

	function order_list($offset=0){
        $this->load->library('pagination');

        $diklats = $this->_model('diklat')->find(null, array('id' => 'asc'));
        $this->_data['diklats'] = array('' => 'Pilih Diklat');
        foreach ($diklats as $diklat) {
            $this->_data['diklats'][$diklat['id']] = strtoupper($diklat['name']);
        }

        $years = $this->db->query('SELECT DISTINCT(year) FROM diklat_order WHERE status !=0')->result_array();
        $this->_data['years'] = array('' => 'Tahun');
        if($years){
            foreach ($years as $y) {
                $this->_data['years'][$y['year']] = $y['year'];
            }
        } else {
            $date = date('Y');
            $this->_data['years'][$date] = $date;
        }

        $filters = '';
        if($_POST){
            if(!$_POST['employee_name'] && !$_POST['employee_id'] && !$_POST['diklat_id'] && !$_POST['year']){
                redirect($this->_get_uri('order_list'));
            }
            $filters = $_POST;
            $this->session->set_userdata('data_post_calon', $_POST);

        }else{
            $filters = $this->session->userdata('data_post_calon');
            $this->_data['diklat_name'] = "Semua Diklat";
        }

        if(!empty($filters['diklat_id'])){
            $diklat_nm = $this->db->query('SELECT * FROM diklat WHERE id =?', array($filters['diklat_id']))->row_array();
            $cp = $this->db->query('SELECT COUNT(*) count FROM diklat_order WHERE diklat_id = ? AND diklat_approval = 1', array($filters['diklat_id']))->row()->count;
            $this->_data['diklat_name'] = $diklat_nm['name'];
            $this->_data['count_participant'] = $cp;
        } else {
            $this->_data['diklat_name'] = "Semua Diklat";
        }
        $this->_data['filter'] = $filters;
        $count = 0;
        $config_grid = $this->_config_grid_order();
        $config_grid['sort'] = $this->_get_sort();
        $config_grid['actions'] = array();
        $this->_data['data'] = $diklat_data = $this->_model()->order_list($filters, $config_grid['sort'], $this->pagination->per_page, $offset, $count);

        $this->load->library('xgrid', $config_grid, 'listing_grid');
        $this->load->library('pagination');
        $param = array(
            'total_rows' => $count,
            'per_page' => $this->pagination->per_page,
            'base_url' => site_url('diklat/order_list'),
            'uri_segment' => 3
        );

        $this->pagination->initialize($param);
	}

	function training_participant($offset=0){
        $this->load->library('pagination');

        $diklats = $this->_model('diklat')->find(null, array('name' => 'asc'));

        $this->_data['diklats'] = array('' => 'Pilih Diklat');
        foreach ($diklats as $diklat) {
            $this->_data['diklats'][$diklat['id']] = strtoupper($diklat['name']);
        }

        $years = $this->db->query('SELECT DISTINCT(year) FROM diklat_order WHERE status !=0')->result_array();
        $this->_data['years'] = array('' => 'Tahun');
        if($years){
            foreach ($years as $y) {
                $this->_data['years'][$y['year']] = $y['year'];
            }
        } else {
            $date = date('Y');
            $this->_data['years'][$date] = $date;
        }

        $filters = '';
        if($_POST){
            $filters = $_POST;
            $this->session->set_userdata('data_post_peserta', $_POST);
        }else{
            $this->_data['diklat_name'] = "Semua Diklat";
            $filters = $this->session->userdata('data_post_peserta');
        }

        if(!empty($filters['diklat_id'])){
            $diklat_nm = $this->db->query('SELECT * FROM diklat WHERE id =?', array($filters['diklat_id']))->row_array();
            $this->_data['diklat_name'] = $diklat_nm['name'];
        }else{
            $this->_data['diklat_name'] = "Semua Diklat";
        }

        $count = 0;
        $config_grid = $this->_config_grid_training_participant();
        $config_grid['sort'] = $this->_get_sort();
        $config_grid['actions'] = array(
            'delete' => $this->_get_uri('delete_training_participant'),
        );
        $this->_data['data'] = array();
        if($filters){
            $this->_data['data'] = $diklat_data = $this->_model()->training_participant($filters, $config_grid['sort'], $this->pagination->per_page, $offset, $count);
            $this->_data['filter'] = $filters;
        }else{
            $this->_data['data'] = $diklat_data = $this->_model()->training_participant($filters=null, $config_grid['sort'], $this->pagination->per_page, $offset, $count);
        }

        $this->load->library('xgrid', $config_grid, 'listing_grid');
        $this->load->library('pagination');
        $param = array(
            'total_rows' => $count,
            'per_page' => $this->pagination->per_page,
            'base_url' => site_url('diklat/training_participant'),
            'uri_segment' => 3
        );

        $this->pagination->initialize($param);
	}

    function export_training_participant(){
        $filters = '';
        if($_POST){
            $filters = $_POST;
            if(!empty($_POST['diklat_id'])){
                $diklat_nm = $this->db->query('SELECT * FROM diklat WHERE id =?', array($_POST['diklat_id']))->row_array();
                $this->_data['diklat_name'] = $diklat_nm['name'];
                $diklat = $diklat_nm['name'];
            }else{
                $this->_data['diklat_name'] = "Semua Diklat";
                $diklat = '';
            }
        }else{
            $this->_data['diklat_name'] = "Semua Diklat";
        }

        $count = 0;
        $this->_data['data'] = $results = $this->_model()->training_participant($filters, '', '', '', $count);
        $cnt = count($results);

        $this->load->library('phpexcel'); //Panggil Library Excel
        $this->phpexcel->getDefaultStyle()->getFont()
                ->setName('Arial')
                ->setSize(9);
        // styling
        $style['header'] = array(
            'font' => array(
                'bold' => true,
                'size' => 11
            ),
        );

        $style['border'] = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '000'),
                ),
            ),
        );


        $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValue('B1', 'DAFTAR PANGGILAN PESERTA DIKLAT '.strtoupper($diklat))
                ->setCellValue('B4', 'NO')
                ->setCellValue('C4', 'NAMA / NIP')
                ->setCellValue('D4', 'GOL')
                ->setCellValue('E4', 'UNIT KERJA')
                ->setCellValue('F4', 'KET')
                ->setCellValue('B5', '1')
                ->setCellValue('C5', '2')
                ->setCellValue('D5', '3')
                ->setCellValue('E5', '4')
                ->setCellValue('F5', '5');
        $row = 6;
        for ($i = 6; $i <= $cnt + 5; $i++) {
            $this->phpexcel->setActiveSheetIndex(0)->setCellValue('B' . $i, $i - 5);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValue('C' . $i, $results[$i - 6]['employee_name']."\rNIP : ".$results[$i - 6]['nip']);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValue('D' . $i, $results[$i - 6]['group_name']);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValue('E' . $i, $results[$i - 6]['unit_name']);
            $this->phpexcel->setActiveSheetIndex(0)->setCellValue('F' . $i, '');
            $this->phpexcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $this->phpexcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $this->phpexcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $this->phpexcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $this->phpexcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
            $this->phpexcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($style['border']);
            $this->phpexcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($style['border']);
            $this->phpexcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($style['border']);
            $this->phpexcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($style['border']);
            $this->phpexcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($style['border']);
            $this->phpexcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
            $row++;
        }

        for ($x = 6; $x <= $cnt + 5; $x++) {
            $this->phpexcel->getActiveSheet()->getStyle('C'.$x)->getAlignment()->setWrapText(true);
        }

        $bottom = $row + 1;
        $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValue('D'.$bottom, 'NAMA / NIP')
                ->setCellValue('D'.($bottom + 1), 'An. SEKRETARIS DIREKTORAT JENDERAL')
                ->setCellValue('D'.($bottom + 2), 'PERHUBUNGAN LAUT')
                ->setCellValue('D'.($bottom + 3), 'KEPALA BAGIAN KEPEGAWAIAN DAN UMUM')
                ->setCellValue('D'.($bottom + 7), '')
                ->setCellValue('D'.($bottom + 8), 'Pembina (IV/a)')
                ->setCellValue('D'.($bottom + 9), 'NIP :');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('B1:F1');
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.$bottom.':F'.$bottom);
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.($bottom+1).':F'.($bottom+1));
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.($bottom+2).':F'.($bottom+2));
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.($bottom+3).':F'.($bottom+3));
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.($bottom+7).':F'.($bottom+7));
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.($bottom+8).':F'.($bottom+8));
        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('D'.($bottom+9).':F'.($bottom+9));

        $this->phpexcel->getActiveSheet()->getStyle('B1')->applyFromArray($style['header']);
        $this->phpexcel->getActiveSheet()->getStyle('B4:F4')->applyFromArray($style['header']);

        $char = array('A','B','C','D','E','F');
        for ($i=1; $i < 6; $i++) {
            $this->phpexcel->getActiveSheet()->getStyle($char[$i].'4')->applyFromArray($style['border']);
            $this->phpexcel->getActiveSheet()->getStyle($char[$i].'5')->applyFromArray($style['border']);
        }

        $this->phpexcel->getActiveSheet()->getStyle("B1:F5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle("D".($bottom).":F".($bottom+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $date = date('Ymd');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if(!empty($_POST['year'])){
            $year = $_POST['year'];
        } else{
            $year = '';
        }
        header('Content-Disposition: attachment;filename="Panggilan Peserta Diklat "'.$diklat.' '.$year.'".xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

	function delete_training_participant($diklat_id){
		$data = array(
			'diklat_approval' => 0
		);
		$this->_model()->before_save($data);
		$this->db->where('id',$diklat_id);
		$this->db->update('diklat_order',$data);
		redirect($this->_get_uri('training_participant'));
	}

	function get_data($id){
		header('content-type:application/json');

		if(!empty($id)){
			$this->db->trans_start();
			$data = $this->db->query('SELECT * FROM requirement WHERE diklat_id = ?',array($id))->result_array();
			$this->db->trans_complete();

			echo json_encode($data);
			exit;
		} else {
			return;
		}
	}

    function get_requirement(){
        header('content-type:application/json');

        $this->db->trans_start();
        $data = $this->db->query('SELECT * FROM requirement WHERE diklat_id = ?',array($_POST['diklat_id']))->result_array();
        $this->db->trans_complete();

        echo json_encode($data);
        exit;
    }

    function update_requirement(){
        header('content-type:application/json');

        $this->db->trans_start();
        $data = $this->db->query('DELETE FROM requirement WHERE id = ?',array($_POST['req_id']));
        $this->db->trans_complete();

        exit;

    }

    function get_history($nip){
        header('content-type:application/json');
        $this->db->trans_start();
        $history1 = $this->db->query('SELECT * FROM diklat_history WHERE nip = ?',array($nip))->result_array();
        $history2 = $this->db->query('SELECT ndh.nip, ndh.year, d.name course_name
                                      FROM new_diklat_history ndh
                                      LEFT JOIN diklat d ON d.id = ndh.diklat_id
                                      WHERE nip = ?',array($nip))->result_array();
        $history = array_merge($history1,$history2);
        $this->db->trans_complete();

        echo json_encode($history);
        exit;
    }

    function _popup_employee($value){
        $e_name = $this->db->query('SELECT * FROM employee WHERE nip = ?',array($value))->row_array();
        return '<a href="#myModal" class="pop-employee" data-toggle="modal" data-employee="'.$value.'">'.$e_name['employee_name'].'</a>';
    }

    function _description($v,$r,$a){
        $diklat_order_id = $a['id'];
        $diklat_id = $a['diklat_id'];
        $employee_id = $a['employee_id'];

        $check = $this->db->query('SELECT *
                                   FROM require_minus
                                   WHERE diklat_id = ?
                                   AND diklat_order_id = ?
                                   AND employee_id = ?
                                   AND status !=0',array($diklat_id,$diklat_order_id,$employee_id))->result_array();

        if(count($check) > 0){
            return '<a href="#minusdesc" class="min-pop-desc" data-toggle="modal" employee="'.$employee_id.'" diklat="'.$diklat_id.'" diklatorder="'.$diklat_order_id.'">Belum Lengkap</a>';
        } else {
            return '<a class="full-pop-desc">Lengkap</a>';
        }
    }

    function get_minus($diklat_id,$diklat_order_id,$employee_id){
        header('content-type:application/json');

        $this->db->trans_start();
        $check = $this->db->query('SELECT req.require_name
                                   FROM require_minus r
                                   INNER JOIN requirement req ON req.id = r.requirement_id
                                   WHERE r.diklat_id = ?
                                   AND r.diklat_order_id = ?
                                   AND r.employee_id = ?',array($diklat_id,$diklat_order_id,$employee_id))->result_array();
        $this->db->trans_complete();

        echo json_encode($check);
        exit;

    }

    function _diklat_history($v,$r,$d){
        $url = site_url('diklat/diklat_history'.'/'.$d['id']);
        $url = '<a href="'.$url.'">'.strtoupper($v).'</a>';
        return $url;
    }

    function diklat_history($id){
        $this->_data['id'] = $id;
        $this->_data['title'] = $this->db->query('SELECT * FROM diklat WHERE id = ?',array($id))->row_array();
    }

    function chart_diklat_history($id){
        header('content-type:application/json');
        $this->db->trans_start();
        $data = $this->db->query('SELECT year, count(year) count
                                  FROM new_diklat_history
                                  WHERE status !=0
                                  AND diklat_id = ?
                                  GROUP BY year
                                  ORDER BY year ',array($id))->result_array();
        $this->db->trans_complete();

        if(!empty($data)){
            foreach ($data as $key => $value) {
                $history[] = array($value['year'],(int)$value['count']);
            }
        } else {
            $history = null;
        }

        echo json_encode($history);
        exit;
    }

    function get_data_history($q = '') {
        $q = (!empty($_GET['q'])) ? $_GET['q'] : $q;
        $rows = $this->db->query('SELECT e.employee_name, e.nip, g.group_name, w.unit_name
                                  FROM employee e
                                  LEFT JOIN groups g ON g.id = e.group_id
                                  LEFT JOIN work_unit w ON w.unit_code = e.unit_of_work
                                  WHERE e.status !=0
                                  AND e.nip LIKE ?
                                  LIMIT 10', array('%'.$q.'%'))->result_array();

        foreach ($rows as $row) {
            echo sprintf("%s|%s|%s|%s\n", $row['nip'],$row['employee_name'],$row['group_name'],$row['unit_name']);
        }
        exit;
    }

    function input_history(){
        header('content-type:application/json');

        $this->db->trans_start();
        $this->_model()->before_save($_POST);
        $this->db->insert('new_diklat_history',$_POST);
        $this->db->trans_complete();

        echo json_encode('success');
        exit;
    }

    function get_diklat_employee($id,$limit){

        header('content-type:application/json');
        $params[] = $id;
        $params[] = intval($limit);

        $this->db->trans_start();
        $employee = $this->db->query('SELECT e.employee_name, e.nip, g.group_name, w.unit_name, ndh.year, ndh.certificate_no, ndh.id, ndh.place
                                      FROM new_diklat_history ndh
                                      LEFT JOIN employee e ON ndh.nip = e.nip
                                      LEFT JOIN groups g ON g.id = e.group_id
                                      LEFT JOIN work_unit w ON w.unit_code = e.unit_of_work
                                      WHERE ndh.diklat_id = ?
                                      ORDER BY ndh.year DESC, e.employee_name ASC
                                      LIMIT ?',$params)->result_array();
        $this->db->trans_complete();

        echo json_encode($employee);
        exit;
    }

    function get_search(){
        header('content-type:application/json');

        $params[] = '%'.$_POST['keyword'].'%';
        $params[] = $_POST['keyword']; //ga pake % karena angka
        $params[] = '%'.$_POST['keyword'].'%';
        $params[] = '%'.$_POST['keyword'].'%';
        $params[] = $_POST['keyword']; //ga pake % karena angka
        $params[] = '%'.$_POST['keyword'].'%';
        $params[] = '%'.$_POST['keyword'].'%';
        $params[] = $_POST['diklat_id'];


        $this->db->trans_start();
        $result = $this->db->query('SELECT ndh.diklat_id, e.employee_name, e.nip, g.group_name, w.unit_name, ndh.year, ndh.certificate_no, ndh.id, ndh.place
                                    FROM new_diklat_history ndh
                                    LEFT JOIN employee e ON ndh.nip = e.nip
                                    LEFT JOIN groups g ON g.id = e.group_id
                                    LEFT JOIN work_unit w ON w.unit_code = e.unit_of_work
                                    WHERE (e.employee_name LIKE ?
                                    OR e.nip LIKE ?
                                    OR g.group_name LIKE ?
                                    OR w.unit_name LIKE ?
                                    OR ndh.year LIKE ?
                                    OR ndh.certificate_no LIKE ?
                                    OR ndh.place LIKE ?)
                                    AND ndh.diklat_id = ?
                                    ORDER BY ndh.year DESC,ndh.id DESC',$params)->result_array();
        $this->db->trans_complete();

        echo json_encode($result);
        exit;

    }

    function update_history(){
        header('content-type:application/json');

        $this->_model()->before_save($_POST);
        $this->db->trans_start();
        $this->db->where('id',$_POST['id']);
        $this->db->update('new_diklat_history',$_POST);
        $this->db->trans_complete();

        echo json_encode('success');
        exit;
    }

    function delete_history(){
        header('content-type:application/json');

        $this->_model()->before_save($_POST);
        $this->db->trans_start();
        $this->db->query('DELETE FROM new_diklat_history WHERE id = ?',array($_POST['id']));
        $this->db->trans_complete();

        echo json_encode('success');
        exit;
    }

    function checking($nip, $diklat){
        header('content-type:application/json');

        $this->db->trans_start();
        $employee = $this->db->query('SELECT * FROM new_diklat_history WHERE nip = ? AND diklat_id =?',array($nip,$diklat))->result_array();
        $this->db->trans_complete();
        $count = count($employee);

        echo json_encode($count);
        exit;

    }

    function check_age($nip){
        header('content-type:application/json');

        $this->db->trans_start();
        $employee = $this->db->query('SELECT * FROM employee WHERE nip = ?',array($nip))->row_array();
        $born = $employee['born_date'];
        $now = date('Y-m-d');
        $age = date_diff(date_create($now),date_create($born));
        $this->db->trans_complete();

        echo json_encode($age->y);
        exit;
    }

    function delete_order_history(){

        $diklats = $this->_model('diklat')->find(null, array('id' => 'asc'));
        $this->_data['diklats'] = array('' => 'Semua History Pengusulan Peserta Diklat');
        foreach ($diklats as $diklat) {
            $this->_data['diklats'][$diklat['id']] = $diklat['name'];
        }

        if($_POST){
            if($_POST['year']){
                $year = array();
                $where = array();
                $params = array();
                $years = explode('-', $_POST['year']);

                $update = 'UPDATE diklat_order SET status = 0';

                if($_POST['diklat_id']){
                    $where[] = 'diklat_id = ?';
                    $params[] = $_POST['diklat_id'];
                }

                if(count($years) > 2) {
                    add_error('Data tahun yang anda masukan salah, contoh : 2013 - 2016');
                    return false;
                } elseif(count($years) == 2) {
                    $where[] = '(year BETWEEN ? AND ?)';
                    foreach ($years as $key => $value) {
                        $params[] = trim($value);
                    }
                } else {
                    $where[] = 'year = ?';
                    $params[] = trim($_POST['year']);
                }

                $wheres = ' WHERE ' . implode(' AND ', $where);
                $query = $update . $wheres;
                $this->db->query($query,$params);
            } else {
                add_error('Data tahun harus diisi');
                return false;
            }
            redirect($this->_get_uri('delete_order_history'));
        }
    }

    function diklat_schedule_listing($offset = 0){
        $this->load->library('pagination');

        $count = 0;
        $config_grid = $this->_config_grid_schedule();
        $this->_data['data'] = $diklat_data = $this->_model()->diklat_schedule($this->pagination->per_page, $offset, $count);

        $this->load->library('xgrid', $config_grid, 'listing_grid');
        $this->load->library('pagination');
        $param = array(
            'total_rows' => $count,
            'per_page' => $this->pagination->per_page,
            'base_url' => site_url('diklat/diklat_schedule_listing')
        );

        $this->pagination->initialize($param);
    }

    function diklat_schedule($id = null){
        $diklats = $this->_model('diklat')->find(null, array('id' => 'asc'));
        foreach ($diklats as $diklat) {
            $this->_data['diklats'][$diklat['id']] = $diklat['name'];
        }

        if($_POST){
            if(is_null($id)){
                $id = $this->add_post(array('id'=>$id,'year'=>$_POST['year']));
                foreach ($_POST['diklat_id'] as $key => $value) {
                    $data = array(
                        'diklat_id' => $value,
                        'diklat_schedule_id' => $id
                    );
                    $this->_model()->before_save($data);
                    $this->db->trans_start();
                    $this->db->insert('diklat_schedule_item',$data);
                    $this->db->trans_complete();
                }
            } else {
                $this->db->query('UPDATE diklat_schedule SET year = ? WHERE id = ?',array($_POST['year'],$id));

                if($_POST['diklat_id']){
                    $this->db->trans_start();
                    $this->db->query('DELETE FROM diklat_schedule_item WHERE diklat_schedule_id = ?',array($id));
                    $this->db->trans_complete();

                    foreach ($_POST['diklat_id'] as $key => $value) {
                        $data = array(
                            'diklat_id' => $value,
                            'diklat_schedule_id' => $id
                        );
                        $this->db->trans_start();
                        $this->_model()->before_save($data);
                        $this->db->insert('diklat_schedule_item',$data);
                        $this->db->trans_complete();
                    }
                }
            }
            redirect($this->_get_uri('diklat_schedule_listing'));
        } else {
            if($id){
                $_POST['diklat_id'] = array();
                $items = $this->db->query('SELECT * FROM diklat_schedule_item WHERE diklat_schedule_id = ?',array($id))->result_array();
                if(!empty($items)){
                    foreach ($items as $key => $value) {
                        $_POST['diklat_id'][] = $value['diklat_id'];
                    }
                }

                $getdata = $this->db->query('SELECT year FROM diklat_schedule WHERE status !=0 AND id = ?',array($id))->row_array();
                $_POST['year'] = $getdata['year'];
            }
        }
    }

    function add_post($post_data){
        $this->db->trans_start();
        $this->_model()->before_save($post_data);
        $this->db->insert('diklat_schedule',$post_data);
        $id = (empty($id)) ? $this->_model()->_db()->insert_id() : $id;
        $insert_id = $this->_model()->_db()->insert_id();
        $this->db->trans_complete();

        return  $insert_id;
    }

    function trash_diklat_schedule($id){
        $data = array( 'status' => 0 );
        $this->_model()->before_save($data, $id);
        if (is_array($id)) {
            $this->_model()->_db()->where_in('id', $id)->update('diklat_schedule', $data);
            redirect($this->_get_uri('diklat_schedule_listing'));
        } else {
            $this->_model()->_db()->where('id', $id)->update('diklat_schedule', $data);
            redirect($this->_get_uri('diklat_schedule_listing'));
        }
    }

    function cheat(){

        $marine_inspector_radio = array(
            "120152406",
            "196010121982021001",
            "196406161989031001",
            "196509121989031002",
            "196703061988031003",
            "196704211991031003",
            "196712311989031009",
            "196803021993121001",
            "196804011989031002",
            "196809151989031003",
            "196811091990031003",
            "196912241997031002",
            "197004101997031001",
            "197004251994032002",
            "197011161993031002",
            "197012311997031007",
            "197103051993031001",
            "197106271996032001",
            "197111202002121001",
            "197111O71997031001",
            "197209172007121001",
            "1973061720071001",
            "197311122008121003",
            "197401041997031001",
            "197408022002121001",
            "197501082007121001",
            "197501212002122002",
            "197505021997032001",
            "197509202007121001",
            "197603082008121001",
            "197608251997031001",
            "197706162008121001",
            "197706182005052001",
            "197707142008122002",
            "197708182006041002",
            "19771115200122003",
            "197805172006041003",
            "197907012008121001",
            "197910032009121001",
            "197910182006041001",
            "198005302007122001",
            "198007112007121001",
            "198011012007121007",
            "198011102002121003",
            "198101012005021001",
            "198106042009121003",
            "198111032010121002",
            "198202222008121002",
            "198210112005021001",
            "198304042009121001",
            "198411242010121006",
            "198505232008121002",
            "198705262010122007",
            "198712152009121001",
            "198808012008121001",
            "199004162008121001",
            "199105092010121004",
            "199203022010121003"
        );


        $sbnp_terampil = array(
            "199804012006041002",
            "199105242009121001",
            "198603042006041001",
            "198411152002121004",
            "198307192002121001",
            "198304142009121002",
            "197903052006041001",
            "197404071997031002",
            "197203011992031002",
            "197104191990031001",
            "197001211994031001",
            "197001181990031002",
            "197001171990031001",
            "19700112199703101",
            "196909191989031001",
            "196907071991031002",
            "196901261992031001",
            "196809051989031003",
            "196808161990091001",
            "196805141997031001",
            "196804131998031001",
            "196606281993121001",
            "196411111983021001",
            "196312311986031012",
            "196212311986031006",
            "196205051986031003",
            "196202221986031003",
            "196201271983021002",
            "19612311987031003",
            "196001011982021001",
            "120140309",
            "198601122006041002"
        );


        $auditor_ism_code = array(
            "198609302010121008",
            "198502132007121001",
            "198410282002121004",
            "198310072002121004",
            "198303182007121001",
            "198302152007121001",
            "198209292003121003",
            "198207112007121001",
            "198205122007121002",
            "198203182003121001",
            "198201132009121001",
            "198201112006041003",
            "198112032005022002",
            "198111172007122001",
            "198110082005021001",
            "198010122008121001",
            "198008312008121001",
            "198008252008122002",
            "198006222008121001",
            "197912162002121002",
            "197912042007121001",
            "197910022008121001",
            "197909282003121001",
            "197909112009121004",
            "197908242208121001",
            "197907312009121001",
            "197907032006041002",
            "197905102010121002",
            "197904112009122001",
            "197812062007121001",
            "197812052006041003",
            "197811242007121003",
            "197811062005022001",
            "197810112007121001",
            "197808312006041001",
            "197804162006041003",
            "197803182008121001",
            "197802022006041001",
            "197712062009121001",
            "197711292007121001",
            "197710192009121001",
            "197709082008121001",
            "197708102007121002",
            "197707302007121002",
            "197707152007121002",
            "197707152007121001",
            "197706182005052001",
            "197706122005121001",
            "197705272010121001",
            "197705272010121001",
            "197703292007122001",
            "197701102007121001",
            "197611101998031002",
            "197610242006041001",
            "197610142007121002",
            "197607252009121001",
            "197606282007121001",
            "197604122008121001",
            "197604122005011013",
            "197604062003121001",
            "197540822008121001",
            "197511212008121002",
            "197510182003121001",
            "197509112007121001",
            "197508282009121002",
            "197507222003121001",
            "197505122006042001",
            "197504222007121001",
            "197503072009122001",
            "197501082007121001",
            "197412202007121001",
            "197412012008121001",
            "197407122006041001",
            "197407122006041001",
            "197401092003121001",
            "197311272003121001",
            "1973061720071001",
            "197303162007121001",
            "197301122009121001",
            "1972051820033121003",
            "197205162006041001",
            "197111072006042001",
            "197109291998032001",
            "197104251998081001",
            "197011232008121002",
            "197011232003121001",
            "197004222003121001",
            "197002011991021001",
            "19702006041001",
            "196812161988031001",
            "196806101989031002",
            "196711191989031003",
            "196611181988031001",
            "196512081992032001",
            "196403141993031001",
            "196312291990031001",
            "196307121983011001",
            "196303271984011001",
            "196111181981031001",
            "196102081994031001",
            "195811211990031002",
            "195811211981031003",
            "120167480",
            "120165980",
            "120162357",
            "197606202007121001",
            "19810404200712"
        );


        $pengawas_pandu = array(
            "199004262010121007",
            "198910122010121005",
            "198507172005021001",
            "198501182007121002",
            "198411182008121002",
            "198405032005021001",
            "198303182007121001",
            "198203182003121001",
            "198112222009121002",
            "198112032005022002",
            "198111272007121002",
            "198108052007121007",
            "198103232007122001",
            "198012132006041001",
            "198008252008122002",
            "198005282006041001",
            "197912102007121001",
            "197912042007121001",
            "197908242208121001",
            "197905312003121001",
            "197811062005022001",
            "197808312006041001",
            "197808162003121002",
            "197806242003121003",
            "197805122007121001",
            "197802212005021001",
            "197711202008121001",
            "197711202008121001",
            "197706152007121002",
            "197705302006041001",
            "197612022006041002",
            "197609292007121003",
            "197609142007122001",
            "197608252009121001",
            "197608082008121001",
            "197604262006041002",
            "197512272006041001",
            "197511212008121002",
            "197507151997031001",
            "197505122006042001",
            "197502032006041002",
            "197408242005021001",
            "197402242007121001",
            "197208092007121001",
            "197205172003121001",
            "197205162006041001",
            "197202241997031002",
            "197107082002121001",
            "197106252001121001",
            "197011232008121002",
            "197002011991021001",
            "19702006121001",
            "196512081992032001",
            "196505311992031001",
            "196307271987031003",
            "196211011983021001",
            "196012291980031001",
            "196005041989031001",
            "195901261980031001",
            "195809241980031001",
            "198101312007121002",
            "196612251989031003",
            "198204032007121001",
            "198111102007121001",
            "198110082005021001",
            "198105162009121002",
            "197909212007122001",
            "197909172003121002",
            "197909062006041002",
            "197906102003121001",
            "197710282007121001",
            "197601182007121001",
            "197511112003122004",
            "197406042002121001",
            "197212032005021001",
            "197109011991121001",
            "196800613188031001",
            "196112281983021002",
        );



        $barang_dan_jasa = array(
            "189605252006041003",
            "190605052007121001",
            "192010192006042001",
            "196004131983021001",
            "19611051994032001",
            "196208041998032002",
            "196309231990031001",
            "196404231992031002",
            "196404241991021001",
            "196406141990032001",
            "196504041991031002",
            "196505271991032001",
            "196507181998031001",
            "196603101991031001",
            "196604071992031003",
            "196604281991031001",
            "196606161993121001",
            "196611071994031001",
            "196611121991031004",
            "196703151991031001",
            "196706061998031001",
            "196706101993031001",
            "196708251991011001",
            "196709221991031004",
            "196710011990091001",
            "196712311991031017",
            "196713211993031009",
            "196805302007121001",
            "196806101989031002",
            "196810241993032001",
            "196812041989031002",
            "196812311993031003",
            "196902201991031001",
            "19690323199707031002",
            "196907211991031002",
            "196910081991031002",
            "196911172002122002",
            "196912131991031001",
            "196912261993031001",
            "197001241994032003",
            "197002181991032002",
            "197002281998021001",
            "197004121992032002",
            "197005201991121001",
            "197005201992031001",
            "197009261992031002",
            "197010101993091001",
            "197011011993032002",
            "197012041992031003",
            "197104151994121001",
            "197106111997032001",
            "1971070319931003",
            "197109121993031002",
            "197109131998032001",
            "197110181992031002",
            "197201151994031001",
            "197202221998032002",
            "197204062002121002",
            "197205201997031002",
            "197208251993031001",
            "197211042005021001",
            "19721151994031001",
            "197212311993021001",
            "19726121996031001",
            "197301242003122001",
            "197303031998031003",
            "197306052007121001",
            "1973080719990311001",
            "197308171998032002",
            "197310201997032001",
            "197402061997031001",
            "197404062005021001",
            "197405062008122001",
            "197407101997031001",
            "197407122006041001",
            "197409211998031001",
            "197409301990031001",
            "197410292002122001",
            "197502112003121001",
            "197503312006041001",
            "197505282007121001",
            "197506152008121002",
            "197508282009121002",
            "197509082008122001",
            "197511292008121001",
            "197601302010121002",
            "197602062009121001",
            "197603102010122002",
            "197603252003122001",
            "197605162006041002",
            "197606132010122005",
            "197606162002122001",
            "197606172006041002",
            "197607262006041001",
            "197608031997031003",
            "197608132010121001",
            "197608202010121003",
            "197608222006041001",
            "197609091999032001",
            "197610082002122002",
            "197610142006041001",
            "197610192007121001",
            "197610252002121001",
            "197611102005021001",
            "197704182010122001",
            "197704272002122002",
            "197705052007121001",
            "197707262006041001",
            "197708042006121001",
            "197709231998031001",
            "197711122008121003",
            "197712202008122001",
            "197802132002121002",
            "197805032005022001",
            "197806302005021002",
            "197807052007121001",
            "197807172006041002",
            "197808012006041001",
            "197810242006041002",
            "197811062003122001",
            "197812052010122003",
            "197812262010121001",
            "197901022010121001",
            "19790108200505021001",
            "197901202003121003",
            "197903012012121001",
            "197903112005021001",
            "197905092002122001",
            "197909142007121001",
            "197910072007121003",
            "197910082007121002",
            "197911012008122001",
            "197911052009121003",
            "197911172006041003",
            "197912132006041002",
            "197912262009121003",
            "197912282006041002",
            "198002142007121003",
            "198002152005021001",
            "198004282006042001",
            "198005132010121002",
            "198006032006041001",
            "198006052006041003",
            "198006062007122001",
            "198007012006041001",
            "198008122006041001",
            "198009232010121001",
            "198009292006041001",
            "198011042009122002",
            "198012152005121001",
            "198101062007121004",
            "198101102005021001",
            "198101132005020001",
            "198102052005021001",
            "198103182008122002",
            "198103302006041001",
            "198103302006041001",
            "198103302008121001",
            "198104042007121002",
            "198104062006041004",
            "198104142007121001",
            "198105122010122002",
            "198105232002121003",
            "198105232008121002",
            "198107122010121001",
            "198107242005022001",
            "198107252007121001",
            "198107262009122001",
            "198108012010122003",
            "198108132005022002",
            "198108142010121004",
            "198108202006041002",
            "198110032008121002",
            "198110302010121004",
            "198111072010122002",
            "198111192006041002",
            "198203132002121003",
            "198205142005022001",
            "1982062006041002",
            "198206042010122001",
            "198206082006121006",
            "198207302002121001",
            "198208062009122004",
            "198209072005021001",
            "198211032005021001",
            "198211092006041002",
            "198211132006041001",
            "198212022006041002",
            "198301182007121002",
            "198302072010122001",
            "198303232003121001",
            "198304072002121004",
            "198304252006041002",
            "198304292009121007",
            "198306052003122001",
            "198306192010121004",
            "198307052007122002",
            "198307072006041001",
            "198308222010121001",
            "198309212009122003",
            "198310191010121004",
            "198311282005022002",
            "198311282005022002",
            "198311292002121002",
            "198312172011012001",
            "198401162005022001",
            "198402012003122005",
            "198402192010121002",
            "198403102006042002",
            "198404111010121004",
            "198404152005021001",
            "198404202010121007",
            "198406042007121001",
            "198406212010122007",
            "198407102003122001",
            "198407192003121007",
            "198407242006042002",
            "198409152006041001",
            "198410012002121003",
            "198411051010122002",
            "198412012010121003",
            "198412122009121003",
            "198501312010121003",
            "198502032007121003",
            "1985021220010121001",
            "198502122010121002",
            "198502182009122002",
            "198503122006041002",
            "198503142010121003",
            "198504012010122004",
            "198504142010121002",
            "198505252008121001",
            "198505282007121001",
            "198506292006041002",
            "198507062007122001",
            "198507232006041002",
            "198508162005021001",
            "198508242007121001",
            "198601162010122008",
            "198603192005021001",
            "198605272008041003",
            "198606262010121001",
            "198607192009121007",
            "198609092010122005",
            "198609202005022001",
            "198610292010122003",
            "198611042010122002",
            "198611052009122003",
            "198611292010122007",
            "198611602006042001",
            "198612032009121005",
            "198702112007121002",
            "198702252009121002",
            "198704222010122007",
            "198705262009122002",
            "198707142010121001",
            "198708312006041002",
            "198710102009121004",
            "198711232007121003",
            "198712232010121005",
            "198802032009122002",
            "198803102008121001",
            "198804302010121007",
            "198805152007122001",
            "198807062010122002",
            "198808032009122002",
            "198808312010121001",
            "1988100220008121001",
            "198810052009121006",
            "198812252008121001",
            "198901022007122001",
            "198902042009121001",
            "1989021120101002",
            "198904062010121004",
            "198904122007121001",
            "198908182007121001",
            "199006072010121001",
            "199008102009121001",
            "199010082010121002",
            "199011132010121003",
            "199012272010122002",
            "199102052010121003",
            "199103101992032001",
            "199105242009121001",
            "199110011993032001",
            "199207152010122001",
            "197311251994032002",
            "1992101220102002",
            "1997409142002121001",
            "199012022009121001",
            "19750430200212001",
            "120156347",
            "120162230",
            "19810404200712"
        );

        foreach ($marine_inspector_radio as $key => $value) {
            $employee = $this->_model()->query('SELECT * FROM employee WHERE nip = ?',array($value))->row_array();

            if(!empty($employee)){
                $data_diklat = array(
                    'diklat_id' => 3,
                    'employee_id' => $employee['id'],
                    'year' => 2014
                );

                // $this->_model()->before_save($data_diklat);
                // $this->db->insert('diklat_order',$data_diklat);
            } else {
                xlog($value);
            }
        }
        exit;

    }

    // function cheat(){
        // ini_set("memory_limit",-1);
        // $this->db->trans_start();
        // $data = $this->_model()->_db('pusdatin')->query('SELECT DISTINCT nama, nip, golongan, jabatan, kerjaunit, pensiun, tgl_lahir
        //                                                  FROM pegawai
        //                                                  WHERE kerjaunit LIKE ?
        //                                                  ',array('L%'))->result_array();
        // $this->db->trans_complete();
        // xlog(count($data));
        // exit;
        // for ($i=0; $i < count($data); $i++) {
        //     $level = array(
        //         'employee_name' => $data[$i]['nama'],
        //         'nip' => $data[$i]['nip'],
        //         'group_id' => $data[$i]['golongan'],
        //         'position' => $data[$i]['jabatan'],
        //         'unit_of_work' => $data[$i]['kerjaunit'],
        //         'pension_status' => $data[$i]['pensiun'],
        //         'born_date' => $data[$i]['tgl_lahir']
        //     );

        //     $this->_model()->before_save($level);
        //     $this->db->insert('employee', $level);
        // }
    // }

    function get_diklat($year = 0) {

        $result = $this->db->query('SELECT DISTINCT d.*
                                    FROM diklat_schedule ds
                                    LEFT JOIN diklat_schedule_item dsi ON ds.id = dsi.diklat_schedule_id
                                    LEFT JOIN diklat d ON dsi.diklat_id = d.id
                                    WHERE ds.year = ?',array($year))->result_array();

        if(empty($result)){
            $result = $this->db->query('SELECT * FROM diklat WHERE status !=0')->result_array();
        }

        echo json_encode($result);
        exit;
    }
}



















