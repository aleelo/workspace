<?php

namespace App\Models;

class Team_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'team';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $team_table = $this->db->prefixTable('team');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $team_table.id=$id";
        }

        $sql = "SELECT $team_table.*
        FROM $team_table
        WHERE $team_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_members($team_ids = array()) {
        $team_table = $this->db->prefixTable('team');
        $team_ids = implode(",", $team_ids);

        $sql = "SELECT $team_table.members
        FROM $team_table
        WHERE $team_table.deleted=0 AND id in($team_ids)";
        return $this->db->query($sql);
    }

    function get_departments_for_select(){
        // $depts = $this->db->table('departments')->select('id,nameEn')->get();
        $depts = $this->db->query('select id,nameEn from rise_departments');

        if(!$depts){
            return [];
        }else{
            $depts = $depts->getResult();
            $data = array();
            foreach($depts as $d){
                $data[$d->id] = $d->nameEn;
            }

            return $data;
        }
    }
}
