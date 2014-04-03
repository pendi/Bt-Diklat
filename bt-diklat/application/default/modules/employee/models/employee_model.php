<?php
class employee_model extends app_base_model{
	
	function update_employee($data){
		$CI = &get_instance();
		$user = $CI->auth->get_user();
		$now = date(l('format.mysql_datetime'));

		$user_id = (isset($user['id'])) ? $user['id'] : 0;
		$data['updated_time'] = $now;
		$data['updated_by'] = $user_id;
		$this->db->where('id', $data['id']);
		$this->db->update('employee', $data); 
	}

	function insert_employee($data){

		$CI = &get_instance();
		$user = $CI->auth->get_user();
		$now = date(l('format.mysql_datetime'));

		$user_id = (isset($user['id'])) ? $user['id'] : 0;
		$data['created_time'] = $data['updated_time'] = $now;
		$data['created_by'] = $data['updated_by'] = $user_id;
		$data['status'] = 1;
		$this->db->insert('employee', $data); 
	}

	function get_data($id){
        $employee = $this->db->query('SELECT e.*, g.group_name, g.rank, w.unit_name unit_of_work, w.unit_code, fe.majors, fe.year_pass, fe.name_of_school, fe.desc_of_education, fe.level_of_education, mle.level_name
                                      FROM employee e
                                      LEFT JOIN formal_education fe ON e.nip = fe.nip
                                      LEFT JOIN groups g ON g.id = e.group_id
                                      LEFT JOIN work_unit w ON w.unit_code = e.unit_of_work
                                      LEFT JOIN master_level_education mle ON mle.level_code = fe.level_of_education
                                      WHERE e.id = ?',array($id))->row_array();

		return $employee;
	}

	function get_group_name(){
		$class_name = $this->db->query('SELECT * FROM groups WHERE status !=0')->result_array();
		return $class_name;
	}

	function find($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $params = array();
        $where_str = '';
        $order_str = '';
        $limit_str = '';
        $wheres = array();
        $filter_wheres = array();
        $orders = array();

        $select_count = 'SELECT COUNT(e.id) count, g.group_name, g.rank';
        $select = 'SELECT e.*, g.group_name, g.rank';

        $user = $this->auth->get_user();
        if (!$user['is_top_member']) {
            $fields = $this->list_fields();
            if (!empty($fields['organization_id'])) {
                if (!empty($user['organization'])) {
                    $wheres[] = 'organization_id = ?';
                    $params[] = $user['organization'][0]['id'];
                } else {
                    $wheres[] = '0';
                }
            }
        }

        $wheres[] = 'e.status != 0';

        if (!empty($filter) && !is_array($filter)) {
            $wheres[] = 'id = ?';
            $params[] = $filter;
        } elseif (is_array($filter)) {
            unset($filter['q']);
            foreach($filter as $k => $f) {
                $filter_wheres[] = $k.' LIKE ?';
                $params[] = '%'.$this->db->escape_like_str($f).'%';
            }
            if (!empty($filter_wheres)) {
                $wheres[] = '('. implode(' OR ', $filter_wheres) .')';
            }
        }

        if (!empty($wheres)) {
            $where_str = ' WHERE '.implode(' AND ', $wheres);
        }

        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $orders[] = $key.' '.(($value) ? $value : 'ASC');
            }
            $order_str = ' ORDER BY '.implode(', ', $orders);
        }

        $join = ' LEFT JOIN groups g ON g.id = e.group_id';
        $sql = ' FROM employee e'.$join.$where_str.$order_str;
        $count = $this->_db()->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }
        $result = $this->_db()->query($select.$sql.$limit_str, $params)->result_array();
        return $result;
    }

    function roleback_employee($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $params = array();
        $where_str = '';
        $order_str = '';
        $limit_str = '';
        $wheres = array();
        $filter_wheres = array();
        $orders = array();

        $select_count = 'SELECT COUNT(*) count';
        $select = 'SELECT *';

        $user = $this->auth->get_user();
        if (!$user['is_top_member']) {
            $fields = $this->list_fields();
            if (!empty($fields['organization_id'])) {
                if (!empty($user['organization'])) {
                    $wheres[] = 'organization_id = ?';
                    $params[] = $user['organization'][0]['id'];
                } else {
                    $wheres[] = '0';
                }
            }
        }

        $wheres[] = 'status = 0';

        if (!empty($filter) && !is_array($filter)) {
            $wheres[] = 'id = ?';
            $params[] = $filter;
        } elseif (is_array($filter)) {
            unset($filter['q']);
            foreach($filter as $k => $f) {
                $filter_wheres[] = $k.' LIKE ?';
                $params[] = '%'.$this->db->escape_like_str($f).'%';
            }
            if (!empty($filter_wheres)) {
                $wheres[] = '('. implode(' OR ', $filter_wheres) .')';
            }
        }

        if (!empty($wheres)) {
            $where_str = ' WHERE '.implode(' AND ', $wheres);
        }

        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $orders[] = $key.' '.(($value) ? $value : 'ASC');
            }
            $order_str = ' ORDER BY '.implode(', ', $orders);
        }

        $table_name = $this->_db()->_protect_identifiers($this->_db()->dbprefix . $this->_name);
        $sql = ' FROM '.$table_name.$where_str.$order_str;
        $count = $this->_db()->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }
        $result = $this->_db()->query($select.$sql.$limit_str, $params)->result_array();
        return $result;
    }

    function roleback($id) {
        $data = array( 'status' => 1 );
        $this->before_save($data, $id);
        if (is_array($id)) {
            $this->_db()->where_in('id', $id)->update('employee', $data);
        } else {
            $this->_db()->where('id', $id)->update('employee', $data);
        }
    }



}







