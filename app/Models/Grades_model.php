<?php

namespace App\Models;

class Grades_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'grades';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $grades_table = $this->db->prefixTable('grades');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $grades_table.id=$id";
        }

        $sql = "SELECT $grades_table.*
        FROM $grades_table
        WHERE $grades_table.deleted=0 $where";
        return $this->db->query($sql);
    }
}
