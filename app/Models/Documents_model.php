<?php

namespace App\Models;

class Documents_model extends Crud_model {

    protected $table = 'documents';

    function __construct() {
        $this->table = 'documents';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $documents_table = $this->db->prefixTable('documents');
        $templates_table = $this->db->prefixTable('templates');
        $users_table = $this->db->prefixTable('users');
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $clients_table = $this->db->prefixTable('clients');
        $partners_table = $this->db->prefixTable('partners');
        $roles_table = $this->db->prefixTable('roles');
        $department_table = $this->db->prefixTable('departments');
        $sections_table = $this->db->prefixTable('sections');
        $units_table = $this->db->prefixTable('units');
        
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
            $where .= " AND $documents_table.id=$id";
        }
        if ($status) {
            $where .= " AND $documents_table.status='$status'";
        }

        // if ($user_type) {
        //     $where .= " AND $documents_table.user_type='$user_type'";
        // }

        // if ($user_type == 'client') {
        //     $where .= " AND $clients_table.deleted=0";
        // }

        if ($first_name) {
            $where .= " AND $documents_table.first_name='$first_name'";
        }

        if ($last_name) {
            $where .= " AND $documents_table.last_name='$last_name'";
        }

        if ($client_id) {
            $where .= " AND $documents_table.client_id=$client_id";
        }

        if ($partner_id) {
            $where .= " AND $documents_table.partner_id=$partner_id";
        }

        if ($exclude_user_id) {
            $where .= " AND $documents_table.id!=$exclude_user_id";
        }

        $non_admin_users_only = $this->_get_clean_value($options, "non_admin_users_only");
        if ($non_admin_users_only) {
            $where .= " AND $documents_table.is_admin=0";
        }

        // $show_own_clients_only_user_id = $this->_get_clean_value($options, "show_own_clients_only_user_id");
        // if ($user_type == "client" && $show_own_clients_only_user_id) {
        //     $where .= " AND $documents_table.client_id IN(SELECT $clients_table.id FROM $clients_table WHERE $clients_table.deleted=0 AND $clients_table.created_by=$show_own_clients_only_user_id)";
        // }

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

        $quick_filter = $this->_get_clean_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $documents_table);
        }

        $client_groups = $this->_get_clean_value($options, "client_groups");
        if ($client_groups) {
            $client_groups_where = $this->prepare_allowed_client_groups_query($clients_table, $client_groups);
            if ($client_groups_where) {
                $where .= " AND $documents_table.client_id IN(SELECT $clients_table.id FROM $clients_table WHERE $clients_table.deleted=0 $client_groups_where)";
            }
        }

        // if($user_type =='staff') {
        //     $where .=" AND $documents_table.id LIKE '$created_by' AND $team_member_job_info_table.department_id like '$department_id'";
        // }else{

        // }

        // $custom_field_type = "team_members";
        // if ($user_type === "client") {
        //     $custom_field_type = "client_contacts";
        // } else if ($user_type === "lead") {
        //     $custom_field_type = "lead_contacts";
        // }

        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }

        // $available_order_by_list = array(
        //     "fullName" => $cardholders_table . ".fullName",
        //     "CID" => $cardholders_table . ".CID",
        //     "titleEng" => $cardholders_table . ".titleEng",
        //     "titleSom" => $cardholders_table . ".titleSom",
        //     "status" => $cardholders_table . ".status"
        // );


        // $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));

        // $order = "ORDER BY $documents_table.document_title";

        // if ($order_by) {
        //     $order_dir = $this->_get_clean_value($options, "order_dir");
        //     $order = " ORDER BY $order_by $order_dir ";
        // }

        $search_by = get_array_value($options, "search_by");
        if ($search_by) {
            $search_by = $this->db->escapeLikeString($search_by);

            $where .= " AND (";
            $where .= " $documents_table.job_title LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $documents_table.email LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $documents_table.private_email LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $documents_table.phone LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $documents_table.skype LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " $documents_table.bachelor_degree LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " $documents_table.master_degree LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " $documents_table.employee_id LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " $documents_table.age_level LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR $clients_table.company_name LIKE '%$search_by%' ESCAPE '!' ";
            $where .= " OR CONCAT($documents_table.first_name, ' ', $documents_table.last_name) LIKE '%$search_by%' ESCAPE '!' ";
            $where .= $this->get_custom_field_search_query($documents_table, "client_contacts", $search_by);
            $where .= " )";
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        // $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $documents_table, $custom_field_filter);
        // $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        // $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        // $custom_fields_where = get_array_value($custom_field_query_info, "where_string");
       
        //prepare full query string

        //if( $role == 'Head Department'){

            $sql = "SELECT SQL_CALC_FOUND_ROWS $documents_table.*,$templates_table.name as template,$department_table.nameSo as department,
            $sections_table.nameSo as section,
            concat($users_table.first_name,' ',$users_table.last_name) user
            FROM $documents_table
            LEFT JOIN $templates_table ON $templates_table.id = $documents_table.template
            LEFT JOIN $users_table ON $users_table.id = $documents_table.created_by
            LEFT JOIN $department_table ON $department_table.id = $templates_table.department_id
            LEFT JOIN $users_table as users_dp ON users_dp.id = $department_table.dep_head_id
            LEFT JOIN $sections_table ON $sections_table.id = $templates_table.section_id
            LEFT JOIN $users_table as users_se ON users_se.id = $sections_table.section_head_id
            LEFT JOIN $units_table ON $units_table.id = $templates_table.unit_id
            LEFT JOIN $users_table as users_un ON users_un.id = $units_table.unit_head_id
              
            WHERE $documents_table.deleted=0 $where 
            $limit_offset";
           
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
