<?php

namespace App\Controllers;

use Twilio\Rest\Client;

class Security_Controller extends App_Controller {

    public $login_user;
    protected $access_type = "";
    protected $allowed_members = array();
    protected $allowed_ticket_types = array();
    protected $allowed_client_groups = array();
    protected $module_group = "";
    protected $is_user_a_project_member = false;
    protected $is_clients_project = false; //check if loged in user's client's project

    

    public function __construct($redirect = true) {
        parent::__construct();

        //check user's login status, if not logged in redirect to signin page
        $login_user_id = $this->Users_model->login_user_id();
        if (!$login_user_id && $redirect) {
            $uri_string = uri_string();

            if (!$uri_string || $uri_string === "signin" || $uri_string === "/signin" || $uri_string === "/") {
                app_redirect('signin');
            } else {
                app_redirect('signin?redirect=' . get_uri($uri_string));
            }
        }

        app_hooks()->do_action('app_hook_before_app_access', array(
            "login_user_id" => $login_user_id,
            "redirect" => $redirect
        ));

        //initialize login users required information
        $this->login_user = $this->Users_model->get_access_info($login_user_id);

        //initialize login users access permissions
        if ($this->login_user && $this->login_user->permissions) {
            $permissions = unserialize($this->login_user->permissions);
            $this->login_user->permissions = is_array($permissions) ? $permissions : array();
        } else {
            if (!$this->login_user) {
                $this->login_user = new \stdClass();
            }
            $this->login_user->permissions = array();
        }
    }

    
    public function get_employees_dropdown() {
        $employees = $this->db->query("SELECT id,concat(first_name,' ',last_name) as name FROM rise_users where user_type = 'Staff'  and deleted=0")->getResult();
        $temp_array = array('' => '---Choose Department Head---');

        if(!$employees){
            return null;
        }
  
        foreach($employees as $e){
            $temp_array[$e->id] = $e->name;
        }

        return $temp_array;
    
    }

    public function get_bank_name_dropdown() {
        
        $bane_names = $this->db->query("SELECT id, bank_name FROM rise_bank_names WHERE deleted=0")->getResult();
        $temp_array = array('' => '---Choose Bank Name---');

        if(!$bane_names){
            return null;
        }
  
        foreach($bane_names as $b){
            $temp_array[$b->id] = $b->bank_name;
        }

        return $temp_array;
    }

    public function get_job_locations_dropdown_list() {
        
        $job_lo = $this->db->query("SELECT id, name FROM rise_job_locations WHERE deleted=0")->getResult();
        $temp_array = array('' => '---Choose Job Location---');

        if(!$job_lo){
            return null;
        }
  
        foreach($job_lo as $j){
            $temp_array[$j->id] = $j->name;
        }

        return $temp_array;
    }

    public function get_director_department_id(){
        $user_id = $this->login_user->id;
        $dep_info = $this->db->query("SELECT dp.id FROM departments dp LEFT JOIN rise_users us ON dp.head_id = us.id WHERE us.id = $user_id")->getRow();

        return $dep_info?->id;
    }

    public function get_user_department_id(){
        $user_id = $this->login_user->id;
        $job_info = $this->db->query("SELECT t.department_id from rise_team_member_job_info t left join rise_users u on u.id=t.user_id where t.user_id = $user_id")->getRow();
        
        return $job_info?->department_id;
    }

    public function get_merchant_types_dropdown() {
        
        $merchant_types = $this->db->query("SELECT mt.id, mt.merchant_type FROM rise_merchant_types mt WHERE mt.deleted=0")->getResult();
        $temp_array = array('' => '---  Choose Merchant Type ---');

        if(!$merchant_types){
            return null;
        }
  
        foreach($merchant_types as $m){
            $temp_array[$m->id] = $m->merchant_type;
        }

        return $temp_array;
    }

    public function get_merchant_types_dropdown_js() {
        
        $merchant_types = $this->db->query("SELECT mt.id, mt.merchant_type FROM rise_merchant_types mt WHERE mt.deleted=0")->getResult();
        // $temp_array[] = array('id' =>'','text'=> '---  Choose Merchant Type ---');

        if(!$merchant_types){
            return [];
        }
  
        foreach($merchant_types as $m){
            $temp_array[] = array('id' => $m->id,'text' => $m->merchant_type);
        }

        return $temp_array;
    }


    public function get_employees_dropdown_for_table() {
        $employees = $this->db->query("SELECT id,concat(first_name,' ',last_name) as name FROM rise_users where user_type = 'Staff'  and deleted=0")->getResult();
        $temp_array[] = array('id' => '', 'text' => '---Choose Employee---');
  
        foreach($employees as $e){
            $temp_array[] = array('id' => $e->id, 'text' => $e->name);
        }

        return json_encode($temp_array);
    }


    function get_departments_for_select(){
        
        $dept_id = $this->get_user_department_id();
        $role = $this->get_user_role();

        if($role == 'admin' || $role == 'Administrator' || $role == 'HRM'){
            $dept_id = '%';
        }

        $depts = $this->db->query("select id,nameSo from departments where id like '$dept_id' and deleted=0");
        
        $data = array('' => '---Choose Department---');

        if(!$depts){
            return [];
        }else{
            
            $depts = $depts->getResult();
            foreach($depts as $d){
                $data[$d->id] = $d->nameSo;
            }

            return $data;
        }
    }




    //initialize the login user's permissions with readable format
    protected function init_permission_checker($module) {
        $info = $this->get_access_info($module);
        $this->access_type = $info->access_type;
        $this->allowed_members = $info->allowed_members;
        $this->allowed_ticket_types = $info->allowed_ticket_types;
        $this->allowed_client_groups = $info->allowed_client_groups;
        $this->module_group = $info->module_group;
    }

