<?php

namespace App\Models;

class Training_budget_payers_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'training_budget_payers';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $training_budget_payers_table = $this->db->prefixTable('training_budget_payers');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $training_budget_payers_table.id=$id";
        }

        $sql = "SELECT $training_budget_payers_table.*
        FROM $training_budget_payers_table
        WHERE $training_budget_payers_table.deleted=0 $where";
        return $this->db->query($sql);
    }
}
