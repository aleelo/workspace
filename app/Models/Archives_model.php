<?php

namespace App\Models;

class General_files_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'archive_files';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $archive_files_table = $this->db->prefixTable('archive_files');
        $users_table = $this->db->prefixTable('users');
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $archive_files_table.id=$id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where = " AND $archive_files_table.client_id=$client_id";
        }

        $user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where = " AND $archive_files_table.user_id = $user_id";
        }

        $department_id = $this->_get_clean_value($options, "department_id");
        if ($department_id) {
            $where = " AND $archive_files_table.department_id like '$department_id'";
        }



        $sql = "SELECT $archive_files_table.*, d.nameSo as department, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS uploaded_by_user_name, $users_table.image AS uploaded_by_user_image, $users_table.user_type AS uploaded_by_user_type
        FROM $archive_files_table
        LEFT JOIN $users_table ON $users_table.id= $archive_files_table.uploaded_by
        LEFT JOIN departments d ON $archive_files_table.department_id = d.id
        WHERE $archive_files_table.deleted=0 $where";
        return $this->db->query($sql);
    }

}