    //prepear the login user's permissions
    protected function get_access_info($group) {
        $info = new \stdClass();
        $info->access_type = "";
        $info->allowed_members = array();
        $info->allowed_ticket_types = array();
        $info->allowed_client_groups = array();
        $info->module_group = $group;

        //admin users has access to everything
        if ($this->login_user->is_admin) {
            $info->access_type = "all";
        } else {

            //not an admin user? check module wise access permissions
            $module_permission = get_array_value($this->login_user->permissions, $group);

            if ($module_permission === "all") {
                //this user's has permission to access/manage everything of this module (same as admin)
                $info->access_type = "all";
            } else if ($module_permission === "specific" || $module_permission === "specific_excluding_own") {
                //this user's has permission to access/manage sepcific items of this module

                $info->access_type = "specific";
                $module_permission = get_array_value($this->login_user->permissions, $group . "_specific");
                $permissions = explode(",", $module_permission);

                //check the accessable users list
                if ($group === "leave" || $group === "attendance" || $group === "team_member_update_permission" || $group === "timesheet_manage_permission" || $group == "message_permission" || $group == "timeline_permission") {
                    $info->allowed_members = prepare_allowed_members_array($permissions, $this->login_user->id);
                } else if ($group === "ticket") {
                    //check the accessable ticket types
                    $info->allowed_ticket_types = $permissions;
                } else if ($group === "client") {
                    //check the accessable client groups
                    $info->allowed_client_groups = $permissions;
                }
            } else if ($module_permission === "own" || $module_permission === "read_only" || $module_permission === "assigned_only" || $module_permission === "own_project_members" || $module_permission === "own_project_members_excluding_own") {
                $info->access_type = $module_permission;
            }
        }
        return $info;
    }

    //only allowed to access for team members 
    protected function access_only_team_members() {
        if ($this->login_user->user_type !== "staff") {
            app_redirect("forbidden");
        }
    }

    //only allowed to access for admin users
    protected function access_only_admin() {
        if (!$this->login_user->is_admin) {
            app_redirect("forbidden");
        }
    }

