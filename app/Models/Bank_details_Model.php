<?php

namespace App\Models;

class Bank_details_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'bank_details';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $social_links_table = $this->db->prefixTable('bank_details');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $social_links_table.id=$id";
        }

        $sql = "SELECT $social_links_table.*
        FROM $social_links_table
        WHERE $social_links_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
