<?php

namespace App\Models;

class Merchant_details_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'merchant_details';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $merchant_details_table = $this->db->prefixTable('merchant_details');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $merchant_details_table.id=$id";
        }

        $sql = "SELECT $merchant_details_table.*
        FROM $merchant_details_table
        WHERE $merchant_details_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
