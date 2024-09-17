<?php

namespace App\Models;

class Leave_applications_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'leave_applications';
        parent::__construct($this->table);
    }

    function get_details_info($id = 0) {
        $leave_applications_table = $this->db->prefixTable('leave_applications');
        $users_table = $this->db->prefixTable('users');
        $leave_types_table = $this->db->prefixTable('leave_types');

        $sql = "SELECT $leave_applications_table.*, 
                CONCAT(applicant_table.first_name, ' ',applicant_table.last_name) AS applicant_name, applicant_table.image as applicant_avatar, applicant_table.job_title,
                CONCAT(checker_table.first_name, ' ',checker_table.last_name) AS checker_name, checker_table.image as checker_avatar,
                $leave_types_table.title as leave_type_title,   $leave_types_table.color as leave_type_color
            FROM $leave_applications_table
            LEFT JOIN $users_table AS applicant_table ON applicant_table.id= $leave_applications_table.applicant_id
            LEFT JOIN $users_table AS checker_table ON checker_table.id= $leave_applications_table.checked_by
            LEFT JOIN $leave_types_table ON $leave_types_table.id= $leave_applications_table.leave_type_id        
            WHERE $leave_applications_table.deleted=0 AND $leave_applications_table.id=$id";
        return $this->db->query($sql)->getRow();
    }

    public function get_director_department_id(){
        
        $Users_model = model("App\Models\Users_model");
        $user = $Users_model->get_access_info($Users_model->login_user_id());

        $dep_info = $this->db->query("SELECT dp.id FROM rise_departments dp LEFT JOIN rise_users us ON dp.dep_head_id = us.id WHERE us.id = $user->id")->getRow();

        return $dep_info?->id;
    }


    function get_list($options = array()) {

        $leave_applications_table = $this->db->prefixTable('leave_applications');
        $leave_types_table = $this->db->prefixTable('leave_types');
        $team_member_job_info_table = $this->db->prefixTable('team_member_job_info');
        $users_table = $this->db->prefixTable('users');
        $department_table = $this->db->prefixTable('departments');
        $sections_table = $this->db->prefixTable('sections');
        $units_table = $this->db->prefixTable('units');
        $where = "";
        
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $leave_applications_table.id=$id";
        }


        // //role and user info:
        // $Users_model = model("App\Models\Users_model");
        // $role = $Users_model->get_user_role();
        // $user = $Users_model->get_access_info($Users_model->login_user_id());

        // // die($role);
        // $dr_dp_id = $this->get_director_department_id();
        // $d = $this->db->query("SELECT t.department_id from rise_team_member_job_info t left join rise_users u on u.id=t.user_id where t.user_id = $user->id")->getRow();
        // $department_id = $d?->department_id;
        // $created_by = $user->id;

        // if($role == 'Employee'){
        //     $created_by = $user->id;
        // }elseif($role == 'Director'){
        //     if(!empty($dr_dp_id)){
        //         $department_id = $dr_dp_id;
        //         $created_by = '%';
        //     }
        // }elseif($role == 'Secretary'){
        //     $created_by = '%';
        // }elseif($role == 'HRM' || $role == 'Admin' || $role == 'Administrator'){
        //     $created_by = '%';
        //     $department_id = '%';
        // }

        $show_own_leaves_only_user_id = $this->_get_clean_value($options, "show_own_leaves_only_user_id");
        if ($show_own_leaves_only_user_id) {
            $where .= " AND ($leave_applications_table.applicant_id=$show_own_leaves_only_user_id)";
        }

        $show_own_unit_leaves_only_user_id = $this->_get_clean_value($options, "show_own_unit_leaves_only_user_id");
        if ($show_own_unit_leaves_only_user_id) {
            $where .= " AND ($units_table.unit_head_id=$show_own_unit_leaves_only_user_id)";
        }
        
        $show_own_section_leaves_only_user_id = $this->_get_clean_value($options, "show_own_section_leaves_only_user_id");
        if ($show_own_section_leaves_only_user_id) {
            $where .= " AND ($sections_table.section_head_id=$show_own_section_leaves_only_user_id)";
        }

        $show_own_department_leaves_only_user_id = $this->_get_clean_value($options, "show_own_department_leaves_only_user_id");
        if ($show_own_department_leaves_only_user_id) {
            $where .= " AND ($department_table.dep_head_id=$show_own_department_leaves_only_user_id";
            $where .= " OR $department_table.secretary_id=$show_own_department_leaves_only_user_id)";
        }

        $status = $this->_get_clean_value($options, "status");
        $view_type = get_array_value($options, 'view_type');

      
        // if($status ){
        //     if($view_type == 'pending_list'){
        //         $where .= " AND $leave_applications_table.status IN('pending','active')";
        //     }else{
        //         $where .= " AND $leave_applications_table.status='$status'";
        //     }
            
        // }

        // if($view_type == 'pending_list'){
        //     $where .= " AND $leave_applications_table.status IN('pending','active')";
        // }
        if($view_type == 'active_list'){
            $where .= " AND $leave_applications_table.status IN('active')";
        }
        if($view_type == 'pending_list'){
            $where .= " AND $leave_applications_table.status IN('pending')";
        }
        if($view_type == 'rejected_list'){
            $where .= " AND $leave_applications_table.status IN('rejected')";
        }
        if($view_type == 'approved_list'){
            $where .= " AND $leave_applications_table.status IN('approved')";
        }
            
        
        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        if ($start_date && $end_date) {
            $where .= " AND ($leave_applications_table.start_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $applicant_id = $this->_get_clean_value($options, "applicant_id");
        // if ($applicant_id) {
        //     $where .= " AND $leave_applications_table.applicant_id=$applicant_id";
        // }

        $today = get_today_date();
        $on_leave_today = $this->_get_clean_value($options, "on_leave_today");
        if ($on_leave_today) {
            $where .= " AND ('$today' BETWEEN $leave_applications_table.start_date AND $leave_applications_table.end_date)";
        }

        $access_type = $this->_get_clean_value($options, "access_type");
     
        // if (!$id && $access_type !== "all") {

        //     $allowed_members = $this->_get_clean_value($options, "allowed_members");
        //     if (is_array($allowed_members) && count($allowed_members)) {
        //         $allowed_members = join(",", $allowed_members);
        //     } else {
        //         $allowed_members = '0';
        //     }
        //     $login_user_id = $this->_get_clean_value($options, "login_user_id");
        //     if ($login_user_id) {
        //         $allowed_members .= "," . $login_user_id;
        //     }
        //     $where .= " AND $leave_applications_table.applicant_id IN($allowed_members)";
        // }
       
        // $where.= " AND $leave_applications_table.applicant_id like '$created_by' AND $team_member_job_info_table.department_id like '$department_id'";

        $sql = "SELECT $leave_applications_table.id, $leave_applications_table.start_date, $units_table.nameEn as unit_name, $sections_table.nameEn as section_name, 
                $department_table.nameEn as dp_name, $leave_applications_table.end_date, $leave_applications_table.total_hours,
                $leave_applications_table.total_days, $leave_applications_table.applicant_id, $leave_applications_table.status,
                CONCAT($users_table.first_name, ' ',$users_table.last_name) AS applicant_name, $users_table.image as applicant_avatar,
                $leave_types_table.title as leave_type_title,   $leave_types_table.color as leave_type_color,$leave_applications_table.leave_type_id,$leave_applications_table.uuid,
                $leave_applications_table.nolo_status
            FROM $leave_applications_table
            LEFT JOIN $users_table ON $users_table.id = $leave_applications_table.applicant_id
            LEFT JOIN $leave_types_table ON $leave_types_table.id = $leave_applications_table.leave_type_id 
            LEFT JOIN $team_member_job_info_table ON $team_member_job_info_table.user_id = $users_table.id
            LEFT JOIN $department_table ON $department_table.id = $team_member_job_info_table.department_id 
            LEFT JOIN $users_table as users_dp ON users_dp.id = $department_table.dep_head_id
            LEFT JOIN $users_table as us_dp_secretray ON us_dp_secretray.id = $department_table.secretary_id
            LEFT JOIN $sections_table ON $sections_table.id = $team_member_job_info_table.section_id
            LEFT JOIN $users_table as users_se ON users_se.id = $sections_table.section_head_id
            LEFT JOIN $units_table ON $units_table.id = $team_member_job_info_table.unit_id
            LEFT JOIN $users_table as users_un ON users_un.id = $units_table.unit_head_id      
            
            WHERE $leave_applications_table.deleted=0 $where order by start_date desc";

            // print_r($sql);
            // die();

        return $this->db->query($sql);

    }

    function get_summary($options = array()) {
        $leave_applications_table = $this->db->prefixTable('leave_applications');
        $users_table = $this->db->prefixTable('users');
        $leave_types_table = $this->db->prefixTable('leave_types');

        $where = "";

        $where .= " AND $leave_applications_table.status='approved'";


        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");

        if ($start_date && $end_date) {
            $where .= " AND ($leave_applications_table.start_date BETWEEN '$start_date' AND '$end_date') ";
        }

        $applicant_id = $this->_get_clean_value($options, "applicant_id");
        if ($applicant_id) {
            $where .= " AND $leave_applications_table.applicant_id=$applicant_id";
        }

        $leave_type_id = $this->_get_clean_value($options, "leave_type_id");
        if ($leave_type_id) {
            $where .= " AND $leave_applications_table.leave_type_id=$leave_type_id";
        }

        $access_type = $this->_get_clean_value($options, "access_type");

        if ($access_type !== "all") {

            $allowed_members = $this->_get_clean_value($options, "allowed_members");
            if (is_array($allowed_members) && count($allowed_members)) {
                $allowed_members = join(",", $allowed_members);
            } else {
                $allowed_members = '0';
            }
            $login_user_id = $this->_get_clean_value($options, "login_user_id");
            if ($login_user_id) {
                $allowed_members .= "," . $login_user_id;
            }
            $where .= " AND $leave_applications_table.applicant_id IN($allowed_members)";
        }


        $sql = "SELECT  SUM($leave_applications_table.total_hours) AS total_hours,
                SUM($leave_applications_table.total_days) AS total_days, MAX($leave_applications_table.applicant_id) AS applicant_id, $leave_applications_table.status,
                CONCAT($users_table.first_name, ' ',$users_table.last_name) AS applicant_name, $users_table.image as applicant_avatar,
                $leave_types_table.title as leave_type_title,   $leave_types_table.color as leave_type_color
            FROM $leave_applications_table
            LEFT JOIN $users_table ON $users_table.id= $leave_applications_table.applicant_id
            LEFT JOIN $leave_types_table ON $leave_types_table.id= $leave_applications_table.leave_type_id        
            WHERE $leave_applications_table.deleted=0 $where
            GROUP BY $leave_applications_table.applicant_id, $leave_applications_table.leave_type_id";
        return $this->db->query($sql);
    }

    
    public function get_allowed_days_by_type($leave_type_id) {
        // Define the query with a placeholder for the leave_type_id
        $sql = "SELECT lt.allowed_days FROM rise_leave_types lt WHERE lt.id = ?";
    
        // Execute the query
        $query = $this->db->query($sql, array($leave_type_id));
    
        if ($query && $query->getNumRows() > 0) {  
            $result = $query->getRow();  
    
            return isset($result->allowed_days) ? $result->allowed_days : 0;
        } else {
            
            log_message('error', 'Query failed or no rows found for leave_type_id: ' . $leave_type_id);
            return 0;
        }
    }

    public function get_taken_days_by_type($user_id, $leave_type_id) {
        // Define the query with a placeholder for the leave_type_id
        $sql = "SELECT SUM(la.total_days) AS total_days FROM rise_leave_applications la WHERE la.applicant_id = ? AND la.leave_type_id = ?";
    
        // Execute the query
        $query = $this->db->query($sql, array($user_id, $leave_type_id));
    
        // Check if the query executed successfully and returned any rows
        if ($query && $query->getNumRows() > 0) {  // Use getNumRows() in CodeIgniter 4
            // Fetch the result row
            $result = $query->getRow();  // Also, use getRow() instead of row()
    
            // Return the allowed_days if it exists, otherwise return 0
            return isset($result->total_days) ? $result->total_days : 0;
        } else {
            // If query fails or no rows are found, log the error and return 0
            log_message('error', 'Query failed or no rows found for leave_type_id: ' . $leave_type_id);
            return 0;
        }
    }

    
    

}
