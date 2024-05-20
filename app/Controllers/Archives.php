<?php

namespace App\Controllers;

class Archives extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {

        if ($this->login_user->user_type == "client") {
            $client_id = $this->login_user->client_id;
            $view_data['client_id'] = clean_data($client_id);
        }else{

            $view_data['client_id'] = 0;
        }


        
        return $this->template->rander("archives/index", $view_data);
   

    }


    // private function can_view_files() {
    //     if ($this->login_user->user_type == "staff") {
    //         $this->access_only_allowed_members();
    //     } else {
    //         if (!get_setting("client_can_view_files")) {
    //             app_redirect("forbidden");
    //         }
    //     }
    // }

    // private function can_add_files() {
    //     if ($this->login_user->user_type == "staff") {
    //         $this->access_only_allowed_members();
    //     } else {
    //         if (!get_setting("client_can_add_files")) {
    //             app_redirect("forbidden");
    //         }
    //     }
    // }
    
    // private function _validate_client_manage_access($client_id = 0) {
    //     if (!$this->can_edit_clients($client_id)) {
    //         app_redirect("forbidden");
    //     }
    // }

    // private function _validate_client_view_access($client_id = 0) {
    //     if (!$this->can_view_clients($client_id)) {
    //         app_redirect("forbidden");
    //     }
    // }

    /* file upload modal */
    function file_modal_form() {

        $view_data['model_info'] = $this->Archives_model->get_one($this->request->getPost('id'));
        $client_id = $this->request->getPost('client_id') ? $this->request->getPost('client_id') : $view_data['model_info']->client_id;        

        $view_data['client_id'] = $client_id;
        $view_data['departments'] = $this->get_departments_for_select();
        return $this->template->view('archives/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "department_id" => "required|numeric",
            "service_type" => "required"
        ));

        $client_id = $this->request->getPost('client_id');
        $department_id = $this->request->getPost('department_id');


        $files = $this->request->getPost("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("archives", $department_id);

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
                        "service_type" => $this->request->getPost('service_type'),
                        "description" => $this->request->getPost('description_' . $file),
                        "file_size" => $this->request->getPost('file_size_' . $file),
                        "department_id" => $this->request->getPost('department_id'),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->Archives_model->ci_save($data);
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

    function files_list_data() {

        $department_id = $this->get_user_department_id();
        $role = $this->get_user_role();

        if($role == 'admin' || $role == 'Administrator' || $role == 'HRM'){
            $department_id = '%';
        }

        $options = array(
            'department_id' => $department_id,
            'role'=> $role
        );
        $list_data = $this->Archives_model->get_details($options)->getResult();
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
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("archives/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("archives/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        if ($this->login_user->user_type == "staff") {
            $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("archives/delete_file"), "data-action" => "delete-confirmation"));
        }


        return array($data->id,
            "<div data-feather='$file_icon' class='mr10 float-start'></div>" . $description,
            convert_file_size($data->file_size),
            $data->service_type,
            $data->department,
            format_to_datetime($data->created_at),
            $uploaded_by,
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->Archives_model->get_details(array("id" => $file_id))->getRow();
      
        if ($file_info) {

            $view_data['can_comment_on_files'] = false;
            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("archives", $file_info->department_id));

        // var_dump($file_url);
        // die;
            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_info->file_name);

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = clean_data($file_id);
            return $this->template->view("archives/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->Archives_model->get_one($id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        // var_dump($file_info);die;

        return $this->download_app_files(get_general_file_path("archives", $file_info->department_id), $file_data);
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
        $info = $this->Archives_model->get_one($id);
        $role = $this->get_user_role();

        if (($info->uploaded_by != $this->login_user->id || $info->department_id != $this->get_user_department_id()) && ( $role != 'admin')) {
            app_redirect("forbidden");
        }

        if ($this->Archives_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("archives", $info->department_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

}

/* End of file Archives.php */
/* Location: ./app/controllers/Archives.php */