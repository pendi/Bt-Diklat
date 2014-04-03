<?php
class diklat_model extends app_base_model{
	
    function order_list($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {

        $where = '';
        $params = array();
        $limit_str = '';
        $order_str = '';

        if(!empty($filter)){
            $where[] = 'do.status !=0';
            foreach ($filter as $key => $value) {
                if(!empty($value)){
                    if($key == 'diklat_id' || $key == 'year'){
                        $where[] = $key . ' = ?';
                        $params[] = $value;
                    } else {
                        $where[] = $key . ' LIKE ?';
                        $params[] = $value.'%';
                    }
                }
            }
            $where = ' WHERE '.implode(' AND ', $where);
        } else {
            $where = ' WHERE do.status !=0';
        }


        $select_count = 'SELECT COUNT(do.id) count, e.employee_name, e.nip, e.position, e.unit_of_work, d.name, do.year, do.diklat_approval, w.unit_name, e.id employee_id, d.id diklat_id';
        $select = 'SELECT do.id,e.employee_name, e.nip, e.position, e.unit_of_work, d.name, do.year, do.diklat_approval, w.unit_name, e.id employee_id, d.id diklat_id';
        $from = ' FROM diklat_order do';
        $join[] = ' LEFT JOIN diklat d ON d.id = do.diklat_id';
        $join[] = 'LEFT JOIN employee e ON e.id = do.employee_id';
        $join[] = 'LEFT JOIN work_unit w ON w.unit_code = e.unit_of_work';

        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $orders[] = $key.' '.(($value) ? $value : 'ASC');
            }
            $order_str = ' ORDER BY '.implode(', ', $orders);
        }

        $sql = $from.implode(' ', $join).$where.$order_str;
        $count = $this->db->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }

        $result = $this->db->query($select.$sql.$limit_str,$params)->result_array();
        return $result;
    }

    function training_participant($filter = null, $sort = null, $limit = null, $offset = null, &$count = 0) {
        $where = '';
        $params = array();
        $limit_str = '';
        $order_str = '';

        if(!empty($filter)){
            foreach ($filter as $key => $value) {
                if(!empty($value)){
                    $where[] = $key . ' LIKE ?';
                    $params[] = $value.'%';
                }
            }
            $where = ' WHERE ( '.implode(' AND ', $where) .' ) AND diklat_approval = 1 ';
        } else {
            $where = ' WHERE diklat_approval = 1';
        }

        $select_count = 'SELECT COUNT(do.id) count, e.employee_name, e.nip, e.position, e.unit_of_work, d.name, do.year, do.diklat_approval';
        $select = 'SELECT do.id,e.employee_name, e.nip, e.position, e.unit_of_work, d.name, do.year, do.diklat_approval, wu.unit_name, g.group_name';
        $from = ' FROM diklat_order do';
        $join[] = ' LEFT JOIN diklat d ON d.id = do.diklat_id';
        $join[] = 'LEFT JOIN employee e ON e.id = do.employee_id';
        $join[] = 'LEFT JOIN work_unit wu ON e.unit_of_work = wu.unit_code';
        $join[] = 'LEFT JOIN groups g ON e.group_id = g.id';

        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                $orders[] = $key.' '.(($value) ? $value : 'ASC');
            }
            $order_str = ' ORDER BY '.implode(', ', $orders);
        }


        $sql = $from.implode(' ', $join).$where.$order_str;
        $count = $this->db->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }
        $result = $this->db->query($select.$sql.$limit_str,$params)->result_array();
        return $result;
    }

	function get_diklat($id){
		$sql = "SELECT d.id AS d_id, do.id AS do_id, e.employee_name AS e_name, e.id AS e_id, e.* 
				FROM diklat_order do 
				JOIN employee e ON do.employee_id = e.id 
				LEFT JOIN diklat d ON d.id = do.diklat_id
				WHERE do.id = ? ";
		$result = $this->db->query($sql, array($id))->row_array();
		return $result;
	}
    
	function insert_order_diklat($data){
        $this->db->trans_start();
        $this->before_save($data);
        $this->db->insert('diklat_order', $data); 
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return  $insert_id;
	}

    function diklat_history() {
        
    }

    function diklat_schedule($limit = null, $offset = null, &$count = 0){
        $params = array();
        $limit_str = '';
        
        $select_count = 'SELECT COUNT(ds.id) count';
        $select = 'SELECT ds.*';

        $sql = ' FROM diklat_schedule ds WHERE ds.status != 0 ';
        $order = ' ORDER BY ds.year DESC ';
        $count = $this->db->query($select_count.$sql, $params)->row()->count;

        if (!empty($limit)) {
            $limit_str = ' LIMIT ?, ?';
            $params[] = intval($offset);
            $params[] = intval($limit);
        }
        $result = $this->_db()->query($select.$sql.$order.$limit_str, $params)->result_array();
        return $result;
    }
	
}














