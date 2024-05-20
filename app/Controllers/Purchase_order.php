<?php

namespace App\Controllers;

use App\Libraries\Excel_import;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Common\Version;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRImageWithLogo;
use Dompdf\Dompdf;
use Dompdf\Options;

class Purchase_order extends Security_Controller
{

    use Excel_import;

    private $lead_statuses_id_by_title = array();
    private $lead_sources_id_by_title = array();
    private $lead_owners_id_by_name = array();

    public function __construct()
    {
        parent::__construct();

    }

    private function validate_lead_access($lead_id)
    {
        
    }

    private function _validate_leads_report_access()
    {
        if (!$this->login_user->is_admin && $this->access_type != "all") {
            app_redirect("forbidden");
        }
    }

    /* load leads list view */

    public function index()
    {
        
    //    $res = $this->check_access('lead');
       $role = $this->get_user_role();
       $can_add_template = $role == 'admin';
       $view_data["can_add_template"] = $can_add_template;
        // $this->access_only_allowed_members();
        // $this->check_module_availability("module_lead");


        return $this->template->rander("purchase_order/index", $view_data);
    }
    
     /* load receive add/edit modal */
     public function order_modal_form()
     {
         $id = $this->request->getPost('id');
 
         $this->validate_submitted_data(array(
             "id" => "numeric",
         ));

         $view_data['suppliers'] = $this->get_suppliers_for_select();
 
         $view_data['label_column'] = "col-md-3";
         $view_data['field_column'] = "col-md-9";
 
         $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loding from the lead's details view
         $view_data['model_info'] = $this->Purchase_Order_model->get_one($id); //$this->Subscriptions_model->get_one($lead_id);//
       
 
         return $this->template->view('purchase_order/order_modal_form', $view_data);
     }