    //only allowed to access for admin users or has admin privileges 
    protected function access_only_admin_or_settings_admin() {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_all_kinds_of_settings"))) {
            app_redirect("forbidden");
        }
    }

    //access only allowed team members
    protected function access_only_allowed_members() {

        // var_dump($this->module_group);
        // var_dump($this->access_type);
        // die();
        if ($this->access_type === "all") {
            return true; //can access if user has permission
        } else if ($this->module_group === "ticket" && ($this->access_type === "specific" || $this->access_type === "assigned_only")) {
            return true; //can access if it's tickets module and user has a pertial access
        } else if ($this->module_group === "lead" && $this->access_type === "own") {
            return true; //can access if it's leads module and user has access to own leads
        } else if ($this->module_group === "client" && ($this->access_type === "own" || $this->access_type === "read_only" || $this->access_type === "specific")) {
            return true;  //can access if it's clients module and user has a pertial access
        } else if ($this->module_group === "estimate" && $this->access_type === "own") {
            return true; //can access if it's estimates module and user has a pertial access
        } else {
            app_redirect("forbidden");
        }
    }

    //access only allowed team members or client contacts 
    protected function access_only_allowed_members_or_client_contact($client_id) {

        if ($this->access_type === "all") {
            return true; //can access if user has permission
        } else if ($this->module_group === "ticket" && ($this->access_type === "specific" || $this->access_type === "assigned_only")) {
            return true; //can access if it's tickets module and user has a pertial access
        } else if ($this->module_group === "client" && ($this->access_type === "own" || $this->access_type === "read_only" || $this->access_type === "specific")) {
            return true; //can access if it's clients module and user has a pertial access
        } else if ($this->login_user->client_id === $client_id) {
            return true; //can access if client id match 
        } else if ($this->module_group === "estimate" && $this->access_type === "own") {
            return true; //can access if it's estimates module and user has a pertial access
        } else {
            app_redirect("forbidden");
        }
    }

    //allowed team members and clint himself can access  
    protected function access_only_allowed_members_or_contact_personally($user_id) {
        if (!($this->access_type === "all" || $this->access_type === "own" || $this->access_type === "read_only" || $user_id === $this->login_user->id)) {
            app_redirect("forbidden");
        }
    }

    //access all team members and client contact
    protected function access_only_team_members_or_client_contact($client_id) {
        if (!($this->login_user->user_type === "staff" || $this->login_user->client_id === $client_id)) {
            app_redirect("forbidden");
        }
    }

    //only allowed to access for admin users
    protected function access_only_clients() {
        if ($this->login_user->user_type != "client") {
            app_redirect("forbidden");
        }
    }

    //check module is enabled or not
    protected function check_module_availability($module_name) {
        if (get_setting($module_name) != "1") {
            app_redirect("forbidden");
        }
    }

    //check who has permission to create projects
    protected function can_create_projects() {
        if ($this->login_user->user_type == "staff") {
            if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_all_projects") == "1") {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_create_projects") == "1") {
                return true;
            }
        } else {
            if (get_setting("client_can_create_projects")) {
                return true;
            }
        }
    }

    //check who has permission to view team members list
    protected function can_view_team_members_list() {
        if ($this->login_user->user_type == "staff") {
            if (get_array_value($this->login_user->permissions, "hide_team_members_list") == "1") {
                return false;
            } else {
                return true; //all members can see team members except the selected roles
            }
        }
        return false;
    }

    //access team members and clients
    protected function access_only_team_members_or_client() {
        if (!($this->login_user->user_type === "staff" || $this->login_user->user_type === "client")) {
            app_redirect("forbidden");
        }
    }

    //When checking project permissions, to reduce db query we'll use this init function, where team members has to be access on the project
    protected function init_project_permission_checker($project_id = 0) {
        if (!$project_id) {
            return false;
        }

        if ($this->login_user->user_type == "client") {
            $project_info = $this->Projects_model->get_one($project_id);
            if ($project_info->client_id == $this->login_user->client_id) {
                $this->is_clients_project = true;
            }
        } else {
            $this->is_user_a_project_member = $this->Project_members_model->is_user_a_project_member($project_id, $this->login_user->id);
        }
    }

    protected function can_manage_all_projects() {
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_manage_all_projects") == "1") {
            return true;
        }
    }

    //get currencies dropdown
    protected function _get_currencies_dropdown($support_empty_value = true) {
        $used_currencies = $this->Invoices_model->get_used_currencies_of_client()->getResult();
        $default_currency = get_setting("default_currency");

        $currencies_dropdown = array();
        if ($support_empty_value) {
            $currencies_dropdown[] = array("id" => "", "text" => "- " . app_lang("currency") . " -");
        }

        $currencies_dropdown[] = array("id" => $default_currency, "text" => $default_currency); // add default currency

        if ($used_currencies) {
            foreach ($used_currencies as $currency) {
                $currencies_dropdown[] = array("id" => $currency->currency, "text" => $currency->currency);
            }
        }
        return json_encode($currencies_dropdown);
    }

    //get hidden topbar menus dropdown
    protected function get_hidden_topbar_menus_dropdown() {
        //topbar menus dropdown
        $hidden_topbar_menus = array(
            "to_do",
            "favorite_projects",
            "dashboard_customization",
            "quick_add"
        );

        if ($this->login_user->user_type == "staff") {
            //favourite clients
            $access_client = get_array_value($this->login_user->permissions, "client");
            if ($this->login_user->is_admin || $access_client) {
                array_push($hidden_topbar_menus, "favorite_clients");
            }

            //custom language
            if (!get_setting("disable_language_selector_for_team_members")) {
                array_push($hidden_topbar_menus, "language");
            }
        } else {
            //custom language
            if (!get_setting("disable_language_selector_for_clients")) {
                array_push($hidden_topbar_menus, "language");
            }
        }

        $hidden_topbar_menus_dropdown = array();
        foreach ($hidden_topbar_menus as $hidden_menu) {
            $hidden_topbar_menus_dropdown[] = array("id" => $hidden_menu, "text" => app_lang($hidden_menu));
        }

        return json_encode($hidden_topbar_menus_dropdown);
    }

    //get existing projects dropdown for income and expenses
    protected function _get_projects_dropdown_for_income_and_expenses($type = "all") {
        $projects = $this->Invoice_payments_model->get_used_projects($type)->getResult();

        if ($projects) {
            $projects_dropdown = array(
                array("id" => "", "text" => "- " . app_lang("project") . " -"),
            );

            foreach ($projects as $project) {
                $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
            }

            return json_encode($projects_dropdown);
        }
    }

    protected function _get_groups_dropdown_select2_data($show_header = false) {
        $client_groups = $this->Client_groups_model->get_all()->getResult();
        $groups_dropdown = array();

        if ($show_header) {
            $groups_dropdown[] = array("id" => "", "text" => "- " . app_lang("client_groups") . " -");
        }

        foreach ($client_groups as $group) {
            $groups_dropdown[] = array("id" => $group->id, "text" => $group->title);
        }
        return $groups_dropdown;
    }

    protected function get_clients_and_leads_dropdown($return_json = false) {
        $clients_dropdown = array("" => "-");
        $clients_json_dropdown = array(array("id" => "", "text" => "-"));
        $clients = $this->Clients_model->get_all_where(array("deleted" => 0), 0, 0, "is_lead")->getResult();

        foreach ($clients as $client) {
            $company_name = $client->is_lead ? app_lang("lead") . ": " . $client->company_name : $client->company_name;

            $clients_dropdown[$client->id] = $company_name;
            $clients_json_dropdown[] = array("id" => $client->id, "text" => $company_name);
        }

        return $return_json ? $clients_json_dropdown : $clients_dropdown;
    }

    //check if the login user has restriction to show all tasks
    protected function show_assigned_tasks_only_user_id() {
        if ($this->login_user->user_type === "staff") {
            return get_array_value($this->login_user->permissions, "show_assigned_tasks_only") == "1" ? $this->login_user->id : false;
        }
    }

    //make calendar filter dropdown
    protected function get_calendar_filter_dropdown($type = "default") {
        /*
         * There should be all filters in main Events
         * On client->events tab, there will be only events and project deadlines field
         * On lead->events tab, there will be only events field
         */

        helper('cookie');
        $selected_filters_cookie = get_cookie("calendar_filters_of_user_" . $this->login_user->id);
        $selected_filters_cookie_array = $selected_filters_cookie ? explode('-', $selected_filters_cookie) : array("events"); //load only events if there is no cookie

        $calendar_filter_dropdown = array(array("id" => "events", "text" => app_lang("events"), "isChecked" => in_array("events", $selected_filters_cookie_array) ? true : false));

        if ($type !== "lead") {
            if ($this->login_user->user_type == "staff" && $type == "default") {
                //approved leaves
                $leave_access_info = $this->get_access_info("leave");
                if ($leave_access_info->access_type && get_setting("module_leave")) {
                    $calendar_filter_dropdown[] = array("id" => "leave", "text" => app_lang("leave"), "isChecked" => in_array("leave", $selected_filters_cookie_array) ? true : false);
                }

                //task start dates
                $calendar_filter_dropdown[] = array("id" => "task_start_date", "text" => app_lang("task_start_date"), "isChecked" => in_array("task_start_date", $selected_filters_cookie_array) ? true : false);

                //task deadlines
                $calendar_filter_dropdown[] = array("id" => "task_deadline", "text" => app_lang("task_deadline"), "isChecked" => in_array("task_deadline", $selected_filters_cookie_array) ? true : false);
            }

            //project start dates
            $calendar_filter_dropdown[] = array("id" => "project_start_date", "text" => app_lang("project_start_date"), "isChecked" => in_array("project_start_date", $selected_filters_cookie_array) ? true : false);

            //project deadlines
            $calendar_filter_dropdown[] = array("id" => "project_deadline", "text" => app_lang("project_deadline"), "isChecked" => in_array("project_deadline", $selected_filters_cookie_array) ? true : false);
        }

        return $calendar_filter_dropdown;
    }

    protected function check_access_to_store() {
        $this->check_module_availability("module_order");
        if (isset($this->login_user->id)) {
            if ($this->login_user->user_type == "staff") {
                $this->access_only_allowed_members();
            } else {
                if (!get_setting("client_can_access_store")) {
                    app_redirect("forbidden");
                }
            }
        } else {
            if (!(get_setting("module_order") && get_setting("visitors_can_see_store_before_login"))) {
                app_redirect("forbidden");
            }
        }
    }

    protected function check_access_to_this_order_item($order_item_info) {
        if ($order_item_info->id) {
            //item created
            if (!$order_item_info->order_id) {
                //on processing order, check if the item is created by the login user
                if ($order_item_info->created_by !== $this->login_user->id) {
                    app_redirect("forbidden");
                }
            } else {
                //order created, now only allowed members can access
                if ($this->login_user->user_type == "client") {
                    app_redirect("forbidden");
                }
            }
        } else if ($this->login_user->user_type !== "staff") {
            //item isn't created, only allowed member can access
            app_redirect("forbidden");
        }
    }

    protected function make_labels_dropdown($type = "", $label_ids = "", $is_filter = false, $custom_filter_title = "") {
        if (!$type) {
            show_404();
        }

        $labels_dropdown = $is_filter ? array(array("id" => "", "text" => "- " . ($custom_filter_title ? $custom_filter_title : app_lang("label")) . " -")) : array();

        $options = array(
            "context" => $type
        );

        if ($type == "event" || $type == "note" || $type == "to_do") {
            $options["user_id"] = $this->login_user->id;
        }

        if ($label_ids) {
            $add_label_option = true;

            //check if any string is exists, 
            //if so, not include this parameter
            $explode_ids = explode(',', $label_ids);
            foreach ($explode_ids as $label_id) {
                if (!is_int($label_id)) {
                    $add_label_option = false;
                    break;
                }
            }

            if ($add_label_option) {
                $options["label_ids"] = $label_ids; //to edit labels where have access of others
            }
        }

        $labels = $this->Labels_model->get_details($options)->getResult();
        foreach ($labels as $label) {
            $labels_dropdown[] = array("id" => $label->id, "text" => $label->title);
        }

        return $labels_dropdown;
    }

    protected function can_edit_projects($project_id = 0) {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            }

            $can_edit_projects = get_array_value($this->login_user->permissions, "can_edit_projects");
            $can_edit_only_own_created_projects = get_array_value($this->login_user->permissions, "can_edit_only_own_created_projects");

            if ($can_edit_projects) {
                return true;
            }

            if ($project_id) {
                $project_info = $this->Projects_model->get_one($project_id);
                if ($can_edit_only_own_created_projects && $project_info->created_by === $this->login_user->id) {
                    return true;
                }
            } else if ($can_edit_only_own_created_projects) { //no project given and the user has partial access
                return true;
            }
        } else {
            if (get_setting("client_can_edit_projects")) {
                return true;
            }
        }
    }

    protected function get_user_options_for_query($only_type = "") {
        /*
         * team members can send message to all team members/can't send to any member/can send to specific members
         * clients can only send message to team members and to own contacts (as defined on Client settings)
         * team members can send message to clients (as defined on Client settings)
         */

        $options = array("login_user_id" => $this->login_user->id);
        $client_message_users = get_setting("client_message_users");

        if ($this->login_user->user_type == "staff") {
            //user is team member
            if ($only_type !== "client") {
                if (!get_array_value($this->login_user->permissions, "message_permission")) {
                    //user can manage all members
                    $options["all_members"] = true;
                } else if (get_array_value($this->login_user->permissions, "message_permission") == "specific") {
                    //user can manage only specific members
                    $options["specific_members"] = $this->allowed_members;
                }
            }

            $client_message_users_array = explode(",", $client_message_users);
            if (in_array($this->login_user->id, $client_message_users_array) && $only_type !== "staff") {
                //user can send message to clients
                $options["member_to_clients"] = true;
            }
        } else {
            //user is a client contact
            if ($client_message_users) {
                if ($only_type !== "client") {
                    $options["client_to_members"] = $client_message_users;
                }

                if (get_setting("client_message_own_contacts") && $only_type !== "staff") {
                    //client has permission to send message to own client contacts
                    $options["client_id"] = $this->login_user->client_id;
                }
            }
        }

        return $options;
    }

    protected function check_access_on_messages_for_this_user() {
        $accessable = true;

        if ($this->login_user->user_type == "staff") {
            $client_message_users = get_setting("client_message_users");
            $client_message_users_array = explode(",", $client_message_users);

            if (!$this->login_user->is_admin && get_array_value($this->login_user->permissions, "message_permission") == "no" && !in_array($this->login_user->id, $client_message_users_array)) {
                $accessable = false;
            }
        } else {
            if (!get_setting("client_message_users")) {
                $accessable = false;
            }
        }

        return $accessable;
    }

    protected function can_view_invoices($client_id = 0) {
        if ($this->login_user->user_type == "staff") {
            if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "invoice") === "all" || get_array_value($this->login_user->permissions, "invoice") === "read_only") {
                return true;
            }
        } else {
            if ($this->login_user->client_id === $client_id) {
                return true;
            }
        }
    }

    protected function can_edit_invoices() {
        if ($this->login_user->user_type == "staff" && ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "invoice") === "all")) {
            return true;
        }

    }

    protected function can_edit_purchases() {
        $role = $this->get_user_role();
        if ($this->login_user->user_type == "staff" && ($role == 'admin' || $role == 'Administrator' || $role == 'Director' || $role == 'HRM')) {
            return true;
        }
    }

    protected function can_access_expenses() {
        $permissions = $this->login_user->permissions;
        if ($this->login_user->is_admin || get_array_value($permissions, "expense")) {
            return true;
        } else {
            return false;
        }
    }

    protected function validate_sending_message($to_user_id) {
        $users = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query())->getResult();
        $users = json_decode(json_encode($users), true); //convert to array
        if (!$this->check_access_on_messages_for_this_user() || !in_array($to_user_id, array_column($users, "id"))) {
            return false;
        }

        //so the sender could send message to the receiver
        //check if the receiver could also send message to the sender
        $to_user_info = $this->Users_model->get_one($to_user_id);
        if ($to_user_info->user_type == "staff") {
            //receiver is a team member
            $permissions = array();
            $user_permissions = $this->Users_model->get_access_info($to_user_id)->permissions;
            if ($user_permissions) {
                $user_permissions = unserialize($user_permissions);
                $permissions = is_array($user_permissions) ? $user_permissions : array();
            }

            if (get_array_value($permissions, "message_permission") == "no") {
                //user doesn't have permission to send any message
                return false;
            } else if (get_array_value($permissions, "message_permission") == "specific") {
                //user has access on specific members
                $module_permission = get_array_value($permissions, "message_permission_specific");
                $permissions = explode(",", $module_permission);
                $allowed_members = prepare_allowed_members_array($permissions, $to_user_id);
                if (!in_array($this->login_user->id, $allowed_members)) {
                    return false;
                }
            }
        }

        return true;
    }

    protected function show_own_clients_only_user_id() {
        if ($this->login_user->user_type === "staff") {
            return get_array_value($this->login_user->permissions, "client") == "own" ? $this->login_user->id : false;
        }
    }

    protected function check_profile_image_dimension($image_file_name = "") {
        if (!$image_file_name) {
            return false;
        }

        list($width, $height) = getimagesize($image_file_name);

        if ($width === 200 && $height === 200) {
            return true;
        }

        return false;
    }

    protected function show_assigned_tickets_only_user_id() {
        if ($this->access_type === "assigned_only") {
            return $this->login_user->id;
        }
    }

    protected function get_team_members_dropdown($is_filter = false) {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();

        $team_members_dropdown = array();
        if ($is_filter) {
            $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("owner") . " -"));
        }

        foreach ($team_members as $member) {
            $team_members_dropdown[] = array("id" => $member->id, "text" => $member->first_name . " " . $member->last_name);
        }

        return json_encode($team_members_dropdown);
    }

    //get projects dropdown
    protected function _get_projects_dropdown() {
        $project_options = array("status_id" => 1);
        if ($this->login_user->user_type == "staff") {
            if (!$this->can_manage_all_projects()) {
                $project_options["user_id"] = $this->login_user->id; //normal user's should be able to see only the projects where they are added as a team mmeber.
            }
        } else {
            $project_options["client_id"] = $this->login_user->client_id; //get client's projects
        }

        $projects = $this->Projects_model->get_details($project_options)->getResult();
        $projects_dropdown = array("" => "-");

        if ($projects) {
            foreach ($projects as $project) {
                $projects_dropdown[$project->id] = $project->title;
            }
        }

        return $projects_dropdown;
    }

    protected function check_access_to_this_item($item_info) {
        if ($this->login_user->user_type === "client") {
            //check if the item has the availability to show on client portal
            if (!$item_info->show_in_client_portal) {
                app_redirect("forbidden");
            }
        }
    }

    protected function get_conversion_rate_with_currency_symbol() {
        $symbol_array = array();

        $conversion_rate = get_setting("conversion_rate");
        $conversion_rate = @unserialize($conversion_rate);
        if (!($conversion_rate && is_array($conversion_rate) && count($conversion_rate))) {
            //no settings found
            return json_encode($symbol_array);
        }

        $clients = $this->Clients_model->get_conversion_rate_with_currency_symbol()->getResult();

        foreach ($clients as $client) {
            $rate_for_this_currency = get_array_value($conversion_rate, $client->currency);
            if ($rate_for_this_currency) {
                $symbol_array[$client->currency_symbol] = $rate_for_this_currency;
            }
        }

        return json_encode($symbol_array);
    }

    protected function can_edit_clients($client_id = 0) {
        $permissions = $this->login_user->permissions;

        if ($this->login_user->is_admin) {
            return true;
        } else if (get_array_value($permissions, "client") == "all") {
            return true;
        } else if (!$client_id && $this->login_user->user_type == "staff" && get_array_value($permissions, "client") === "read_only") {
            return false;
        } else if (!$client_id && get_array_value($permissions, "client")) {
            //clients list
            return true;
        } else if ($client_id) {
            $client_info = $this->Clients_model->get_one($client_id);

            if ($this->login_user->user_type == "client" && $client_info->id === $this->login_user->client_id) {
                return true;
            } else if (get_array_value($permissions, "client") === "own" && ($client_info->created_by == $this->login_user->id || $client_info->owner_id == $this->login_user->id)) {
                return true;
            } else if (get_array_value($permissions, "client") === "specific") {
                $specific_client_groups = explode(',', get_array_value($permissions, "client_specific"));
                if (array_intersect($specific_client_groups, explode(',', $client_info->group_ids))) {
                    return true;
                }
            }
        }
    }

    protected function can_view_clients($client_id = 0) {
        if ($this->can_edit_clients($client_id)) {
            return true;
        } else if (get_array_value($this->login_user->permissions, "client") === "read_only") {
            return true;
        }
    }

    protected function can_access_tickets($ticket_id = 0) {
        $permissions = $this->login_user->permissions;

        if ($this->login_user->is_admin) {
            return true;
        } else if (get_array_value($permissions, "ticket") == "all") {
            return true;
        } else if (!$ticket_id && get_array_value($permissions, "ticket") || $this->login_user->user_type == "client") {
            return true;
        } else if ($ticket_id) {
            $ticket_info = $this->Tickets_model->get_one($ticket_id);

            if ($this->login_user->user_type == "client" && $ticket_info->client_id === $this->login_user->client_id) {
                return true;
            } else if (get_array_value($permissions, "ticket") === "assigned_only" && $ticket_info->assigned_to == $this->login_user->id) {
                return true;
            } else if (get_array_value($permissions, "ticket") === "specific") {
                $allowed_ticket_types = explode(',', get_array_value($permissions, "ticket_specific"));
                if (in_array($ticket_info->ticket_type_id, $allowed_ticket_types)) {
                    return true;
                }
            }
        }
    }

    
    public function get_department_name($id){
        $d = $this->db->query("SELECT nameSo from departments where id = $id")->getRow();
        
        if(empty($d)){
            return '';
        }
        return $d->nameSo;
    }

    public function get_user_department_name(){
        $user_id = $this->login_user->id;
        $job_info = $this->db->query("SELECT d.nameSo from rise_team_member_job_info t 
        left join rise_users u on u.id=t.user_id 
        left join departments d on d.id=t.department_id 
        where t.user_id = $user_id")->getRow();

        if(empty($job_info)){
            return '';
        }
        return $job_info->nameSo;
    }



    public function check_access($name = null){
        
        $this->check_module_availability('module_'.$name);
        $role = $this->get_user_role();
        $dr_dp_id = $this->get_director_department_id();
        $dept_id = $this->get_user_department_id();
        $permissions = $this->login_user->permissions;

        $perm = get_array_value($permissions, $name);
        
        if ($this->login_user->is_admin || $role == 'Administrator'  || $role == 'Access Control' || $role == 'HRM' || $perm == "all") {
            $created_by = '%';
            $dept_id = '%';
        } else if ($role == 'Director') {

             if(!empty($dr_dp_id)){
                 $dept_id = $dr_dp_id;
                 $created_by = '%';
             }else{
                $created_by = $this->login_user->id;
             }

        } else if ($role == 'Secretary') {
            $created_by = '%';
        } else if ($perm == "own" || $role == 'Employee') {
            $created_by = $this->login_user->id;
        }else{
            
            app_redirect("forbidden");
        }

        $data['role'] = $role;
        $data['created_by'] = $created_by;
        $data['department_id'] = $dept_id;
        // die($created_by);

        return $data;

    }

    protected function can_access_this_lead($lead_id = 0) {
        $permissions = $this->login_user->permissions;

        // var_dump(get_array_value($permissions, "lead"));
        // var_dump($lead_id);
        // var_dump(!$lead_id);
        // die();
        
        if ($this->login_user->is_admin) {
            return true;
        } else if (get_array_value($permissions, "lead") == "all") {
            return true;
        } else if (!$lead_id && get_array_value($permissions, "lead")) {
            return true;
        } else if ($lead_id) {
            $lead_info = $this->Documents_model->get_one($lead_id); 
            
            if ($lead_info->id && get_array_value($permissions, "lead") == "own" && $lead_info->created_by == $this->login_user->id) {
                return true;
            }
        }
    }

    protected function show_own_leads_only_user_id() {
        if ($this->login_user->user_type === "staff") {
            return get_array_value($this->login_user->permissions, "lead") == "own" ? $this->login_user->id : false;
        }
    }

    protected function get_own_user_id($permission = null) {
        if ($this->login_user->user_type === "staff") {
            return get_array_value($this->login_user->permissions, $permission) == "own" ? $this->login_user->id : false;
        }
    }

    protected function prepare_custom_field_filter_values($related_to, $is_admin = 0, $user_type = "") {
        $custom_fields_for_filter = $this->Custom_fields_model->get_available_filters($related_to, $is_admin, $user_type);

        $data = array();
        foreach ($custom_fields_for_filter as $column) {
            if ($this->request->getPost("custom_field_filter_$column->id")) {
                $data[$column->id] = $this->request->getPost("custom_field_filter_$column->id");
            }
        }

        return $data;
    }

    //prepare the dropdown list of roles
    protected function _get_roles_dropdown() {
        // $role_dropdown = array(
        //     "0" => app_lang('team_member')
        // );

        if ($this->login_user->is_admin) {
            $role_dropdown["admin"] = app_lang('admin'); //static role
        }

        $roles = $this->Roles_model->get_all()->getResult();
        foreach ($roles as $role) {
            $role_dropdown[$role->id] = $role->title;
        }
        return $role_dropdown;
    }

    protected function is_own_id($user_id) {
        return $this->login_user->id === $user_id;
    }

    protected function role_has_permission($perm) {
        return get_array_value($this->login_user->permissions, $perm);
    }

    protected function has_role_manage_permission() {
        return get_array_value($this->login_user->permissions, "can_manage_user_role_and_permissions");
    }
    protected function is_admin_role($role) {
        return $role == "admin";
    }

    public function get_user_role() {
        $user = $this->login_user;

        if($user->is_admin){
            return 'admin';
        }
        
        $role = $this->Roles_model->get_one($user->role_id);
        return $role->title;
    }

    //make it public function to access from helper functions
    public function get_allowed_user_ids() {
        $users = $this->Messages_model->get_users_for_messaging($this->get_user_options_for_query())->getResult();
        $users = json_decode(json_encode($users), true); //convert to array
        return implode(',', array_column($users, "id"));
    }

    protected function _check_valid_date($string = "") {
        try {
            if (strtotime($string)) {
                //date is valid
            } else {
                //some available format won't works with this method, replace with the suppported format
                if (strtotime($modified_string = str_replace('-', '/', $string))) { //m-d-Y > m/d/Y
                    $string = $modified_string;
                } else if (strtotime($modified_string = str_replace('/', '-', $string))) { //d/m/Y > d-m-Y
                    $string = $modified_string;
                } else if (strtotime($modified_string = str_replace('.', '/', $string))) { //m.d.Y and Y.m.d > m/d/Y
                    $string = $modified_string;
                } else {
                    return false;
                }
            }
        } catch (\Exception $ex) {
            return false;
        }

        //the given date is valid
        //convert to y-m-d
        return date("Y-m-d", strtotime($string));
    }

    protected function has_all_projects_restricted_role() {
        if ($this->login_user->user_type === "staff" && !$this->login_user->is_admin && get_array_value($this->login_user->permissions, "do_not_show_projects") == "1") {
            return true;
        }
    }

    //get companies dropdown
    protected function _get_companies_dropdown() {
        $Company_model = model('App\Models\Company_model');
        $companies = $Company_model->get_details()->getResult();

        $companies_dropdown = array();
        foreach ($companies as $company) {
            $companies_dropdown[] = array("id" => $company->id, "text" => $company->name);
        }

        return $companies_dropdown;
    }

    /* prepare a row of order item list table */

    protected function _make_order_item_row($data) {
        $item = "<div class='item-row strong mb5' data-id='$data->id'><div class='float-start move-icon'><i data-feather='menu' class='icon-16'></i></div> $data->title</div>";
        if ($data->description) {
            $item .= "<span>" . nl2br($data->description) . "</span>";
        }
        $type = $data->unit_type ? $data->unit_type : "";
        return array(
            $data->sort,
            $item,
            to_decimal_format($data->quantity) . " " . $type,
            to_currency($data->rate),
            to_currency($data->total),
            modal_anchor(get_uri("store/item_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_item'), "data-post-id" => $data->id, "data-post-order_id" => $data->order_id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("store/delete_item"), "data-action" => "delete"))
        );
    }

    protected function can_view_subscriptions($client_id = 0) {
        if ($this->login_user->user_type == "staff") {
            if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "subscription")) {
                return true;
            }
        } else {
            if ($this->login_user->client_id === $client_id) {
                return true;
            }
        }
    }

    protected function can_edit_subscriptions($subscription_id = 0) {
        $permissions = $this->login_user->permissions;

        if ($this->login_user->is_admin) {
            return true;
        } else if (get_array_value($permissions, "subscription") == "all") {
            return true;
        } else if (!$subscription_id && $this->login_user->user_type == "staff" && get_array_value($permissions, "subscription") === "read_only") {
            return false;
        } else if ($subscription_id) {
            $subscription_info = $this->Subscriptions_model->get_one($subscription_id);

            if ($this->login_user->user_type == "staff" && get_array_value($permissions, "subscription") !== "read_only" && $subscription_info->status !== "active") {
                return true;
            }
        }
    }

    //only admin/permitted users can access user's notes
    //users can't access own notes
    protected function can_access_team_members_note($user_id) {
        if (($this->login_user->is_admin || get_array_value($this->login_user->permissions, "team_members_note_manage_permission")) && $user_id != $this->login_user->id) {
            return true;
        }
    }

    /*
     * admin can manage all members timesheet
     * allowed member can manage other members timesheet accroding to permission
     */

    protected function _get_members_to_manage_timesheet() {
        $access_info = $this->get_access_info("timesheet_manage_permission");
        $access_type = $access_info->access_type;

        if (!$access_type || $access_type === "own") {
            return array($this->login_user->id); //permission: no / own
        } else if (($access_type === "specific" || $access_type === "specific_excluding_own") && count($access_info->allowed_members)) {
            return $access_info->allowed_members; //permission: specific / specific_excluding_own
        } else {
            return $access_type; //permission: all / own_project_members / own_project_members_excluding_own
        }
    }

    
    function get_suppliers_for_select(){
        
        $dept_id = $this->get_user_department_id();
        $role = $this->get_user_role();


        $suppliers = $this->db->query("select id,supplier_name from rise_suppliers where deleted=0");
        
        $data = array('' => 'Choose Supplier');

        if(!$suppliers){
            return [];
        }else{
            
            $suppliers = $suppliers->getResult();
            foreach($suppliers as $d){
                $data[$d->id] = $d->supplier_name;
            }

            return $data;
        }
    }

    function get_departments_for_table(){
        // $depts = $this->db->table('departments')->select('id,nameEn')->get();
        $dept_id = $this->get_user_department_id();
        $role = $this->get_user_role();

        if($role == 'admin' || $role == 'Administrator' || $role == 'HRM'){
            $dept_id = '%';
        }

        $depts = $this->db->query("select id,nameSo from departments where id like '$dept_id'");
        $data[] = array('id' => '', 'text' => 'Choose Department');

        if(!$depts){
            return [];
        }else{
            $depts = $depts->getResult();
            foreach($depts as $d){
                $data[] = array('id' => $d->id, 'text' => $d->nameSo);
            }

            return json_encode($data);
        }
    }
    
    function get_departments_for_table_emp(){
        // $depts = $this->db->table('departments')->select('id,nameEn')->get();
        $dept_id = $this->get_user_department_id();
        $role = $this->get_user_role();

        if($role == 'admin' || $role == 'Administrator' || $role == 'HRM'){
            $dept_id = '%';
        }

        $depts = $this->db->query("select id,nameSo from departments where id like '$dept_id'");
        $data[] = array('id' => '', 'text' => 'Choose Department');

        if(!$depts){
            return [];
        }else{
            $depts = $depts->getResult();
            foreach($depts as $d){
                $data[] = array('id' => $d->id, 'text' => $d->nameSo);
            }

            return json_encode($data);
        }
    }

    /** send whatsup message */
    protected function send_whatsup_message($phoneNumber,$body){

        require(ROOTPATH.'\vendor\twilio\sdk\src\Twilio\autoload.php');

        die(ROOTPATH.'\vendor\twilio\sdk\src\Twilio\autoload.php');

        $sid = getenv('TWILIO_ACCOUNT_SID');
        $token = getenv('TWILIO_AUTH_TOKEN');
        $client = new Client($sid, $token);

        $message = $client->messages->create(
            $phoneNumber,
            [
                'from' => '',
                'body' => $body
            ]
        );

    }

    /* load the project settings into ci settings */

    protected function init_project_settings($project_id) {
        if (!$project_id) {
            return false;
        }

        $settings = $this->Project_settings_model->get_all_where(array("project_id" => $project_id))->getResult();
        foreach ($settings as $setting) {
            config('Rise')->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

    protected function can_view_timesheet($project_id = 0, $show_all_personal_timesheets = false) {
        if (!get_setting("module_project_timesheet")) {
            return false;
        }

        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {


                if ($project_id) {
                    //check is user a project member
                    $this->init_project_permission_checker($project_id);
                    return $this->is_user_a_project_member;
                } else {
                    $access_info = $this->get_access_info("timesheet_manage_permission");

                    if ($access_info->access_type) {
                        return true;
                    } else if (count($access_info->allowed_members)) {
                        return true;
                    } else if ($show_all_personal_timesheets) {
                        return true;
                    }
                }
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_timesheet")) {
                //even the settings allow to view gantt, the client can only view on their own project's gantt
                return $this->is_clients_project;
            }
        }
    }

    protected function can_access_clients($is_task = false) {
        $permissions = $this->login_user->permissions;
        if ($is_task) {
            if (get_setting("client_can_view_tasks") && ($this->login_user->is_admin ||
                    ($this->login_user->user_type == "staff" && get_array_value($permissions, "client") && get_array_value($permissions, "show_assigned_tasks_only") !== "1")
                    )) {
                return true;
            }
        } else {
            if ($this->login_user->is_admin || get_array_value($permissions, "client")) {
                return true;
            }
        }
    }

    protected function can_view_milestones() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        } else {
            //check settings for client's project permission
            if (get_setting("client_can_view_milestones")) {
                //even the settings allow to view milestones, the client can only create their own project's milestones
                return $this->is_clients_project;
            }
        }
    }

    protected function show_own_estimates_only_user_id() {
        if ($this->login_user->user_type === "staff") {
            return get_array_value($this->login_user->permissions, "estimate") == "own" ? $this->login_user->id : false;
        }
    }

    protected function can_access_this_estimate($estimate_id = 0, $check_client = false) {
        $permissions = $this->login_user->permissions;

        if ($this->login_user->is_admin) {
            return true;
        } else if (get_array_value($permissions, "estimate") == "all") {
            return true;
        } else if (!$estimate_id && get_array_value($permissions, "estimate")) {
            return true;
        } else if ($estimate_id) {
            $estimate_info = $this->Estimates_model->get_one($estimate_id);
            if ($check_client && $this->login_user->user_type == "client" && $estimate_info->client_id === $this->login_user->client_id) {
                return true;
            } else if ($estimate_info->id && get_array_value($permissions, "estimate") == "own" && $estimate_info->created_by == $this->login_user->id) {
                return true;
            }
        }
    }

    //prevent editing of invoice after certain state
    protected function is_invoice_editable($_invoice, $is_clone = 0) {
        if (get_setting("enable_invoice_lock_state")) {
            $invoice_info = is_object($_invoice) ? $_invoice : $this->Invoices_model->get_one($_invoice);
            if (!$invoice_info->id || $is_clone) {
                return true;
            }

            if ($invoice_info->status == "draft") {
                return true;
            }
        } else {
            return true;
        }
    }

}
