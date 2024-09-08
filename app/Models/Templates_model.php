<?php

namespace App\Models;

class Templates_model extends Crud_model {

    protected $table = 'templates';

    function __construct() {
        
        parent::__construct($this->table);

    }

    function get_templates_dropdown_permission($options = array()) {
        $templates_table = $this->db->prefixTable('templates');
        $department_table = $this->db->prefixTable('departments');
        $sections_table = $this->db->prefixTable('sections');
        $units_table = $this->db->prefixTable('units');
        $users_table = $this->db->prefixTable('users');
        

        $documents_table = $this->db->prefixTable('documents');
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $clients_table = $this->db->prefixTable('clients');
        $partners_table = $this->db->prefixTable('partners');
        $roles_table = $this->db->prefixTable('roles');
        
        $Users_model = model("App\Models\Users_model");
        $login_user_id = $Users_model->login_user_id();
        $user = $Users_model->get_access_info($login_user_id);

        $Roles_model = model("App\Models\Roles_model");
        
        $r = $Roles_model->get_one($user->role_id);

        if($user->is_admin){
            $role = 'Admin';
        }else{
            $role = $r->title;
        }

        $role = get_array_value($options,'role') ?? '%';
        $created_by = get_array_value($options,'created_by') ?? '%';
        $department_id = get_array_value($options,'department_id') ?? '%';

        
        // if($role == 'Head Department'){
        //     $department_id = get_user_department_id();
        //     $created_by = "%";
        // }

        // if($role == 'Head Department'){
        //     $department_id = get_dept_id_of_Head_list();
        //     $created_by = "%";
        // }


        $where = "";
        $id = $this->_get_clean_value($options, "id");
        $status = $this->_get_clean_value($options, "status");
        // $user_type = $this->_get_clean_value($options, "user_type");
        $client_id = $this->_get_clean_value($options, "client_id");
        $partner_id = $this->_get_clean_value($options, "partner_id");
        $exclude_user_id = $this->_get_clean_value($options, "exclude_user_id");
        $first_name = $this->_get_clean_value($options, "first_name");
        $last_name = $this->_get_clean_value($options, "last_name");

        if ($id) {
            $where .= " AND $templates_table.id=$id";
        }
        

      

        $non_admin_users_only = $this->_get_clean_value($options, "non_admin_users_only");
        if ($non_admin_users_only) {
            $where .= " AND $templates_table.is_admin=0";
        }

      

        $show_own_unit_documents_only_user_id = $this->_get_clean_value($options, "show_own_unit_documents_only_user_id");
        if ($show_own_unit_documents_only_user_id) {
            $where .= " AND ($units_table.unit_head_id=$show_own_unit_documents_only_user_id)";
        }
        
        $show_own_section_documents_only_user_id = $this->_get_clean_value($options, "show_own_section_documents_only_user_id");
        if ($show_own_section_documents_only_user_id) {
            $where .= " AND ($sections_table.section_head_id=$show_own_section_documents_only_user_id)";
        }

        $show_own_department_documents_only_user_id = $this->_get_clean_value($options, "show_own_department_documents_only_user_id");
        if ($show_own_department_documents_only_user_id) {
            $where .= " AND ($department_table.dep_head_id=$show_own_department_documents_only_user_id)";
        }

        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }




            $sql = "SELECT SQL_CALC_FOUND_ROWS $templates_table.*
            FROM $templates_table
            LEFT JOIN $department_table ON $department_table.id = $templates_table.department_id
            LEFT JOIN $users_table as us_dp ON us_dp.id = $department_table.dep_head_id
            LEFT JOIN $sections_table ON $sections_table.id = $templates_table.section_id
            LEFT JOIN $users_table as us_se ON us_se.id = $sections_table.section_head_id
            LEFT JOIN $units_table ON $units_table.id = $templates_table.unit_id
            LEFT JOIN $users_table as us_un ON us_un.id = $units_table.unit_head_id
              
            WHERE $templates_table.deleted=0 AND destination_folder != 'Leave' $where ";
          
           
        // print_r($sql);
        // die();
        
            $raw_query = $this->db->query($sql);

            $total_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->getRow();

            if ($limit) {
                return array(
                    "data" => $raw_query->getResult(),
                    "recordsTotal" => $total_rows->found_rows,
                    "recordsFiltered" => $total_rows->found_rows,
                );
            } else {
                return $raw_query;
            }

    }

    
}
