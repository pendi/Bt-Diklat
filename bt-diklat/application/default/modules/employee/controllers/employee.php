<?php

class employee extends app_crud_controller {

    function _config_grid() {
        $config = parent::_config_grid();
        $config['fields'] = array('employee_name','nip','position','group_name','rank','pension_status','unit_of_work');
        $config['names'] = array('Nama Pegawai', 'NIP','Jabatan','Golongan','Pangkat','Pensiun','Unit Kerja');
        $config['formats'] = array('row_detail','','','','','callback__pension','callback__unit_of_work');
        return $config;
    }

    function _config_grid_roleback() {
        $config = parent::_config_grid();
        $config['fields'] = array('employee_name','nip','position','group_name','rank','pension_status','unit_of_work');
        $config['names'] = array('Nama Pegawai', 'NIP','Jabatan','Golongan','Pangkat','Pensiun','Unit Kerja');
        $config['formats'] = array('row_detail','','','','','callback__pension','callback__unit_of_work');
        $config['actions'] = array(
            'refresh' => $this->_get_uri('roleback')
        );
        return $config;
    }

    function _pension($v){
        if(empty($v)){
            return $v;
        } else {
            return 'Sudah Pensiun';
        }
    }

    function _unit_of_work($v){
        $unit_of_work = $this->db->query('SELECT * FROM work_unit WHERE unit_code = ?',array($v))->row_array();
        if(empty($unit_of_work)){
            return $v;
        }
        return $unit_of_work['unit_name'];
    }

