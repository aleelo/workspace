<?php

namespace App\Models;

class Trainers_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'trainers';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $trainers_table = $this->db->prefixTable('trainers');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $trainers_table.id=$id";
        }

        $sql = "SELECT $trainers_table.*
        FROM $trainers_table
        WHERE $trainers_table.deleted=0 $where";
        return $this->db->query($sql);
    }
}