    //get owners dropdown
    //owner will be team member
    private function _get_owners_dropdown($view_type = "")
    {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
        $team_members_dropdown = array();

        if ($view_type == "filter") {
            $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("owner") . " -"));
        }

        foreach ($team_members as $member) {
            $team_members_dropdown[] = array("id" => $member->id, "text" => $member->first_name . " " . $member->last_name);
        }

        return $team_members_dropdown;
    }

    private function _get_sources_dropdown()
    {

        $sources = $this->Lead_source_model->get_details()->getResult();

        $dropdown = array(array("id" => "", "text" => "- " . app_lang("source") . " -"));
        foreach ($sources as $source) {
            $dropdown[] = array("id" => $source->id, "text" => $source->title);
        }

        return $dropdown;
    }

    /* insert or update a receive */
    public function save()
    {
        $id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "fuel_type" => "required",
            "supplier" => "required",
            "receive_date" => "required",
            "barrels" => "required",
            "vehicle_model" => "required",
            "plate" => "required",
        ));
           
        // fuel_type supplier receive_date barrels	litters	received_by	vehicle_model	plate	
        $input = array(
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            "fuel_type" => $this->request->getPost('fuel_type'),
            "supplier" => $this->request->getPost('supplier'),
            "department_id" => $this->get_user_department_id(),
            "receive_date" => $this->request->getPost('receive_date'),
            "barrels" => $this->request->getPost('barrels'),
            "litters" => $this->request->getPost('litters'),
            "vehicle_model" => $this->request->getPost('vehicle_model'),
            "plate" => $this->request->getPost('plate'),
            "received_by" => $this->login_user->id,
            "created_at" => date('Y-m-d'),
            "remarks" => $this->request->getPost('remarks'),

        );

        $input = clean_data($input);
        $save_id = $this->Fuel_Receive_model->ci_save($input, $id);

        
        if ($save_id) {
            // save_custom_fields("leads", $save_id, $this->login_user->is_admin, $this->login_user->user_type);
            $data = $this->_row_data($save_id);

            if (!$id) { //create operation
                
                log_notification("fuel_receive_created", array("fuel_receive_id" => $save_id), $this->login_user->id);

                echo json_encode(array("success" => true, "data" =>  $data, 'id' => $save_id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_saved')));
            } else { //update operation
                
                log_notification("fuel_receive_updated", array("fuel_receive_id" => $id), $this->login_user->id);

                echo json_encode(array("success" => true, "data" => $data, 'id' => $id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_updated')));
            }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred').', Data not saved.'));
        }
    }

    /* insert or update a order */
    public function save_order()
    {
        $id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "supplier_id" => "required",
            "order_date" => "required",
            "quantity" => "required"
        ));
           
        // fuel_type supplier receive_date barrels	litters	received_by	vehicle_model	plate	
        $input = array(
            "product_type" => $this->request->getPost('product_type'),
            "supplier_id" => $this->request->getPost('supplier_id'),
            "department_id" => $this->get_user_department_id(),
            "order_date" => $this->request->getPost('order_date'),
            "ordered_by" => $this->login_user->id,
            "created_at" => date('Y-m-d'),
            "remarks" => $this->request->getPost('remarks'),

        );

        if(!$id){            
            $input['uuid'] = $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid;
        }

        $input = clean_data($input);
        $save_id = $this->Purchase_Order_model->ci_save($input, $id);

        
        if ($save_id) {
            // save_custom_fields("leads", $save_id, $this->login_user->is_admin, $this->login_user->user_type);
            $data = $this->order_row_data($save_id);

            if (!$id) { //create operation
                
                log_notification("purchase_order_created", array("fuel_order_id" => $save_id), $this->login_user->id);

                echo json_encode(array("success" => true, "data" =>  $data, 'id' => $save_id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_saved')));
            } else { //update operation
                
                log_notification("purchase_order_updated", array("fuel_order_id" => $id), $this->login_user->id);

                echo json_encode(array("success" => true, "data" => $data, 'id' => $id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_updated')));
            }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred').', Data not saved.'));
        }
    }

    
    /* load item modal */
    function item_modal_form() {
        $purchase_order_id = $this->request->getPost('purchase_order_id');

        if (!$this->can_edit_purchases()) {
            app_redirect("forbidden");
        }

        // if (!$this->is_invoice_editable($invoice_id)) {
        //     app_redirect("forbidden");
        // }

        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));
        
        $id = $this->request->getPost('id');

        $view_data['model_info'] = $this->Purchase_Order_Items_model->get_one($id);

        if (!$purchase_order_id) {
            $purchase_order_id = $view_data['model_info']->purchase_order_id;
        }

        $view_data['purchase_order_id'] = $purchase_order_id;
        return $this->template->view('purchase_order/item_modal_form', $view_data);
    }

     /* prepare suggestion of purchase item */
     function get_purchase_item_suggestion() {
        $key = $this->request->getPost("q");
        $suggestion = array();

        $items = $this->Purchase_Order_Items_model->get_item_suggestion($key);

        foreach ($items as $item) {
            $suggestion[] = array("id" => $item->id, "text" => $item->name);
        }

        $suggestion[] = array("id" => "+", "text" => "+ " . app_lang("create_new_item"));

        echo json_encode($suggestion);
    }

     /* purchase total section */
     private function _get_purchase_total_view($purchase_id = 0) {
        $view_data["invoice_total_summary"] = null;$this->Purchase_Order_model->get_purchase_total_summary($purchase_id);
        $view_data["purchase_id"] = $purchase_id;
        $can_edit_purchases = false;
        if ($this->can_edit_purchases()) {
            $can_edit_purchases = true;
        }
        $view_data["can_edit_purchases"] = $can_edit_purchases;
        return $this->template->view('purchase_order/purchase_total_section', $view_data);
    }

    /* add or edit purchase item */
    function save_item() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "purchase_order_id" => "required|numeric"
        ));

        $purchase_order_id = $this->request->getPost('purchase_order_id');

        if (!$this->can_edit_purchases()) {
            app_redirect("forbidden");
        }

        $id = $this->request->getPost('id');
        $price = unformat_currency($this->request->getPost('invoice_item_rate'));
        $quantity = unformat_currency($this->request->getPost('invoice_item_quantity'));
        $invoice_item_name = $this->request->getPost('invoice_item_name');
        $item_id = 0;

        if (!$id) {
            //on adding item for the first time, get the id to store
            $item_id = $this->request->getPost('item_id');
        }

        //check if the add_new_item flag is on, if so, add the item to libary. 
        $add_new_item_to_library = $this->request->getPost('add_new_item_to_library');
        if ($add_new_item_to_library) {
            $library_item_data = array(
                "name" => $invoice_item_name,
                "description" => $this->request->getPost('invoice_item_description'),
                "unit_type" => $this->request->getPost('invoice_unit_type'),
                "price" => unformat_currency($this->request->getPost('invoice_item_rate'))
            );
            $item_id = $this->Purchase_Item_model->ci_save($library_item_data);
        }

        $purchase_item_data = array(
            "purchase_order_id" => $purchase_order_id,
            "name" => $this->request->getPost('invoice_item_name'),
            "description" => $this->request->getPost('invoice_item_description'),
            "quantity" => $quantity,
            "unit_type" => $this->request->getPost('invoice_unit_type'),
            "price" => unformat_currency($this->request->getPost('invoice_item_rate')),
            "total" => $price * $quantity
        );

        if ($item_id) {
            $purchase_item_data["item_id"] = $item_id;
        }

        // $purchase_item_id = $this->Purchase_Order_Items_model->save_item_and_update_invoice($purchase_item_data, $id, $purchase_order_id);
        $purchase_item_id = $this->Purchase_Order_Items_model->ci_save($purchase_item_data);
        if ($purchase_item_id) {
            $options = array("id" => $purchase_item_id);
            $purchase_item_info = $this->Purchase_Order_Items_model->get_details($options)->getRow();
            echo json_encode(array("success" => true, "purchase_order_id" => $purchase_item_info->purchase_order_id, "data" => $this->_make_item_row($purchase_item_info, true), "invoice_total_view" => $this->_get_purchase_total_view($purchase_item_info->invoice_id), 'id' => $purchase_item_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo an invoice item */

    function delete_item() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $item_info = $this->Purchase_Order_Items_model->get_one($id);

        if (!$this->can_edit_invoices()) {
            app_redirect("forbidden");
        }

        if ($this->request->getPost('undo')) {
            if ($this->Purchase_Order_Items_model->delete($id, true)) {
                $options = array("id" => $id);
                $item_info = $this->Purchase_Order_Items_model->get_details($options)->getRow();
                echo json_encode(array("success" => true, "invoice_id" => $item_info->invoice_id, "data" => $this->_make_item_row($item_info, true), "invoice_total_view" => $this->_get_purchase_total_view($item_info->invoice_id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }

        } else {
            if ($this->Purchase_Order_Items_model->delete($id)) {
                $item_info = $this->Purchase_Order_Items_model->get_one($id);
                echo json_encode(array("success" => true, "invoice_id" => $item_info->invoice_id, "invoice_total_view" => $this->_get_purchase_total_view($item_info->invoice_id), 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of invoice items, prepared for datatable  */

    function item_list_data($purchase_order_id = 0) {
        validate_numeric_value($purchase_order_id);

        if ($purchase_order_id ) {
            app_redirect("forbidden");
        }

        $list_data = $this->Purchase_Order_Items_model->get_details(array("purchase_order_id" => $purchase_order_id))->getResult();

        $is_ediable = false;
        if ($this->can_edit_purchases()) {
            $is_ediable = true;
        }

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_item_row($data, $is_ediable);
        }
        echo json_encode(array("data" => $result));
    }

    /* prepare a row of invoice item list table */

    private function _make_item_row($data, $is_ediable) {
        $move_icon = "";
        $desc_style = "";
        if ($is_ediable) {
            $move_icon = "<div class='float-start move-icon'><i data-feather='menu' class='icon-16'></i></div>";
            $desc_style = "style='margin-left:25px'";
        }
        $item = "<div class='item-row strong mb5' data-id='$data->id'>$move_icon $data->name</div>";
        if ($data->description) {
            $item .= "<span class='text-wrap' $desc_style>" . nl2br($data->description) . "</span>";
        }
        $type = $data->unit_type ? $data->unit_type : "";

        return array(
            $data->id,
            $item,
            to_decimal_format($data->quantity) . " " . $type,
            to_currency($data->price, $data->currency_symbol),
            to_currency($data->total, $data->currency_symbol),
            modal_anchor(get_uri("purchase_order/item_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_purchase'), "data-post-id" => $data->id, "data-post-purchase_order_id" => $data->purchase_order_id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("purchase_order/delete_item"), "data-action" => "delete"))
        );
    }


    /* insert or update a request */
    public function request_save()
    {
        $id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "request_type" => "required",
            "purpose" => "required",
            "request_date" => "required",
            "litters" => "required",
            "vehicle_engine" => "required",
            "plate" => "required",
        ));
           
        $requested_by = $this->request->getPost('requested_by');
        // requested_by	department_id	litters	vehicle_engine	plate	request_type	request_date	purpose	status	remarks	
        $input = array(
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            "request_type" => $this->request->getPost('request_type'),
            "department_id" => $this->get_user_department_id(),
            "request_date" => $this->request->getPost('request_date'),
            "litters" => $this->request->getPost('litters'),
            "fuel_type" => $this->request->getPost('fuel_type'),
            "vehicle_engine" => $this->request->getPost('vehicle_engine'),
            "plate" => $this->request->getPost('plate'),
            "requested_by" => isset($requested_by) ? $this->request->getPost('requested_by') : $this->login_user->id,
            "created_at" => date('Y-m-d'),
            "purpose" => $this->request->getPost('purpose'),
            "remarks" => $this->request->getPost('remarks'),

        );

        $input = clean_data($input);
        $save_id = $this->Fuel_Request_model->ci_save($input, $id);

       

        if ($save_id) {
            // save_custom_fields("leads", $save_id, $this->login_user->is_admin, $this->login_user->user_type);
            $data = $this->request_row_data($save_id);

            if (!$id) { //create operation
                
                log_notification("fuel_request_created", array("fuel_request_id" => $save_id), $this->login_user->id);

                echo json_encode(array("success" => true, "data" =>  $data, 'id' => $save_id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_saved')));
            } else { //update operation
                
                log_notification("fuel_request_updated", array("fuel_request_id" => $id), $this->login_user->id);

                // var_dump($doc->getRowArray());
                // die();

                echo json_encode(array("success" => true, "data" => $data, 'id' => $id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_updated')));
            }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred').', Data not saved.'));
        }
    }

    function receive_details() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        
        $id = $this->request->getPost('id');
        $model_info = $this->db->query("select rc.*,u.image as avatar,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_receives rc 
        LEFT JOIN rise_users u on rc.received_by = u.id 
        LEFT JOIN departments dp on rc.department_id = dp.id 
        where rc.id=$id")->getRow();

        if (!$model_info) {
            show_404();
        }

        $view_data['model_info'] = $model_info;
        return $this->template->view("fuel/receive_details", $view_data);
    }

    function order_details() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        
        $id = $this->request->getPost('id');
        $model_info = $this->Purchase_Order_model->get_details(['id' => $id])->getRow();

        if (!$model_info) {
            show_404();
        }

        $view_data['model_info'] = $model_info;
        return $this->template->view("purchase_order/order_details", $view_data);
    }

    function order_details_tab($id) {
                
        $model_info = $this->Purchase_Order_model->get_details(['id' => $id])->getRow();

        if (!$model_info) {
            show_404();
        }

        $view_data['model_info'] = $model_info;
        return $this->template->view("purchase_order/order_details_tab", $view_data);
    }

    function order_details_json() {
        $this->validate_submitted_data(array(
            "order_id" => "required|numeric"
        ));

        
        $id = $this->request->getPost('order_id');
     
        $model_info = $this->Purchase_Order_model->get_details(['id' => $id])->getRow();

        if (!$model_info) {
            show_404();
        }

        echo json_encode($model_info); 
    }

    
    function view($id = 0) {
        if (!$id) {
            app_redirect("forbidden");
        }

        if ($id) {;
            $order_options = array('purchase_order_id' => $id);

            $purchase_info = $this->Purchase_Order_model->get_details(['id' => $id])->getRow();

            $view_data['purchase_info'] = $purchase_info;
            $view_data['purchase_order_items'] = $this->Purchase_Order_model->get_order_items($order_options)->getResult();

                $view_data['purchase_status'] = $purchase_info->status;
                $view_data["can_add_purchase"] = true;
                return $this->template->rander("purchase_order/view_purchase", $view_data);
        } else {
            show_404();
        }
        
    }

    function request_details() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        
        $id = $this->request->getPost('id');
        $model_info = $this->db->query("select rc.*,u.image as avatar,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_requests rc 
        LEFT JOIN rise_users u on rc.requested_by = u.id 
        LEFT JOIN departments dp on rc.department_id = dp.id 
        where rc.id=$id")->getRow();

        if (!$model_info) {
            show_404();
        }

        $view_data['model_info'] = $model_info;
        return $this->template->view("fuel/request_details", $view_data);
    }

    function request_pdf($id) {
    
        $model_info = $this->db->query("select rc.*,u.image as avatar,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_requests rc 
        LEFT JOIN rise_users u on rc.requested_by = u.id 
        LEFT JOIN departments dp on rc.department_id = dp.id 
        where rc.uuid='$id'")->getRow();

        if (!$model_info) {
            show_404();
        }


        $options = new QROptions([
            'eccLevel' => EccLevel::L,
            'outputBase64' => true,
            'logoSpaceHeight' => 17,
            'logoSpaceWidth' => 17,
            'scale' => 2,
            'version' => Version::AUTO,

          ]);

        //   $options->outputType = ;

        $qrcode = (new QRCode($options))->render(get_uri('visitors_info/request_qrcode/'.$model_info->uuid));//->getQRMatrix(current_url())

        $view_data['model_info'] = $model_info;
        $view_data['qrcode'] = $qrcode;

        $this->get_fuel_request_pdf("fuel/request_pdf", $view_data);
        
        // return $this->template->view("fuel/request_pdf", $view_data);
    }
    
    
    public function get_fuel_request_pdf($path,$data,$mode='view'){

        $data_info = get_array_value($data, "model_info");
        $pdf_file_name = "fuel_request_pdf_".$data_info->id.".pdf";
       
        $options = new Options([
            'enable_remote' => true,
            'isRemoteEnabled' => true,
            'chroot',base_url('files/visitors'),
        ]);
        
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $dompdf->setOptions($options);
        
        // var_dump($options->get('chroot'));
        // die();
        // file_get_contents('visitors/',$dompdf->output());

        $html = view($path,$data);
        $dompdf->loadHtml($html);
        

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($pdf_file_name,['Attachment'=>0]);
        exit();
    }

    
    function confirm_dispense() {

        $this->validate_submitted_data(array(
            "id" => "required|numeric",
            "driver_name" => "required"
        ));

        $id = $this->request->getPost('id');
        $driver_name = $this->request->getPost('driver_name');
        // die($status);
        $role = $this->get_user_role();
        $data = array('driver_name' => $driver_name,'status'=>'dispensed');

        $save_id = $this->Fuel_Request_model->ci_save($data, $id);

        if ($save_id) {

            // $notification_options = array("leave_id" => $id, "to_user_id" => $applicatoin_info->applicant_id);

            // if ($status == "approved") {
            //     log_notification("leave_approved_HR", $notification_options);//leave_approved
            // } else if ($status == "pending") {
            //     log_notification("leave_approved_Director", $notification_options);
            // } else if ($status == "rejected") {
            //     log_notification("leave_rejected", $notification_options);
            // } else if ($status == "canceled") {
            //     log_notification("leave_canceled", $notification_options);
            // }

            echo json_encode(array("success" => true, "data" => $this->request_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

     //update request status
     function update_status() {

        $this->validate_submitted_data(array(
            "id" => "required|numeric",
            "status" => "required"
        ));

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        // die($status);
        $role = $this->get_user_role();
        $data = array('status' => $status);

        $save_id = $this->Fuel_Request_model->ci_save($data, $id);

        if ($save_id) {

            // $notification_options = array("leave_id" => $id, "to_user_id" => $applicatoin_info->applicant_id);

            // if ($status == "approved") {
            //     log_notification("leave_approved_HR", $notification_options);//leave_approved
            // } else if ($status == "pending") {
            //     log_notification("leave_approved_Director", $notification_options);
            // } else if ($status == "rejected") {
            //     log_notification("leave_rejected", $notification_options);
            // } else if ($status == "canceled") {
            //     log_notification("leave_canceled", $notification_options);
            // }

            echo json_encode(array("success" => true, "data" => $this->request_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo a receive */
    public function delete()
    {
        $this->validate_submitted_data(array(
            "id" => "required|numeric",
        ));

        $id = $this->request->getPost('id');
        // $this->validate_lead_access($id);

        if ($this->Purchase_Order_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

/* delete or undo a request */
public function r_delete()
{
    $this->validate_submitted_data(array(
        "id" => "required|numeric",
    ));

    $id = $this->request->getPost('id');
    // $this->validate_lead_access($id);

    if ($this->Fuel_Request_model->delete($id)) {
        echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
    } else {
        echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
    }
}
    /* list of receive report, prepared for datatable  */
    public function rec_rpt_list_data()
    {
        $role = get_user_role();
        $department_id = get_user_department_id();
        $received_by = $this->login_user->id;
        
        $received_by_search = $this->request->getPost('received_by');
        $department_id_search = $this->request->getPost('department_id');
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');

        if ($this->login_user->is_admin || $role == 'Administrator'  || $role == 'Access Control' || $role == 'HRM' ) { //|| $perm == "all"
            $received_by = '%';
            $department_id = '%';
        } else if ($role == 'Director'|| $role == 'Secretary') {
            $received_by = '%';
        } else if ($role == 'Employee') { //$perm == "own" || 
            $received_by = $this->login_user->id;
        }else{
            
            app_redirect("forbidden");
        }

        if($received_by_search){
            $received_by = $received_by_search;
        }

        if($department_id_search){
            $department_id = $department_id_search;
        }

        $where = " and rc.deleted=0";
        //by this, we can handel the server side or client side from the app table prams.
      
            if($start_date){
                $where .= " and receive_date between '$start_date' and '$end_date'";
            }

            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_receives rc 
            LEFT JOIN rise_users u on rc.received_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            where rc.received_by LIKE '$received_by' and rc.department_id LIKE '$department_id'  $where");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_fuel_receives rc
            where received_by LIKE '$received_by' and department_id LIKE '$department_id'  $where")->getRow()->affected;
            $result = array();
        


        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = array(
                $data->id,
                $data->fuel_type,
                $data->supplier,
                $data->receive_date,
                $data->barrels,
                $data->litters,
                $data->user,
                $data->department,
                $data->vehicle_model,
                $data->plate
            );
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }

    /* list of request report, prepared for datatable  */
    public function req_rpt_list_data()
    {
        $role = get_user_role();
        $department_id = get_user_department_id();
        $requested_by = $this->login_user->id;
        
        $requested_by_search = $this->request->getPost('requested_by');
        $department_id_search = $this->request->getPost('department_id');
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');

        if ($this->login_user->is_admin || $role == 'Administrator'  || $role == 'Access Control' || $role == 'HRM' ) { //|| $perm == "all"
            $requested_by = '%';
            $department_id = '%';
        } else if ($role == 'Director'|| $role == 'Secretary') {
            $requested_by = '%';
        } else if ($role == 'Employee') { //$perm == "own" || 
            $requested_by = $this->login_user->id;
        }else{
            
            app_redirect("forbidden");
        }

        if($requested_by_search){
            $requested_by = $requested_by_search;
        }

        if($department_id_search){
            $department_id = $department_id_search;
        }

        $where = " and rc.deleted=0";
        //by this, we can handel the server side or client side from the app table prams.
      
            if($start_date){
                $where .= " and request_date between '$start_date' and '$end_date'";
            }

            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_requests rc 
            LEFT JOIN rise_users u on rc.requested_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            where rc.requested_by LIKE '$requested_by' and rc.department_id LIKE '$department_id'  $where");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_fuel_requests rc
            where requested_by LIKE '$requested_by' and department_id LIKE '$department_id'  $where")->getRow()->affected;
            $result = array();
        


        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = array(
                $data->id,
                $data->request_type,
                $data->litters,
                $data->request_date,
                $data->purpose,
                $data->user,
                $data->department,
                $data->vehicle_engine,
                $data->plate,
                $data->status
            );
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }

    /* list of activity report, prepared for datatable  */
    public function activity_rpt_list_data()
    {
        $role = get_user_role();
        
        $requested_by_search = $this->request->getPost('requested_by');
        $received_by_search = $this->request->getPost('received_by');
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');

        if($requested_by_search){
            $requested_by = $requested_by_search;
        }else{
            $requested_by = '%';
        }

        if($received_by_search){
            $received_by = $received_by_search;
        }else{
            $received_by = '%';
        }

        $where = "";
        //by this, we can handel the server side or client side from the app table prams.
      
            if($start_date){
                $where .= " and date between '$start_date' and '$end_date'";
            }

            $result = $this->db->query("SELECT date,type,person,user_id,fuel_type,max(deleted) as deleted, sum(recqty) recqty,sum(reqqty) reqqty FROM(
                        SELECT receive_date date,'receive' as type,fuel_type,concat(u.first_name,' ',u.last_name) person,received_by user_id,rc.deleted,litters recqty,0 reqqty  from rise_fuel_receives rc
                        LEFT JOIN rise_users u on rc.received_by = u.id 
                        UNION all
                        SELECT request_date date,'request' as type,rq.fuel_type,concat(u.first_name,' ',u.last_name) person,requested_by user_id,rq.deleted,0 recqty,litters reqqty  from rise_fuel_requests rq
                        LEFT JOIN rise_users u on rq.requested_by = u.id where rq.status like 'dispensed'
                    ) r where (user_id LIKE '$requested_by' or user_id LIKE '$received_by') and deleted=0 $where
                    GROUP BY date,type,person,user_id,fuel_type");

            $list_data = $result->getResult();

            $total_rows =$this->db->query(" SELECT count(*) as affected FROM(
                            SELECT receive_date date,'receive' as type,fuel_type,concat(u.first_name,' ',u.last_name) person,received_by user_id,rc.deleted,litters recqty,0 reqqty  from rise_fuel_receives rc
                            LEFT JOIN rise_users u on rc.received_by = u.id 
                            UNION all
                            SELECT request_date date,'request' as type,rq.fuel_type ,concat(u.first_name,' ',u.last_name) person,requested_by user_id,rq.deleted,0 recqty,litters reqqty  from rise_fuel_requests rq
                            LEFT JOIN rise_users u on rq.requested_by = u.id where rq.status like 'dispensed'
                            )r where (user_id LIKE '$requested_by' or user_id LIKE '$received_by') and deleted=0 $where")->getRow()->affected;
         
         $result = array();
        


        $result_data = array();
        foreach ($list_data as $data) {
            
            $balance = $data->recqty - $data->reqqty;

            $result_data[] = array(
                $data->date,
                $data->type,
                $data->fuel_type,
                $data->person,
                $data->recqty,
                $data->reqqty,
                $balance
            );
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }

    public function list_data()
    {
        
        // $permissions = $this->login_user->permissions;

        // $perm = get_array_value($permissions, $name);
       
        // $result = $this->check_access('lead');//here means documents for us.

        $role = get_user_role();
        $department_id = get_user_department_id();

        $created_by = $this->login_user->id;

        if ($this->login_user->is_admin || $role == 'Administrator'  || $role == 'Access Control' || $role == 'HRM' ) { //|| $perm == "all"
            $created_by = '%';
            $department_id = '%';
        } else if ($role == 'Director'|| $role == 'Secretary') {
            $created_by = '%';
        } else if ($role == 'Employee') { //$perm == "own" || 
            $created_by = $this->login_user->id;
        }else{
            
            app_redirect("forbidden");
        }
        
        $options = append_server_side_filtering_commmon_params([]);


        $extraWhere = "";
        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($options, "server_side")) {
            $order_by = $options['order_by'];
            $order_direction = $options['order_dir'];
            $search_by = $options["search_by"] ;
            $skip = $options["skip"] ;

            
            $limit_offset = "";
            $limit = $options['limit'] ?? 10;
            $where="rc.deleted=0";

            if ($limit) {
            
                $offset = $skip ? $skip : 0;
                $limit_offset = " LIMIT $limit OFFSET $offset ";
            }

            if ($order_by) {
                $order_by = "$order_by $order_direction ";
            }

            if ($search_by) {
                $search_by = $this->db->escapeLikeString($search_by);

            // id	uuid	supplier	fuel_type	barrels	litters	receive_date	received_by	department_id	vehicle_model	plate	remarks	created_at	deleted	
                $where .= " AND (";
                $where .= " rc.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.uuid LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.supplier LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.fuel_type LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.barrels LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.litters LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.receive_date LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.department_id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.vehicle_model LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.remarks LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.plate LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.first_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.last_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " )";
            }


            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_receives rc 
            LEFT JOIN rise_users u on rc.received_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            where rc.received_by LIKE '$created_by' and rc.department_id LIKE '$department_id' and $where $extraWhere order by $order_by $limit_offset");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_fuel_receives rc
            where received_by LIKE '$created_by' and department_id LIKE '$department_id' and rc.deleted=0 $extraWhere")->getRow()->affected;
            $result = array();

        } else {
            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_receives rc 
            LEFT JOIN rise_users u on rc.received_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            where rc.received_by LIKE '$created_by' and rc.department_id LIKE '$department_id' and  rc.deleted=0 $extraWhere");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_fuel_receives rc
            where received_by LIKE '$created_by' and department_id LIKE '$department_id' and  rc.deleted=0 $extraWhere")->getRow()->affected;
            $result = array();
        }


        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_row($data);
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }

    
    public function order_list_data()
    {
        
        // $permissions = $this->login_user->permissions;

        // $perm = get_array_value($permissions, $name);
       
        // $result = $this->check_access('lead');//here means documents for us.

        $role = get_user_role();
        $department_id = get_user_department_id();

        $created_by = $this->login_user->id;

        if ($this->login_user->is_admin || $role == 'Administrator'  || $role == 'Access Control' || $role == 'HRM' ) { //|| $perm == "all"
            $created_by = '%';
            $department_id = '%';
        } else if ($role == 'Director'|| $role == 'Secretary') {
            $created_by = '%';
        } else if ($role == 'Employee') { //$perm == "own" || 
            $created_by = $this->login_user->id;
        }else{
            
            app_redirect("forbidden");
        }
        
        $options = append_server_side_filtering_commmon_params([]);


        $extraWhere = "";
        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($options, "server_side")) {
            $order_by = $options['order_by'];
            $order_direction = $options['order_dir'];
            $search_by = $options["search_by"] ;
            $skip = $options["skip"] ;

            
            $limit_offset = "";
            $limit = $options['limit'] ?? 10;
            $where="rc.deleted=0";

            if ($limit) {
            
                $offset = $skip ? $skip : 0;
                $limit_offset = " LIMIT $limit OFFSET $offset ";
            }

            if ($order_by) {
                $order_by = "$order_by $order_direction ";
            }

            if ($search_by) {
                $search_by = $this->db->escapeLikeString($search_by);

            // id	uuid	supplier	fuel_type	barrels	litters	receive_date	received_by	department_id	vehicle_model	plate	remarks	created_at	deleted	
                $where .= " AND (";
                $where .= " rc.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.uuid LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.supplier LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.product_type LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.quantity LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.order_date LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.department_id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.remarks LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.first_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.last_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " )";
            }


            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user,s.supplier_name as supplier from rise_purchase_orders rc 
            LEFT JOIN rise_users u on rc.ordered_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            LEFT JOIN rise_suppliers s on rc.supplier_id = s.id 
            where rc.ordered_by LIKE '$created_by' and rc.department_id LIKE '$department_id' and $where $extraWhere order by $order_by $limit_offset");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_purchase_orders rc
            where ordered_by LIKE '$created_by' and department_id LIKE '$department_id' and rc.deleted=0 $extraWhere")->getRow()->affected;
            $result = array();

        } else {
            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user,s.supplier_name as supplier from rise_purchase_orders rc 
            LEFT JOIN rise_users u on rc.ordered_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            LEFT JOIN rise_suppliers s on rc.supplier_id = s.id 
            where rc.ordered_by LIKE '$created_by' and rc.department_id LIKE '$department_id' and  rc.deleted=0 $extraWhere");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_purchase_orders rc
            where ordered_by LIKE '$created_by' and department_id LIKE '$department_id' and  rc.deleted=0 $extraWhere")->getRow()->affected;
            $result = array();
        }


        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_order_row($data);
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }
    
    public function request_list_data()
    {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);

        $permissions = $this->login_user->permissions;

        // $perm = get_array_value($permissions, $name);
       
        // $result = $this->check_access('lead');//here means documents for us.

        $role = get_user_role();
        $department_id = get_user_department_id();

        $created_by = $this->login_user->id;

        if ($this->login_user->is_admin || $role == 'Administrator'  || $role == 'Access Control' || $role == 'HRM' ) { //|| $perm == "all"

            $created_by = '%';
            $department_id = '%';

        } else if ($role == 'Director'|| $role == 'Secretary') {
            $created_by = '%';
        } else if ($role == 'Employee') { //$perm == "own" || 
            $created_by = $this->login_user->id;
        }else{
            
            app_redirect("forbidden");
        }
        
        $options = append_server_side_filtering_commmon_params([]);


        $extraWhere = "";
        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($options, "server_side")) {
            $order_by = $options['order_by'];
            $order_direction = $options['order_dir'];
            $search_by = $options["search_by"] ;
            $skip = $options["skip"] ;

            
            $limit_offset = "";
            $limit = $options['limit'] ?? 10;
            $where="rc.deleted=0";

            if ($limit) {
            
                $offset = $skip ? $skip : 0;
                $limit_offset = " LIMIT $limit OFFSET $offset ";
            }

            if ($order_by) {
                $order_by = "$order_by $order_direction ";
            }

            if ($search_by) {
                $search_by = $this->db->escapeLikeString($search_by);

            // id	uuid	requested_by	department_id	litters	vehicle_engine	plate	request_type	request_date	purpose	status	remarks	created_at	deleted	
                $where .= " AND (";
                $where .= " rc.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.uuid LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.request_type LIKE '%$search_by%' ESCAPE '!' ";//user requested
                $where .= " OR rc.litters LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.department_id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.vehicle_engine LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.plate LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.first_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.last_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.request_date LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.purpose LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.status LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR rc.remarks LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " )";
            }


            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_requests rc 
            LEFT JOIN rise_users u on rc.requested_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            where rc.requested_by LIKE '$created_by' and rc.department_id LIKE '$department_id' and $where $extraWhere order by $order_by $limit_offset");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_fuel_requests rc
            where requested_by LIKE '$created_by' and department_id LIKE '$department_id' and rc.deleted=0 $extraWhere")->getRow()->affected;
            $result = array();

        } else {
            $result = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_requests rc 
            LEFT JOIN rise_users u on rc.requested_by = u.id 
            LEFT JOIN departments dp on rc.department_id = dp.id 
            where rc.requested_by LIKE '$created_by' and rc.department_id LIKE '$department_id' and  rc.deleted=0 $extraWhere");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_fuel_requests rc
            where requested_by LIKE '$created_by' and department_id LIKE '$department_id' and  rc.deleted=0 $extraWhere")->getRow()->affected;
            $result = array();
        }


        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_request_row($data, $custom_fields);
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }

    /* return a row of receive list table */
    private function _row_data($id)
    {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields,
            // "leads_only" => true
        );
        $data = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_receives rc 
        LEFT JOIN rise_users u on rc.received_by = u.id 
        LEFT JOIN departments dp on rc.department_id = dp.id 
        where rc.id=$id")->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* return a row of receive list table */
    private function order_row_data($id)
    {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id
        );
        
        $data = $this->Purchase_Order_model->get_details($options)->getRow();

        return $this->_make_order_row($data, $custom_fields);
    }
    
    private function request_row_data($id)
    {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields,
            // "leads_only" => true
        );

        $data = $this->db->query("select rc.*,dp.nameSo as department,concat(u.first_name,' ',u.last_name) user from rise_fuel_requests rc 
        LEFT JOIN rise_users u on rc.requested_by = u.id 
        LEFT JOIN departments dp on rc.department_id = dp.id 
        where rc.id=$id")->getRow();
        return $this->_make_request_row($data, $custom_fields);
    }

    private function _make_row($data)
    {
        // $image_url = get_avatar($data->contact_avatar);
       
        // id	uuid	supplier	fuel_type	barrels	litters	receive_date	received_by	department_id	vehicle_model	plate	remarks	created_at	deleted	
        
        $owner = "-";
        if ($data->received_by) {
            // $owner_image_url = get_avatar($data->owner_avatar);
            // $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->user";
            // $owner = get_team_member_profile_link($data->created_by, $owner_user);
            $owner =$data->user;//$this->db->query("select * from rise_users where id = $data->created_by");
            
        }

        // $lead_labels = make_labels_view_data($data->labels_list, true);

        $row_data = array(
            $data->id,
            $data->fuel_type,
            $data->supplier,
            $data->receive_date,
            $data->barrels,
            $data->litters,
            $owner,
            // $data->vehicle_model,
            // $data->plate,
            // format_to_date($data->created_at, false),
        );

        // $row_data[] = js_anchor($data->document_title, array("style" => "background-color: green;",
        // "class" => "badge", "data-id" => $data->id, "data-value" => $data->id, "data-act" => "update-lead-status"));

        //open doc link:
        // $link = "<a href='$data->webUrl' class='btn btn-success' target='_blank' title='Open Document' style='background: #1cc976;color: white'><i data-feather='eye' class='icon-16'></i>";

        $row_data[] = modal_anchor(get_uri("fuel/receive_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => 'Edit Receive Information', "data-post-id" => $data->id))
        .modal_anchor(get_uri("fuel/receive_details"), "<i data-feather='info' class='icon-16'></i>", array("class" => "info", "title" => 'Show Receive Information', "data-post-id" => $data->id))
        . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => 'Delete Receive Information', "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("fuel/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    private function _make_order_row($data)
    {
        // $image_url = get_avatar($data->contact_avatar);
       
        // id	uuid	supplier	fuel_type	barrels	litters	receive_date	received_by	department_id	vehicle_model	plate	remarks	created_at	deleted	
        
        $owner = "-";
        if ($data->ordered_by) {
            // $owner_image_url = get_avatar($data->owner_avatar);
            // $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->user";
            // $owner = get_team_member_profile_link($data->created_by, $owner_user);
            $owner =$data->user;//$this->db->query("select * from rise_users where id = $data->created_by");
            
        }

        if ($data->status === "Pending") {
            $status_class = "bg-warning";
        } else if ($data->status === "approved") {
            $status_class = "badge bg-success";//btn-success
        } else if ($data->status === "cancelled") {
            $status_class = "bg-dark";//btn-success
           
        } else {
            $status_class = "bg-dark";
        }

        $status_meta = "<span class='badge $status_class'>" . app_lang($data->status) . "</span>";
   
        $fOrderDate = empty($data->order_date)? '' : date_format(new \DateTime($data->order_date),'F d, Y');
        $row_data = array(
            $data->id,
            anchor(get_uri("purchase_order/view/".$data->id), 'PO-'.str_pad($data->id,4,'0',STR_PAD_LEFT), array("title" => app_lang("purchase_details"), "data-post-id" => $data->id)),
            // 'PO-'.str_pad($data->id,4,'0',STR_PAD_LEFT),
            $data->product_type,
            $data->supplier,
            $fOrderDate,
            $owner,
            $status_meta,
        );

        // $row_data[] = js_anchor($data->document_title, array("style" => "background-color: green;",
        // "class" => "badge", "data-id" => $data->id, "data-value" => $data->id, "data-act" => "update-lead-status"));

        //open doc link:
        // $link = "<a href='$data->webUrl' class='btn btn-success' target='_blank' title='Open Document' style='background: #1cc976;color: white'><i data-feather='eye' class='icon-16'></i>";

        $row_data[] = modal_anchor(get_uri("purchase_order/order_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => 'Edit Order Information', "data-post-id" => $data->id))
        .modal_anchor(get_uri("purchase_order/order_details"), "<i data-feather='info' class='icon-16'></i>", array("class" => "info", "title" => 'Show Order Information', "data-post-id" => $data->id))
        . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => 'Delete Order Information', "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("purchase_order/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }



    /* prepare a row of request list table */
    private function _make_request_row($data, $custom_fields)
    {
        // $image_url = get_avatar($data->contact_avatar);
       
        // id	uuid	requested_by	department_id	litters	vehicle_engine	plate	request_type	request_date	purpose	status	remarks	created_at	deleted	
        
        $owner = "-";
        if ($data->requested_by) {
            // $owner_image_url = get_avatar($data->owner_avatar);
            // $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->user";
            // $owner = get_team_member_profile_link($data->created_by, $owner_user);
            $owner =$data->user;//$this->db->query("select * from rise_users where id = $data->created_by");
            
        }

        // $lead_labels = make_labels_view_data($data->labels_list, true);

            if ($data->status === "pending") {
                $status_class = "bg-warning";
            } else if ($data->status === "approved") {
                $status_class = "badge bg-success";//btn-success
            }else if ($data->status === "dispensed") {
                $status_class = "badge btn-success";//btn-success
            }  else if ($data->status === "cancelled") {
                $status_class = "bg-dark";//btn-success
               
            } else if ($data->status === "rejected") {
                $status_class = "bg-danger";
            } else {
                $status_class = "bg-dark";
            }

            $status_meta = "<span class='badge $status_class'>" . app_lang($data->status) . "</span>";
       

        $row_data = array(
            $data->id,
            $data->request_type,
            $data->fuel_type,
            $data->litters,
            $data->request_date,
            $data->purpose,
            $owner,
            $status_meta
        );

        // $row_data[] = js_anchor($data->document_title, array("style" => "background-color: green;",
        // "class" => "badge", "data-id" => $data->id, "data-value" => $data->id, "data-act" => "update-lead-status"));

        //open doc link:
        // $link = "<a href='$data->webUrl' class='btn btn-success' target='_blank' title='Open Document' style='background: #1cc976;color: white'><i data-feather='eye' class='icon-16'></i>";

        $row_data[] = modal_anchor(get_uri("fuel/request_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => 'Edit Fuel Request', "data-post-id" => $data->id))
        .modal_anchor(get_uri("fuel/request_details"), "<i data-feather='info' class='icon-16'></i>", array("class" => "info", "title" => 'Show Request Information', "data-post-id" => $data->id))
        . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => 'Delete Request', "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("fuel/r_delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

   
    /* load estimates tab  */

    public function estimates($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data["lead_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("estimates", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("estimates", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("fuel/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    public function estimate_requests($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data['client_id'] = $client_id;
            return $this->template->view("fuel/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    public function notes($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data['client_id'] = $client_id;
            return $this->template->view("fuel/notes/index", $view_data);
        }
    }

    /* load events tab  */

    public function events($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data['client_id'] = $client_id;
            $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown("lead");
            $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, app_lang("event") . " " . strtolower(app_lang("label"))));
            return $this->template->view("events/index", $view_data);
        }
    }

    /* load files tab */

    public function files($client_id)
    {
        validate_numeric_value($client_id);
        $this->validate_lead_access($client_id);

        $options = array("client_id" => $client_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->getResult();
        $view_data['client_id'] = $client_id;
        return $this->template->view("fuel/files/index", $view_data);
    }

    /* file upload modal */

    public function file_modal_form()
    {
        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $client_id = $this->request->getPost('client_id') ? $this->request->getPost('client_id') : $view_data['model_info']->client_id;

        $this->validate_lead_access($client_id);

        $view_data['client_id'] = $client_id;
        return $this->template->view('fuel/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    public function save_file()
    {

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "required|numeric",
        ));

        $client_id = $this->request->getPost('client_id');
        $this->validate_lead_access($client_id);

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
                        "uploaded_by" => $this->login_user->id,
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

    public function files_list_data($client_id = 0)
    {
        validate_numeric_value($client_id);
        $this->validate_lead_access($client_id);

        $options = array("client_id" => $client_id);
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
        js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("fuel/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("fuel/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("fuel/delete_file"), "data-action" => "delete-confirmation"));

        return array($data->id,
            "<div data-feather='$file_icon' class='mr10 float-start'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options,
        );
    }

    public function view_file($file_id = 0)
    {
        validate_numeric_value($file_id);
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->getRow();

        if ($file_info) {
            if (!$file_info->client_id) {
                app_redirect("forbidden");
            }

            $this->validate_lead_access($file_info->client_id);

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
            $view_data['file_id'] = $file_id;
            return $this->template->view("fuel/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    public function download_file($id)
    {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            app_redirect("forbidden");
        }

        $this->validate_lead_access($file_info->client_id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        return $this->download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    public function upload_file()
    {
        upload_file_to_temp();
    }

    /* check valid file for lead */

    public function validate_file()
    {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */

    public function delete_file()
    {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            app_redirect("forbidden");
        }

        $this->validate_lead_access($info->client_id);

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("client", $info->client_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    public function contact_profile($contact_id = 0, $tab = "")
    {
        validate_numeric_value($contact_id);
        $this->check_module_availability("module_lead");

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->validate_lead_access($view_data['user_info']->client_id);

        $view_data['lead_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = clean_data($tab);
        if ($view_data['user_info']->user_type === "lead") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            return $this->template->rander("fuel/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    /* load contacts tab  */

    public function contacts($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("fuel/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    public function add_new_contact_modal_form()
    {
        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->request->getPost('client_id');
        $this->validate_lead_access($view_data['model_info']->client_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('fuel/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    public function contact_general_info_tab($contact_id = 0)
    {
        if ($contact_id) {
            validate_numeric_value($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $this->validate_lead_access($view_data['model_info']->client_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            return $this->template->view('fuel/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    public function company_info_tab($client_id = 0)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);
            $view_data['statuses'] = $this->Lead_status_model->get_details()->getResult();
            $view_data['sources'] = $this->Lead_source_model->get_details()->getResult();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("leads", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";

            $view_data["owners_dropdown"] = $this->_get_owners_dropdown();
            $view_data['label_suggestions'] = $this->make_labels_dropdown("client", $view_data['model_info']->labels);

            return $this->template->view('fuel/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    public function contact_social_links_tab($contact_id = 0)
    {
        if ($contact_id) {
            validate_numeric_value($contact_id);
            $this->access_only_allowed_members();

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "lead";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            return $this->template->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    public function save_contact()
    {
        $contact_id = $this->request->getPost('contact_id');
        $client_id = $this->request->getPost('client_id');

        $this->validate_lead_access($client_id);

        $user_data = array(
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "phone" => $this->request->getPost('phone'),
            "skype" => $this->request->getPost('skype'),
            "job_title" => $this->request->getPost('job_title'),
            "gender" => $this->request->getPost('gender'),
            "note" => $this->request->getPost('note'),
            "user_type" => "lead",
        );

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric",
            "email" => "valid_email",
        ));

        $user_data["email"] = trim($this->request->getPost('email'));

        if ($user_data["email"]) {
            //validate duplicate email address
            if ($this->Users_model->is_email_exists($user_data["email"], $contact_id)) {
                echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
                exit();
            }
        }

        if (!$contact_id) {
            //inserting new contact. client_id is required
            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            $user_data["created_at"] = get_current_utc_time();
        }

        //by default, the first contact of a lead is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
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

            save_custom_fields("lead_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->ci_save($user_data, $primary_contact);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save social links of a contact
    public function save_contact_social_links($contact_id = 0)
    {
        validate_numeric_value($contact_id);

        $lead_info = $this->Users_model->get_one($contact_id);
        $this->validate_lead_access($lead_info->client_id);

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
            "id" => $id ? $id : $contact_id,
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->ci_save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
    }

    //save profile image of a contact
    public function save_profile_image($user_id = 0)
    {
        validate_numeric_value($user_id);
        $lead_info = $this->Users_model->get_one($user_id);
        $this->validate_lead_access($lead_info->client_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->request->getPost("profile_image"));

        if ($profile_image) {
            $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image));

            //delete old file
            delete_app_files(get_setting("profile_image_path"), array(@unserialize($lead_info->image)));

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
                if ($lead_info->image) {
                    delete_app_files(get_setting("profile_image_path"), array(@unserialize($lead_info->image)));
                }

                $image_data = array("image" => $profile_image);
                $this->Users_model->ci_save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed'), "reload_page" => true));
            }
        }
    }

    /* delete or undo a contact */

    public function delete_contact()
    {

        $this->validate_submitted_data(array(
            "id" => "required|numeric",
        ));

        $id = $this->request->getPost('id');

        $lead_info = $this->Users_model->get_one($id);
        $this->validate_lead_access($lead_info->client_id);

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

    public function contacts_list_data($client_id = 0)
    {
        validate_numeric_value($client_id);
        $this->validate_lead_access($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("user_type" => "lead", "client_id" => $client_id, "custom_fields" => $custom_fields, "custom_field_filter" => $this->prepare_custom_field_filter_values("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type));
        $list_data = $this->Users_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id)
    {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "lead",
            "custom_fields" => $custom_fields,
        );
        $data = $this->Users_model->get_details($options)->getRow();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields)
    {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1") {
            $primary_contact = "<span class='bg-info badge text-white'>" . app_lang('primary_contact') . "</span>";
        }

        $contact_link = anchor(get_uri("fuel/contact_profile/" . $data->id), $full_name . $primary_contact);
        if ($this->login_user->user_type === "lead") {
            $contact_link = $full_name; //don't show clickable link to lead
        }

        $row_data = array(
            $user_avatar,
            $contact_link,
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-",
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("fuel/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* upadate a lead status */

    public function save_lead_status($id = 0)
    {
        validate_numeric_value($id);
        $this->validate_lead_access($id);

        $data = array(
            "lead_status_id" => $this->request->getPost('value'),
        );

        $save_id = $this->Clients_model->ci_save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    public function all_leads_kanban()
    {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");
        $view_data['lead_sources'] = $this->Lead_source_model->get_details()->getResult();
        $view_data['labels_dropdown'] = json_encode($this->make_labels_dropdown("client", "", true));
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("leads", $this->login_user->is_admin, $this->login_user->user_type);

        return $this->template->rander("fuel/kanban/all_leads", $view_data);
    }

    public function all_leads_kanban_data()
    {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");
        $show_own_leads_only_user_id = $this->show_own_leads_only_user_id();

        $options = array(
            "status" => $this->request->getPost('status'),
            "owner_id" => $show_own_leads_only_user_id ? $show_own_leads_only_user_id : $this->request->getPost('owner_id'),
            "source" => $this->request->getPost('source'),
            "search" => $this->request->getPost('search'),
            "label_id" => $this->request->getPost('label_id'),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("leads", $this->login_user->is_admin, $this->login_user->user_type),
        );

        $view_data["leads"] = $this->Clients_model->get_leads_kanban_details($options)->getResult();

        $statuses = $this->Lead_status_model->get_details();
        $view_data["total_columns"] = $statuses->resultID->num_rows;
        $view_data["columns"] = $statuses->getResult();

        return $this->template->view('fuel/kanban/kanban_view', $view_data);
    }

    public function save_lead_sort_and_status()
    {
        $this->check_module_availability("module_lead");

        $this->validate_submitted_data(array(
            "id" => "required|numeric",
        ));

        $id = $this->request->getPost('id');
        $this->validate_lead_access($id);

        $lead_status_id = $this->request->getPost('lead_status_id');
        $data = array(
            "sort" => $this->request->getPost('sort'),
        );

        if ($lead_status_id) {
            $data["lead_status_id"] = $lead_status_id;
        }

        $this->Clients_model->ci_save($data, $id);
    }

    public function make_client_modal_form($lead_id = 0)
    {
        validate_numeric_value($lead_id);
        $this->validate_lead_access($lead_id);

        //prepare company details
        $view_data["lead_info"] = $this->make_lead_modal_form_data($lead_id);
        $view_data["lead_info"]["to_custom_field_type"] = "clients";

        //prepare contacts info
        $final_contacts = array();
        $contacts = $this->Users_model->get_all_where(array("user_type" => "lead", "deleted" => 0, "status" => "active", "client_id" => $lead_id))->getResult();

        //add custom fields for contacts
        foreach ($contacts as $contact) {
            $contact->custom_fields = $this->Custom_fields_model->get_combined_details("lead_contacts", $contact->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $final_contacts[] = $contact;
        }

        $view_data["contacts"] = $final_contacts;

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        return $this->template->view('fuel/migration/modal_form', $view_data);
    }

    public function save_as_client()
    {

        $client_id = $this->request->getPost('main_client_id');
        $this->validate_lead_access($client_id);

        if ($client_id) {
            //save client info
            $this->validate_submitted_data(array(
                "main_client_id" => "numeric",
                "company_name" => "required",
            ));

            $company_name = $this->request->getPost('company_name');

            $client_info = $this->Clients_model->get_details(array("id" => $client_id))->getRow();

            $data = array(
                "company_name" => $company_name,
                "address" => $this->request->getPost('address'),
                "city" => $this->request->getPost('city'),
                "state" => $this->request->getPost('state'),
                "zip" => $this->request->getPost('zip'),
                "country" => $this->request->getPost('country'),
                "phone" => $this->request->getPost('phone'),
                "website" => $this->request->getPost('website'),
                "vat_number" => $this->request->getPost('vat_number'),
                "gst_number" => $this->request->getPost('gst_number'),
                "group_ids" => $this->request->getPost('group_ids') ? $this->request->getPost('group_ids') : "",
                "is_lead" => 0,
                "client_migration_date" => get_current_utc_time(),
                "last_lead_status" => $client_info->lead_status_title,
                "created_by" => $this->request->getPost('created_by') ? $this->request->getPost('created_by') : $client_info->owner_id,
            );

            if ($this->login_user->is_admin) {
                $data["currency_symbol"] = $this->request->getPost('currency_symbol') ? $this->request->getPost('currency_symbol') : "";
                $data["currency"] = $this->request->getPost('currency') ? $this->request->getPost('currency') : "";
                $data["disable_online_payment"] = $this->request->getPost('disable_online_payment') ? $this->request->getPost('disable_online_payment') : 0;
            }

            $data = clean_data($data);

            //check duplicate company name, if found then show an error message
            if (get_setting("disallow_duplicate_client_company_name") == "1" && $this->Clients_model->is_duplicate_company_name($company_name, $client_id)) {
                echo json_encode(array("success" => false, 'message' => app_lang("account_already_exists_for_your_company_name")));
                exit();
            }

            $save_client_id = $this->Clients_model->ci_save($data, $client_id);

            //save contacts
            if ($save_client_id) {
                log_notification("client_created_from_lead", array("client_id" => $save_client_id), $this->login_user->id);

                //save custom field for client
                if ($this->request->getPost("merge_custom_fields-$client_id")) {
                    save_custom_fields("leads", $save_client_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "clients");
                }

                $contacts = $this->Users_model->get_all_where(array("user_type" => "lead", "deleted" => 0, "status" => "active", "client_id" => $client_id))->getResult();
                $found_primary_contact = false;

                foreach ($contacts as $contact) {
                    $this->validate_submitted_data(array(
                        'first_name-' . $contact->id => "required",
                        'last_name-' . $contact->id => "required",
                        'email-' . $contact->id => "required|valid_email",
                    ));

                    $user_data = array(
                        "first_name" => $this->request->getPost('first_name-' . $contact->id),
                        "last_name" => $this->request->getPost('last_name-' . $contact->id),
                        "phone" => $this->request->getPost('contact_phone-' . $contact->id),
                        "skype" => $this->request->getPost('skype-' . $contact->id),
                        "job_title" => $this->request->getPost('job_title-' . $contact->id),
                        "gender" => $this->request->getPost('gender-' . $contact->id),
                        "email" => trim($this->request->getPost('email-' . $contact->id)),
                        "password" => md5($this->request->getPost('login_password-' . $contact->id)),
                        "user_type" => "client",
                    );

                    if ($this->request->getPost('is_primary_contact_value-' . $contact->id) && !$found_primary_contact) {
                        $user_data["is_primary_contact"] = 1;
                        $found_primary_contact = true; //flag that, a primary contact found
                    } else {
                        $user_data["is_primary_contact"] = 0;
                    }

                    if ($this->Users_model->is_email_exists($user_data["email"], $contact->id)) {
                        echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
                        exit();
                    }

                    $user_data = clean_data($user_data);

                    $save_contact_id = $this->Users_model->ci_save($user_data, $contact->id);

                    if ($save_contact_id) {
                        //save custom fields for client contacts
                        if ($this->request->getPost("merge_custom_fields-$contact->id")) {
                            save_custom_fields("lead_contacts", $save_contact_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "client_contacts", $contact->id);
                        }

                        if ($this->request->getPost('email_login_details-' . $contact->id)) {
                            $email_template = $this->Email_templates_model->get_final_template("login_info", true);

                            $user_language = $contact->language;
                            $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");
                            $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                            $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                            $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                            $parser_data["USER_LOGIN_PASSWORD"] = $this->request->getPost('login_password-' . $contact->id);
                            $parser_data["DASHBOARD_URL"] = base_url();
                            $parser_data["LOGO_URL"] = get_logo_url();

                            $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
                            $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");

                            $message = $this->parser->setData($parser_data)->renderString($message);
                            $subject = $this->parser->setData($parser_data)->renderString($subject);

                            send_app_mail($this->request->getPost('email-' . $contact->id), $subject, $message);
                        }
                    }
                }

                echo json_encode(array("success" => true, 'redirect_to' => get_uri("clients/view/$save_client_id"), "message" => app_lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        }
    }

    /* load proposals tab  */

    public function proposals($client_id)
    {
        validate_numeric_value($client_id);

        if ($client_id) {
            $this->validate_lead_access($client_id);
            $view_data["lead_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("proposals", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("proposals", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("fuel/proposals/index", $view_data);
        }
    }

    /* load contracts tab  */

    public function contracts($client_id)
    {
        if ($client_id) {
            $this->validate_lead_access($client_id);
            $view_data["lead_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("contracts", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("contracts", $this->login_user->is_admin, $this->login_user->user_type);
            return $this->template->view("fuel/contracts/index", $view_data);
        }
    }

    /* load tasks tab  */

    public function tasks($client_id)
    {
        $this->validate_lead_access($client_id);

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = clean_data($client_id);
        return $this->template->view("fuel/tasks/index", $view_data);
    }

    private function _validate_excel_import_access()
    {
        return $this->access_only_allowed_members();
    }

    private function _get_controller_slag()
    {
        return "leads";
    }

    private function _get_custom_field_context()
    {
        return "leads";
    }

    private function _get_headers_for_import()
    {
        return array(
            array("name" => "company_name", "required" => true, "required_message" => app_lang("import_client_error_company_name_field_required")),
            array("name" => "status"),
            array("name" => "owner"),
            array("name" => "source"),
            array("name" => "contact_first_name"),
            array("name" => "contact_last_name", "custom_validation" => function ($contact_last_name, $row_data) {
                //if there is contact first name then the contact last name is required
                if (get_array_value($row_data, "4") && !$contact_last_name) {
                    return array("error" => app_lang("import_lead_error_contact_name"));
                }
            }),
            array("name" => "contact_email"),
            array("name" => "address"),
            array("name" => "city"),
            array("name" => "state"),
            array("name" => "zip"),
            array("name" => "country"),
            array("name" => "phone"),
            array("name" => "website"),
            array("name" => "vat_number"),
            array("name" => "currency"),
            array("name" => "currency_symbol"),
        );
    }

    public function download_sample_excel_file()
    {
        $this->access_only_allowed_members();
        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-leads-sample.xlsx"))));
    }

    private function _init_required_data_before_starting_import()
    {

        $lead_statuses = $this->Lead_status_model->get_details()->getResult();
        $lead_statuses_id_by_title = array();
        foreach ($lead_statuses as $status) {
            $lead_statuses_id_by_title[$status->title] = $status->id;
        }

        $lead_sources = $this->Lead_source_model->get_details()->getResult();
        $lead_sources_id_by_title = array();
        foreach ($lead_sources as $source) {
            $lead_sources_id_by_title[$source->title] = $source->id;
        }

        $lead_owners = $this->Users_model->get_team_members_id_and_name()->getResult();
        $lead_owners_id_by_name = array();
        foreach ($lead_owners as $owner) {
            $lead_owners_id_by_name[$owner->user_name] = $owner->id;
        }

        $this->lead_statuses_id_by_title = $lead_statuses_id_by_title;
        $this->lead_sources_id_by_title = $lead_sources_id_by_title;
        $this->lead_owners_id_by_name = $lead_owners_id_by_name;
    }

    private function _save_a_row_of_excel_data($row_data)
    {
        $now = get_current_utc_time();

        $lead_data_array = $this->_prepare_lead_data($row_data);
        $lead_data = get_array_value($lead_data_array, "lead_data");
        $lead_contact_data = get_array_value($lead_data_array, "lead_contact_data");
        $custom_field_values_array = get_array_value($lead_data_array, "custom_field_values_array");

        //couldn't prepare valid data
        if (!($lead_data && count($lead_data) > 1)) {
            return false;
        }

        if (!isset($lead_data["owner_id"])) {
            $lead_data["owner_id"] = $this->login_user->id;
        }

        //found information about lead, add some additional info
        $lead_data["created_date"] = $now;
        $lead_contact_data["created_at"] = $now;

        //save lead data
        $saved_id = $this->Clients_model->ci_save($lead_data);
        if (!$saved_id) {
            return false;
        }

        //save custom fields
        $this->_save_custom_fields($saved_id, $custom_field_values_array);

        //add lead id to contact data
        $lead_contact_data["client_id"] = $saved_id;
        $this->Users_model->ci_save($lead_contact_data);
        return true;
    }

    private function _prepare_lead_data($row_data)
    {

        $lead_data = array("is_lead" => 1);
        $lead_contact_data = array("user_type" => "lead", "is_primary_contact" => 1);
        $custom_field_values_array = array();

        foreach ($row_data as $column_index => $value) {
            if (!$value) {
                continue;
            }

            $column_name = $this->_get_column_name($column_index);
            if ($column_name == "contact_first_name") {
                $lead_contact_data["first_name"] = $value;
            } else if ($column_name == "contact_last_name") {
                $lead_contact_data["last_name"] = $value;
            } else if ($column_name == "contact_email") {
                $lead_contact_data["email"] = $value;
            } else if ($column_name == "status") {
                //get existing status, if not create new one and add the id

                $status_id = get_array_value($this->lead_statuses_id_by_title, $value);
                if ($status_id) {
                    $lead_data["lead_status_id"] = $status_id;
                } else {
                    $max_sort_value = $this->Lead_status_model->get_max_sort_value();
                    $status_data = array("title" => $value, "color" => "#f1c40f", "sort" => ($max_sort_value * 1 + 1));
                    $saved_status_id = $this->Lead_status_model->ci_save($status_data);
                    $lead_data["lead_status_id"] = $saved_status_id;
                    $this->lead_statuses_id_by_title[$value] = $saved_status_id;
                }
            } else if ($column_name == "owner") {
                $owner_id = get_array_value($this->lead_owners_id_by_name, $value);
                if ($owner_id) {
                    $lead_data["owner_id"] = $owner_id;
                } else {
                    $lead_data["owner_id"] = $this->login_user->id;
                }
            } else if ($column_name == "source") {
                //get existing source, if not create new one and add the id

                $source_id = get_array_value($this->lead_sources_id_by_title, $value);
                if ($source_id) {
                    $lead_data["lead_source_id"] = $source_id;
                } else {
                    $max_sort_value = $this->Lead_source_model->get_max_sort_value();
                    $source_data = array("title" => $value, "sort" => ($max_sort_value * 1 + 1));
                    $saved_source_id = $this->Lead_source_model->ci_save($source_data);
                    $lead_data["lead_status_id"] = $saved_source_id;
                    $this->lead_sources_id_by_title[$value] = $saved_source_id;
                }
            } else if (strpos($column_name, 'cf') !== false) {
                $this->_prepare_custom_field_values_array($column_name, $value, $custom_field_values_array);
            } else {
                $lead_data[$column_name] = $value;
            }
        }

        return array(
            "lead_data" => $lead_data,
            "lead_contact_data" => $lead_contact_data,
            "custom_field_values_array" => $custom_field_values_array,
        );
    }

    public function converted_to_client_report()
    {
        $this->_validate_leads_report_access();

        $view_data['sources_dropdown'] = json_encode($this->_get_sources_dropdown());
        $view_data['owners_dropdown'] = json_encode($this->_get_owners_dropdown("filter"));

        return $this->template->rander("fuel/reports/converted_to_client", $view_data);
    }

    public function converted_to_client_charts_data()
    {

        $this->_validate_leads_report_access();

        $start_date = $this->request->getPost("start_date");
        $options = array(
            "start_date" => $start_date,
            "end_date" => $this->request->getPost("end_date"),
            "owner_id" => $this->request->getPost("owner_id"),
            "source_id" => $this->request->getPost("source_id"),
            "date_range_type" => $this->request->getPost("date_range_type"),
        );

        $days_of_month = date("t", strtotime($start_date));

        $day_wise_data = $this->_converted_to_client_chart_day_wise_data($options, $days_of_month);

        $view_data["day_wise_labels"] = json_encode($day_wise_data['labels']);
        $view_data["day_wise_data"] = json_encode($day_wise_data['data']);

        $view_data["month"] = strtolower(date("F", strtotime($start_date)));

        $owner_wise_data = $this->_converted_to_client_chart_owner_wise_data($options);

        $view_data["owner_wise_labels"] = json_encode($owner_wise_data['labels']);
        $view_data["owner_wise_data"] = json_encode($owner_wise_data['data']);

        $source_wise_data = $this->_converted_to_client_chart_source_wise_data($options);

        $view_data["source_wise_labels"] = json_encode($source_wise_data['labels']);
        $view_data["source_wise_data"] = json_encode($source_wise_data['data']);

        return $this->template->view("fuel/reports/converted_to_client_monthly_chart", $view_data);
    }

    private function _converted_to_client_chart_day_wise_data($options, $days_of_month)
    {
        $data_array = array();
        $labels = array();
        $converted_to_client = array();

        $options["group_by"] = "created_date";
        $converted_result = $this->Clients_model->get_converted_to_client_statistics($options)->getResult();

        for ($i = 1; $i <= $days_of_month; $i++) {
            $converted_to_client[$i] = 0;
        }

        foreach ($converted_result as $value) {
            $converted_to_client[$value->day * 1] = $value->total_converted ? $value->total_converted : 0;
        }

        foreach ($converted_to_client as $value) {
            $data_array[] = $value;
        }

        for ($i = 1; $i <= $days_of_month; $i++) {
            $labels[] = $i;
        }

        return array("labels" => $labels, "data" => $data_array);
    }

    private function _converted_to_client_chart_owner_wise_data($options)
    {

        $options["group_by"] = "owner_id";
        $converted_result = $this->Clients_model->get_converted_to_client_statistics($options)->getResult();

        $labels_array = array();
        $data_array = array();

        foreach ($converted_result as $value) {
            $labels_array[] = $value->owner_name;
            $data_array[] = $value->total_converted ? $value->total_converted : 0;
        }

        return array("labels" => $labels_array, "data" => $data_array);
    }

    private function _converted_to_client_chart_source_wise_data($options)
    {

        $options["group_by"] = "source_id";
        $converted_result = $this->Clients_model->get_converted_to_client_statistics($options)->getResult();

        $labels_array = array();
        $data_array = array();

        foreach ($converted_result as $value) {
            $labels_array[] = $value->title;
            $data_array[] = $value->total_converted ? $value->total_converted : 0;
        }
        return array("labels" => $labels_array, "data" => $data_array);
    }

    public function team_members_summary()
    {
        $this->_validate_leads_report_access();

        $view_data["lead_statuses"] = $this->Lead_status_model->get_details()->getResult();
        $view_data['sources_dropdown'] = json_encode($this->_get_sources_dropdown());
        $view_data['labels_dropdown'] = json_encode($this->make_labels_dropdown("client", "", true));

        return $this->template->view("fuel/reports/team_members_summary", $view_data);
    }

    public function team_members_summary_data()
    {
        $this->_validate_leads_report_access();

        $options = array(
            "created_date_from" => $this->request->getPost("created_date_from"),
            "created_date_to" => $this->request->getPost("created_date_to"),
            "source_id" => $this->request->getPost("source_id"),
            "label_id" => $this->request->getPost("label_id"),
        );

        $list_data = $this->Clients_model->get_leads_team_members_summary($options)->getResult();

        $lead_statuses = $this->Lead_status_model->get_details()->getResult();
        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_team_members_summary_row($data, $lead_statuses);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }

    private function _make_team_members_summary_row($data, $lead_statuses)
    {

        $image_url = get_avatar($data->image);
        $member = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span> $data->team_member_name";

        $row_data = array(
            get_team_member_profile_link($data->team_member_id, $member),
        );

        $status_total_meta = $data->status_total_meta ? $data->status_total_meta : "";
        $statuses_meta = explode(",", $status_total_meta);
        $status_total_array = array();
        foreach ($statuses_meta as $meta) {
            $status_total = explode("_", $meta);
            $status_total_array[get_array_value($status_total, 0)] = get_array_value($status_total, 1);
        }

        foreach ($lead_statuses as $status) {
            $total = get_array_value($status_total_array, $status->id);
            $row_data[] = $total ? $total : 0;
        }
        $row_data[] = $data->converted_to_client ? $data->converted_to_client : 0;

        return $row_data;
    }
}

/* End of file leads.php */
/* Location: ./app/controllers/leads.php */
