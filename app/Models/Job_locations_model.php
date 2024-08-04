<?php

namespace App\Models;

class Job_locations_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'job_locations';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $job_locations_table = $this->db->prefixTable('job_locations');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $job_locations_table.id=$id";
        }

        $sql = "SELECT $job_locations_table.*
        FROM $job_locations_table
        WHERE $job_locations_table.deleted=0 $where";
        return $this->db->query($sql);
    }
}
