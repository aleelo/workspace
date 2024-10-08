<?php

namespace App\Models;

class Training_funders_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'training_funders';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $training_funders_table = $this->db->prefixTable('training_funders');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $training_funders_table.id=$id";
        }

        $sql = "SELECT $training_funders_table.*
        FROM $training_funders_table
        WHERE $training_funders_table.deleted=0 $where";
        return $this->db->query($sql);
    }
}
