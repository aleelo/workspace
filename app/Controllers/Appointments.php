<?php

namespace App\Controllers;

class Appointments extends Security_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("appointment");
    }

    private function _validate_client_manage_access($client_id = 0) {
        if (!$this->can_edit_clients($client_id)) {
            app_redirect("forbidden");
        }
    }

    private function _validate_client_view_access($client_id = 0) {
        if (!$this->can_view_clients($client_id)) {
            app_redirect("forbidden");
        }
    }

    private function _get_secretary_director()
    {

        $role = $this->get_user_role();
        $user_id = $this->login_user->id;

        $options = array(
            "user_id" => $user_id,
        );

        $team_members = $this->Appointments_model->get_secretary_director($options);
        
        $team_members = get_array_value($team_members,'data') ? get_array_value($team_members,'data') : $team_members->getResult(); 
        $recordsTotal =  get_array_value($team_members,'recordsTotal');
        $recordsFiltered =  get_array_value($team_members,'recordsFiltered');
        
        $result = array();
        foreach ($team_members as $t) {
            $temp_array[$t->id] = $t->name;
        }


        // $view_data["host"] = $temp_array;
        return $temp_array;
    }

    /* load clients list view */

    function index($tab = "") {

        $this->check_module_availability("module_appointment");
        $this->access_only_allowed_members();

        $view_data = $this->make_access_permissions_view_data();

        // $view_data['can_edit_clients'] = $this->can_edit_clients();
        $view_data["show_project_info"] = $this->can_manage_all_projects() && !$this->has_all_projects_restricted_role();

        $view_data["show_own_clients_only_user_id"] = $this->show_own_clients_only_user_id();
        $view_data["allowed_client_groups"] = $this->allowed_client_groups;

        $view_data['tab'] = clean_data($tab);

        

        return $this->template->rander("appointments/index", $view_data);
    }

    function appointments_calendar($encrypted_event_id = "") {
        $view_data['encrypted_event_id'] = clean_data($encrypted_event_id);
        $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown();
        $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, app_lang("event_label")));
        return $this->template->view("appointments/appointments_calendar", $view_data);
    }


    public function get_appointments() {
        $appointments = $this->Appointments_model->get_all(); // Fetch appointments from the database

        $events = array();
        foreach ($appointments as $appointment) {
            $events[] = array(
                'id' => $appointment->id,
                'title' => $appointment->title,
                'start' => $appointment->start_date,
                'end' => $appointment->end_date,
                'color' => $appointment->color
            );
        }

        echo json_encode($events);
    }

    /* load client add/edit modal */

    function modal_form() {
        
        $appointments_id = $this->request->getPost('id');
        // $this->_validate_client_manage_access($appointments_id);

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-2 text-right";
        $view_data['field_column'] = "col-md-10";

        $view_data['label_column_2'] = "col-md-2 text-right";
        $view_data['field_column_2'] = "col-md-4";

        $view_data['field_column_3'] = "col-md-10";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loading from the client's details view
        $view_data["ticket_id"] = $this->request->getPost('ticket_id'); //needed only when loading from the ticket's details view and created by unknown client
        $view_data['model_info'] = $this->Appointments_model->get_one($appointments_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
        $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;

        $role = $this->get_user_role();
        $user_id = $this->login_user->id;

        if($role === "Secretary"){
            $view_data['host'] = $this->_get_secretary_director();
        }else{
            $view_data['host'] = array("" => " -- Choose Host -- ") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");
        }

        $view_data['departments'] = $this->Departments_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Sections'] = $this->Sections_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Units'] = $this->Units_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['payers'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id");
        $view_data['partners'] = $this->Partners_model->get_dropdown_list(array("name"), "id");
        $view_data['guests'] = $this->Visitors_model->get_dropdown_list(array("name"), "id");
        $view_data['employees'] = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");

        // $view_data['Section_heads'] = array("" => " -- Choose Section Head -- ") + $this->Users_model->get_dropdown_list(array("first_name"," ","last_name")), "id");

        $view_data['label_suggestions'] = $this->make_labels_dropdown("client", $view_data['model_info']->labels);

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();


        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        //prepare label suggestions

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $appointments_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('appointments/modal_form', $view_data);
    }

    function decline_reason() {
        
        $appointments_id = $this->request->getPost('id');
        // $this->_validate_client_manage_access($appointments_id);

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-2 text-right";
        $view_data['field_column'] = "col-md-10";

        $view_data['label_column_2'] = "col-md-2 text-right";
        $view_data['field_column_2'] = "col-md-4";

        $view_data['field_column_3'] = "col-md-10";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loading from the client's details view
        $view_data["ticket_id"] = $this->request->getPost('ticket_id'); //needed only when loading from the ticket's details view and created by unknown client
        $view_data['model_info'] = $this->Appointments_model->get_one($appointments_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
        $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;

        $role = $this->get_user_role();
        $user_id = $this->login_user->id;

        if($role === "Secretary"){
            $view_data['host'] = $this->_get_secretary_director();
        }else{
            $view_data['host'] = array("" => " -- Choose Host -- ") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");
        }

        $view_data['departments'] = $this->Departments_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Sections'] = $this->Sections_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Units'] = $this->Units_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['payers'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id");
        $view_data['partners'] = $this->Partners_model->get_dropdown_list(array("name"), "id");
        $view_data['guests'] = $this->Visitors_model->get_dropdown_list(array("name"), "id");
        $view_data['employees'] = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");

        // $view_data['Section_heads'] = array("" => " -- Choose Section Head -- ") + $this->Users_model->get_dropdown_list(array("first_name"," ","last_name")), "id");

        $view_data['label_suggestions'] = $this->make_labels_dropdown("client", $view_data['model_info']->labels);

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();


        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        //prepare label suggestions

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $appointments_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('appointments/decline_reason', $view_data);
    }

    public function send_appointment_created_email($data = array()) {

        $host_email = $data['HOST_EMAIL'];
        $secretary_email = $data['SECRETARY_EMAIL'];

        $host_email_template = $this->Email_templates_model->get_final_template("appointment_created_to_host_email", true);
        $secretary_email_template = $this->Email_templates_model->get_final_template("appointment_created_to_sectetary_email", true);
 
        $parser_data["APPOINTMENT_ID"] = $data['APPOINTMENT_ID'];
        $parser_data["APPOINTMENT_TITLE"] = $data['APPOINTMENT_TITLE'];
        $parser_data["APPOINTMENT_DATE"] = $data['APPOINTMENT_DATE'];
        $parser_data["APPOINTMENT_TIME"] = $data['APPOINTMENT_TIME'];
        $parser_data["APPOINTMENT_ROOM"] = $data['APPOINTMENT_ROOM'];
        $parser_data["APPOINTMENT_NOTE"] = $data['APPOINTMENT_NOTE'];
        $parser_data["APPOINTMENT_MEETING_WITH"] = $data['APPOINTMENT_MEETING_WITH'];
        // $parser_data["APPOINTMENT_DECLINE_REASON"] = $data['APPOINTMENT_DECLINE_REASON'];
        $parser_data["HOST_NAME"] = $data['HOST_NAME'];
        $parser_data["HOST_DEPARTMENT"] = $data['HOST_DEPARTMENT'];
        $parser_data["SECRETARY_NAME"] = $data['SECRETARY_NAME'];
        $parser_data["REGARD_NAME"] = $data['REGARD_NAME'];
        $parser_data["REGARD_POSITION"] = $data['REGARD_POSITION'];
        $parser_data["MEETING_WITH_NAMES"] = $data['MEETING_WITH_NAMES'];

        $parser_data["LEAVE_URL"] = get_uri('leaves');
        $parser_data["SIGNATURE"] = get_array_value($host_email_template, "signature_default");
        $parser_data["SIGNATURE"] = get_array_value($secretary_email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["EMAIL_HEADER_URL"] = get_uri('assets/images/email_header.jpg');
        $parser_data["EMAIL_FOOTER_URL"] = get_uri('assets/images/email_footer.png');
 
        $host_message =  get_array_value($host_email_template, "message_default");
        $host_subject =  get_array_value($host_email_template, "subject_default");

        $host_message = $this->parser->setData($parser_data)->renderString($host_message);
        $host_subject = $this->parser->setData($parser_data)->renderString($host_subject);

        if(!empty($host_email)){
            $hrm_email =  send_app_mail($host_email, $host_subject, $host_message);
        }

        $secretary_message =  get_array_value($secretary_email_template, "message_default");
        $secretary_subject =  get_array_value($secretary_email_template, "subject_default");
 
        $secretary_message = $this->parser->setData($parser_data)->renderString($secretary_message);
        $secretary_subject = $this->parser->setData($parser_data)->renderString($secretary_subject);
 
        if(!empty($secretary_email)){
            $hrm_email =  send_app_mail($secretary_email, $secretary_subject, $secretary_message);
        }

 
    }

    public function send_appointment_created_email_participant($data = array()) {

        $participant_email = $data['PARTICIPANT_EMAIL'];

        $participant_email_template = $this->Email_templates_model->get_final_template("appointment_created_to_participant_email", true);
 
        $parser_data["APPOINTMENT_ID"] = $data['APPOINTMENT_ID'];
        $parser_data["APPOINTMENT_TITLE"] = $data['APPOINTMENT_TITLE'];
        $parser_data["APPOINTMENT_DATE"] = $data['APPOINTMENT_DATE'];
        $parser_data["APPOINTMENT_TIME"] = $data['APPOINTMENT_TIME'];
        $parser_data["APPOINTMENT_ROOM"] = $data['APPOINTMENT_ROOM'];
        $parser_data["APPOINTMENT_NOTE"] = $data['APPOINTMENT_NOTE'];
        $parser_data["APPOINTMENT_MEETING_WITH"] = $data['APPOINTMENT_MEETING_WITH'];
        // $parser_data["APPOINTMENT_DECLINE_REASON"] = $data['APPOINTMENT_DECLINE_REASON'];
        $parser_data["HOST_NAME"] = $data['HOST_NAME'];
        $parser_data["HOST_DEPARTMENT"] = $data['HOST_DEPARTMENT'];
        $parser_data["SECRETARY_NAME"] = $data['SECRETARY_NAME'];
        $parser_data["REGARD_NAME"] = $data['REGARD_NAME'];
        $parser_data["REGARD_POSITION"] = $data['REGARD_POSITION'];
        $parser_data["PARTICIPANT_NAME"] = $data['PARTICIPANT_NAME'];
        $parser_data["MEETING_WITH_NAMES"] = $data['MEETING_WITH_NAMES'];

        $parser_data["LEAVE_URL"] = get_uri('leaves');
        $parser_data["SIGNATURE"] = get_array_value($participant_email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["EMAIL_HEADER_URL"] = get_uri('assets/images/email_header.jpg');
        $parser_data["EMAIL_FOOTER_URL"] = get_uri('assets/images/email_footer.png');
 
        $participant_message =  get_array_value($participant_email_template, "message_default");
        $participant_subject =  get_array_value($participant_email_template, "subject_default");

        $participant_message = $this->parser->setData($parser_data)->renderString($participant_message);
        $participant_subject = $this->parser->setData($parser_data)->renderString($participant_subject);

        if(!empty($participant_email)){
            $hrm_email =  send_app_mail($participant_email, $participant_subject, $participant_message);
        }
 
     }

    public function send_notify_appointment_status_email($data = array()) {

        $host_email = $data['HOST_EMAIL'];
        $secretary_email = $data['SECRETARY_EMAIL'];
 
        $status = $data['APPOINTMENT_STATUS'];
 
         if($status == 'approved'){
            $host_email_template = $this->Email_templates_model->get_final_template("appointment_approved_to_host_email", true);
            $secretary_email_template = $this->Email_templates_model->get_final_template("appointment_approved_to_sectetary_email", true);
         }else if($status == 'rejected'){
            $host_email_template = $this->Email_templates_model->get_final_template("appointment_rejected_to_host_email", true);
            $secretary_email_template = $this->Email_templates_model->get_final_template("appointment_rejected_to_sectetary_email", true);
         }
 
        $parser_data["APPOINTMENT_ID"] = $data['APPOINTMENT_ID'];
        $parser_data["APPOINTMENT_TITLE"] = $data['APPOINTMENT_TITLE'];
        $parser_data["APPOINTMENT_DATE"] = $data['APPOINTMENT_DATE'];
        $parser_data["APPOINTMENT_TIME"] = $data['APPOINTMENT_TIME'];
        $parser_data["APPOINTMENT_ROOM"] = $data['APPOINTMENT_ROOM'];
        $parser_data["APPOINTMENT_NOTE"] = $data['APPOINTMENT_NOTE'];
        $parser_data["APPOINTMENT_MEETING_WITH"] = $data['APPOINTMENT_MEETING_WITH'];
        $parser_data["APPOINTMENT_DECLINE_REASON"] = $data['APPOINTMENT_DECLINE_REASON'];
        $parser_data["HOST_NAME"] = $data['HOST_NAME'];
        $parser_data["HOST_DEPARTMENT"] = $data['HOST_DEPARTMENT'];
        $parser_data["SECRETARY_NAME"] = $data['SECRETARY_NAME'];
        $parser_data["REGARD_NAME"] = $data['REGARD_NAME'];
        $parser_data["REGARD_POSITION"] = $data['REGARD_POSITION'];
        $parser_data["MEETING_WITH_NAMES"] = $data['MEETING_WITH_NAMES'];

        $parser_data["LEAVE_URL"] = get_uri('leaves');
        $parser_data["SIGNATURE"] = get_array_value($host_email_template, "signature_default");
        $parser_data["SIGNATURE"] = get_array_value($secretary_email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["EMAIL_HEADER_URL"] = get_uri('assets/images/email_header.jpg');
        $parser_data["EMAIL_FOOTER_URL"] = get_uri('assets/images/email_footer.png');
 
        $host_message =  get_array_value($host_email_template, "message_default");
        $host_subject =  get_array_value($host_email_template, "subject_default");

        $host_message = $this->parser->setData($parser_data)->renderString($host_message);
        $host_subject = $this->parser->setData($parser_data)->renderString($host_subject);

        if(!empty($host_email)){
            $hrm_email =  send_app_mail($host_email, $host_subject, $host_message);
        }

        $secretary_message =  get_array_value($secretary_email_template, "message_default");
        $secretary_subject =  get_array_value($secretary_email_template, "subject_default");
 
        $secretary_message = $this->parser->setData($parser_data)->renderString($secretary_message);
        $secretary_subject = $this->parser->setData($parser_data)->renderString($secretary_subject);
 
        if(!empty($secretary_email)){
            $hrm_email =  send_app_mail($secretary_email, $secretary_subject, $secretary_message);
        }

 
     }

    public function send_notify_appointment_status_email_participant($data = array()) {

        $participant_email = $data['PARTICIPANT_EMAIL'];
 
        $status = $data['APPOINTMENT_STATUS'];
 
         if($status == 'approved'){
            $participant_email_template = $this->Email_templates_model->get_final_template("appointment_approved_to_participant_email", true);
         }else if($status == 'rejected'){
            $participant_email_template = $this->Email_templates_model->get_final_template("appointment_rejected_to_participant_email", true);
         }
 
        $parser_data["APPOINTMENT_ID"] = $data['APPOINTMENT_ID'];
        $parser_data["APPOINTMENT_TITLE"] = $data['APPOINTMENT_TITLE'];
        $parser_data["APPOINTMENT_DATE"] = $data['APPOINTMENT_DATE'];
        $parser_data["APPOINTMENT_TIME"] = $data['APPOINTMENT_TIME'];
        $parser_data["APPOINTMENT_ROOM"] = $data['APPOINTMENT_ROOM'];
        $parser_data["APPOINTMENT_NOTE"] = $data['APPOINTMENT_NOTE'];
        $parser_data["APPOINTMENT_MEETING_WITH"] = $data['APPOINTMENT_MEETING_WITH'];
        $parser_data["APPOINTMENT_DECLINE_REASON"] = $data['APPOINTMENT_DECLINE_REASON'];
        $parser_data["HOST_NAME"] = $data['HOST_NAME'];
        $parser_data["HOST_DEPARTMENT"] = $data['HOST_DEPARTMENT'];
        $parser_data["SECRETARY_NAME"] = $data['SECRETARY_NAME'];
        $parser_data["REGARD_NAME"] = $data['REGARD_NAME'];
        $parser_data["REGARD_POSITION"] = $data['REGARD_POSITION'];
        $parser_data["PARTICIPANT_NAME"] = $data['PARTICIPANT_NAME'];
        $parser_data["MEETING_WITH_NAMES"] = $data['MEETING_WITH_NAMES'];

        $parser_data["LEAVE_URL"] = get_uri('leaves');
        $parser_data["SIGNATURE"] = get_array_value($participant_email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["EMAIL_HEADER_URL"] = get_uri('assets/images/email_header.jpg');
        $parser_data["EMAIL_FOOTER_URL"] = get_uri('assets/images/email_footer.png');
 
        $participant_message =  get_array_value($participant_email_template, "message_default");
        $participant_subject =  get_array_value($participant_email_template, "subject_default");

        $participant_message = $this->parser->setData($parser_data)->renderString($participant_message);
        $participant_subject = $this->parser->setData($parser_data)->renderString($participant_subject);

        if(!empty($participant_email)){
            $hrm_email =  send_app_mail($participant_email, $participant_subject, $participant_message);
        }
 
     }

     
    //get calendar event
    function calendar_appointments($filter_values = "", $event_label_id = 0, $client_id = 0) {
        $start = $_GET["start"];
        $end = $_GET["end"];

        $result = array();

        $filter_values_array = explode('-', $filter_values);

        if (in_array("events", $filter_values_array)) {
            //get all events
            $is_client = false;
            if ($this->login_user->user_type == "client") {
                $is_client = true;
            }

            validate_numeric_value($event_label_id);
            validate_numeric_value($client_id);
            $options_of_appointments = array("created_by" => $this->login_user->id, "date" => $start);

            $list_data_of_appointments = $this->Appointments_model->get_details($options_of_appointments)->getResult();

            foreach ($list_data_of_appointments as $data) {

                //check if this recurring event, generate recurring evernts based on the condition

                $data->cycle = 0; //it's required to calculate the recurring events

                $result[] = $this->_make_calendar_appointment($data); //add regular event

            }
        }


        echo json_encode($result);
    }

    //prepare calendar event
    private function _make_calendar_appointment($data) {

        $end_time = $data->time;
        // if ($data->start_date != $data->end_date && $end_time == "00:00:00") {
        //     $end_time = "23:59:59";
        // }

        $style = '';
        if (isset($data->status)) {
            if (str_contains($data->status, "approved")) {
                $status_meta = "#08976d"; // Green for approved
            } else if (str_contains($data->status, "active")) {
                // $status_class = "btn-dark"; // Dark background for active
                $status_meta = "#6690f4";
            } else if (str_contains($data->status, "rejected")) {
                $status_meta = "#fc0758"; // Red for rejected
            }
        }

        return array(
            "title" => $data->title,
            "start" => $data->date . " " . $data->time,
            // "end" => $data->end_date . " " . $end_time,
            // "backgroundColor" => "#6690f4",
            "backgroundColor" => $status_meta,
            "borderColor" => "#6690f4",
            "extendedProps" => array(
                "icon" => get_event_icon('all'),
                "encrypted_appointment_id" => $data->id, //to make is secure we'll use the encrypted id
                // "cycle" => $data->cycle,
                "event_type" => "appointment",
            )
        );
    }

    function appointments_view() {
        $encrypted_appointment_id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "required"
        ));

        $view_data = $this->_make_view_data($encrypted_appointment_id);

        return $this->template->view('appointments/appointments_view', $view_data);
    }

    private function _make_view_data($encrypted_appointment_id, $cycle = "0") {
        $appointment_id = $encrypted_appointment_id;

        $model_info = $this->Appointments_model->get_details(array("id" => $appointment_id))->getRow();

        if ($appointment_id && $model_info->id) {

            $view_data['encrypted_appointment_id'] = $encrypted_appointment_id; //to make is secure we'll use the encrypted id 
            $view_data['editable'] = $this->request->getPost('editable');
            $view_data['model_info'] = $model_info;
            $view_data['event_icon'] = get_event_icon('all');
            // $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("events", $event_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $approved_by_array = explode(",", $model_info->approved_by);
            $rejected_by_array = explode(",", $model_info->rejected_by);

            $view_data['confirmed_by'] = $model_info->approved_by; //get_array_value($confimed_rejected_users, 'confirmed_by');
            $view_data['rejected_by'] = $model_info->rejected_by; //get_array_value($confimed_rejected_users, 'rejected_by');

             // Assign the appropriate class based on the status
            $style = '';
            if (isset($model_info->status)) {
                if (str_contains($model_info->status, "approved")) {
                    $status_class = ""; // Green for approved
                    $style = "background-color:#08976d";
                } else if (str_contains($model_info->status, "active")) {
                    $status_class = "btn-dark"; // Dark background for active
                    $style = "background-color:#6690f4";
                } else if (str_contains($model_info->status, "rejected")) {
                    $status_class = "bg-danger"; // Red for rejected
                    $style = "background-color:#fc0758;";
                }
        
                // Add status and title meta information
                $view_data["status_meta"] = "<span style='$style' class='badge $status_class'>" . app_lang($model_info->status) . "</span>";
            }

            return $view_data;
        } else {
            show_404();
        }
    }

    /* insert or update a client */
    
    function save() {
        
        $appointments_id = $this->request->getPost('id');
        
        $this->validate_submitted_data(array(
            "id" => "numeric",
        ));


        $meeting_with = $this->request->getPost('meeting_with');

        $department_ids = ($meeting_with === 'Departments') ? implode(',', $this->request->getPost('appointment_department_ids')) : null;
        $section_ids = ($meeting_with === 'Sections') ? implode(',', $this->request->getPost('appointment_section_ids')) : null;
        $unit_ids = ($meeting_with === 'Units') ? implode(',', $this->request->getPost('appointment_unit_ids')) : null;
        $payer_ids = ($meeting_with === 'Payers') ? implode(',', $this->request->getPost('appointment_payer_ids')) : null;
        $partner_ids = ($meeting_with === 'Partners') ? implode(',', $this->request->getPost('appointment_partner_ids')) : null;
        $visitor_ids = ($meeting_with === 'Visitors') ? implode(',', $this->request->getPost('appointment_visitor_ids')) : null;
        $employee_ids = ($meeting_with === 'Employees') ? implode(',', $this->request->getPost('appointment_employee_ids')) : null;

        $data = array(
            "title" => $this->request->getPost('appointment_title'),
            "date" => $this->request->getPost('appointment_date'),
            "time" => $this->request->getPost('appointment_time'),
            "room" => $this->request->getPost('appointment_room'),
            "note" => $this->request->getPost('appointment_note'),
            "host_id" => $this->request->getPost('appointment_host_id'),
            
            "meeting_with" => $meeting_with,
            "department_ids" => $department_ids,
            "section_ids" => $section_ids,
            "unit_ids" => $unit_ids,
            "payer_ids" => $payer_ids,
            "partner_ids" => $partner_ids,
            "visitor_ids" => $visitor_ids,
            "employee_ids" => $employee_ids
        );

        if ($this->login_user->user_type === "staff") {
            $data["labels"] = $this->request->getPost('labels');
        }

        if (!$appointments_id) {
            $data["created_at"] = get_current_utc_time();
        }

        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "client") === "all") {
            //user has access to change created by
            $data["created_by"] = $this->request->getPost('created_by') ? $this->request->getPost('created_by') : $this->login_user->id;
        } else if (!$appointments_id) {
            //the user hasn't permission to change created by but s/he can create new client
            $data["created_by"] = $this->login_user->id;
        }

        $data = clean_data($data);

        $save_id = $this->Appointments_model->ci_save($data, $appointments_id);

        $host_sec_info = $this->db->query("SELECT concat(host.first_name,' ',host.last_name) as host_name, host.private_email as host_email, dp.id as dp_id,
            dp.nameEn as host_department, concat(sec.first_name,' ',sec.last_name) as sec_name, sec.private_email as sec_email
            FROM rise_appointments ap
            LEFT JOIN rise_users host ON host.id = ap.host_id
            LEFT JOIN rise_team_member_job_info tj on tj.user_id = host.id
            LEFT JOIN rise_departments dp ON dp.id = tj.department_id
            LEFT JOIN rise_users sec ON sec.id = dp.secretary_id
            WHERE ap.id = $save_id")->getRow();

        $regard_name = $this->login_user->first_name.' '.$this->login_user->last_name;
        $loginuser = $this->login_user->id;
        $regard_position = $this->db->query("SELECT tj.job_title_en as job_title 
            FROM rise_team_member_job_info tj 
            LEFT JOIN rise_users us ON us.id = tj.user_id 
            WHERE us.id = $loginuser")->getRow();
        
        $host_department_id["app_department_id"] = $host_sec_info->dp_id;

        $save_id = $this->Appointments_model->ci_save($host_department_id, $save_id);
        

        if ($save_id) {

            if(!$appointments_id){
                    
                $options = array('id'=>$save_id);

                $appoinment = $this->Appointments_model->get_details($options)->getRow();

                $user_info = $this->db->query("SELECT u.*,j.job_title_so,j.department_id FROM rise_users u left join rise_team_member_job_info j on u.id=j.user_id where u.id = $appoinment?->created_by")->getRow();

                 // Retrieve and format the "meeting_with" list
            $meeting_with_names = '';
            $participants = [];
            $meeting_with_header = ucfirst($meeting_with);

            if ($meeting_with === 'Departments' && !empty($department_ids)) {
                $department_info = $this->db->query("SELECT dp.nameEn as name, dp.email FROM rise_departments dp WHERE id IN ($department_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];  // Collect participant names and emails
                    return '<li>' . $info['name'] . '</li>';
                }, $department_info));
                $meeting_with_header = "<strong>Departments List</strong>";
            } elseif ($meeting_with === 'Sections' && !empty($section_ids)) {
                $section_info = $this->db->query("SELECT se.nameEn as name, se.email FROM rise_sections se WHERE id IN ($section_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $section_info));
                $meeting_with_header = "<strong>Sections List</strong>";
            } elseif ($meeting_with === 'Units' && !empty($unit_ids)) {
                $unit_info = $this->db->query("SELECT un.nameEn as name, un.email FROM rise_units un WHERE id IN ($unit_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $unit_info));
                $meeting_with_header = "<strong>Units List</strong>";
            } elseif ($meeting_with === 'Payers' && !empty($payer_ids)) {
                $payer_info = $this->db->query("SELECT pa.company_name as name, pa.email FROM rise_clients pa WHERE id IN ($payer_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $payer_info));
                $meeting_with_header = "<strong>Payers List</strong>";
            } elseif ($meeting_with === 'Partners' && !empty($partner_ids)) {
                $partner_info = $this->db->query("SELECT pr.name as name, pr.email FROM rise_partners pr WHERE id IN ($partner_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $partner_info));
                $meeting_with_header = "<strong>Partners List</strong>";
            } elseif ($meeting_with === 'Visitors' && !empty($visitor_ids)) {
                $visitor_info = $this->db->query("SELECT v.name as name, v.email FROM rise_visitors v WHERE id IN ($visitor_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $visitor_info));
                $meeting_with_header = "<strong>Visitors List</strong>";
            } elseif ($meeting_with === 'Employees' && !empty($employee_ids)) {
                $employee_info = $this->db->query("SELECT CONCAT(first_name, ' ', last_name) AS name, private_email as email FROM rise_users WHERE id IN ($employee_ids)")
                    ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $employee_info));
                $meeting_with_header = "<strong>Employees List</strong>";
            }
        
                // Add the header and the bullet points in the email content
                $meeting_with_names = $meeting_with_header  . $meeting_with_names ;
        
                $appoinment_email_data = [
                    'APPOINTMENT_ID' => $save_id,
                    'APPOINTMENT_TITLE' => $appoinment->title,
                    'APPOINTMENT_DATE' => $appoinment->date,
                    'APPOINTMENT_TIME' => $appoinment->time,
                    'APPOINTMENT_ROOM' => $appoinment->room,
                    'APPOINTMENT_NOTE' => $appoinment->note,
                    'HOST_NAME' => $host_sec_info->host_name,
                    'HOST_EMAIL' => $host_sec_info->host_email,
                    'HOST_DEPARTMENT' => $host_sec_info->host_department,
                    'SECRETARY_NAME' => $host_sec_info->sec_name,
                    'SECRETARY_EMAIL' => $host_sec_info->sec_email,
                    'REGARD_NAME' => $regard_name,
                    'REGARD_POSITION' => $regard_position->job_title,
                    'APPOINTMENT_MEETING_WITH' => $appoinment->meeting_with, 
                    'MEETING_WITH_NAMES' => $meeting_with_names,  // The names in bullet format with a bold header
                ];
        
                $r = $this->send_appointment_created_email($appoinment_email_data);

                 // Send email to each participant with their name included
                foreach ($participants as $participant) {
                    $appoinment_email_data['PARTICIPANT_EMAIL'] = $participant['email'];
                    $appoinment_email_data['PARTICIPANT_NAME'] = $participant['name'];
                    $this->send_appointment_created_email_participant($appoinment_email_data);
                }
            }

            save_custom_fields("clients", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            $ticket_id = $this->request->getPost('ticket_id');
            if ($ticket_id) {
                $ticket_data = array("appointments_id" => $save_id);
                $this->Tickets_model->ci_save($ticket_data, $ticket_id);
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->request->getPost('view'), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function update_status() {
        $appointment_id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        // die($appointment_id);
        $decline_reason = $this->request->getPost('decline_reason');
        $now = get_current_utc_time();
    
        $role = $this->get_user_role();
    
        $appointment_data = array(
            "status" => $status
        );
    
        if ($status === "approved") {
            $appointment_data["approved_by"] = $this->login_user->id;
            $appointment_data["approved_at"] = $now;
        } else if ($status === "rejected") {
            $appointment_data["decline_reason"] = $decline_reason;
            $appointment_data["rejected_by"] = $this->login_user->id;
            $appointment_data["rejected_at"] = $now;
        }
    
        $application_info = $this->Appointments_model->get_one($appointment_id);
    
        $save_id = $this->Appointments_model->ci_save($appointment_data, $appointment_id);
    
        if ($save_id) {
            $options = array('id' => $save_id);
            $appoinment = $this->Appointments_model->get_details($options)->getRow();
    
            $host_sec_info = $this->db->query("SELECT concat(host.first_name,' ',host.last_name) as host_name, host.private_email as host_email, 
                    dp.nameEn as host_department, concat(sec.first_name,' ',sec.last_name) as sec_name, sec.private_email as sec_email
                    FROM rise_appointments ap
                    LEFT JOIN rise_users host ON host.id = ap.host_id
                    LEFT JOIN rise_team_member_job_info tj on tj.user_id = host.id
                    LEFT JOIN rise_departments dp ON dp.id = tj.department_id
                    LEFT JOIN rise_users sec ON sec.id = dp.secretary_id
                    WHERE ap.id = $save_id")->getRow();
    
            $regard_name = $this->login_user->first_name . ' ' . $this->login_user->last_name;
            $loginuser = $this->login_user->id;
            $regard_position = $this->db->query("SELECT tj.job_title_en as job_title 
                FROM rise_team_member_job_info tj 
                LEFT JOIN rise_users us ON us.id = tj.user_id 
                WHERE us.id = $loginuser")->getRow();
    
            // Retrieve and format the "meeting_with" list
            $meeting_with_names = '';
            $participants = []; // Will hold name and email of each participant
            $meeting_with_header = ucfirst($appoinment->meeting_with);  // Capitalize the first letter for the header
    
            // ----------------- Departments ---------------
            if ($appoinment->meeting_with === 'Departments') {
                $department_ids = $appoinment->department_ids;
                $department_info = $this->db->query("SELECT dp.nameEn as name, dp.email FROM rise_departments dp WHERE id IN ($department_ids)")
                                            ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];  // Collect participant names and emails
                    return '<li>' . $info['name'] . '</li>';
                }, $department_info));
                $meeting_with_header = "<strong>Departments List</strong>";
    
            // --------------- Sections ------------
            } elseif ($appoinment->meeting_with === 'Sections') {
                $section_ids = $appoinment->section_ids;
                $section_info = $this->db->query("SELECT se.nameEn as name, se.email FROM rise_sections se WHERE id IN ($section_ids)")
                                         ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $section_info));
                $meeting_with_header = "<strong>Sections List</strong>";
    
            // -------------- Units ------------------
            } elseif ($appoinment->meeting_with === 'Units') {
                $unit_ids = $appoinment->unit_ids;
                $unit_info = $this->db->query("SELECT un.nameEn as name, un.email FROM rise_units un WHERE id IN ($unit_ids)")
                                      ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $unit_info));
                $meeting_with_header = "<strong>Units List</strong>";
    
            // -------------- Payers --------------
            } elseif ($appoinment->meeting_with === 'Payers') {
                $payer_ids = $appoinment->payer_ids;
                $payer_info = $this->db->query("SELECT pa.company_name as name, pa.email as email FROM rise_clients pa WHERE id IN ($payer_ids)")
                                       ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $payer_info));
                $meeting_with_header = "<strong>Payers List</strong>";
    
            // ------------- Partners --------------------
            } elseif ($appoinment->meeting_with === 'Partners') {
                $partner_ids = $appoinment->partner_ids;
                $partner_info = $this->db->query("SELECT pr.name as name, pr.email as email FROM rise_partners pr WHERE id IN ($partner_ids)")
                                         ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $partner_info));
                $meeting_with_header = "<strong>Partners List</strong>";
    
            // ------------------- Visitors -------------
            } elseif ($appoinment->meeting_with === 'Visitors') {
                $visitor_ids = $appoinment->visitor_ids;
                $visitor_info = $this->db->query("SELECT v.name as name, v.email as email FROM rise_visitors v WHERE id IN ($visitor_ids)")
                                         ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $visitor_info));
                $meeting_with_header = "<strong>Visitors List</strong>";
    
            // ------------------ Employees --------------------
            } elseif ($appoinment->meeting_with === 'Employees') {
                $employee_ids = $appoinment->employee_ids;
                $employee_info = $this->db->query("SELECT CONCAT(u.first_name, ' ', u.last_name) as name, u.private_email as email FROM rise_users u WHERE id IN ($employee_ids)")
                                          ->getResultArray();
                $meeting_with_names = implode('', array_map(function($info) use (&$participants) {
                    $participants[] = ['name' => $info['name'], 'email' => $info['email']];
                    return '<li>' . $info['name'] . '</li>';
                }, $employee_info));
                $meeting_with_header = "<strong>Employees List</strong>";
            }
    
            // Combine header and names in bullet format
            $meeting_with_names = $meeting_with_header . $meeting_with_names;
    
            // Send email to the host
            $appoinment_email_data = [
                'APPOINTMENT_ID' => $save_id,
                'APPOINTMENT_TITLE' => $appoinment->title,
                'APPOINTMENT_DATE' => $appoinment->date,
                'APPOINTMENT_TIME' => $appoinment->time,
                'APPOINTMENT_ROOM' => $appoinment->room,
                'APPOINTMENT_NOTE' => $appoinment->note,
                'APPOINTMENT_MEETING_WITH' => $appoinment->meeting_with, 
                'APPOINTMENT_DECLINE_REASON' => $appoinment->decline_reason,
                'HOST_NAME' => $host_sec_info->host_name,
                'HOST_EMAIL' => $host_sec_info->host_email,
                'HOST_DEPARTMENT' => $host_sec_info->host_department,
                'SECRETARY_NAME' => $host_sec_info->sec_name,
                'SECRETARY_EMAIL' => $host_sec_info->sec_email,
                'REGARD_NAME' => $regard_name,
                'REGARD_POSITION' => $regard_position->job_title,
                'APPOINTMENT_STATUS' => $status, 
                'MEETING_WITH_NAMES' => $meeting_with_names,  // The list of people to meet with
            ];
    
            $r = $this->send_notify_appointment_status_email($appoinment_email_data);

            // Send email to each participant with their name included
            foreach ($participants as $participant) {
                $appoinment_email_data['PARTICIPANT_EMAIL'] = $participant['email'];
                $appoinment_email_data['PARTICIPANT_NAME'] = $participant['name'];
                $this->send_notify_appointment_status_email_participant($appoinment_email_data);
            }
    
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete() {

        $id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        if ($this->Appointments_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    function list_data() {

        $this->access_only_allowed_members();

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        
        $options = array(
            "custom_fields" => $custom_fields,
            "show_own_department_appointment_only_user_id" => $this->show_own_department_appointment_only_user_id(),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("clients", $this->login_user->is_admin, $this->login_user->user_type),
            "group_id" => $this->request->getPost("group_id"),
            "show_own_clients_only_user_id" => $this->show_own_clients_only_user_id(),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "created_by" => $this->request->getPost("created_by"),
            "client_groups" => $this->allowed_client_groups,
            "label_id" => $this->request->getPost('label_id')
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Appointments_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_row($data, $custom_fields);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }

    /* return a row of client list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );
        $data = $this->Appointments_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of client list table */

    private function _make_row($data, $custom_fields) {
        $meta_info = $this->_prepare_appointment_info($data);
        $option_icon = "info";

        $actions= '';

        $role = $this->get_user_role();

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $actions = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $actions = modal_anchor(get_uri("appointments/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_appointment'), "data-post-id" => $data->id));
        if($role === 'Director' || $role === 'admin' || $role === 'Administrator'){
            $actions .= modal_anchor(get_uri("appointments/appointment_details"), "<i data-feather='$option_icon' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('appointment_details'), "data-post-id" => $data->id));
        }
        $actions .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_appointment'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("appointments/delete"), "data-action" => "delete-confirmation"));


        return array($data->id,
            anchor(get_uri("appointments/view/" . $data->id), $meta_info->title_meta),
            $data->date,
            $data->time,
            $data->room,
            $data->note,
            $data->HostName,
            $data->meeting_with,
            $data->department,
            $meta_info->status_meta,
            $actions,
        );
    }

    function appointment_details() {

        $appointment_id = $this->request->getPost('id');
        $info = $this->Appointments_model->get_details_info($appointment_id);
        if (!$info) {
            show_404();
        }
        
        $can_manage_application = false;
        if ($this->access_type === "own_department" || $this->access_type === "all") {
            $can_manage_application = true;
        } 

        if (!$can_manage_application) {
            app_redirect("forbidden");
        }
        $role = $this->get_user_role();
        $view_data['appointment_info'] = $this->_prepare_appointment_info($info);
        $view_data['role']=$role;
        return $this->template->view("appointments/appointment_details", $view_data);
    }

    private function _prepare_appointment_info($data) {
        $style = '';
        $current_date = date('Y-m-d'); // Get the current date in 'Y-m-d' format
    
        // Check if the appointment date is set and if it is in the past
        if (isset($data->date) && $data->date < $current_date) {
            // Add (expired) to the status, if not already appended
            if (!str_contains($data->status, "(expired)")) {
                $data->status .= " (expired)";
            }
        }

        if (str_contains($data->status, "approved")) {
            $status_meta = "#08976d"; // Green for approved
        } else if (str_contains($data->status, "active")) {
            // $status_class = "btn-dark"; // Dark background for active
            $status_meta = "#6690f4";
        } else if (str_contains($data->status, "rejected")) {
            $status_meta = "#fc0758"; // Red for rejected
        }
    
        // Assign the appropriate class based on the status
        if (isset($data->status)) {
            if (str_contains($data->status, "approved")) {
                $status_class = ""; // Green for approved
                $style = "background-color:#08976d";
            } else if (str_contains($data->status, "active")) {
                $status_class = "btn-dark"; // Dark background for active
                $style = "background-color:#6690f4";
            } else if (str_contains($data->status, "rejected")) {
                $status_class = "bg-danger"; // Red for rejected
                $style = "background-color:#fc0758;";
            }
    
            // Apply the orange color if the status includes (expired)
            if (str_contains($data->status, "(expired)")) {
                $status_class = "badge bg-orange"; // Orange for expired
                $style = "background-color:orange;";
            }
    
            // Add status and title meta information
            $data->status_meta = "<span style='$style' class='badge $status_class'>" . app_lang($data->status) . "</span>";
            $data->title_meta = "<span style='$style' class='badge $status_class'>" . $data->title . "</span>";
        }
    
        return $data;
    }

    private function can_view_files() {
        if ($this->login_user->user_type == "staff") {
            $this->access_only_allowed_members();
        } else {
            if (!get_setting("client_can_view_files")) {
                app_redirect("forbidden");
            }
        }
    }

    private function can_add_files() {
        if ($this->login_user->user_type == "staff") {
            $this->access_only_allowed_members();
        } else {
            if (!get_setting("client_can_add_files")) {
                app_redirect("forbidden");
            }
        }
    }

    /* load client details view */

    function view($appointments_id = 0, $tab = "") {
        
        if ($appointments_id) {
            $options = array("id" => $appointments_id);
            $appointments_info = $this->Appointments_model->get_details($options)->getRow();
            if ($appointments_info && !$appointments_info->is_lead) {

                $view_data = $this->make_access_permissions_view_data();

                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $access_info = $this->get_access_info("expense");
                $view_data["show_expense_info"] = (get_setting("module_expense") && $access_info->access_type == "all") ? true : false;

                $view_data['appointments_info'] = $appointments_info;

                $view_data["is_starred"] = strpos($appointments_info->starred_by, ":" . $this->login_user->id . ":") ? true : false;

                $view_data["tab"] = clean_data($tab);

                $view_data["view_type"] = "";

                $view_data['hidden_menu'] = array("");

                return $this->template->rander("appointments/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /* add-remove start mark from client */

    function add_remove_star($client_id, $type = "add") {
        if ($client_id) {
            $view_data["client_id"] = clean_data($client_id);

            if ($type === "add") {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "add");
                return $this->template->view('appointments/star/starred', $view_data);
            } else {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "remove");
                return $this->template->view('appointments/star/not_starred', $view_data);
            }
        }
    }

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id, $this->allowed_client_groups)->getResult();
        return $this->template->view('appointments/star/clients_list', $view_data);
    }

    /* load projects tab  */

    function projects($client_id) {
        $this->_validate_client_view_access($client_id);

        $view_data['can_create_projects'] = $this->can_create_projects();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = clean_data($client_id);
        $view_data['project_statuses'] = $this->Project_status_model->get_details()->getResult();
        return $this->template->view("appointments/projects/index", $view_data);
    }

    /* load payments tab  */

    function payments($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = clean_data($client_id);
            return $this->template->view("appointments/payments/index", $view_data);
        }
    }

    /* load tickets tab  */

    function tickets($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {

            $view_data['client_id'] = clean_data($client_id);
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tickets", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tickets", $this->login_user->is_admin, $this->login_user->user_type);

            $view_data['show_project_reference'] = get_setting('project_reference_in_tickets');

            return $this->template->view("appointments/tickets/index", $view_data);
        }
    }

    /* load invoices tab  */

    function invoices($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = clean_data($client_id);

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("invoices", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("invoices", $this->login_user->is_admin, $this->login_user->user_type);

            $view_data["can_edit_invoices"] = $this->can_edit_invoices();

            $type_suggestions = array(
                array("id" => "", "text" => "- " . app_lang('type') . " -"),
                array("id" => "invoice", "text" => app_lang("invoice")),
                array("id" => "credit_note", "text" => app_lang("credit_note"))
            );
            $view_data['types_dropdown'] = json_encode($type_suggestions);

            return $this->template->view("appointments/invoices/index", $view_data);
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = clean_data($client_id);

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("estimates", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("estimates", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("appointments/estimates/estimates", $view_data);
        }
    }

    /* load orders tab  */

    function orders($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = clean_data($client_id);

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("orders", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("orders", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("appointments/orders/orders", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data['client_id'] = clean_data($client_id);
            return $this->template->view("appointments/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data['client_id'] = clean_data($client_id);
            return $this->template->view("appointments/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data['client_id'] = clean_data($client_id);
            $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown("client");
            $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, app_lang("event") . " " . strtolower(app_lang("label"))));
            return $this->template->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id, $view_type = "") {
        $this->can_view_files();

        if ($this->login_user->user_type == "client") {
            $client_id = $this->login_user->client_id;
        }

        $this->_validate_client_view_access($client_id);

        $view_data['client_id'] = clean_data($client_id);
        $view_data['page_view'] = false;

        if ($view_type == "page_view") {
            $view_data['page_view'] = true;
            return $this->template->rander("appointments/files/index", $view_data);
        } else {
            return $this->template->view("appointments/files/index", $view_data);
        }
    }

    /* file upload modal */

    function file_modal_form() {
        $this->can_add_files();

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $client_id = $this->request->getPost('client_id') ? $this->request->getPost('client_id') : $view_data['model_info']->client_id;
        $this->_validate_client_manage_access($client_id);

        $view_data['client_id'] = $client_id;
        return $this->template->view('appointments/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {
        $this->can_add_files();

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "required|numeric"
        ));

        $client_id = $this->request->getPost('client_id');
        $this->_validate_client_manage_access($client_id);

        $files = $this->request->getPost("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->request->getPost('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "client_id" => $client_id,
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

    function files_list_data($client_id = 0) {
        $this->can_view_files();
        $this->_validate_client_view_access($client_id);

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        if ($data->uploaded_by_user_type == "staff") {
            $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);
        } else {
            $uploaded_by = get_client_contact_profile_link($data->uploaded_by, $uploaded_by);
        }

        $description = "<div class='float-start'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("appointments/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("appointments/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        if ($this->login_user->user_type == "staff") {
            $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("appointments/delete_file"), "data-action" => "delete-confirmation"));
        }


        return array($data->id,
            "<div data-feather='$file_icon' class='mr10 float-start'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();

        if ($file_info) {
            $this->can_view_files();

            if (!$file_info->client_id) {
                app_redirect("forbidden");
            }

            $this->_validate_client_manage_access($file_info->client_id);

            $view_data['can_comment_on_files'] = false;
            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("client", $file_info->client_id));

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = clean_data($file_id);
            return $this->template->view("appointments/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {
        $this->can_view_files();

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            app_redirect("forbidden");
        }

        $this->_validate_client_manage_access($file_info->client_id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        return $this->download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for client */

    function validate_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id || ($this->login_user->user_type == "client" && $info->uploaded_by !== $this->login_user->id)) {
            app_redirect("forbidden");
        }

        $this->_validate_client_manage_access($info->client_id);

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("client", $info->client_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->_validate_client_view_access($view_data['user_info']->client_id);
        $view_data['client_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = clean_data($tab);
        if ($view_data['user_info']->user_type === "client") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            return $this->template->rander("appointments/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    //show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $this->_validate_client_view_access($view_data['user_info']->client_id);
        return $this->template->view("users/account_settings", $view_data);
    }

    //show my preference settings of a team member
    function my_preferences() {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);

        //language dropdown
        $view_data['language_dropdown'] = array();
        if (!get_setting("disable_language_selector_for_clients")) {
            $view_data['language_dropdown'] = get_language_list();
        }

        $view_data["hidden_topbar_menus_dropdown"] = $this->get_hidden_topbar_menus_dropdown();

        return $this->template->view("appointments/contacts/my_preferences", $view_data);
    }

    function save_my_preferences() {
        //setting preferences
        $settings = array("notification_sound_volume", "disable_push_notification", "disable_keyboard_shortcuts", "reminder_sound_volume", "reminder_snooze_length");

        if (!get_setting("disable_topbar_menu_customization")) {
            array_push($settings, "hidden_topbar_menus");
        }

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

        if (!get_setting("disable_language_selector_for_clients")) {
            $user_data["language"] = $this->request->getPost("personal_language");
        }

        $user_data = clean_data($user_data);

        $this->Users_model->ci_save($user_data, $this->login_user->id);

        try {
            app_hooks()->do_action("app_hook_clients_my_preferences_save_data");
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function save_personal_language($language) {
        if (!get_setting("disable_language_selector_for_clients") && ($language || $language === "0")) {

            $language = clean_data($language);
            $data["language"] = strtolower($language);

            $this->Users_model->ci_save($data, $this->login_user->id);
        }
    }

    /* load contacts tab  */

    function contacts($Sections_id = 0) {

        $this->_validate_client_view_access($Sections_id);

        if ($Sections_id) {
            $view_data["Sections_id"] = clean_data($Sections_id);
            $view_data["view_type"] = "";
        } else {
            $view_data["Sections_id"] = "";
            $view_data["view_type"] = "list_view";
        }
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['can_edit_clients'] = $this->can_edit_clients();

        return $this->template->view("appointments/contacts/index", $view_data);
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->_validate_client_manage_access();

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->Sections_id = $this->request->getPost('Sections_id');

        $view_data['add_type'] = $this->request->getPost('add_type');

        $this->_validate_client_manage_access($view_data['model_info']->Sections_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("client_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('appointments/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $this->_validate_client_view_access($view_data['model_info']->client_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("client_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $view_data['can_edit_clients'] = $this->can_edit_clients($view_data['model_info']->client_id);
            return $this->template->view('appointments/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($Sections_id = 0) {
        if ($Sections_id) {
            // $this->_validate_client_view_access($Sections_id);

            $view_data['model_info'] = $this->Appointments_model->get_one($Sections_id);
            $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

            $view_data['Bank_names_dropdown'] = $this->get_bank_name_dropdown();

            $view_data['Merchant_types_dropdown'] = $this->get_merchant_types_dropdown();

            $view_data['host'] = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");
            $view_data['departments'] = $this->Departments_model->get_dropdown_list(array("nameSo"), "id");
            $view_data['Sections'] = $this->Sections_model->get_dropdown_list(array("nameSo"), "id");
            $view_data['Units'] = $this->Units_model->get_dropdown_list(array("nameSo"), "id");
            $view_data['payers'] = $this->Clients_model->get_dropdown_list(array("Contact_Name"), "id");
            $view_data['partners'] = $this->Partners_model->get_dropdown_list(array("contact_name"), "id");
            $view_data['guests'] = $this->Visitors_model->get_dropdown_list(array("name"), "id");
            $view_data['employees'] = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");

            $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $Sections_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

             // $view_data['label_column'] = "col-md-2 text-right";
        $view_data['label_column'] = "col-md-2 text-right";
        $view_data['field_column'] = "col-md-10";

        $view_data['label_column_2'] = "col-md-2 text-right";
        $view_data['field_column_2'] = "col-md-4";

        $view_data['field_column_3'] = "col-md-10";

            $view_data['can_edit_clients'] = $this->can_edit_clients($Sections_id);

            $view_data['Departments'] = array("" => " -- Choose Department -- ") + $this->Departments_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Sections'] = array("" => " -- Choose Section -- ") + $this->Sections_model->get_dropdown_list(array("nameSo"), "id");
        $view_data['Unit_heads'] = array("" => " -- Choose Unit Head -- ") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id");

            $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();
            $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
            $view_data['label_suggestions'] = $this->make_labels_dropdown("client", $view_data['model_info']->labels);

            return $this->template->view('appointments/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $contact_info = $this->Users_model->get_one($contact_id);
            $this->_validate_client_view_access($contact_info->client_id);

            $view_data['user_id'] = clean_data($contact_id);
            $view_data['user_type'] = "client";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $view_data['can_edit_clients'] = $this->can_edit_clients();
            return $this->template->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {

        $contact_id = $this->request->getPost('contact_id');
        $Sections_id = $this->request->getPost('Sections_id');
        $this->_validate_client_manage_access($Sections_id);

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $user_data = array(            
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "phone" => $this->request->getPost('phone'),
            "skype" => $this->request->getPost('skype'),
            "job_title" => $this->request->getPost('job_title'),
            "gender" => is_null($this->request->getPost('gender')) ? "" : $this->request->getPost('gender'),
            "note" => $this->request->getPost('note')
        );

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "Sections_id" => "required|numeric"
        ));

        if (!$contact_id) {
            //inserting new contact. Sections_id is required

            $this->validate_submitted_data(array(
                "email" => "required|valid_email",
            ));

            //we'll save following fields only when creating a new contact from this form
            $user_data["Sections_id"] = $Sections_id;
            $user_data["email"] = trim($this->request->getPost('email'));
            $user_data["password"] = $this->request->getPost("login_password") ? password_hash($this->request->getPost("login_password"), PASSWORD_DEFAULT) : "";
            $user_data["created_at"] = get_current_utc_time();
            $user_data["login_type"] = 'normal_login';

            //validate duplicate email address
            if ($this->Users_model->is_email_exists($user_data["email"], 0, $Sections_id)) {
                echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
                exit();
            }
        }

        //by default, the first contact of a client is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Appointments_model->get_primary_contact($Sections_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->request->getPost('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }

        $user_data = clean_data($user_data);

        $save_id = $this->Users_model->ci_save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("client_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->ci_save($user_data, $primary_contact);
            }

            //send login details to user only for first time. when creating  a new contact
            if (!$contact_id && $this->request->getPost('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info"); //use default template since creating a new contact

                $parser_data["SIGNATURE"] = $email_template->signature;
                $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $this->request->getPost('login_password');
                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["LOGO_URL"] = get_logo_url();

                $message = $this->parser->setData($parser_data)->renderString($email_template->message);
                $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);

                send_app_mail($this->request->getPost('email'), $subject, $message);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, "Sections_id" => $Sections_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        $contact_id = clean_data($contact_id);

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $contact_info = $this->Users_model->get_one($contact_id);
        $this->_validate_client_manage_access($contact_info->client_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
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
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->ci_save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
    }

    //save account settings of a client contact (user)
    function save_account_settings($user_id) {
        $this->access_only_allowed_members_or_contact_personally($user_id);

        $contact_info = $this->Users_model->get_one($user_id);
        $this->_validate_client_manage_access($contact_info->client_id);

        $this->validate_submitted_data(array(
            "email" => "required|valid_email"
        ));

        $email = $this->request->getPost('email');
        $password = $this->request->getPost("password");

        if ($this->Users_model->is_email_exists($email, $user_id, $contact_info->client_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
            exit();
        }

        $account_data = array(
            "email" => $email,            
            "login_type" => $this->request->getPost('login_type')
        );

        //don't reset password if user doesn't entered any password
        if ($password) {
            $this->Users_model->update_password($email, password_hash($password, PASSWORD_DEFAULT));
        }

        //only admin can disable other users login permission
        if ($this->login_user->is_admin) {
            $account_data['disable_login'] = $this->request->getPost('disable_login');
        }


        if ($this->Users_model->ci_save($account_data, $user_id)) {

            //resend new password to client contact
            if ($this->request->getPost('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info", true);

                $user_language = $this->Users_model->get_one($user_id)->language;
                $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");
                $parser_data["USER_FIRST_NAME"] = $this->request->getPost('first_name');
                $parser_data["USER_LAST_NAME"] = $this->request->getPost('last_name');
                $parser_data["USER_LOGIN_EMAIL"] = $account_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $password;
                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["LOGO_URL"] = get_logo_url();

                $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
                $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");

                $message = $this->parser->setData($parser_data)->renderString($message);
                $subject = $this->parser->setData($parser_data)->renderString($subject);
                send_app_mail($email, $subject, $message);
            }

            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        $this->access_only_allowed_members_or_contact_personally($user_id);
        $user_info = $this->Users_model->get_one($user_id);
        $this->_validate_client_manage_access($user_info->client_id);

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

    /* delete or undo a contact */

    function delete_contact() {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        $contact_info = $this->Users_model->get_one($id);
        $this->_validate_client_manage_access($contact_info->client_id);

        if ($this->request->getPost('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($Sections_id = 0) {

        $this->_validate_client_view_access($Sections_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "user_type" => "client",
            "Sections_id" => $Sections_id,
            "custom_fields" => $custom_fields,
            "show_own_clients_only_user_id" => $this->show_own_clients_only_user_id(),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("client_contacts", $this->login_user->is_admin, $this->login_user->user_type),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "client_groups" => $this->allowed_client_groups
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Users_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $hide_primary_contact_label = false;
        if (!$Sections_id) {
            $hide_primary_contact_label = true;
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_contact_row($data, $custom_fields, $hide_primary_contact_label);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "client",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->getRow();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields, $hide_primary_contact_label = false) {

        $image_url = get_avatar($data?->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1" && !$hide_primary_contact_label) {
            $primary_contact = "<span class='bg-info badge text-white'>" . app_lang('primary_contact') . "</span>";
        }

        $removal_request_pending = "";
        if ($this->login_user->user_type == "staff" && $data->requested_account_removal) {
            $removal_request_pending = "<span class='bg-danger badge'>" . app_lang("removal_request_pending") . "</span>";
        }

        $contact_link = anchor(get_uri("appointments/contact_profile/" . $data->id), $full_name . $primary_contact) . $removal_request_pending;
        if ($this->login_user->user_type === "client") {
            $contact_link = $full_name; //don't show clickable link to client
        }

        $client_info = $this->Clients_model->get_one($data->Sections_id);

        $row_data = array(
            $user_avatar,
            $contact_link,
            anchor(get_uri("appointments/view/" . $data->Sections_id), $client_info->company_name),
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-"
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("appointments/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* open invitation modal */

    function invitation_modal() {
        if (get_setting("disable_user_invitation_option_by_clients") && $this->login_user->user_type == "client") {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "client_id" => "required|numeric"
        ));

        $client_id = $this->request->getPost('client_id');
        $this->_validate_client_manage_access($client_id);

        $view_data["client_info"] = $this->Clients_model->get_one($client_id);
        return $this->template->view('appointments/contacts/invitation_modal', $view_data);
    }

    //send a team member invitation to an email address
    function send_invitation() {
        if (get_setting("disable_user_invitation_option_by_clients") && $this->login_user->user_type == "client") {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('client_id');
        $this->_validate_client_manage_access($client_id);

        $email = trim($this->request->getPost('email'));

        $this->validate_submitted_data(array(
            "client_id" => "required|numeric",
            "email" => "required|valid_email|trim"
        ));

        $email_template = $this->Email_templates_model->get_final_template("client_contact_invitation"); //use default template since sending new invitation

        $parser_data["INVITATION_SENT_BY"] = $this->login_user->first_name . " " . $this->login_user->last_name;
        $parser_data["SIGNATURE"] = $email_template->signature;
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["LOGO_URL"] = get_logo_url();

        $verification_data = array(
            "type" => "invitation",
            "code" => make_random_string(),
            "params" => serialize(array(
                "email" => $email,
                "type" => "client",
                "client_id" => $client_id,
                "expire_time" => time() + (24 * 60 * 60) //make the invitation url with 24hrs validity
            ))
        );

        $save_id = $this->Verification_model->ci_save($verification_data);
        $verification_info = $this->Verification_model->get_one($save_id);

        $parser_data['INVITATION_URL'] = get_uri("signup/accept_invitation/" . $verification_info->code);

        //send invitation email
        $message = $this->parser->setData($parser_data)->renderString($email_template->message);
        $subject = $this->parser->setData($parser_data)->renderString($email_template->subject);

        if (send_app_mail($email, $subject, $message)) {
            echo json_encode(array('success' => true, 'message' => app_lang("invitation_sent")));
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* only visible to client  */

    function users() {
        if ($this->login_user->user_type === "client") {
            $view_data['client_id'] = $this->login_user->client_id;
            return $this->template->rander("appointments/contacts/users", $view_data);
        }
    }

    /* show keyboard shortcut modal form */

    function keyboard_shortcut_modal_form() {
        return $this->template->view('team_members/keyboard_shortcut_modal_form');
    }

    function upload_excel_file() {
        upload_file_to_temp(true);
    }

    function validate_events_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    function import_clients_modal_form() {
        $this->_validate_client_manage_access();

        return $this->template->view("appointments/import_clients_modal_form");
    }

    private function _prepare_client_data($data_row, $allowed_headers) {
        //prepare client data
        $client_data = array();
        $client_contact_data = array("user_type" => "client", "is_primary_contact" => 1);
        $custom_field_values_array = array();

        foreach ($data_row as $row_data_key => $row_data_value) { //row values
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if (strpos($header_key_value, 'cf') !== false) { //custom field
                $explode_header_key_value = explode("-", $header_key_value);
                $custom_field_id = get_array_value($explode_header_key_value, 1);

                //modify date value
                $custom_field_info = $this->Custom_fields_model->get_one($custom_field_id);
                if ($custom_field_info->field_type === "date") {
                    $row_data_value = $this->_check_valid_date($row_data_value);
                }

                $custom_field_values_array[$custom_field_id] = $row_data_value;
            } else if ($header_key_value == "client_groups") { //we've to make client groups data differently
                $client_data["group_ids"] = $this->_get_client_group_ids($row_data_value);
            } else if ($header_key_value == "contact_first_name") {
                $client_contact_data["first_name"] = $row_data_value;
            } else if ($header_key_value == "contact_last_name") {
                $client_contact_data["last_name"] = $row_data_value;
            } else if ($header_key_value == "contact_email") {
                $client_contact_data["email"] = $row_data_value;
            } else {
                $client_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "client_data" => $client_data,
            "client_contact_data" => $client_contact_data,
            "custom_field_values_array" => $custom_field_values_array
        );
    }

    private function _get_existing_custom_field_id($title = "") {
        if (!$title) {
            return false;
        }

        $custom_field_data = array(
            "title" => $title,
            "related_to" => "clients"
        );

        $existing = $this->Custom_fields_model->get_one_where(array_merge($custom_field_data, array("deleted" => 0)));
        if ($existing->id) {
            return $existing->id;
        }
    }

    private function _prepare_headers_for_submit($headers_row, $headers) {
        foreach ($headers_row as $key => $header) {
            if (!((count($headers) - 1) < $key)) { //skip default headers
                continue;
            }

            //so, it's a custom field
            //check if there is any custom field existing with the title
            //add id like cf-3
            $existing_id = $this->_get_existing_custom_field_id($header);
            if ($existing_id) {
                array_push($headers, "cf-$existing_id");
            }
        }

        return $headers;
    }

    function save_client_from_excel_file() {
        $this->_validate_client_manage_access();

        if (!$this->validate_import_clients_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }

        $file_name = $this->request->getPost('file_name');
        require_once(APPPATH . "ThirdParty/PHPOffice-PhpSpreadsheet/vendor/autoload.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = \PhpOffice\PhpSpreadsheet\IOFactory::load($temp_file_path . $file_name);
        $excel_file = $excel_file->getActiveSheet()->toArray();

        $allowed_headers = $this->_get_allowed_headers();
        $now = get_current_utc_time();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, modify this for custom fields and continue for the next loop
                $allowed_headers = $this->_prepare_headers_for_submit($value, $allowed_headers);
                continue;
            }

            $client_data_array = $this->_prepare_client_data($value, $allowed_headers);
            $client_data = get_array_value($client_data_array, "client_data");
            $client_contact_data = get_array_value($client_data_array, "client_contact_data");
            $custom_field_values_array = get_array_value($client_data_array, "custom_field_values_array");

            //couldn't prepare valid data
            if (!($client_data && count($client_data))) {
                continue;
            }

            //found information about client, add some additional info
            $client_data["created_date"] = $now;
            $client_data["created_by"] = $this->login_user->id;
            $client_contact_data["created_at"] = $now;

            //save client data
            $client_save_id = $this->Clients_model->ci_save($client_data);
            if (!$client_save_id) {
                continue;
            }

            //save custom fields
            $this->_save_custom_fields_of_client($client_save_id, $custom_field_values_array);

            //add client id to contact data
            $client_contact_data["client_id"] = $client_save_id;
            $this->Users_model->ci_save($client_contact_data);
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _save_custom_fields_of_client($client_id, $custom_field_values_array) {
        if (!$custom_field_values_array) {
            return false;
        }

        foreach ($custom_field_values_array as $key => $custom_field_value) {
            $field_value_data = array(
                "related_to_type" => "clients",
                "related_to_id" => $client_id,
                "custom_field_id" => $key,
                "value" => $custom_field_value
            );

            $field_value_data = clean_data($field_value_data);

            $this->Custom_field_values_model->ci_save($field_value_data);
        }
    }

    private function _get_client_group_ids($client_groups_data) {
        $explode_client_groups = explode(", ", $client_groups_data);
        if (!($explode_client_groups && count($explode_client_groups))) {
            return false;
        }

        $groups_ids = "";

        foreach ($explode_client_groups as $group) {
            $group_id = "";
            $existing_group = $this->Client_groups_model->get_one_where(array("title" => $group, "deleted" => 0));
            if ($existing_group->id) {
                //client group exists, add the group id
                $group_id = $existing_group->id;
            } else {
                //client group doesn't exists, create a new one and add group id
                $group_data = array("title" => $group);
                $group_id = $this->Client_groups_model->ci_save($group_data);
            }

            //add the group id to group ids
            if ($groups_ids) {
                $groups_ids .= ",";
            }
            $groups_ids .= $group_id;
        }

        if ($groups_ids) {
            return $groups_ids;
        }
    }

    private function _get_allowed_headers() {
        return array(
            "company_name",
            "contact_first_name",
            "contact_last_name",
            "contact_email",
            "address",
            "city",
            "state",
            "zip",
            "country",
            "phone",
            "website",
            "vat_number",
            "client_groups",
            "currency",
            "currency_symbol"
        );
    }

    private function _store_headers_position($headers_row = array()) {
        $allowed_headers = $this->_get_allowed_headers();

        //check if all headers are correct and on the right position
        $final_headers = array();
        foreach ($headers_row as $key => $header) {
            if (!$header) {
                continue;
            }

            $key_value = str_replace(' ', '_', strtolower(trim($header, " ")));
            $header_on_this_position = get_array_value($allowed_headers, $key);
            $header_array = array("key_value" => $header_on_this_position, "value" => $header);

            if ($header_on_this_position == $key_value) {
                //allowed headers
                //the required headers should be on the correct positions
                //the rest headers will be treated as custom fields
                //pushed header at last of this loop
            } else if (((count($allowed_headers) - 1) < $key) && $key_value) {
                //custom fields headers
                //check if there is any existing custom field with this title
                $existing_id = $this->_get_existing_custom_field_id(trim($header, " "));
                if ($existing_id) {
                    $header_array["custom_field_id"] = $existing_id;
                } else {
                    $header_array["has_error"] = true;
                    $header_array["custom_field"] = true;
                }
            } else { //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            if ($key_value) {
                array_push($final_headers, $header_array);
            }
        }

        return $final_headers;
    }

    function validate_import_clients_file() {
        $this->access_only_allowed_members();

        $file_name = $this->request->getPost("file_name");
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if ($file_ext == "xlsx") {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_a_excel_file') . " (.xlsx)"));
        }
    }

    function validate_import_clients_file_data($check_on_submit = false) {
        $this->access_only_allowed_members();

        $table_data = "";
        $error_message = "";
        $headers = array();
        $got_error_header = false; //we've to check the valid headers first, and a single header at a time
        $got_error_table_data = false;

        $file_name = $this->request->getPost("file_name");

        require_once(APPPATH . "ThirdParty/PHPOffice-PhpSpreadsheet/vendor/autoload.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = \PhpOffice\PhpSpreadsheet\IOFactory::load($temp_file_path . $file_name);
        $excel_file = $excel_file->getActiveSheet()->toArray();

        $table_data .= '<table class="table table-responsive table-bordered table-hover" style="width: 100%; color: #444;">';

        $table_data_header_array = array();
        $table_data_body_array = array();

        foreach ($excel_file as $row_key => $value) {
            if ($row_key == 0) { //validate headers
                $headers = $this->_store_headers_position($value);

                foreach ($headers as $row_data) {
                    $has_error_class = false;
                    if (get_array_value($row_data, "has_error") && !$got_error_header) {
                        $has_error_class = true;
                        $got_error_header = true;

                        if (get_array_value($row_data, "custom_field")) {
                            $error_message = app_lang("no_such_custom_field_found");
                        } else {
                            $error_message = sprintf(app_lang("import_client_error_header"), app_lang(get_array_value($row_data, "key_value")));
                        }
                    }

                    array_push($table_data_header_array, array("has_error_class" => $has_error_class, "value" => get_array_value($row_data, "value")));
                }
            } else { //validate data
                if (!array_filter($value)) {
                    continue;
                }

                $error_message_on_this_row = "<ol class='pl15'>";
                $has_contact_first_name = get_array_value($value, 1) ? true : false;

                foreach ($value as $key => $row_data) {
                    $has_error_class = false;

                    if (!$got_error_header) {
                        $row_data_validation = $this->_row_data_validation_and_get_error_message($key, $row_data, $has_contact_first_name, $headers);
                        if ($row_data_validation) {
                            $has_error_class = true;
                            $error_message_on_this_row .= "<li>" . $row_data_validation . "</li>";
                            $got_error_table_data = true;
                        }
                    }

                    if (count($headers) > $key) {
                        $table_data_body_array[$row_key][] = array("has_error_class" => $has_error_class, "value" => $row_data);
                    }
                }

                $error_message_on_this_row .= "</ol>";

                //error messages for this row
                if ($got_error_table_data) {
                    $table_data_body_array[$row_key][] = array("has_error_text" => true, "value" => $error_message_on_this_row);
                }
            }
        }

        //return false if any error found on submitting file
        if ($check_on_submit) {
            return ($got_error_header || $got_error_table_data) ? false : true;
        }

        //add error header if there is any error in table body
        if ($got_error_table_data) {
            array_push($table_data_header_array, array("has_error_text" => true, "value" => app_lang("error")));
        }

        //add headers to table
        $table_data .= "<tr>";
        foreach ($table_data_header_array as $table_data_header) {
            $error_class = get_array_value($table_data_header, "has_error_class") ? "error" : "";
            $error_text = get_array_value($table_data_header, "has_error_text") ? "text-danger" : "";
            $value = get_array_value($table_data_header, "value");
            $table_data .= "<th class='$error_class $error_text'>" . $value . "</th>";
        }
        $table_data .= "</tr>";

        //add body data to table
        foreach ($table_data_body_array as $table_data_body_row) {
            $table_data .= "<tr>";
            $error_text = "";

            foreach ($table_data_body_row as $table_data_body_row_data) {
                $error_class = get_array_value($table_data_body_row_data, "has_error_class") ? "error" : "";
                $error_text = get_array_value($table_data_body_row_data, "has_error_text") ? "text-danger" : "";
                $value = get_array_value($table_data_body_row_data, "value");
                $table_data .= "<td class='$error_class $error_text'>" . $value . "</td>";
            }

            if ($got_error_table_data && !$error_text) {
                $table_data .= "<td></td>";
            }

            $table_data .= "</tr>";
        }

        //add error message for header
        if ($error_message) {
            $total_columns = count($table_data_header_array);
            $table_data .= "<tr><td class='text-danger' colspan='$total_columns'><i data-feather='alert-triangle' class='icon-16'></i> " . $error_message . "</td></tr>";
        }

        $table_data .= "</table>";

        echo json_encode(array("success" => true, 'table_data' => $table_data, 'got_error' => ($got_error_header || $got_error_table_data) ? true : false));
    }

    private function _row_data_validation_and_get_error_message($key, $data, $has_contact_first_name, $headers = array()) {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //company name field is required
        if ($header_value == "company_name" && !$data) {
            return app_lang("import_client_error_company_name_field_required");
        }

        //if there is contact first name then the contact last name and email is required
        //the email should be unique then
        if ($has_contact_first_name) {
            if ($header_value == "contact_last_name" && !$data) {
                return app_lang("import_client_error_contact_name");
            }

            if ($header_value == "contact_email") {
                if ($data) {
                    if ($this->Users_model->is_email_exists($data)) {
                        return app_lang("duplicate_email");
                    }
                } else {
                    return app_lang("import_client_error_contact_email");
                }
            }
        }

        //there has no date field on default import fields
        //check on custom fields
        if (((count($allowed_headers) - 1) < $key) && $data) {
            $header_info = get_array_value($headers, $key);
            $custom_field_info = $this->Custom_fields_model->get_one(get_array_value($header_info, "custom_field_id"));
            if ($custom_field_info->field_type === "date" && !$this->_check_valid_date($data)) {
                return app_lang("import_date_error_message");
            }
        }
    }

    function download_sample_excel_file() {
        $this->access_only_allowed_members();
        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-clients-sample.xlsx"))));
    }

    function gdpr() {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);
        return $this->template->view("appointments/contacts/gdpr", $view_data);
    }

    function export_my_data() {
        if (get_setting("enable_gdpr") && get_setting("allow_clients_to_export_their_data")) {
            $user_info = $this->Users_model->get_one($this->login_user->id);

            $txt_file_name = $user_info->first_name . " " . $user_info->last_name . ".txt";

            $data = $this->_make_export_data($user_info);

            $handle = fopen($txt_file_name, "w");
            fwrite($handle, $data);
            fclose($handle);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($txt_file_name));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($txt_file_name));
            readfile($txt_file_name);

            //delete local file
            if (file_exists($txt_file_name)) {
                unlink($txt_file_name);
            }

            exit;
        }
    }

    private function _make_export_data($user_info) {
        $required_general_info_array = array("first_name", "last_name", "email", "job_title", "phone", "gender", "skype", "created_at");

        $data = strtoupper(app_lang("general_info")) . "\n";

        //add general info
        foreach ($required_general_info_array as $field) {
            if ($user_info->$field) {
                if ($field == "created_at") {
                    $data .= app_lang("created") . ": " . format_to_datetime($user_info->$field) . "\n";
                } else if ($field == "gender") {
                    $data .= app_lang($field) . ": " . ucfirst($user_info->$field) . "\n";
                } else if ($field == "skype") {
                    $data .= "Skype: " . ucfirst($user_info->$field) . "\n";
                } else {
                    $data .= app_lang($field) . ": " . $user_info->$field . "\n";
                }
            }
        }

        $data .= "\n\n";
        $data .= strtoupper(app_lang("client_info")) . "\n";

        //add company info
        $client_info = $this->Clients_model->get_one($user_info->client_id);
        $required_client_info_array = array("company_name", "address", "city", "state", "zip", "country", "phone", "website", "vat_number");
        foreach ($required_client_info_array as $field) {
            if ($client_info->$field) {
                $data .= app_lang($field) . ": " . $client_info->$field . "\n";
            }
        }

        $data .= "\n\n";
        $data .= strtoupper(app_lang("social_links")) . "\n";

        //add social links
        $social_links = $this->Social_links_model->get_one($user_info->id);

        unset($social_links->id);
        unset($social_links->user_id);
        unset($social_links->deleted);

        foreach ($social_links as $key => $value) {
            if ($value) {
                $data .= ucfirst($key) . ": " . $value . "\n";
            }
        }

        return $data;
    }

    function request_my_account_removal() {
        if (get_setting("enable_gdpr") && get_setting("clients_can_request_account_removal")) {

            $user_id = $this->login_user->id;
            $data = array("requested_account_removal" => 1);
            $this->Users_model->ci_save($data, $user_id);

            $client_id = $this->Users_model->get_one($user_id)->client_id;
            log_notification("client_contact_requested_account_removal", array("client_id" => $client_id), $user_id);

            $this->session->setFlashdata("success_message", app_lang("estimate_submission_message"));
            app_redirect("appointments/contact_profile/$user_id/gdpr");
        }
    }

    /* load expenses tab  */

    function expenses($client_id) {
        $this->can_access_expenses();
        $this->_validate_client_view_access($client_id);

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = clean_data($client_id);

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("expenses", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("expenses", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("appointments/expenses/index", $view_data);
        }
    }

    function contracts($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("contracts", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("contracts", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("appointments/contracts/contracts", $view_data);
        }
    }

    function appointments_list() {
        $this->access_only_allowed_members();

        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['groups_dropdown'] = json_encode($this->_get_groups_dropdown_select2_data(true));
        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown(true);
        $view_data['labels_dropdown'] = json_encode($this->make_labels_dropdown("client", "", true));

        return $this->template->view("appointments/appointments_list", $view_data);
    }


    private function make_access_permissions_view_data() {

        $access_invoice = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_invoice->access_type == "all") ? true : false;

        $access_estimate = $this->get_access_info("estimate");
        $view_data["show_estimate_info"] = (get_setting("module_estimate") && $access_estimate->access_type == "all") ? true : false;

        $access_estimate_request = $this->get_access_info("estimate_request");
        $view_data["show_estimate_request_info"] = (get_setting("module_estimate_request") && $access_estimate_request->access_type == "all") ? true : false;

        $access_order = $this->get_access_info("order");
        $view_data["show_order_info"] = (get_setting("module_order") && $access_order->access_type == "all") ? true : false;

        $access_proposal = $this->get_access_info("proposal");
        $view_data["show_proposal_info"] = (get_setting("module_proposal") && $access_proposal->access_type == "all") ? true : false;

        $access_ticket = $this->get_access_info("ticket");
        $view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_ticket->access_type == "all") ? true : false;

        $access_contract = $this->get_access_info("contract");
        $view_data["show_contract_info"] = (get_setting("module_contract") && $access_contract->access_type == "all") ? true : false;
        $view_data["show_project_info"] = !$this->has_all_projects_restricted_role();

        return $view_data;
    }

    function proposals($client_id) {
        validate_numeric_value($client_id);
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("proposals", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("proposals", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("appointments/proposals/proposals", $view_data);
        }
    }

    function switch_account($user_id) {
        validate_numeric_value($user_id);
        $this->access_only_clients();

        $options = array(
            'id' => $user_id,
            'email' => $this->login_user->email,
            'status' => 'active',
            'deleted' => 0,
            'disable_login' => 0,
            'user_type' => 'client'
        );

        $user_info = $this->Users_model->get_one_where($options);
        if (!$user_info->id) {
            show_404();
        }

        $session = \Config\Services::session();
        $session->set('user_id', $user_info->id);

        app_redirect('dashboard/view');
    }

    /* load tasks tab  */

    function tasks($client_id) {
        $this->_validate_client_view_access($client_id);

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["can_create_task"] = $this->can_edit_clients();

        $view_data['client_id'] = clean_data($client_id);
        return $this->template->view("appointments/tasks/index", $view_data);
    }
}

/* End of file clients.php */
/* Location: ./app/controllers/clients.php */