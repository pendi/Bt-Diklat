<?php

/**
 * site.php
 *
 * @package     arch-php
 * @author      xinixman <xinixman@xinix.co.id>
 * @copyright   Copyright(c) 2012 PT Sagara Xinix Solusitama.  All Rights Reserved.
 *
 * Created on 2011/11/21 00:00:00
 *
 * This software is the proprietary information of PT Sagara Xinix Solusitama.
 *
 * History
 * =======
 * (dd/mm/yyyy hh:mm:ss) (author)
 * 2011/11/21 00:00:00   xinixman <xinixman@xinix.co.id>
 *
 *
 */

class site extends app_base_controller {

    function index() {
    	/** Get Diklat Code **/
        $diklats = $this->db->query('SELECT * FROM diklat WHERE status !=0')->result_array();
    	foreach ($diklats as $key => $value) {
    		$diklat[] = array(
    			'name' => $value['name'],
    			'code' => $value['code']
    		);
            $diklat_code[] = $value['code'];
    	}
    	$this->_data['diklats'] = $diklat;
        $this->_data['diklat_code'] = json_encode($diklat_code);
        /** Get Diklat Code **/

        /** Generate Years **/
        $this_year = date('Y') - 4;
        for ($i=0; $i < 5; $i++) { 
            $allyear[] = $this_year + $i;
        }
        $this->_data['allyear'] = json_encode($allyear);
        /** Generate Years **/

        /** Get Data For Chart1 **/
        $get_order = $this->db->query('SELECT d.code, count(do.diklat_id) count
                                       FROM diklat d 
                                       LEFT JOIN diklat_order do on d.id = do.diklat_id
                                       WHERE d.status !=0
                                       AND do.year = ?
                                       GROUP BY d.code',array(date('Y')))->result_array();

        for ($i=0; $i < count($get_order); $i++) { 
            foreach ($get_order[$i] as $k => $v) {
                if($k == 'code'){
                    $get_order[$i][] = $v;
                } else {
                    $get_order[$i][] = (int)$v;
                }
                unset($get_order[$i][$k]);
            }
        }
        $this->_data['diklat_data'] = json_encode($get_order);
        /** Get Data For Chart1 **/
        
        /** Get Data For Chart 2 **/
        foreach ($diklats as $key => $value) {
            $color = '#'.substr(md5(rand()), 0, 6);

            $get_history_by_diklat_id = $this->db->query('SELECT * FROM new_diklat_history WHERE diklat_id = ?',array($value['id']))->result_array();
            $count_by_diklat_id = count($get_history_by_diklat_id);
            
            for ($i=0; $i < count($allyear) ; $i++) { 
                $data_by_year = $this->db->query('SELECT * FROM new_diklat_history WHERE diklat_id = ? AND year = ?',array($value['id'],$allyear[$i]))->result_array();
                $count_data_by_year[$i] = count($data_by_year);
            }

            if($count_by_diklat_id !== 0){
                $data[] = array(
                    'y' => $count_by_diklat_id,
                    'color' => $color,
                    'drilldown' => array(
                        'name' => 'PUTRA',
                        'categories' => $allyear,
                        'data' => $count_data_by_year,
                        'color' => $color
                    ),
                );
            }
        }
        $this->_data['data_participant'] = json_encode($data);
        /** Get Data For Chart 2 **/

        /** Get Data For Chart 3 **/
        $count_data_by_year2 = array();
        foreach ($allyear as $key => $y) {
            for($i=0;$i<count($diklats);$i++){
                $data_by_year2 = $this->db->query('SELECT COUNT(*) count FROM new_diklat_history WHERE diklat_id = ? AND year = ?',array($diklats[$i]['id'],$y))->row()->count;
                $count_data_by_year2[$y][] = (int)$data_by_year2;
                $coba[] = $diklats[$i]['code'];
            }
            if(isset($count_data_by_year2[$y])){
                $data2[] = array(
                    'name' => $y,
                    'data' => $count_data_by_year2[$y]
                );
            } else {
                $data2[] = array(
                    'name' => $y,
                    'data' => array(0)
                );
            }
        }
        $this->_data['data_participant2'] = json_encode($data2);
        /** Get Data For Chart 3 **/

        /** Get Data For Chart4 **/
        $organizer = $this->db->query('SELECT * FROM organizer WHERE status !=0')->result_array();
        foreach ($organizer as $key => $value) {
            $color = '#'.substr(md5(rand()), 0, 6);
            $get_participant = $this->db->query('SELECT d.*
                                                 FROM diklat d
                                                 WHERE d.organizer_id = ?',array($value['id']))->result_array();


            if(count($get_participant) !== 0){
                $count_participant = 0;
                for ($i=0; $i < count($get_participant); $i++) { 
                    $get_data_participant = $this->db->query('SELECT * FROM new_diklat_history WHERE diklat_id = ?',array($get_participant[$i]['id']))->result_array();
                    $get_count_participant = count($get_data_participant);
                    $count_participant += $get_count_participant;
                }
                if($count_participant !== 0){
                    $data_organizer[] = array(
                        'name' => $value['organizer_name'],
                        'data' => array($count_participant),
                        'color' => $color
                    );
                }
            }
        }
        $this->_data['data_organizer'] = json_encode($data_organizer);
        /** Get Data For Chart4 **/
    }

    function get_data($year){
        header('content-type:application/json');

        $this->db->trans_start();
        $get_order = $this->db->query('SELECT d.code, count(do.diklat_id) count
                                       FROM diklat d 
                                       LEFT JOIN diklat_order do on d.id = do.diklat_id
                                       WHERE d.status !=0
                                       AND do.year = ?
                                       GROUP BY d.code',array($year))->result_array();
        $this->db->trans_complete();

        for ($i=0; $i < count($get_order); $i++) { 
            foreach ($get_order[$i] as $k => $v) {
                if($k == 'code'){
                    $get_order[$i][] = $v;
                } else {
                    $get_order[$i][] = (int)$v;
                }
                unset($get_order[$i][$k]);
            }
        }

        echo json_encode($get_order);
        exit;
    }

    function search(){
        $this->_data['tables'] = array(
            '' => 'Pilih Table',
            'diklat' => 'Diklat',
            'employee' => 'Pegawai',
            'diklat_order' => 'Daftar Usulan Diklat',
            'diklat_order_by_status' => 'Daftar Panggilan Peserta Diklat' 
        );

        $organizers = $this->_model('organizer')->find(null, array('organizer_name' => 'asc'));  
        
        $this->_data['organizer'] = array('' => 'Pilih Lembaga Diklat');
        foreach ($organizers as $org) {
            $this->_data['organizer'][$org['id']] = $org['organizer_name'];
        }

        // dropdown for golongan
        $groups = $this->_model('groups')->find(null, array('group_name' => 'asc'));  
        
        $this->_data['group'] = array('' => 'Pilih Golongan');
        foreach ($groups as $group) {
            $this->_data['group'][$group['id']] = $group['group_name'] . ' - ' .$group['rank'];
        }
    }

    function advance_search(){

        /** Create Diklat Dropdown **/
        $diklats = $this->_model('diklat')->find(null, array('id' => 'asc'));
        $this->_data['diklats'] = array('' => 'Pilih Diklat');
        foreach ($diklats as $diklat) {
            $this->_data['diklats'][$diklat['id']] = strtoupper($diklat['name']);
        }

        /** Create Groups Dropdown **/
        $groups = $this->_model('groups')->find(null, array('id' => 'asc'));
        $this->_data['groups'] = array('' => 'Pilih Golongan');
        foreach ($groups as $group) {
            $this->_data['groups'][$group['id']] = $group['group_name'] . ' - ' . $group['rank'];
        }

        /** Create Formal Education Dropdown **/
        $educations = $this->db->query('SELECT * FROM master_level_education WHERE status != 0')->result_array();
        $this->_data['formal_education'] = array('' => 'Pilih Pendidikan Terakhir');
        foreach ($educations as $education) {
            $this->_data['formal_education'][$education['level_code']] = $education['level_name'];
        }

        /** Create Certificate Dropdown **/
        $certificates = $this->_model('master_certificate')->find(null, array('id' => 'asc'));
        $this->_data['certificate'] = array('' => 'Pilih Ijazah Kelautan');
        foreach ($certificates as $certificate) {
            $this->_data['certificate'][$certificate['id']] = $certificate['certificate_name'];
        }
    }

    function get_advance_search($offset){
        header('content-type:application/json');

        $params = array();
        $by = array();

        if(isset($_POST['select'])){
            $select = $_POST['select'];
            $select = 'SELECT DISTINCT '.implode(',', $select);
            unset($_POST['select']);
        } else {
            $select = 'SELECT DISTINCT(e.nip), e.employee_name,e.born_date,g.group_name,g.rank,e.position,w.unit_name,e.pension_status';
        }
        
        $operator = $_POST['operator'].' ';
        unset($_POST['operator']);

        $participant_type = $_POST['participant_type'];
        unset($_POST['participant_type']);

        $join = '';
        if($participant_type == ''){
            if(!empty($_POST['diklat_id'])){
                $keys = 'do-diklat_id';
                $_POST[$keys] = $_POST['diklat_id'];
                unset($_POST['diklat_id']);
                $join = ' INNER JOIN diklat_order do ON e.id = do.employee_id
                          LEFT JOIN diklat d ON d.id = do.diklat_id';
            }

            if(!empty($_POST['year'])){
                $keys = 'do-year';
                $_POST[$keys] = $_POST['year'];
                unset($_POST['year']);
            }
        } else if($participant_type == 0){
            if(!empty($_POST['diklat_id'])){
                $keys = 'do-diklat_id';
                $_POST[$keys] = $_POST['diklat_id'];
                unset($_POST['diklat_id']);
            }

            if(!empty($_POST['year'])){
                $keys = 'do-year';
                $_POST[$keys] = $_POST['year'];
                unset($_POST['year']);
            }
            $_POST['do-diklat_approval'] = $participant_type;
            $join = ' INNER JOIN diklat_order do ON e.id = do.employee_id
                      LEFT JOIN diklat d ON d.id = do.diklat_id';
        } else if($participant_type == 1){
            if(!empty($_POST['diklat_id'])){
                $keys = 'do-diklat_id';
                $_POST[$keys] = $_POST['diklat_id'];
                unset($_POST['diklat_id']);
            }

            if(!empty($_POST['year'])){
                $keys = 'do-year';
                $_POST[$keys] = $_POST['year'];
                unset($_POST['year']);
            }
            $_POST['do-diklat_approval'] = $participant_type;
            $join = ' INNER JOIN diklat_order do ON e.id = do.employee_id
                      LEFT JOIN diklat d ON d.id = do.diklat_id';
        } else if($participant_type == 2){
            if(!empty($_POST['diklat_id'])){
                $keys = 'ndh-diklat_id';
                $_POST[$keys] = $_POST['diklat_id'];
                unset($_POST['diklat_id']);
            }

            if(!empty($_POST['year'])){
                $keys = 'ndh-year';
                $_POST[$keys] = $_POST['year'];
                unset($_POST['year']);
            }

            $join = ' INNER JOIN new_diklat_history ndh ON ndh.nip = e.nip
                      LEFT JOIN diklat d ON d.id = ndh.diklat_id';
        }

        $join = $join . ' LEFT JOIN certificate c ON e.id = c.employee_id
                          LEFT JOIN work_unit w ON e.unit_of_work = w.unit_code
                          LEFT JOIN formal_education f ON e.nip = f.nip
                          LEFT JOIN master_level_education mle ON mle.level_code = f.level_of_education
                          LEFT JOIN groups g ON g.id = e.group_id';

        $where = '';
        foreach ($_POST as $key => $v) {
            if(!empty($v) || $key == 'do-diklat_approval'){

                $key = str_replace('-', '.', $key);
                $fields[$key] = $v;
                
                if($key == 'e.pension_status'){
                    if($v == 1){
                        $where[] = $key.' = ? ';
                        $params[] = '';
                    } else {
                        $where[] = $key.' <> ? ';
                        $params[] = '';
                    }
                } else if($key == 'e.group_id'){
                    if(!empty($key)){
                        $where[] = $key.' = ? ';
                        $params[] = $v;    
                    }
                } else if($key == 'c.certificate'){
                    if(!empty($key)){
                        $where[] = $key.' = ? ';
                        $params[] = $v;    
                    }
                } else if($key == 'do.diklat_id'){
                    if(!empty($key)){
                        $where[] = $key.' = ? ';
                        $params[] = $v;    
                    }
                } else if($key == 'do.diklat_approval'){
                    if(!empty($key)){
                        $where[] = $key.' = ? ';
                        $params[] = $v;    
                    }
                } else if($key == 'f.level_of_education'){
                    if(!empty($key)){
                        $where[] = $key.' = ? ';
                        $params[] = $v;    
                    }
                } else if($key == 'e.born_date'){
                    if(!empty($key)){
                        foreach ($v as $key => $born_year) {
                            if($born_year){
                                $by[] = $born_year;
                            }
                        }
                        if(count($by) > 1){
                            $where[] = "DATE_FORMAT(e.born_date, '%Y') BETWEEN ? AND ?";
                            $params[] = $by[0];        
                            $params[] = $by[1];
                        } elseif(count($by) == 1){
                            $where[] = "DATE_FORMAT(e.born_date, '%Y') = ?";
                            $params[] = $by[0];
                        } else {
                            
                        }
                    }
                } else {
                    $where[] = $key.' LIKE ? ';
                    $params[] = '%'.$v.'%';
                }
            }
        }
        
        if($where){
            $where = ' WHERE '.implode($operator, $where);
        }

        $from = ' FROM employee e';
        $limit = ' LIMIT ?, ?';
        $params[] = intval($offset);
        $params[] = intval(20);
        $sql = $select.$from.$join.$where.' ORDER BY g.id '.$limit;

        $result = $this->db->query($sql,$params)->result_array();

        if(empty($result)){
            $result[0]['nip'] = '';
            $result[0]['employee_name'] = '';
            $result[0]['born_date'] = '';
            $result[0]['group_name'] = '';
            $result[0]['rank'] = '';
            $result[0]['position'] = '';
            $result[0]['unit_name'] = '';
            $result[0]['pension_status'] = '';
        }

        $this->session->set_userdata('advance_search', $this->db->last_query());

        echo json_encode($result);
        exit;
    }

    function export_result(){

        $query = $this->session->userdata('advance_search');
        $query = explode('LIMIT', $query);
        $query = $query[0];

        if(empty($query)){
            add_error('Cari data terlebih dahulu');
            redirect($this->_get_uri('advance_search'));
        }

        $label = array();
        $abjad = array('C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

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

        $per_page = 1000;
        $offset = 0;
        $z = 0;
        $c = 6;

        do {
            $data_export = $this->db->query($query.' LIMIT ?, ?',array($offset,$per_page))->result_array();
            
            if(!empty($data_export)){
                /** Get Label **/
                $a = 0;
                $b = 1;
                for ($i=0;$i<count($data_export);$i++) {
                    if($z == 0){
                        $labels = array_keys($data_export[$i]);
                        foreach ($labels as $key => $value) {
                            if($value == 'nip'){
                                $label = str_replace($value,'NIP', $value);
                            } elseif($value == 'employee_name'){
                                $label = str_replace($value,'Nama Karyawan', $value);
                            } elseif($value == 'group_name') {
                                $label = str_replace($value,'Golongan', $value);
                            } elseif($value == 'rank') {
                                $label = str_replace($value,'Pangkat', $value);
                            } elseif($value == 'position') {
                                $label = str_replace($value,'Jabatan', $value);
                            } elseif($value == 'born_date'){
                                $label = str_replace($value,'Tanggal Lahir', $value);
                            } elseif($value == 'unit_name') {
                                $label = str_replace($value,'Unit Kerja', $value);
                            } elseif($value == 'pension_status'){
                                $label = str_replace($value,'Status Pensiun', $value);
                            } elseif($value == 'level_name'){
                                $label = str_replace($value,'Pendidikan Terakhir', $value);
                            } elseif($value == 'name'){
                                $label = str_replace($value,'Jenis Diklat', $value);
                            } elseif($value == 'year'){
                                $label = str_replace($value,'Tahun', $value);
                            }
                            $this->phpexcel->setActiveSheetIndex(0)->setCellValue($abjad[$a].'4', $label);
                            $this->phpexcel->setActiveSheetIndex(0)->setCellValue($abjad[$a].'5', $b);
                            
                            $this->phpexcel->getActiveSheet()->getStyle($abjad[$a].'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                            $this->phpexcel->getActiveSheet()->getStyle($abjad[$a].'4')->applyFromArray($style['border']);
                            $this->phpexcel->getActiveSheet()->getStyle($abjad[$a].'5')->applyFromArray($style['border']);
                            
                            $this->phpexcel->getActiveSheet()->getStyle($abjad[$a].'4')->applyFromArray($style['header']);
                            $this->phpexcel->getActiveSheet()->getStyle($abjad[$a].'5')->applyFromArray($style['header']);

                            $a++;
                            $b++;
                        }
                    }

                    $d = 0;
                    foreach ($data_export[$i] as $key => $val) {
                        if($key == 'nip'){
                            $this->phpexcel->setActiveSheetIndex(0)->setCellValue($abjad[$d].$c, "'".$val);
                        } else {
                            $this->phpexcel->setActiveSheetIndex(0)->setCellValue($abjad[$d].$c, $val);
                        }
                        
                        $this->phpexcel->getActiveSheet()->getStyle($abjad[$d].$c)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                        $this->phpexcel->getActiveSheet()->getStyle($abjad[$d].$c)->applyFromArray($style['border']);
                        $d++;
                    }
                    $c++;
                    $z++;
                }
            }
            $offset = $offset + $per_page;
        } while(!empty($data_export));

        $this->session->unset_userdata('advance_search');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Panggilan Peserta Diklat.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter->save('php://output');
        exit;
    }
}































