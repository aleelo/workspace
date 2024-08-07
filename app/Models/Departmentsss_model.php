<?php

namespace App\Models;

class Departments_model extends Crud_model {

    protected $table = 'departments';

    function __construct() {
        parent::__construct($this->table);
        // die($this->table.','.$this->table_without_prefix.',');
    }

    
    function get_details($options = array()) {
        $departments_table = $this->db->prefixTable('departments');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $departments_table.id=$id";
        }

        $name = $this->_get_clean_value($options, "name");
        if ($name) {
            $where .= " AND $departments_table.nameSo = '$name' or nameEn = '$name'";
        }

        $head = $this->_get_clean_value($options, "head");
        if ($head) {
            $where .= " AND CONCAT($users_table.first_name,' ',$users_table.last_name) = '$head' or head_id = '$users_table.id'";
        }

        $email = $this->_get_clean_value($options, "email");
        if ($email) {
            $where .= " AND $users_table.email = '$email'";
        }
        
        $sql = "SELECT $departments_table.*,CONCAT($users_table.first_name,' ',$users_table.last_name) head FROM $departments_table
        LEFT JOIN $users_table ON $users_table.id = $departments_table.head_id
        WHERE $departments_table.deleted=0 $where
        ORDER BY $departments_table.id ASC";

        return $this->db->query($sql);
    }
    
}
