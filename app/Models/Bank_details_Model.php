<?php

namespace App\Models;

class Bank_details_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'bank_details';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $bank_details_table = $this->db->prefixTable('bank_details');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $bank_details_table.id=$id";
        }

        $sql = "SELECT $bank_details_table.*
        FROM $bank_details_table
        WHERE $bank_details_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
