<?php

namespace App\Models;

class Bank_names_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'bank_names';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $bank_names_table = $this->db->prefixTable('bank_names');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $bank_names_table.id=$id";
        }

        $sql = "SELECT $bank_names_table.*
        FROM $bank_names_table
        WHERE $bank_names_table.deleted=0 $where";
        return $this->db->query($sql);
    }
}