    function _save($id = null) {
        $this->_view = $this->_name . '/show';

        $group_names = $this->_model()->get_group_name();
        $this->_data['group_name'] = array('' => 'Pilih Golongan');
        foreach ($group_names as $key => $cn) {
            $this->_data['group_name'][$cn['id']] = $cn['group_name'].' - '.$cn['rank'];
        }

        $level_education = $this->db->query('SELECT * FROM master_level_education')->result_array();
        $this->_data['level_of_education'] = array('' => 'Pilih Pendidikan Terakhir');
        foreach ($level_education as $key => $le) {
            $this->_data['level_of_education'][$le['level_code']] = $le['level_name'];
        }

        if ($_POST) {

            if(!empty($_FILES['image']['name'])){
                $config['upload_path'] = 'employee';
                $config['allowed_types'] = 'png|jpg|jpeg|gif';
                $config['field'] = 'image';
                $config['encrypt_name'] = 'TRUE';
                $this->load->library('upload',$config);
            }

            if ($this->_validate()) {

                if($id){
                    $data_formal_education = array(
                        'nip' => $_POST['nip'],
                        'majors' => $_POST['majors'],
                        'year_pass' => $_POST['year_pass'],
                        'name_of_school' => $_POST['name_of_school'],
                        'desc_of_education' => $_POST['desc_of_education'],
                        'level_of_education' => $_POST['level_of_education']
                    );
                    $this->_model()->before_save($data_formal_education);
                    $this->db->where('nip',$_POST['nip']);
                    $this->db->update('formal_education',$data_formal_education);
                } else {
                    $data_formal_education = array(
                        'nip' => $_POST['nip'],
                        'majors' => $_POST['majors'],
                        'year_pass' => $_POST['year_pass'],
                        'name_of_school' => $_POST['name_of_school'],
                        'desc_of_education' => $_POST['desc_of_education'],
                        'level_of_education' => $_POST['level_of_education']
                    );
                    $this->_model()->before_save($data_formal_education);
                    $this->db->insert($data_formal_education);
                }

                unset($_POST['majors']);
                unset($_POST['year_pass']);
                unset($_POST['name_of_school']);
                unset($_POST['desc_of_education']);
                unset($_POST['level_of_education']);

                $_POST['id'] = $id;
                $_POST['unit_of_work'] = $_POST['unit_code'];
                unset($_POST['unit_code']);

                if(!empty($_FILES['image']['name'])){
                    $image = $this->upload->data();
                    $_POST['image'] = $image[0]['file_name'];
                }

                try {
                    $this->_model()->save($_POST, $id);
                    $this->session->unset_userdata('referrer');
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
                $_POST = $this->_model()->get_data($id);
                $this->_data['img'] = $_POST['image'];

                if (empty($_POST)) {
                    show_404($this->uri->uri_string);
                }
            }
            $this->load->library('user_agent');
            $this->session->set_userdata('referrer', $this->agent->referrer());
        }
        $this->_data['fields'] = $this->_model()->list_fields(true);
    }

	function employee_option($q = '') {
        $q = (!empty($_GET['q'])) ? $_GET['q'] : $q;
        $rows = $this->db->query('SELECT nip, id, employee_name, position FROM employee WHERE employee_name LIKE ? LIMIT 10', array('%'.$q.'%'))->result_array();


        foreach ($rows as $row) {
            echo sprintf("%s|%s|%s|%s|%s\n", $row['nip'].' - '.$row['employee_name'], $row['id'], $row['nip'], $row['position'], $row['employee_name']);
        }
        exit;
    }

    function order_employee_option($q = '') {
        $q = (!empty($_GET['q'])) ? $_GET['q'] : $q;
        $rows = $this->db->query('SELECT e.nip,
                                         e.id,
                                         e.employee_name,
                                         e.position,
                                         e.group_id,
                                         e.image,
                                         e.born_date,
                                         g.group_name,
                                         g.rank,
                                         wu.unit_code,
                                         fe.level_of_education,
                                         fe.majors,
                                         fe.year_pass,
                                         fe.name_of_school,
                                         fe.desc_of_education
                                  FROM employee e
                                  LEFT JOIN groups g ON e.group_id = g.id
                                  LEFT JOIN work_unit wu ON e.unit_of_work = wu.unit_code
                                  LEFT JOIN formal_education fe ON e.nip = fe.nip
                                  WHERE e.employee_name LIKE ?
                                  LIMIT 15', array('%'.$q.'%'))->result_array();

        foreach ($rows as $row) {

            $born = $row['born_date'];
            $now = date('Y-m-d');
            $age = date_diff(date_create($now),date_create($born));
            $row['age'] = $age->y;

            $row['born_date'] = date("d/m/Y", strtotime($row['born_date']));

            echo sprintf("%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s|%s\n",
                $row['nip'].' - '.$row['employee_name'],
                $row['id'],
                $row['nip'],
                $row['position'],
                $row['employee_name'],
                $row['group_name'],
                $row['rank'],
                $row['group_id'],
                $row['image'],
                $row['unit_code'],
                $row['level_of_education'],
                $row['majors'],
                $row['year_pass'],
                $row['name_of_school'],
                $row['desc_of_education'],
                $row['age'],
                $row['born_date']
            );
        }
        exit;
    }

    function get_certificate($employee_id){
        header('content-type:application/json');

        $this->db->trans_start();
        $certificates = $this->db->query('SELECT c.certificate_no, c.certificate
                                          FROM certificate c
                                          WHERE c.status !=0
                                          AND c.employee_id = ?',array($employee_id))->result_array();
        $this->db->trans_complete();

        echo json_encode($certificates);
        exit;

    }

    function unit_work_options($q = '') {
        $q = (!empty($_GET['q'])) ? $_GET['q'] : $q;
        $rows = $this->db->query('SELECT unit_name, unit_code FROM work_unit WHERE status !=0 AND unit_name LIKE ? ', array('%' . $q . '%'))->result_array();
        foreach ($rows as $row) {
            echo sprintf("%s|%s\n", $row['unit_name'],$row['unit_code']);
        }
        exit;
    }

    function detail($id) {
        $this->load->helper('format');
        $this->_data['fields'] = $this->_model()->list_fields(true);
        $this->_data['data'] = $data = $this->_model()->get_data($id);
        $this->_data['diklat'] = $this->db->query('SELECT * FROM diklat_history WHERE nip = ?',array($data['nip']))->result_array();

    }

    function delete_image($id){
        $image = $this->db->query('SELECT * FROM employee WHERE id = ?',array($id))->row_array();
        unlink('data/employee/image/' . $image['image']);

        $this->db->query('UPDATE employee SET image = "" WHERE id = ?', array($id));
        redirect(site_url('employee/edit' .'/'. $id ));
    }

    function roleback_employee($offset = 0) {
        $this->load->library('pagination');

        $config_grid = $this->_config_grid_roleback();
        $config_grid['sort'] = $this->_get_sort();

        $filter = $this->_get_filter();

        $count = 0;
        $this->_data['data'] = array();
        $this->_data['data']['items'] = $this->_model()->roleback_employee($filter, $config_grid['sort'], $this->pagination->per_page, $offset, $count);
        $this->_data['filter'] = $filter;
        $this->load->library('xgrid', $config_grid, 'listing_grid');

        $this->load->library('pagination');
        $param = array(
            'total_rows' => $count,
            'per_page' => $this->pagination->per_page,
            'base_url' => site_url('employee/roleback_employee')
        );
        if (!empty($_GET)) {
            $param['suffix'] = '?'.http_build_query($_GET, '', '&');
        }
        $this->pagination->initialize($param);
    }

    function roleback($id){
        if (!isset($id)) {
            show_404($this->uri->uri_string);
        }

        $id = explode(',', $id);
        $this->_model()->roleback($id);
        redirect($this->_get_uri('roleback_employee'));








    }

}



















