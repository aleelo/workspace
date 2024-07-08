<?php

namespace App\Controllers;

class Departments extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

 
    //load items list view
    function index() {

        return $this->template->rander("departments/index");
    }

    //get categories dropdown
    private function _get_categories_dropdown() {
        $categories = $this->Item_categories_model->get_all_where(array("deleted" => 0), 0, 0, "title")->getResult();

        $categories_dropdown = array(array("id" => "", "text" => "- " . app_lang("category") . " -"));
        foreach ($categories as $category) {
            $categories_dropdown[] = array("id" => $category->id, "text" => $category->title);
        }

        return json_encode($categories_dropdown);
    }

    /* load item modal */

    function modal_form($sid = 0) {

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        // die('hers');

        $id = $this->request->getPost('id');

        if($id){
            $options = ['id' => $id];
            $view_data['model_info'] = $this->Departments_model->get_details($options)->getRow();
        }else{
            $view_data['model_info'] = null;
        }


        $view_data['employees_dropdown'] = $this->get_employees_dropdown();

        return $this->template->view('departments/modal_form', $view_data);
    }

    /* add or edit an item */

    function save() {

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id = $this->request->getPost('id');

            $nameSo = $this->request->getPost('nameSo');
            $nameEn = $this->request->getPost('nameEn');
           $head_id = $this->request->getPost('head_id');
            $email = $this->request->getPost('email');
            $remarks = $this->request->getPost('remarks');
       

            if($id){
                $this->db->query("update departments set nameSo='$nameSo',nameEn='$nameEn',head_id=$head_id,email='$email',remarks='$remarks' where id=$id");
                $save_id = $id;
            }else{

                $this->db->query("insert into departments (nameSo,nameEn,head_id,email,remarks) values('$nameSo','$nameEn',$head_id,'$email','$remarks')");
                $save_id = $this->db->insertID();
            }

        if ($save_id && $id) {
            $data = $this->Departments_model->get_details(['id' => $save_id])->getRow();
            echo json_encode(array("success" => true, "id" => $save_id, "data" => $this->_make_department_row($data), 'message' => app_lang('record_updated')));
        }elseif ($save_id) {
            $data = $this->Departments_model->get_details(['id' => $save_id])->getRow();
            echo json_encode(array("success" => true, "id" => $save_id, "data" => $this->_make_department_row($data), 'message' => app_lang('record_saved')));
        } 
         else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an item */

    function delete() {
        // $this->access_only_team_members();

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
    
        if ($this->db->query("delete from departments where id = $id")) {
            echo json_encode(array("success" => true, "id" => $id, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
        
    }

    /* list of items, prepared for datatable  */

    function list_data() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        // die('dsf:list_data');

        $list_data = $this->Departments_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_department_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of item list table */

    private function _make_department_row($data) {

        return array(
            $data->id,
            $data->nameSo,
            $data->nameEn,
            $data->email,
            $data->head,
            $data->remarks,
            modal_anchor(get_uri("departments/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("departments/delete"), "data-action" => "delete"))
        );
    }

    function upload_file() {
        $this->access_only_team_members();
        upload_file_to_temp();
    }

    function validate_items_file() {
        $this->access_only_team_members();
        $file_name = $this->request->getPost("file_name");
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if (is_image_file($file_name)) {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_valid_image_files')));
        }
    }

    function view() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $model_info = $this->Purchase_Item_model->get_details(array("id" => $this->request->getPost('id'), "login_user_id" => $this->login_user->id))->getRow();

        $view_data['model_info'] = $model_info;
        $view_data["client_info"] = $this->Clients_model->get_one($this->login_user->client_id);

        return $this->template->view('departments/view', $view_data);
    }

    function save_files_sort() {
        $this->access_only_allowed_members();
        $id = $this->request->getPost("id");
        $sort_values = $this->request->getPost("sort_values");
        if ($id && $sort_values) {
            //extract the values from the :,: separated string
            $sort_array = explode(":,:", $sort_values);

            $item_info = $this->Purchase_Item_model->get_one($id);
            if ($item_info->id) {
                $updated_file_indexes = update_file_indexes($item_info->files, $sort_array);
                $item_data = array(
                    "files" => serialize($updated_file_indexes)
                );

                $this->Purchase_Item_model->ci_save($item_data, $id);
            }
        }
    }

    function import_items_modal_form() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        return $this->template->view("departments/import_items_modal_form");
    }

    function download_sample_excel_file() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-items-sample.xlsx"))));
    }

    function upload_excel_file() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();
        upload_file_to_temp(true);
    }

    function validate_import_items_file() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

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

    function save_item_from_excel_file() {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

        if (!$this->validate_import_items_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }

        $file_name = $this->request->getPost('file_name');
        require_once(APPPATH . "ThirdParty/PHPOffice-PhpSpreadsheet/vendor/autoload.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = \PhpOffice\PhpSpreadsheet\IOFactory::load($temp_file_path . $file_name);
        $excel_file = $excel_file->getActiveSheet()->toArray();
        $allowed_headers = $this->_get_allowed_headers();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, continue to the next loop
                continue;
            }

            $item_data_array = $this->_prepare_item_data($value, $allowed_headers);
            $item_data = get_array_value($item_data_array, "item_data");

            //couldn't prepare valid data
            if (!($item_data && count($item_data))) {
                continue;
            }

            //save item data
            $item_save_id = $this->Purchase_Item_model->ci_save($item_data);
            if (!$item_save_id) {
                continue;
            }
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _get_item_category_id($category = "") {
        if (!$category) {
            return false;
        }

        $existing_category = $this->Item_categories_model->get_one_where(array("title" => $category, "deleted" => 0));
        if ($existing_category->id) {
            //item category exists, add the category id
            return $existing_category->id;
        } else {
            //item category doesn't exists, create a new one and add category id
            $category_data = array("title" => $category);
            return $this->Item_categories_model->ci_save($category_data);
        }
    }

    private function _get_allowed_headers() {
        return array(
            "title", //required
            "description",
            "category", //required
            "unit_type",
            "rate", //required, use unformat_currency()
            "show_in_client_portal"
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
                //pushed header at last of this loop
            } else {
                //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            if ($key_value) {
                array_push($final_headers, $header_array);
            }
        }

        return $final_headers;
    }

    function validate_import_items_file_data($check_on_submit = false) {
        // $this->access_only_team_members();
        // $this->validate_access_to_items();

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

                        $error_message = sprintf(app_lang("import_client_error_header"), app_lang(get_array_value($row_data, "key_value")));
                    }

                    array_push($table_data_header_array, array("has_error_class" => $has_error_class, "value" => get_array_value($row_data, "value")));
                }
            } else { //validate data
                if (!array_filter($value)) {
                    continue;
                }

                $error_message_on_this_row = "<ol class='pl15'>";

                foreach ($value as $key => $row_data) {
                    $has_error_class = false;

                    if (!$got_error_header) {
                        $row_data_validation = $this->_row_data_validation_and_get_error_message($key, $row_data);
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

    private function _row_data_validation_and_get_error_message($key, $data) {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //item title field is required
        if (($header_value == "title" && !$data) || ($header_value == "category" && !$data) || ($header_value == "rate" && !$data)) {
            return sprintf(app_lang("import_error_field_required"), app_lang($header_value));
        }
    }

    private function _prepare_item_data($data_row, $allowed_headers) {
        //prepare item data
        $item_data = array();

        foreach ($data_row as $row_data_key => $row_data_value) { //row values
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if ($header_key_value == "category") { //we've to make category data differently
                $item_data["category_id"] = $this->_get_item_category_id($row_data_value);
            } else if ($header_key_value == "rate") { //unformat currency of rate
                $item_data["rate"] = unformat_currency($row_data_value);
            } else if ($header_key_value == "show_in_client_portal") {
                $item_data["show_in_client_portal"] = ($row_data_value === "Yes" ? 1 : "");
            } else {
                $item_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "item_data" => $item_data
        );
    }

}

/* End of file items.php */
/* Location: ./app/controllers/items.php */