<?php

namespace App\Controllers;

class Training_locations extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    //load leave type list view
    function index() {
        return $this->template->rander("training_locations/index");
    }

    //load leave type add/edit form
    function modal_form() {
        $view_data['model_info'] = $this->Training_locations_model->get_one($this->request->getPost('id'));
        return $this->template->view('training_locations/modal_form', $view_data);
    }

    //save leave type
    function save() {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "training_location" => "required"
        ));

        $id = $this->request->getPost('id');

        $data = array(
            "location" => $this->request->getPost('training_location'),
           
        );

        $save_id = $this->Training_locations_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //delete/undo a leve type
    function delete() {

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->request->getPost('undo')) {
            if ($this->Training_locations_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Training_locations_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    //prepare leave types list data for datatable
    function list_data() {
        $list_data = $this->Training_locations_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    //get a row of leave types row
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Training_locations_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    //make a row of leave types row
    private function _make_row($data) {
        return array(

            $data->location,
            modal_anchor(get_uri("training_locations/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_training_location'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_training_location'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("training_locations/delete"), "data-action" => "delete"))

        );
    }

}

/* End of file leave_types.php */
/* Location: ./app/controllers/leave_types.php */