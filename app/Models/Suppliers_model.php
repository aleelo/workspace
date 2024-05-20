<?php

namespace App\Models;

class Suppliers_model extends Crud_model {

    protected $table = 'suppliers';

    function __construct() {
        $this->table = 'suppliers';
        parent::__construct($this->table);
    }

    
    function get_details($options = array()) {
        $suppliers_table = $this->db->prefixTable('suppliers');
        $users_table = $this->db->prefixTable('users');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $suppliers_table.id=$id";
        }
        
        $sql = "SELECT $suppliers_table.* FROM $suppliers_table
        WHERE $suppliers_table.deleted=0 $where
        ORDER BY $suppliers_table.id DESC";

        return $this->db->query($sql);
    }
    
}
