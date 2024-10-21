<?php

namespace App\Controllers;

class Team_members extends Security_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->access_only_team_members();
    }

    // private function can_view_team_members_contact_info()
    // {
    //     if ($this->login_user->user_type == "staff") {
    //         if ($this->login_user->is_admin) {
    //             return true;
    //         } else if (get_array_value($this->login_user->permissions, "can_view_team_members_contact_info") == "1") {
    //             return true;
    //         }
    //     }
    // }

    private function can_view_team_members_social_links()
    {
        if ($this->login_user->user_type == "staff") {
            if ($this->login_user->is_admin) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_view_team_members_social_links") == "1") {
                return true;
            }
        }
    }

    private function update_only_allowed_members($user_id)
    {
        if ($this->can_update_team_members_info($user_id)) {
            return true; //own profile
        } else {
            app_redirect("forbidden");
        }
    }

    //only admin can change other user's info
    //none admin users can only change his/her own info
    //allowed members can update other members info    
    private function can_update_team_members_info($user_id)
    {
        $access_info = $this->get_access_info("team_member_update_permission");

        if ($this->login_user->id === $user_id) {
            return true; //own profile
        } else if ($access_info->access_type == "all") {
            return true; //has access to change all user's profile
        } else if ($user_id && in_array($user_id, $access_info->allowed_members)) {
            return true; //has permission to update this user's profile
        } else {

            return false;
        }
    }

    //only admin/permitted users can change other user's info
    //other users can only change his/her own info
    private function can_access_user_settings($user_id)
    {
        if ($user_id && ($this->login_user->is_admin || $this->login_user->id === $user_id || get_array_value($this->login_user->permissions, "can_manage_user_role_and_permissions"))) {
            return true;
        } else {
            return false;
            // app_redirect("forbidden");
        }
    }

    private function _can_activate_deactivate_team_member($member_info)
    {

        if ($member_info && !$this->is_own_id($member_info->id) && ($this->login_user->is_admin || (get_array_value($this->login_user->permissions, "can_activate_deactivate_team_members") && $member_info->is_admin != 1))) {
            return true;
        }
        return false;
    }

    private function _can_delete_team_member($member_info)
    {

        //can't delete own user
        //only admin can delete other admin users.
        //non-admin users can delete other users but can't delete admin user. 
        if ($member_info && !$this->is_own_id($member_info->id) && ($this->login_user->is_admin || (get_array_value($this->login_user->permissions, "can_delete_team_members") && $member_info->is_admin != 1))) {
            return true;
        }
        return false;
    }

    public function index()
    {
        if (!$this->can_view_team_members_list()) {
            app_redirect("forbidden");
        }

        $view_data["show_contact_info"] = $this->can_view_team_members_contact_info();
        $view_data['departments_dropdown'] = $this->get_departments_for_table_emp();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("team_members", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("team_members", $this->login_user->is_admin, $this->login_user->user_type);

        return $this->template->rander("team_members/index", $view_data);
    }

    private function access_only_admin_or_member_creator()
    {
        if (!($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_add_or_invite_new_team_members"))) {
            app_redirect("forbidden");
        }
    }


    /* open new member modal */

    function modal_form()
    {
        $this->access_only_admin_or_member_creator();

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['role_dropdown'] = $this->_get_roles_dropdown();

        $id = $this->request->getPost('id');
        $options = array(
            "id" => $id,
        );

        $view_data['bank_names_dropdown'] = array("" => " - ") + $this->Bank_names_model->get_dropdown_list(array("bank_name"), "id");

        $view_data['model_info'] = $this->Users_model->get_details($options)->getRow();
        // $view_data['departments'] = array("" => " -- Choose Department -- ") + $this->Departments_model->get_dropdown_list(array("nameSo"), "id");

        // $view_data['departments'] = $this->Team_model->get_departments_for_select();
        $view_data['departments'] = array("" => " -- Choose Department -- ") + $this->Departments_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Sections'] = array("" => " -- Choose Section -- ") + $this->Sections_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Units'] = array("" => " -- Choose Unit -- ") + $this->Units_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['grades'] = array("" => " -- Choose Grade -- ") + $this->Grades_model->get_dropdown_list(array("grade"), "id");
        $view_data['job_locations'] = array("" => " -- Choose Job Location -- ") + $this->Job_locations_model->get_dropdown_list(array("name"), "id");
        $view_data['education_levels'] = [
            '' => ' -- Choose Education Level -- ',
            'Primary' => 'Primary',
            'Secondary' => 'Secondary',
            'Diploma' => 'Diploma',
            'Bachelor' => 'Bachelor',
            'Bachelor & Master' => 'Bachelor & Master',
            '2 Bachelors' => '2 Bachelors',
            '2 Bachelors & Master' => '2 Bachelors & Master',
            '2 Bachelors & 2 Masters' => '2 Bachelors & 2 Masters',
            'Doctor' => 'Doctor',
            'Other/Skill' => 'Other/Skill'
        ];
        $view_data['sections'] = ['' => 'Choose Department Section', '1' => 'ICT & Cyber Security', '2' => 'Other'];
        $education_fields = $this->db->query("select '' id,'-- Select Field of Study --' name UNION ALL select id,name from rise_education_industry where deleted=0")->getResult();

        $view_data['field_of_study'] = array("" => " -- Choose Field of Study -- ") + $this->Field_of_study_model->get_dropdown_list(array("name"), "id");

        /** new form education info tab */
        $view_data['university_names'] = array("" => " -- Choose University Name -- ") + $this->University_names_model->get_dropdown_list(array("university_name"), "id");
        /** end new from education info tab */

        $age_levels = [
            '' => '-- Choose Age Level --',
            '18 - 25' => '18 - 25',
            '26 - 35' => '26 - 35',
            '36 - 45' => '36 - 45',
            '46 -55' => '46 -55',
            '56 - 65' => '56 - 65',
            '66 ama kaweyn' => '66 ama kaweyn'
        ];

        $view_data['age_levels'] = $age_levels;

        $array_fields = [];
        foreach ($education_fields as $f) {
            $array_fields[$f->id] = $f->name;
        }
        $view_data['education_fields'] = $array_fields;

        // array_unshift($view_data['departments'],'Choose Department');
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("team_members", 0, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('team_members/modal_form', $view_data);
    }



    /* save new member */

    function add_team_member()
    {

        $this->access_only_admin_or_member_creator();

        //check duplicate email address, if found then show an error message
        if ($this->Users_model->is_email_exists($this->request->getPost('email'))) {
            echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
            exit();
        }

        $this->validate_submitted_data(array(
            "email" => "required|valid_email",
            "private_email" => "required|valid_email",
            "first_name" => "required",
            "last_name" => "required",
            "job_title_en" => "required",
            "job_title_so" => "required",
            "department_id" => "required",
            "role" => "required"
        ));

        $password = $this->request->getPost("password");

        // new fields for emof: //

        // age_level,	
        // work_experience	,	
        // faculty	,
        // faculty2,	
        // place_of_work,	
        // bachelor_degree,	
        // master_degree,	
        // highest_school,	
        // relevant_document_url,

        // new Data evilla:  `marital_status`, `emergency_name`, `emergency_phone`, `birth_date`, `birth_place`, `education_level`, `education_field`, `education_school`
        $user_data = array(
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,

            // Profile Info
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "address" => $this->request->getPost('address'),
            "alternative_address" => $this->request->getPost('alternative_address'),
            "phone" => $this->request->getPost('phone'),
            "alternative_phone" => $this->request->getPost('alternative_phone'),
            "skype" => $this->request->getPost('skype'),
            "ssn" => $this->request->getPost('ssn'),
            "gender" => $this->request->getPost('gender'),
            "marital_status" => $this->request->getPost('marital_status'),
            "age_level" => $this->request->getPost('age_level'),
            "birth_date" => $this->request->getPost('birth_date'),
            "birth_place" => $this->request->getPost('birth_place'),
            "passport_no" => $this->request->getPost('passport_no'),
            "relevant_document_url" => $this->request->getPost('relevant_document_url'),
            "emergency_name" => $this->request->getPost('emergency_name'),
            "emergency_phone" => $this->request->getPost('emergency_phone'),

            //
            "is_admin" => $this->request->getPost('is_admin'),
            "job_title" => $this->request->getPost('job_title_en'),
            "education_level" => $this->request->getPost('education_level'),
            "education_field" => $this->request->getPost('education_field'),
            "education_school" => $this->request->getPost('education_school'),
            "faculty" => $this->request->getPost('faculty'),
            "faculty2" => $this->request->getPost('faculty2'),
            "bachelor_degree" => $this->request->getPost('bachelor_degree'),
            "master_degree" => $this->request->getPost('master_degree'),
            "highest_school" => $this->request->getPost('highest_school'),
            "employee_id" => $this->request->getPost('employee_id'),
            "department_id" => $this->request->getPost('department_id'),
            "user_type" => "staff",
            "created_at" => get_current_utc_time(),

            // Bank Details
            "bank_id" => $this->request->getPost('bank_id'),
            "bank_account" => $this->request->getPost('bank_account'),
            "registered_name" => $this->request->getPost('bank_registered_name'),

            // Account Settings
            "email" => $this->request->getPost('email'),
            "private_email" => $this->request->getPost('private_email'),
        );

        if ($password) {
            $user_data["password"] = password_hash($password, PASSWORD_DEFAULT);
        }

        //make role id or admin permission 
        $role = $this->request->getPost('role');
        $role_id = $role;

        if ($this->login_user->is_admin && $role === "admin") {
            $user_data["is_admin"] = 1;
            $user_data["role_id"] = 0;
        } else {
            $user_data["is_admin"] = 0;
            $user_data["role_id"] = $role_id;
        }


        //add a new team member
        $user_id = $this->Users_model->ci_save($user_data);

        $target_path = get_setting("signature_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "signature");
        $signature = unserialize($files_data);

        if ($user_id) {
            //user added, now add the job info for the user
            // new Data: `department_id`, `section_id`, `job_title_en`, `job_title_so`, `employee_type`, `employee_id`
            $job_data = array(
                "user_id" => $user_id,
                "employee_type" => $this->request->getPost('employee_type'),
                "department_id" => $this->request->getPost('department_id'),
                "section_id" => $this->request->getPost('section_id'),
                "unit_id" => $this->request->getPost('unit_id'),
                "grade_id" => $this->request->getPost('grade_id'),
                "job_title_so" => $this->request->getPost('job_title_so'),
                "job_title_en" => $this->request->getPost('job_title_en'),
                "job_description" => $this->request->getPost('job_description'),
                "work_experience" => $this->request->getPost('work_experience'),
                "job_location_id" => $this->request->getPost('job_location'),
                "date_of_hire" => $this->request->getPost('date_of_hire'),
                "employee_id" => $this->request->getPost('employee_id'),


                "salary" => $this->request->getPost('salary') ? $this->request->getPost('salary') : 0,
                "salary_term" => $this->request->getPost('salary_term'),
            );

            $job_data["signature"] = serialize($signature);

            $this->Users_model->save_job_info($job_data);

            $education_data = array(
                "user_id" => $user_id,
                "education_level" => $this->request->getPost('education_level'),
                "primary_school_name" => $this->request->getPost('primary_school_name'),
                "primary_graduation_date" => $this->request->getPost('primary_graduation_date'),
                "secondary_school_name" => $this->request->getPost('secondary_school_name'),
                "secondary_graduation_date" => $this->request->getPost('secondary_graduation_date'),
                "university_name_diploma" => $this->request->getPost('university_name_diploma'),
                "field_of_study_diploma" => $this->request->getPost('field_of_study_diploma'),
                "graduation_date_diploma" => $this->request->getPost('graduation_date_diploma'),
                "university_name_foculty_1" => $this->request->getPost('university_name_foculty_1'),
                "field_of_study_foculty_1" => $this->request->getPost('field_of_study_foculty_1'),
                "graduation_date_foculty_1" => $this->request->getPost('graduation_date_foculty_1'),
                "university_name_foculty_2" => $this->request->getPost('university_name_foculty_2'),
                "field_of_study_foculty_2" => $this->request->getPost('field_of_study_foculty_2'),
                "graduation_date_foculty_2" => $this->request->getPost('graduation_date_foculty_2'),
                "university_name_master_1" => $this->request->getPost('university_name_master_1'),
                "field_of_study_master_1" => $this->request->getPost('field_of_study_master_1'),
                "graduation_date_master_1" => $this->request->getPost('graduation_date_master_1'),
                "university_name_master_2" => $this->request->getPost('university_name_master_2'),
                "field_of_study_master_2" => $this->request->getPost('field_of_study_master_2'),
                "graduation_date_master_2" => $this->request->getPost('graduation_date_master_2'),
                "university_name_phd" => $this->request->getPost('university_name_phd'),
                "field_of_study_phd" => $this->request->getPost('field_of_study_phd'),
                "graduation_date_phd" => $this->request->getPost('graduation_date_phd'),
                "other_skills" => $this->request->getPost('other_skills'),
                "graduation_date_other_skills" => $this->request->getPost('graduation_date_other_skills'),
            );
           
    
    
            $this->Users_model->save_education_info($education_data);
                 
             
            save_custom_fields("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

            //send login details to user
            if ($this->request->getPost('email_login_details')) {

                //get the login details template
                $email_template = $this->Email_templates_model->get_final_template("login_info"); //use default template

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $this->request->getPost('password');
                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["LOGO_URL"] = get_logo_url();
                $parser_data["RECIPIENTS_EMAIL_ADDRESS"] = $user_data["email"];

                $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);

                send_app_mail($this->request->getPost('email'), $subject, $message);
            }
        }else {
            echo json_encode(array("success" => false, 'message' => 'Data Saving error.'));
        }

        if ($user_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($user_id), 'id' => $user_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }



    /* open invitation modal */

    function invitation_modal()
    {
        $this->access_only_admin_or_member_creator();

        $role_dropdown = array(
            "0" => app_lang('team_member')
        );

        $roles = $this->Roles_model->get_all()->getResult();
        foreach ($roles as $role) {
            $role_dropdown[$role->id] = $role->title;
        }

        $view_data['role_dropdown'] = $role_dropdown;

        return $this->template->view('team_members/invitation_modal', $view_data);
    }

    //send a team member invitation to an email address
    function send_invitation()
    {
        $this->access_only_admin_or_member_creator();

        $this->validate_submitted_data(array(
            "email.*" => "required|valid_email"
        ));

        $email_array = $this->request->getPost('email');
        $email_array = array_unique($email_array);

        //get the send invitation template 
        $email_template = $this->Email_templates_model->get_final_template("team_member_invitation"); //use default template

        $parser_data["INVITATION_SENT_BY"] = $this->login_user->first_name . " " . $this->login_user->last_name;
        $parser_data["SIGNATURE"] = $email_template->signature;
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["LOGO_URL"] = get_logo_url();

        $send_email = array();

        $role_id = $this->request->getPost('role');

        foreach ($email_array as $email) {
            $verification_data = array(
                "type" => "invitation",
                "code" => make_random_string(),
                "params" => serialize(array(
                    "email" => $email,
                    "type" => "staff",
                    "expire_time" => time() + (24 * 60 * 60), //make the invitation url with 24hrs validity
                    "role_id" => $role_id
                ))
            );

            $save_id = $this->Verification_model->ci_save($verification_data);
            $verification_info = $this->Verification_model->get_one($save_id);

            $parser_data['INVITATION_URL'] = get_uri("signup/accept_invitation/" . $verification_info->code);

            //send invitation email
            $message = $this->parser->setData($parser_data)->renderString($email_template->message);
            $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);

            $send_email[] = send_app_mail($email, $subject, $message);
        }

        if (!in_array(false, $send_email)) {
            if (count($send_email) != 0 && count($send_email) == 1) {
                echo json_encode(array('success' => true, 'message' => app_lang("invitation_sent")));
            } else {
                echo json_encode(array('success' => true, 'message' => app_lang("invitations_sent")));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

    function tickets_chart_report()
    {
        $this->_validate_tickets_report_access();

        $view_data['ticket_labels_dropdown'] = json_encode($this->make_labels_dropdown("ticket", "", true));
        $view_data['assigned_to_dropdown'] = json_encode($this->_get_assiged_to_dropdown());
        $view_data['ticket_types_dropdown'] = json_encode($this->_get_ticket_types_dropdown_list_for_filter());

        return $this->template->rander("tickets/reports/chart_report_container", $view_data);
    }

    private function _get_assiged_to_dropdown()
    {
        $assigned_to_dropdown = array(array("id" => "", "text" => "- " . app_lang("assigned_to") . " -"));

        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff", "status" => "active"));
        foreach ($assigned_to_list as $key => $value) {
            $assigned_to_dropdown[] = array("id" => $key, "text" => $value);
        }
        return $assigned_to_dropdown;
    }

    public function charts()
    {
        // Fetch bank usage data
        $data['bank_usage_data'] = $this->db->query("SELECT bank_name, COUNT(*) as count FROM rise_users JOIN rise_bank_names ON rise_users.bank_id = rise_bank_names.id GROUP BY bank_name")->getResultArray();

        // Fetch gender data
        // Fetch gender data
        $data['gender_data'] = $this->db->query("SELECT LOWER(gender) as gender, COUNT(*) as count FROM rise_users GROUP BY gender")->getResultArray();

        // Fetch marital status data
        $data['marital_status_data'] = $this->db->query("SELECT marital_status, COUNT(*) as count FROM rise_users GROUP BY marital_status")->getResultArray();

        // Fetch age level data
        $data['age_level_data'] = $this->db->query("SELECT age_level, COUNT(*) as count FROM rise_users GROUP BY age_level")->getResultArray();

        // Fetch marital status data
        $view_data['gender_data'] = json_encode($data['gender_data']);
        $view_data['marital_status_data'] = json_encode($data['marital_status_data']);
        $view_data['age_level_data'] = json_encode($data['age_level_data']);

        // Fetch age level data

        // Fetch job info data (static_salary and work_experience)
        $data['job_info_data'] = $this->db->query("SELECT salary, work_experience FROM rise_team_member_job_info")->getResultArray();

        // Convert the data into JSON format for use in the view
        $view_data['bank_usage_data'] = json_encode($data['bank_usage_data']);

        $view_data['job_info_data'] = json_encode($data['job_info_data']);

        // Render the view with the prepared data
        return $this->template->rander("team_members/reports/chart_report_container", $view_data);
    }



    //prepere the data for members list
    function list_data()
    {

        if (!$this->can_view_team_members_list()) {
            app_redirect("forbidden");
        }

        $result = $this->check_access('lead'); //here means documents for us.

        $role = get_array_value($result, 'role');
        $department_id = get_array_value($result, 'department_id');
        $created_by = get_array_value($result, 'created_by');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("team_members", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            'role' => $role,
            'created_by' => $created_by,
            'department_id' => $this->request->getPost("department_id") ? $this->request->getPost("department_id") : $department_id,
            "status" => $this->request->getPost("status"),
            "show_own_unit_only_user_id" => $this->show_own_unit_only_user_id(),
            "show_own_section_only_user_id" => $this->show_own_section_only_user_id(),
            "show_own_department_only_user_id" => $this->show_own_department_only_user_id(),
            "user_type" => "staff",
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("team_members", $this->login_user->is_admin, $this->login_user->user_type)
        );

        // var_dump($this->request->getPost("department_id"));
        // var_dump($options);
        // die();

        $list_data = $this->Users_model->get_details($options);

        $list_data = get_array_value($list_data, 'data') ? get_array_value($list_data, 'data') : $list_data->getResult();
        $recordsTotal =  get_array_value($list_data, 'recordsTotal');
        $recordsFiltered =  get_array_value($list_data, 'recordsFiltered');

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array(
            "data" => $result,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ));
    }



    //get a row data for member list
    function _row_data($id)
    {
        validate_numeric_value($id);
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("team_members", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );

        $data = $this->Users_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }



    //prepare team member list row
    private function _make_row($data, $custom_fields)
    {

        $image_url = get_avatar($data->image);


        // var_dump($a);
        // var_dump($image_url);
        // die();

        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";

        //check contact info view permissions
        $show_cotact_info = $this->can_view_team_members_contact_info();

        $row_data = array(
            $user_avatar,
            "<span title='" . htmlspecialchars($data->employee_id) . "'>" . $this->truncateText($data->employee_id, 20) . "</span>", // Job title with tooltip
            get_team_member_profile_link($data->id, "<span title='$full_name'>" . $this->truncateText($full_name, 20) . "</span>"),
            "<span title='" . htmlspecialchars($data->job_title) . "'>" . $this->truncateText($data->job_title, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->sc_name_so) . "'>" . $this->truncateText($data->sc_name_so, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->dp_name_so) . "'>" . $this->truncateText($data->dp_name_so, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->dp_short_name_so) . "'>" . $this->truncateText($data->dp_short_name_so, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->dp_name_en) . "'>" . $this->truncateText($data->dp_name_en, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->dp_short_name_en) . "'>" . $this->truncateText($data->dp_short_name_en, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->place_of_work) . "'>" . $this->truncateText($data->place_of_work, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->email) . "'>" . $this->truncateText($data->email, 20) . "</span>", // Job title with tooltip
            "<span title='" . htmlspecialchars($data->phone) . "'>" . $this->truncateText($data->phone, 20) . "</span>", // Job title with tooltip
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $delete_link = "";
        if ($this->_can_delete_team_member($data)) {
            $delete_link = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_team_member'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("team_members/delete"), "data-action" => "delete-confirmation"));
        }

        // $delete_link = "";

        // // if ($login_user->is_admin || get_array_value($login_user->permissions, "can_add_or_invite_new_team_members")) {
        //     $delete_link = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_team_member'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("team_members/delete"), "data-action" => "delete-confirmation"));
        // // }

        $row_data[] = $delete_link;

        return $row_data;
    }

    function truncateText($text, $maxLength) {
        if (strlen($text) > $maxLength) {
            return substr($text, 0, $maxLength) . '…'; // Append ellipsis
        }
        return $text;
    }

    //delete a team member
    function delete()
    {


        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        $user_info = $this->Users_model->get_one($id);
        if (!$this->_can_delete_team_member($user_info)) {
            app_redirect("forbidden");
        }

        if ($this->Users_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }



    //show team member's details view
    function view($id = 0, $tab = "")
    {
        if ((int)$id * 1) {
            validate_numeric_value($id);

            //if team member's list is disabled, but the user can see his/her own profile.
            if (!$this->can_view_team_members_list() && $this->login_user->id != $id) {
                app_redirect("forbidden");
            }

            //we have an id. view the team_member's profie
            $options = array("id" => $id, "user_type" => "staff");
            $user_info = $this->Users_model->get_details($options)->getRow();

            // var_dump($user_info);die;
            $age_levels = [
                '18 - 25' => '18 - 25',
                '26 - 35' => '26 - 35',
                '36 - 45' => '36 - 45',
                '46 -55' => '46 -55',
                '56 - 65' => '56 - 65',
                '66 ama kaweyn' => '66 ama kaweyn'
            ];

            $view_data['age_levels'] = $age_levels;

            if ($user_info) {

                // var_dump($user_info);
                //check which tabs are viewable for current logged in user
                $view_data['show_timeline'] = get_setting("module_timeline") ? true : false;

                $can_update_team_members_info = $this->can_update_team_members_info($id);

                $view_data['show_general_info'] = $can_update_team_members_info;
                $view_data['show_job_info'] = false;

                if ($this->login_user->is_admin || $user_info->id === $this->login_user->id || $this->has_job_info_manage_permission()) {
                    $view_data['show_job_info'] = true;
                }

                $view_data['show_account_settings'] = false;

                $show_attendance = false;
                $show_leave = false;

                $expense_access_info = $this->get_access_info("expense");
                $view_data["show_expense_info"] = (get_setting("module_expense") == "1" && $expense_access_info->access_type == "all") ? true : false;

                //admin can access all members attendance and leave
                //none admin users can only access to his/her own information 

                if ($this->login_user->is_admin || $user_info->id === $this->login_user->id || get_array_value($this->login_user->permissions, "can_manage_user_role_and_permissions")) {
                    $show_attendance = true;
                    $show_leave = true;
                    $view_data['show_account_settings'] = true;
                } else {
                    //none admin users but who has access to this team member's attendance and leave can access this info
                    $access_timecard = $this->get_access_info("attendance");
                    if ($access_timecard->access_type === "all" || in_array($user_info->id, $access_timecard->allowed_members)) {
                        $show_attendance = true;
                    }

                    $access_leave = $this->get_access_info("leave");
                    if ($access_leave->access_type === "all" || in_array($user_info->id, $access_leave->allowed_members)) {
                        $show_leave = true;
                    }
                }


                //check module availability
                $view_data['show_attendance'] = $show_attendance && get_setting("module_attendance") ? true : false;
                $view_data['show_leave'] = $show_leave && get_setting("module_leave") ? true : false;

                //check contact info view permissions
                $show_cotact_info = $this->can_view_team_members_contact_info();
                $show_social_links = $this->can_view_team_members_social_links();

                //own info is always visible
                if ($id == $this->login_user->id) {
                    $show_cotact_info = true;
                    $show_social_links = true;
                }

                $view_data['show_cotact_info'] = $show_cotact_info;
                $view_data['show_social_links'] = $show_social_links;

                //show projects tab to admin
                $view_data['show_projects'] = false;
                if ($this->login_user->is_admin) {
                    $view_data['show_projects'] = true;
                }

                $view_data['show_projects_count'] = false;
                if ($this->can_manage_all_projects() && !$this->has_all_projects_restricted_role()) {
                    $view_data['show_projects_count'] = true;
                }

                $view_data['tab'] = clean_data($tab); //selected tab
                $view_data['user_info'] = $user_info;
                $view_data['social_link'] = $this->Social_links_model->get_one($id);

                $hide_send_message_button = true;
                $this->init_permission_checker("message_permission");
                if ($this->check_access_on_messages_for_this_user() && $this->validate_sending_message($id)) {
                    $hide_send_message_button = false;
                }
                $view_data['hide_send_message_button'] = $hide_send_message_button;

                $view_data["show_notes"] = false;
                if ($this->can_access_team_members_note($user_info->id)) {
                    $view_data["show_notes"] = true;
                }

                return $this->template->rander("team_members/view", $view_data);
            } else {
                show_404();
            }
        } else {

            if (!$this->can_view_team_members_list()) {
                app_redirect("forbidden");
            }

            //we don't have any specific id to view. show the list of team_member
            $view_data['team_members'] = $this->Users_model->get_details(array("user_type" => "staff", "status" => "active"))->getResult();
            return $this->template->rander("team_members/profile_card", $view_data);
        }
    }

    //show the job information of a team member
    function job_info($user_id)
    {

        validate_numeric_value($user_id);
        if (!($this->login_user->is_admin || $this->login_user->id === $user_id || $this->has_job_info_manage_permission())) {
            app_redirect("forbidden");
        }

        // $view_data['departments'] = $this->get_departments_for_select();
        $view_data['departments'] = array("" => " -- Choose Department -- ") + $this->Departments_model->get_dropdown_list(array("nameSo"), "id");

        // echo json_encode($view_data['departments']);
        // die('ok');
        $view_data['Sections'] = array("" => " -- Choose Section -- ") + $this->Sections_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Units'] = array("" => " -- Choose Unit -- ") + $this->Units_model->get_dropdown_list(array("nameSo"), "id");

        $view_data['job_locations'] = array("" => " -- Choose Job Location -- ") + $this->Job_locations_model->get_dropdown_list(array("name"), "id");
        $view_data['grades'] = array("" => " -- Choose Grade -- ") + $this->Grades_model->get_dropdown_list(array("grade"), "id");

        $view_data['education_levels'] = [
            '' => ' -- Choose Education Level -- ',
            'Primary' => 'Primary',
            'Secondary' => 'Secondary',
            'Diploma' => 'Diploma',
            'Bachelor' => 'Bachelor',
            'Bachelor & Master' => 'Bachelor & Master',
            '2 Bachelors' => '2 Bachelors',
            '2 Bachelors & Master' => '2 Bachelors & Master',
            '2 Bachelors & 2 Masters' => '2 Bachelors & 2 Masters',
            'Doctor' => 'Doctor',
            'Other/Skill' => 'Other/Skill'
        ];
        $view_data['sections'] = ['' => 'Choose Department Section', '1' => 'ICT & Cyber Security', '2' => 'Other'];

        // var_dump($view_data['departments']);
        // die();

        $options = array("id" => $user_id);
        $user_info = $this->Users_model->get_details($options)->getRow();

        $view_data['user_id'] = $user_id;
        $view_data['job_info'] = $this->Users_model->get_job_info($user_id);
        $view_data['job_info']->job_title = $user_info->job_title;

        $view_data['can_manage_team_members_job_information'] = $this->has_job_info_manage_permission();
        $view_data['can_edit_profile']  = !$this->can_edit_profile();

        return $this->template->view("team_members/job_info", $view_data);
    }


    private function has_job_info_manage_permission()
    {
        return get_array_value($this->login_user->permissions, "job_info_manage_permission");
    }



    //save job information of a team member
    function save_job_info()
    {

        if (!($this->login_user->is_admin || $this->has_job_info_manage_permission())) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "user_id" => "required|numeric"
        ));

        $user_id = $this->request->getPost('user_id');

        $target_path = get_setting("signature_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "signature");
        $new_files = unserialize($files_data);


        $job_data = array(
            "user_id" => $user_id,
            "employee_type" => $this->request->getPost('employee_type'),
            "department_id" => $this->request->getPost('department_id'),
            "section_id" => $this->request->getPost('section_id'),
            "unit_id" => $this->request->getPost('unit_id'),
            "grade_id" => $this->request->getPost('grade_id'),
            "job_title_so" => $this->request->getPost('job_title_so'),
            "job_title_en" => $this->request->getPost('job_title_en'),
            "job_description" => $this->request->getPost('job_description'),
            "work_experience" => $this->request->getPost('work_experience'),
            "job_location_id" => $this->request->getPost('job_location'),
            "date_of_hire" => $this->request->getPost('date_of_hire'),
            "employee_id" => $this->request->getPost('employee_id'),

            "salary" => unformat_currency($this->request->getPost('salary')),
            "salary_term" => $this->request->getPost('salary_term'),
        );


        $user_data = array(
            "job_title" => $this->request->getPost('job_title_en'),
            "employee_id" => $this->request->getPost('employee_id'),
            "department_id" => $this->request->getPost('department_id'),
        );

        if ($user_id) {
            $user_j0b_info = $this->Users_model->get_details(['id' => $user_id])->getRow();
            $timeline_file_path = get_setting("signature_file_path");
            $new_files = update_saved_files($timeline_file_path, $user_j0b_info->signature, $new_files);
        }


        $job_data["signature"] = serialize($new_files);


        $this->Users_model->ci_save($user_data, $user_id);

        if ($this->Users_model->save_job_info($job_data)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }


    //show general information of a team member
    function general_info($user_id)
    {
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $view_data['departments'] = $this->Team_model->get_departments_for_select();
        $view_data['education_levels'] = [
            '' => ' -- Choose Education Level -- ',
            'Primary' => 'Primary',
            'Secondary' => 'Secondary',
            'Diploma' => 'Diploma',
            'Bachelor' => 'Bachelor',
            'Bachelor & Master' => 'Bachelor & Master',
            '2 Bachelors' => '2 Bachelors',
            '2 Bachelors & Master' => '2 Bachelors & Master',
            '2 Bachelors & 2 Masters' => '2 Bachelors & 2 Masters',
            'Doctor' => 'Doctor',
            'Other/Skill' => 'Other/Skill'
        ];
        $view_data['sections'] = ['' => 'Choose Department Section', '1' => 'ICT & Cyber Security', '2' => 'Other'];
        $view_data['education_fields'] = $this->db->query("select id,name from rise_education_industry where deleted=0")->getResult();

        $view_data['user_info'] = $this->Users_model->get_one($user_id);
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        $age_levels = [
            '' => '-- Choose Age Level --',
            '18 - 25' => '18 - 25',
            '26 - 35' => '26 - 35',
            '36 - 45' => '36 - 45',
            '46 -55' => '46 -55',
            '56 - 65' => '56 - 65',
            '66 ama kaweyn' => '66 ama kaweyn'
        ];

        $view_data['age_levels'] = $age_levels;
        $view_data['can_edit_profile']  = !$this->can_edit_profile();

        return $this->template->view("team_members/general_info", $view_data);
    }

    //save general information of a team member
    function save_general_info($user_id)
    {

        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required"
        ));

        $user_data = array(

            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "address" => $this->request->getPost('address'),
            "alternative_address" => $this->request->getPost('alternative_address'),
            "phone" => $this->request->getPost('phone'),
            "alternative_phone" => $this->request->getPost('alternative_phone'),
            "skype" => $this->request->getPost('skype'),
            "ssn" => $this->request->getPost('ssn'),
            "gender" => $this->request->getPost('gender'),
            "marital_status" => $this->request->getPost('marital_status'),
            "age_level" => $this->request->getPost('age_level'),
            "birth_date" => $this->request->getPost('birth_date'),
            "birth_place" => $this->request->getPost('birth_place'),
            "passport_no" => $this->request->getPost('passport_no'),
            "relevant_document_url" => $this->request->getPost('relevant_document_url'),
            "emergency_name" => $this->request->getPost('emergency_name'),
            "emergency_phone" => $this->request->getPost('emergency_phone'),
            "dob" => $this->request->getPost('dob')
        );

        $user_data = clean_data($user_data);

        $user_info_updated = $this->Users_model->ci_save($user_data, $user_id);

        save_custom_fields("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

        if ($user_info_updated) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //show general information of a team member
    function education_info($user_id)
    {
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $view_data['departments'] = $this->Team_model->get_departments_for_select();

        $view_data['education_levels'] = [
            '' => ' -- Choose Education Level -- ',
            'Primary' => 'Primary',
            'Secondary' => 'Secondary',
            'Diploma' => 'Diploma',
            'Bachelor' => 'Bachelor',
            'Bachelor & Master' => 'Bachelor & Master',
            '2 Bachelors' => '2 Bachelors',
            '2 Bachelors & Master' => '2 Bachelors & Master',
            '2 Bachelors & 2 Masters' => '2 Bachelors & 2 Masters',
            'Doctor' => 'Doctor',
            'Other/Skill' => 'Other/Skill'
        ];
        // print_r($view_data['education_levels']);die();
        $view_data['sections'] = ['' => 'Choose Department Section', '1' => 'ICT & Cyber Security', '2' => 'Other'];
        $view_data['education_fields'] = $this->db->query("select id,name from rise_education_industry where deleted=0")->getResult();
        $view_data['field_of_study'] = array("" => " -- Choose Field of Study -- ") + $this->Field_of_study_model->get_dropdown_list(array("name"), "id");
        $view_data['university_names'] = array("" => " -- Choose University Name -- ") + $this->University_names_model->get_dropdown_list(array("university_name"), "id");

        // $options = array("id" => $user_id);
        // $user_info = $this->Users_model->get_details($options)->getRow();

        $view_data['user_id'] = $user_id;
        $view_data['education_info'] = $this->Users_model->get_education_info($user_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        $view_data['can_edit_profile']  = !$this->can_edit_profile();

        return $this->template->view("team_members/education_info", $view_data);
    }


    //save general information of a team member
    function save_education_info($user_id)
    {


        $user_id = $this->request->getPost('user_id');


        $education_data = array(
            "user_id" => $user_id,
            "education_level" => $this->request->getPost('education_level'),
            "primary_school_name" => $this->request->getPost('primary_school_name'),
            "primary_graduation_date" => $this->request->getPost('primary_graduation_date'),
            "secondary_school_name" => $this->request->getPost('secondary_school_name'),
            "secondary_graduation_date" => $this->request->getPost('secondary_graduation_date'),
            "university_name_diploma" => $this->request->getPost('university_name_diploma'),
            "field_of_study_diploma" => $this->request->getPost('field_of_study_diploma'),
            "graduation_date_diploma" => $this->request->getPost('graduation_date_diploma'),
            "university_name_foculty_1" => $this->request->getPost('university_name_foculty_1'),
            "field_of_study_foculty_1" => $this->request->getPost('field_of_study_foculty_1'),
            "graduation_date_foculty_1" => $this->request->getPost('graduation_date_foculty_1'),
            "university_name_foculty_2" => $this->request->getPost('university_name_foculty_2'),
            "field_of_study_foculty_2" => $this->request->getPost('field_of_study_foculty_2'),
            "graduation_date_foculty_2" => $this->request->getPost('graduation_date_foculty_2'),
            "university_name_master_1" => $this->request->getPost('university_name_master_1'),
            "field_of_study_master_1" => $this->request->getPost('field_of_study_master_1'),
            "graduation_date_master_1" => $this->request->getPost('graduation_date_master_1'),
            "university_name_master_2" => $this->request->getPost('university_name_master_2'),
            "field_of_study_master_2" => $this->request->getPost('field_of_study_master_2'),
            "graduation_date_master_2" => $this->request->getPost('graduation_date_master_2'),
            "university_name_phd" => $this->request->getPost('university_name_phd'),
            "field_of_study_phd" => $this->request->getPost('field_of_study_phd'),
            "graduation_date_phd" => $this->request->getPost('graduation_date_phd'),
            "other_skills" => $this->request->getPost('other_skills'),
            "graduation_date_other_skills" => $this->request->getPost('graduation_date_other_skills'),
        );



        // if ($user_id) {
        //     $user_j0b_info = $this->Users_model->get_details(['id'=>$user_id])->getRow();
        //     // print_r($user_j0b_info);die;
        //     $timeline_file_path = get_setting("signature_file_path");
        //     $new_files = update_saved_files($timeline_file_path, $user_j0b_info->signature, $new_files);
        // }

        //     $job_data["signature"] = serialize($new_files);


        // $this->Users_model->ci_save($user_data, $user_id);

        if ($this->Users_model->save_education_info($education_data)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

        // education

        // validate_numeric_value($user_id);
        // $this->update_only_allowed_members($user_id);


        // $user_data = array(

        //     "education_level" => $this->request->getPost('education_level'),
        //     "education_field" => $this->request->getPost('education_field'),
        //     "faculty" => $this->request->getPost('faculty'),
        //     "faculty2" => $this->request->getPost('faculty2'),
        //     "education_school" => $this->request->getPost('education_school'),
        //     "bachelor_degree" => $this->request->getPost('bachelor_degree'),
        //     "master_degree" => $this->request->getPost('master_degree'),

        // );

        // $user_data = clean_data($user_data);

        // $user_info_updated = $this->Users_model->ci_save($user_data, $user_id);

        // save_custom_fields("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

        // if ($user_info_updated) {
        //     echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        // } else {
        //     echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        // }
    }

    //show general information of a team member
    function bank_details($user_id)
    {
        
        $role = $this->get_user_role(); // Assuming this method returns the user's role
        $view_data['is_admin'] = ($role === 'admin'); // Check if the user is an admin        
        
        // Set readonly attribute based on user's role
        $view_data['readonly'] = ($role === 'admin') ? array() : array('readonly' => true);
        $view_data['disabled'] = ($role === 'admin') ? array() : array('disabled' => true);
         
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);


        // array_unshift($view_data['departments'],'Choose Department');
        

        $view_data['bank_names_dropdown'] = array("" => " - ") + $this->Bank_names_model->get_dropdown_list(array("bank_name"), "id");


        $view_data['user_info'] = $this->Users_model->get_one($user_id);
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        $view_data['can_edit_profile']  = !$this->can_edit_profile();

        return $this->template->view("team_members/bank_details", $view_data);
    }

    //save general information of a team member
    function save_bank_details($user_id)
    {

        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);


        $user_data = array(
            "bank_id" => $this->request->getPost('bank_id'),
            "bank_account" => $this->request->getPost('bank_account'),
            "registered_name" => $this->request->getPost('bank_registered_name'),
        );

        $user_data = clean_data($user_data);

        $user_info_updated = $this->Users_model->ci_save($user_data, $user_id);

        save_custom_fields("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

        if ($user_info_updated) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }
    // //show social links of a team member

    //show social links of a team member
    function social_links($user_id)
    {
        //important! here id=user_id
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $view_data['user_id'] = $user_id;
        $view_data['model_info'] = $this->Social_links_model->get_one($user_id);
        $view_data['can_edit_profile']  = !$this->can_edit_profile();

        return $this->template->view("users/social_links", $view_data);
    }

    //save social links of a team member
    function save_social_links($user_id)
    {
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $id = 0;
        $has_social_links = $this->Social_links_model->get_one($user_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->request->getPost('facebook'),
            "twitter" => $this->request->getPost('twitter'),
            "linkedin" => $this->request->getPost('linkedin'),
            "digg" => $this->request->getPost('digg'),
            "youtube" => $this->request->getPost('youtube'),
            "pinterest" => $this->request->getPost('pinterest'),
            "instagram" => $this->request->getPost('instagram'),
            "github" => $this->request->getPost('github'),
            "tumblr" => $this->request->getPost('tumblr'),
            "vine" => $this->request->getPost('vine'),
            "whatsapp" => $this->request->getPost('whatsapp'),
            "user_id" => $user_id,
            "id" => $id ? $id : $user_id
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->ci_save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
    }


    //show account settings of a team member
    function account_settings($user_id)
    {
        validate_numeric_value($user_id);
        // $this->can_access_user_settings($user_id);

        $view_data['user_info'] = $this->Users_model->get_one($user_id);
        if ($view_data['user_info']->is_admin) {
            $view_data['user_info']->role_id = "admin";
        }
        $view_data['role_dropdown'] = $this->_get_roles_dropdown();
        $view_data['can_activate_deactivate_team_members'] = $this->_can_activate_deactivate_team_member($view_data['user_info']);
        $view_data['can_edit_profile']  = !$this->can_edit_profile();

        return $this->template->view("users/account_settings", $view_data);
    }

    //show my preference settings of a team member
    function my_preferences()
    {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);

        //language dropdown
        $view_data['language_dropdown'] = array();
        if (!get_setting("disable_language_selector_for_team_members")) {
            $view_data['language_dropdown'] = get_language_list();
        }

        $view_data["hidden_topbar_menus_dropdown"] = $this->get_hidden_topbar_menus_dropdown();
        $view_data["recently_meaning_dropdown"] = $this->get_recently_meaning_dropdown();

        return $this->template->view("team_members/my_preferences", $view_data);
    }

    function save_my_preferences()
    {
        //setting preferences
        $settings = array("notification_sound_volume", "disable_push_notification", "hidden_topbar_menus", "disable_keyboard_shortcuts", "recently_meaning", "reminder_sound_volume", "reminder_snooze_length");

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting("user_" . $this->login_user->id . "_" . $setting, $value, "user");
        }

        //there was 3 settings in users table.
        //so, update the users table also

        $user_data = array(
            "enable_web_notification" => $this->request->getPost("enable_web_notification"),
            "enable_email_notification" => $this->request->getPost("enable_email_notification"),
        );

        if (!get_setting("disable_language_selector_for_team_members")) {
            $user_data["language"] = $this->request->getPost("personal_language");
        }

        $user_data = clean_data($user_data);

        $this->Users_model->ci_save($user_data, $this->login_user->id);

        try {
            app_hooks()->do_action("app_hook_team_members_my_preferences_save_data");
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function save_personal_language($language)
    {
        if (!get_setting("disable_language_selector_for_team_members") && ($language || $language === "0")) {

            $language = clean_data($language);
            $data["language"] = strtolower($language);

            $this->Users_model->ci_save($data, $this->login_user->id);
        }
    }

    //save account settings of a team member
    function save_account_settings($user_id)
    {
        validate_numeric_value($user_id);
        $this->can_access_user_settings($user_id);

        if ($this->Users_model->is_email_exists($this->request->getPost('email'), $user_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
            exit();
        }

        $account_data = array(
            "email" => $this->request->getPost('email'),
            "private_email" => $this->request->getPost('private_email'),
            "login_type" => $this->request->getPost('login_type')
        );

        // var_dump($account_data);
        // die();

        $role = $this->request->getPost('role');
        $user_info = $this->Users_model->get_one($user_id);

        if (!$this->is_own_id($user_id) && ($this->login_user->is_admin || (!$user_info->is_admin && $this->has_role_manage_permission() && !$this->is_admin_role($role)))) {
            //only admin user/eligible user has permission to update team member's role
            //but admin user/eligible user can't update his/her own role 
            //eligible user can't update admin user's role or can't give admin role to anyone
            $role_id = $role;

            if ($this->login_user->is_admin && $role === "admin") {
                $account_data["is_admin"] = 1;
                $account_data["role_id"] = 0;
            } else {
                $account_data["is_admin"] = 0;
                $account_data["role_id"] = $role_id;
            }

            if ($this->_can_activate_deactivate_team_member($user_info)) {
                $account_data['disable_login'] = $this->request->getPost('disable_login');
                $account_data['status'] = $this->request->getPost('status') === "inactive" ? "inactive" : "active";
            }
        }

        //don't reset password if user doesn't entered any password
        if ($this->request->getPost('password') && ($this->login_user->is_admin || $this->is_own_id($user_id))) {
            $account_data['password'] = password_hash($this->request->getPost("password"), PASSWORD_DEFAULT);
        }

        if ($this->Users_model->ci_save($account_data, $user_id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save profile image of a team member
    function save_profile_image($user_id = 0)
    {
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);
        $user_info = $this->Users_model->get_one($user_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->request->getPost("profile_image"));

        if ($profile_image) {
            $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image));

            //delete old file
            delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));

            $image_data = array("image" => $profile_image);

            $this->Users_model->ci_save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            $image_file_size = get_array_value($profile_image_file, "size");
            if ($image_file_name) {
                if (!$this->check_profile_image_dimension($image_file_name)) {
                    echo json_encode(array("success" => false, 'message' => app_lang('profile_image_error_message')));
                    exit();
                }

                $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name, "", "", false, $image_file_size));

                //delete old file
                if ($user_info->image) {
                    delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));
                }

                $image_data = array("image" => $profile_image);
                $this->Users_model->ci_save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed'), "reload_page" => true));
            }
        }
    }

    //show projects list of a team member
    function projects_info($user_id)
    {
        if ($user_id) {
            validate_numeric_value($user_id);
            $view_data['user_id'] = $user_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("projects", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data['project_statuses'] = $this->Project_status_model->get_details()->getResult();
            return $this->template->view("team_members/projects_info", $view_data);
        }
    }

    //show attendance list of a team member
    function attendance_info($user_id)
    {
        if ($user_id) {
            validate_numeric_value($user_id);
            $view_data['user_id'] = $user_id;
            return $this->template->view("team_members/attendance_info", $view_data);
        }
    }

    //show weekly attendance list of a team member
    function weekly_attendance()
    {
        return $this->template->view("team_members/weekly_attendance");
    }

    //show weekly attendance list of a team member
    function custom_range_attendance()
    {
        return $this->template->view("team_members/custom_range_attendance");
    }

    //show attendance summary of a team member
    function attendance_summary($user_id)
    {
        validate_numeric_value($user_id);
        $view_data["user_id"] = $user_id;
        return $this->template->view("team_members/attendance_summary", $view_data);
    }

    //show leave list of a team member
    function leave_info($applicant_id)
    {
        if ($applicant_id) {
            validate_numeric_value($applicant_id);
            $view_data['applicant_id'] = $applicant_id;
            return $this->template->view("team_members/leave_info", $view_data);
        }
    }

    //show yearly leave list of a team member
    function yearly_leaves()
    {
        return $this->template->view("team_members/yearly_leaves");
    }

    //show yearly leave list of a team member
    function expense_info($user_id)
    {
        validate_numeric_value($user_id);
        $view_data["user_id"] = $user_id;
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("expenses", $this->login_user->is_admin, $this->login_user->user_type);
        return $this->template->view("team_members/expenses", $view_data);
    }

    /* load files tab */

    function files($user_id)
    {
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $options = array("user_id" => $user_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->getResult();
        $view_data['user_id'] = $user_id;
        return $this->template->view("team_members/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form()
    {
        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $user_id = $this->request->getPost('user_id') ? $this->request->getPost('user_id') : $view_data['model_info']->user_id;

        $this->update_only_allowed_members($user_id);

        $view_data['user_id'] = $user_id;
        return $this->template->view('team_members/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file()
    {


        $this->validate_submitted_data(array(
            "id" => "numeric",
            "user_id" => "required|numeric"
        ));

        $user_id = $this->request->getPost('user_id');
        $this->update_only_allowed_members($user_id);

        $files = $this->request->getPost("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("team_members", $user_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->request->getPost('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "user_id" => $user_id,
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->ci_save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($user_id = 0)
    {
        validate_numeric_value($user_id);
        $options = array("user_id" => $user_id);

        $this->update_only_allowed_members($user_id);

        $list_data = $this->General_files_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data)
    {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='float-start'>" .
            js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("team_members/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("team_members/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        if ($this->login_user->is_admin || $data->uploaded_by == $this->login_user->id) {
            $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("team_members/delete_file"), "data-action" => "delete-confirmation"));
        }

        return array(
            $data->id,
            "<div data-feather='$file_icon' class='mr10 float-start'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0)
    {
        validate_numeric_value($file_id);
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();

        if ($file_info) {

            if (!$file_info->user_id) {
                app_redirect("forbidden");
            }

            $this->update_only_allowed_members($file_info->user_id);

            $view_data['can_comment_on_files'] = false;

            $view_data["file_url"] = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("team_members", $file_info->user_id));
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            return $this->template->view("team_members/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id)
    {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->user_id) {
            app_redirect("forbidden");
        }
        $this->update_only_allowed_members($file_info->user_id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        return $this->download_app_files(get_general_file_path("team_members", $file_info->user_id), $file_data);
    }

    /* upload a post file */

    function upload_file()
    {
        upload_file_to_temp();
    }

    function validate_team_file()
    {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* check valid file for user */

    function validate_file()
    {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */

    function delete_file()
    {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->user_id) {
            app_redirect("forbidden");
        }

        if ($info->user_id && ($this->login_user->is_admin || $this->login_user->id === $info->uploaded_by)) {
            if ($this->General_files_model->delete($id)) {

                //delete the files
                delete_app_files(get_general_file_path("team_members", $info->user_id), array(make_array_of_file($info)));

                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        } else {
            app_redirect("forbidden");
        }
    }

    /* show keyboard shortcut modal form */

    function keyboard_shortcut_modal_form()
    {
        return $this->template->view('team_members/keyboard_shortcut_modal_form');
    }

    private function get_recently_meaning_dropdown()
    {
        return array(
            "2_hours" => app_lang("in") . " 2 " . strtolower(app_lang("hours")),
            "5_hours" => app_lang("in") . " 5 " . strtolower(app_lang("hours")),
            "8_hours" => app_lang("in") . " 8 " . strtolower(app_lang("hours")),
            "1_days" => app_lang("in") . " 1 " . strtolower(app_lang("day")),
            "2_days" => app_lang("in") . " 2 " . strtolower(app_lang("days")),
            "3_days" => app_lang("in") . " 3 " . strtolower(app_lang("days")),
            "5_days" => app_lang("in") . " 5 " . strtolower(app_lang("days")),
            "7_days" => app_lang("in") . " 7 " . strtolower(app_lang("days")),
            "15_days" => app_lang("in") . " 15 " . strtolower(app_lang("days")),
            "1_month" => app_lang("in") . " 1 " . strtolower(app_lang("month")),
        );
    }

    function recently_meaning_modal_form()
    {
        $view_data["recently_meaning_dropdown"] = $this->get_recently_meaning_dropdown();
        return $this->template->view('tasks/recently_meaning_modal_form', $view_data);
    }

    function save_recently_meaning()
    {
        $recently_meaning = $this->request->getPost("recently_meaning");
        $this->Settings_model->save_setting("user_" . $this->login_user->id . "_recently_meaning", $recently_meaning, "user");
        echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
    }

    /* load notes tab  */

    function notes($user_id)
    {
        validate_numeric_value($user_id);
        $this->can_access_team_members_note($user_id);

        if ($user_id) {
            $view_data['user_id'] = clean_data($user_id);
            return $this->template->view("team_members/notes/index", $view_data);
        }
    }
}

/* End of file team_member.php */
/* Location: ./app/controllers/team_member.php */