<?php

namespace App\Models;

class Training_locations_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'training_locations';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $training_locations = $this->db->prefixTable('training_locations');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $training_locations.id=$id";
        }

        $sql = "SELECT $training_locations.*
        FROM $training_locations
        WHERE $training_locations.deleted=0 $where";
        return $this->db->query($sql);
    }
}
