<?php

namespace App\Models;

class Merchant_types_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'merchant_types';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $merchant_types_table = $this->db->prefixTable('merchant_types');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $merchant_types_table.id=$id";
        }

        $sql = "SELECT $merchant_types_table.*
        FROM $merchant_types_table
        WHERE $merchant_types_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
