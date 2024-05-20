<?php

namespace App\Controllers;

use App\Libraries\Excel_import;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Common\Version;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Output\QRImageWithLogo;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\Border;
use PhpOffice\PhpWord\SimpleType\TblWidth;

class Visitors extends Security_Controller
{

    use Excel_import;

    private $lead_statuses_id_by_title = array();
    private $lead_sources_id_by_title = array();
    private $lead_owners_id_by_name = array();

    public function __construct()
    {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("lead");
    }

    private function validate_lead_access($lead_id)
    {
        if (!$this->can_access_this_lead($lead_id)) {
            app_redirect("forbidden");
        }
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
        // $this->access_only_allowed_members();
        $this->check_module_availability("module_visitor");
        $role = $this->get_user_role();
        $view_data['can_add_requests'] = $role == 'Access Controll' || $role == 'Secretary' || $role == 'Director'  || $role == 'HRM' || $role == 'admin' || $role == 'Administrator'; 

        // die($role != 'admin' );

        
        if($role != 'Access Controll' && $role != 'admin' && $role != 'Administrator' && $role != 'Director' && $role != 'Secretary' && $role != 'HRM'){ //not allowed to others including 'admistrator' role
            app_redirect("forbidden");
        }
        
        // $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        // $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("leads", $this->login_user->is_admin, $this->login_user->user_type);

        // $view_data['lead_statuses'] = $this->Lead_status_model->get_details()->getResult();
        // $view_data['lead_sources'] = $this->Lead_source_model->get_details()->getResult();
        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");
        $view_data['labels_dropdown'] = json_encode($this->make_labels_dropdown("client", "", true));

        return $this->template->rander("visitors/index", $view_data);
    }

    /* load lead add/edit modal */
    public function modal_form()
    {
        $lead_id = $this->request->getPost('id');
        $dept_id = $this->get_user_department_id();
        $role = $this->get_user_role();

        if($role === 'Access Controll' || $role === 'admin' || $role === 'Administrator'){
            $dept_id = '%';
        }

        $view_data = $this->make_lead_modal_form_data($lead_id);
        $depts = $this->db->query("select * from departments where id like '$dept_id'")->getResult();

        if($dept_id == '%'){

            $departments =array(
                ''=>'Choose Office/Xafiiska'
            );
        }

        foreach($depts as $d){
            $departments[$d->id] = $d->nameSo;
        }

        $view_data['departments'] = $departments;

        return $this->template->view('visitors/modal_form', $view_data);
    }

    private function make_lead_modal_form_data($lead_id = 0)
    {
        $this->access_only_allowed_members();

        $this->validate_submitted_data(array(
            "id" => "numeric",
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loding from the lead's details view
        $view_data['model_info'] = $this->Visitors_model->get_one($lead_id); //$this->Subscriptions_model->get_one($lead_id);//
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
        $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

        $view_data['statuses'] = $this->Lead_status_model->get_details()->getResult();
        $view_data['sources'] = $this->Lead_source_model->get_details()->getResult();

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        //prepare label suggestions
        // $view_data['label_suggestions'] = $this->make_labels_dropdown("client", $view_data['model_info']->labels);

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("leads", $lead_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        // var_dump( $temp_array);
        // die();

        return $view_data;
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

    public function AccesToken()
    {
        $appid = getenv('AZURE_APP_ID'); //"a70c275e-7713-46eb-8a09-6d5a7c3b823d";
        $tennantid = getenv('AZURE_TENANT_ID'); //"695822cd-3aaa-446d-aac2-3ebb02854b8a";
        $secret = getenv('AZURE_SECRET_ID'); //"e54c00ad-6cfd-4113-b46f-5a3de239d13b";
        $env = getenv('ENVIRONMENT'); //ENVIRONMENT

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://login.microsoftonline.com/'.$tennantid.'/oauth2/v2.0/token?Content-Type=application%2Fx-www-form-urlencoded',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'client_id='.$appid.'&scope=https%3A%2F%2Fgraph.microsoft.com%2F.default&client_secret='.$secret.'&grant_type=client_credentials',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: fpc=AvtPK5Dz759HgjJgzmeSAChRGrKTAQAAAIgG3NwOAAAA; stsservicecookie=estsfd; x-ms-gateway-slice=estsfd',
            ),
        ));

        $response = curl_exec($curl);

        // Decode the JSON response into an associative array
        $data = json_decode($response, true);
        // var_dump($data);
        // die();
        // Get the web URL of the file from the array
        $accessToken = $data["access_token"];

        curl_close($curl);
        return $accessToken;

    }
    /* insert or update a lead */

    public function save()
    {
        $id = $this->request->getPost('id');
        // $this->validate_lead_access($id);

        $primary =  [
            "id" => "numeric",
            "name" => "required",
            'visitor_name'=>'required',
            'department_id'=>'required',
            'allowed_gates'=>'required',
            'document_title'=>'required',
        ];

        $rules = array_merge($primary); 

        $this->validate_with_messages($rules,[
            'visitor_name'=>[
                'required' => 'Please add at least one visitor details.'
            ],
            // 'visitor_mobile'=>[
            //     'required' => 'Visitor mobile is required.'
            // ],
            'document_title'=>[
                'required' => 'Document title is required.'
            ]
        ]);
    
        $duration = $this->request->getPost('access_duration');
        $hours_per_day = 8;
        $hours = 0;
        $days = 0;

        // $target_path = get_setting("timeline_file_path");
        // $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "leave");
        // $new_files = unserialize($files_data);

        if ($duration === "multiple_days") {

            $this->validate_submitted_data(array(
                "start_date" => "required",
                "end_date" => "required"
            ));

            $start_date = $this->request->getPost('start_date');
            $end_date = $this->request->getPost('end_date');

            //calculate total days
            $d_start = new \DateTime($start_date);
            $d_end = new \DateTime($end_date);
            $d_diff = $d_start->diff($d_end);

            $days = $d_diff->days + 1;
            $hours = $days * $hours_per_day;

        } else if ($duration === "hours") {

            $this->validate_submitted_data(array(
                "hour_date" => "required"
            ));

            $start_date = $this->request->getPost('hour_date');
            $end_date = $start_date;
            $hours = $this->request->getPost('hours');
            $days = $hours / $hours_per_day;
        } else {

            $this->validate_submitted_data(array(
                "single_date" => "required"
            ));

            $start_date = $this->request->getPost('single_date');
            $end_date = $start_date;
            $hours = $hours_per_day;
            $days = 1;
        }

        if($days > 2){
            $access_type = 'Long Period';
        }else{
            $access_type = 'Short Period';
        }

        //get dept for login user
        $user_id = $this->login_user->id;
        // `document_title`,`created_by`, `ref_number`, `depertment`, `template`, `item_id`, `created_at`
        $parent_input = array(
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            "name" => $this->request->getPost('name'),
            "client_type" => $this->request->getPost('client_type'),
            "visit_time" => $this->request->getPost('visit_time'),
            "document_title" => $this->request->getPost('document_title'),
            "allowed_gates" => $this->request->getPost('allowed_gates'),
            "start_date" => $start_date,
            "visit_date" => $start_date,
            "end_date" => $end_date,
            "total_days" => $days,
            "total_hours" => $hours,
            "access_type" => $access_type,
            "access_duration" => $this->request->getPost('access_duration'),
            "department_id" => $this->request->getPost('department_id'),
            "remarks" => $this->request->getPost('remarks'),
            "created_by" => $this->request->getPost('owner_id') ? $this->request->getPost('owner_id') : $user_id,
            "created_at" => date('Y-m-d H:i:s'),
        );

        $parent_input = clean_data($parent_input);
        $save_id = null;
        $webUrl = null;

        if (!$id) { 
        
            //save visitor details:
            $visitor_name = $this->request->getPost('visitor_name');
            $visitor_mobile = $this->request->getPost('visitor_mobile');
            $vehicle = $this->request->getPost('vehicle_details');
                     
            //validate files before saving any thing:
            if ($_FILES) {

                foreach($_FILES as $f=>$v){
                    $j = 1;
                    
                    
                    $visitor_image_file = get_array_value($_FILES, "visitor_image_file_".$j);
                    $image_file_name = get_array_value($visitor_image_file, "tmp_name");
                    $file_name = get_array_value($visitor_image_file, "name");
                    $image_file_size = get_array_value($visitor_image_file, "size");
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                                    
                    $size_kb = $image_file_size/1024;

                    if ($image_file_name) {
                        
                        if(!starts_with($visitor_image_file['type'], 'image/')) {
                            echo json_encode(array("success" => false, 'message' => 'Invalid file, upload image file, at row '.$j));
                            exit();
                        }elseif(!in_array($ext, array('png', 'jpg', 'jpeg'))) {
                            echo json_encode(array("success" => false, 'message' => 'Invalid image extension, shoud be png or jpg, at row '.$j));
                            exit();
                        }elseif ($size_kb > 2048) {
                            echo json_encode(array("success" => false, 'message' => app_lang('visitor_image_error_message').', at row '.$j));
                            exit();
                        
                        }
        
                        
                    }
                    $j = $j + 1;
                }
            }
            //end validate

            $save_id = $this->Visitors_model->ci_save($parent_input);

            foreach($visitor_name as $k => $v){
                $i = $k + 1;


                $this->db->query("INSERT INTO rise_visitors_detail(visitor_name,mobile,vehicle_details,visitor_id)
                            VALUES('$visitor_name[$k]','$visitor_mobile[$k]','$vehicle[$k]',$save_id)");
                $insert_id = $this->db->insertID();

                if (get_array_value($_FILES,"visitor_image_file_".$i)) {
                            
                    $visitor_image_file = get_array_value($_FILES,"visitor_image_file_".$i);
                    $image_file_name = get_array_value($visitor_image_file, "tmp_name");
                    $image_file_size = get_array_value($visitor_image_file, "size");

        
                    $visitor_image = serialize(move_temp_file("visitor.png", get_setting("visitor_image_path"), "", $image_file_name, "", "", false, $image_file_size));
    
                    //delete old file
                    // if ($lead_info->image) {
                    //     delete_app_files(get_setting("profile_image_path"), array(@unserialize($lead_info->image)));
                    // }
    
                    $this->db->query("UPDATE rise_visitors_detail set image = '$visitor_image' where id = $insert_id");
                    // echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed'), "reload_page" => true));
                    }
                }
            

            $visitor_info = $this->db->query("SELECT v.*,concat(u.first_name,' ',u.last_name) user from rise_visitors v  
                    LEFT JOIN rise_users u on v.created_by = u.id  where v.id = $save_id")->getRow();

            $detail_info = $this->db->query("SELECT vd.* from rise_visitors v left join rise_visitors_detail vd on v.id=vd.visitor_id where v.id = $save_id")->getResult();

            $arr_table = [];
            $image_block = [];
            $index = 0;

            foreach($detail_info as $d){
                $index = $index + 1;
                $arr_table[] = array(
                                'id'=>$index,
                                'visitorName'=>$d->visitor_name,
                                // 'visitorMobile'=>$d->mobile,
                                'vehicle'=>$d->vehicle_details,
                                // 'image'=>$d->image
                            );
                            
                if($d->image){

                    $image_block[] = array(
                        'id'=>$index,
                        'name'=>$d->visitor_name,
                        'image'=>$d->image
                    );
                }
            }

            // var_dump($visitor_info);
            // var_dump($detail_info);
            // var_dump($arr_table);
            // die();

            $template = $this->db->query("SELECT * FROM rise_templates where destination_folder = 'Visitor'")->getRow();
            $this->db->query("update rise_templates set sqn = sqn + 1 where id = $template->id");
            $sqn = $this->db->query("SELECT lpad(max(sqn),4,0) as sqn FROM rise_templates where id = $template->id")->getRow()->sqn;
            
            $doc_visitor_data = [
                'id'=>$save_id,
                'uuid'=>$visitor_info->uuid,
                'ref_number'=> $template->ref_prefix.'/'.$sqn.'/'.date('m').'/'.date('y'),
                'template' => $template->path,
                'folder' => $template->destination_folder,
                'date' => date('Y-m-d'),
                'visit_date' => date('h:i a, F d, Y',strtotime($start_date.' '.$this->request->getPost('visit_time'))),
                'table' => $arr_table,
                'images_table' => $image_block,
                "document_title" => $visitor_info->document_title,
                "allowed_gates" => $visitor_info->allowed_gates,
                "department" => $this->get_department_name($visitor_info->department_id),
                "remarks" => $this->request->getPost('remarks'),
                "created_at" => $start_date
            ];
    
            $doc_data = [
                'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
                'document_title' =>'Visitors Request - '.$this->request->getPost('name'),
                'ref_number' =>$template->ref_prefix.'/'.$sqn.'/'.date('m').'/'.date('y'),
                "depertment" => $this->get_user_department_id(),
                "template" => $template->id,
                "created_by" => $this->login_user->id,
                "created_at" => date('Y-m-d H:i:s')
            ];
    
            $doc_id = $this->Documents_model->ci_save($doc_data);
            $doc = $this->db->query("SELECT * FROM rise_documents where id = $doc_id")->getRow();
            $this->db->query("insert into rise_visitor_document(visitor_id,document_id) values($save_id,$doc_id)");
            $doc_visitor_data['document_id'] = $doc->id;
    
            $path = $this->createDoc($doc_visitor_data);
            $token = $this->AccesToken();  
            $data = $this->uploadDoc($token,$doc_visitor_data,$path);   
            
            // var_dump($data['parentReference']);
            // die();

            //send whatsup message to access team:
            // $r = $this->send_whatsup_message($number,'Test Message');
            if (isset($data['error'])) {

                // var_dump($data['error']['code'] . ', ' . $data['error']['message']);
                if($data['error']['code'] == "notAllowed"){
                    $msg = $data['error']['code'] . ', ' . $data['error']['message'];//"The file is being edited by another user";
                }else{
                    $msg=$data['error']['code'] . ', ' . $data['error']['message'];
                }
                
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred').', '.$msg));
                exit;

            } else {

                // Get the web URL of the file from the array
                $webUrl = $data["webUrl"];
                $itemId = $data["id"];
                $drive_ref = $data['parentReference'];

                //update item id and web url
                $u_data= array('item_id' => $itemId,'webUrl' => $webUrl,'ref_number'=>$doc_data['ref_number'],'drive_info'=>@serialize($drive_ref));
                
                $this->Documents_model->ci_save($u_data, $doc->id);

                // echo $webUrl;
                // die();

            }

        } else {
            $input = array(
                "name" => $this->request->getPost('name'),
                "client_type" => $this->request->getPost('client_type'),
                "visit_date" => $start_date,
                "visit_time" => $this->request->getPost('visit_time'),
                "remarks" => $this->request->getPost('remarks'),
            );

            $updated = $this->Visitors_model->ci_save($input, $id);

            //delete old data
            $this->db->query("DELETE FROM rise_visitors_detail where visitor_id = $id");

            //insert new data
             $visitor_name = $this->request->getPost('visitor_name');
             $visitor_mobile = $this->request->getPost('visitor_mobile');
             $vehicle = $this->request->getPost('vehicle_details');

 
             foreach($visitor_name as $k => $v){
                
                 $this->db->query("INSERT INTO rise_visitors_detail(visitor_name,mobile,vehicle_details,visitor_id)
                             VALUES('$visitor_name[$k]','$visitor_mobile[$k]','$vehicle[$k]',$id)");
             } 

            //get row           
            $visitor_info = $this->db->query("SELECT v.*,concat(u.first_name,' ',u.last_name) user from rise_visitors v  
                    LEFT JOIN rise_users u on v.created_by = u.id  where v.id = $id")->getRow();
        }

        if ($save_id || $updated) {
            // save_custom_fields("leads", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            if (!$id) { //create operation
                
                log_notification("visitor_created", array("visitor_id" => $save_id), $this->login_user->id);

                echo json_encode(array("success" => true, "data" => $this->_make_row($visitor_info, null), 'webUrl'=>$webUrl, 'id' => $save_id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_saved')));
            } else { //update operation
                
                log_notification("visitor_updated", array("visitor_id" => $id), $this->login_user->id);

                // var_dump($doc->getRowArray());
                // die();

                echo json_encode(array("success" => true, "data" => $this->_make_row($visitor_info, null), 'id' => $id, 'view' => $this->request->getPost('view'),
                    'message' => app_lang('record_updated')));
            }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred').', Visitor not saved.'));
        }
    }

    public function visitor_details_json($id) {

        $detail_info = $this->db->query("SELECT vd.* from rise_visitors v left join rise_visitors_detail vd on v.id=vd.visitor_id where v.id = $id")->getResult();

        return json_encode($detail_info);
    }

    public function access_request_pdf($id=0) {

        require_once ROOTPATH . 'vendor/autoload.php';

        if($id){
        
            if (@ob_get_length())
                @ob_clean();

            $visitor_info = $this->db->query("SELECT v.*,cb.image as created_avatar FROM rise_visitors v 

                            LEFT JOIN rise_visitors_detail vd on v.id = vd.visitor_id
                            LEFT JOIN rise_users cb on v.created_by = cb.id
                            WHERE v.uuid = '$id'

                        ")->getRow();
            if($visitor_info){
                
                $visitor_details = $this->db->query("SELECT vd.* FROM rise_visitors v 
                LEFT JOIN rise_visitors_detail vd on v.id = vd.visitor_id
                WHERE visitor_id = $visitor_info->id
                ")->getResult();

            }else{
                $visitor_details = [];
            }

            $qr = '<img width="150" src="'.(new QRCode)->render(get_uri('visitors_info/show_visitor_qrcode/'.$visitor_info->uuid)).'" alt="Show Access Info" />';
            $data['visitor_details'] = $visitor_details;
            $data['visitor_info'] = $visitor_info;
            $data['qrcode'] = $qr;

            //download pdf:
            $this->get_access_pdf('visitors/access_request_pdf', $data);
            // prepare_pdf('visitors/access_request_pdf',$data,'view');           
            // return $this->template->view('visitors/access_request_pdf',$data);

        } else {
            show_404();
        }
    }

    public function get_access_pdf($path,$data,$mode='view'){

        $data_info = get_array_value($data, "visitor_info");
        $pdf_file_name = "access_info_".$data_info->id.".pdf";
       
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

    // Creates the Document Using the Provided Template
    public function createDoc($data =array())
    {

        require_once ROOTPATH . 'vendor/autoload.php';

        // Creating the new document...
        // $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor(APPPATH . 'Views/documents/'.$data['template']);
       
        $ext = pathinfo(APPPATH.'Views/documents/'.$data['template'],PATHINFO_EXTENSION);
        $save_as_name = $data['id'].'.'.date('m').'.'.date('Y').'.'.$ext;
        

        $path_absolute = APPPATH . 'Views/documents/'.$save_as_name;
        // var_dump($data);
        // var_dump($save_as_name);
        // die();
        
        $template->setValues([

            'ref' => $data['ref_number'],
            'date' => date('F d, Y',strtotime($data['created_at'])),
            'visitDate' => $data['visit_date'],
            'documentTitle'=>$data['document_title'],
            'gatesText'=>$data['allowed_gates'],
            'sqn'=>$data['id'],
            'department'=>$data['department'],

        ]);

        if($data['remarks']){
            $template->setValue('remarksTitle','Faahfaahin Dheeri ah:');
            $template->setValue('remarks',$data['remarks']);
        }else{
            $template->setValue('remarksTitle','');
            $template->setValue('remarks','');
        }

        $template->cloneRowAndSetValues(
            'id',
            $data['table']
        );

        $options = new QROptions([
            'eccLevel' => EccLevel::H,
            'outputBase64' => true,
            'cachefile' => APPPATH . 'Views/documents/qrcode.png',
            'outputType'=>QROutputInterface::GDIMAGE_PNG,
            'logoSpaceHeight' => 17,
            'logoSpaceWidth' => 17,
            'scale' => 20,
            'version' => Version::AUTO,

          ]);

        //   $template->setMacroChars()
        //   $options->outputType = ;

        $qrcode = (new QRCode($options))->render(get_uri('visitors_info/show_visitor_qrcode/'.$data['uuid']));//->getQRMatrix(current_url())

        // $qrOutputInterface = new QRImageWithLogo($options, $qrcode);

        // // dump the output, with an additional logo
        // $out = $qrOutputInterface->dump(APPPATH . 'Views/documents/qrcode.png', APPPATH . 'Views/documents/logo.png');

        $template->setImageValue('qrcode',
            [
                'path' => APPPATH . 'Views/documents/qrcode.png',
                'width' => '100',
                'height' => '100',
                'ratio' => false,
            ]);

            // if(count($data['images_table'])){
            //     foreach($data['table'] as $t){
                   
            //         if($t['image']){
            //             $image = @unserialize($t['image']);
            //             $image = $image['file_name'];
            //         }else{
            //             $image = 'avatar.jpg';
            //         }
                    
            //         $template->setImageValue('avatar#'.$t['id'],
            //             [
            //                 'path' => ROOTPATH . 'files/visitors/'.$image,
            //                 'width' => '30',
            //                 'height' => '30',
            //                 'ratio' => false,
            //             ]);
            //     }
            // }

            if(count($data['images_table'])){

                    $main_table = new Table(['borderSize' => 0, 'borderColor' => 'ffffff', 
                                'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER, 
                                'cellSpacing' => 120]);

                    $rowStyle = ['cantSplit' => false];
                    $main_table->addRow(null);
                   $k = 1;
                   foreach($data['images_table'] as $t){
                        if($t['image']){
                            $j = $k-1; 
                        if($j%4 == 0 && $j != 0){
                            $main_table->addRow(null);
                        }

                        $img_cell = $main_table->addCell(2000, ['borderColor' => '4691f9','borderSize'=>6,'borderBottomColor'=>'4691f9']);//
                        $img_cell->addText('${avatar#'.$k.'}');
                        // $img_cell->addImage('http://localhost/rise/files/visitors/'.$image, ['width' => 80, 'height' => 80]);
                        
                        $img_cell->addText($t['id'].'. '.$t['name']);

                        $k = $k+1;
                    }
                }

                $template->setComplexBlock('images_table',$main_table);
               
                $k = 1;
                foreach($data['images_table'] as $t){
                        
                        if($t['image']){
                            $image = @unserialize($t['image']);
                            $image = $image['file_name'];
                        }else{
                            $image = 'avatar.jpg';
                        }
                        if($t['image'] && $image != 'avatar.jpg'){
                            $template->setImageValue('avatar#'.$k,
                                        [
                                            'path' => ROOTPATH . 'files/visitors/'.$image,
                                            'width' => '100',
                                            'height' => '100',
                                            'ratio' => false,
                                        ]);
                            $k = $k+1;
                        }
                    }
            }else{
                $template->setValue('images_table','');
            }

        $template->saveAs($path_absolute);

        return $save_as_name;

    }

    // Gets the created file and uploads it to the SharePoint Drive
    public function uploadDoc($accessToken,$data, $path)
    {

        $fileContents = file_get_contents(APPPATH . 'Views/documents/' . $path); // Read the contents of the image file

        $curl = curl_init();
        $driveId = getenv('DRIVE_ID');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://graph.microsoft.com/v1.0/drives/$driveId/root:/".$data['folder'].'/' . $path . ':/content',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $fileContents,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
            ),
        ));

        $json = curl_exec($curl);

        curl_close($curl);

        // Decode the JSON response into an associative array
        $res = json_decode($json, true);

       
        if(file_exists(APPPATH . 'Views/documents/'.$path)){
            unlink(APPPATH . 'Views/documents/'.$path);
        }       

        //send whatsapp message:        
        $baseUrl = getenv('WHATSAPP_BASE_URL');
        $phoneNumber = getenv('TO_WHATSAPP_PHONE_NUMBER');
        $message = "New Access Request.\n";
        $messageType = "text";
        $apiKey = getenv('WHATSAPP_API_KEY');
        
        // get visitors details:
        $id = $data['id'];
        $visitor_name = '';
        $mobile = '';
        $vehicle_details = '';

        $vdetails = $this->db->query("SELECT * FROM rise_visitors_detail WHERE visitor_id = $id")->getResult();

        if($vdetails){
            foreach($vdetails as $k => $d){
                $visitor_name = $d->visitor_name;
                $mobile = $d->mobile;
                $vehicle_details = $d->vehicle_details;
                $k = $k + 1;

                $message.="\n$k.";
                $message.="\nName: " . $visitor_name;
                if($mobile){
                    $message.="\nMobile: " . $mobile;
                }
                if($vehicle_details){
                    $message.="\nVehicle Details: " . $vehicle_details."\n";
                }
                
            }
        }

        $message.="\n https://localhost/evilla/visitors";
        

        sendWhatsappMessage($baseUrl, $phoneNumber, $message,$messageType, $apiKey);

        
        return $res;

    }

    //opens document with [itemid] in sharepoint
    public function openDoc($accessToken, $itemID)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.microsoft.com/v1.0/sites/villasomaliafrs.sharepoint.com,47e1c0f0-d924-4f35-aeba-f45b4948148d,6d6454c2-2184-4c7b-84dc-1a701bdb5a9b/drive/root:/test/' . $itemID . '?Autho=null',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
            ),
        ));

        $json = curl_exec($curl);

        curl_close($curl);

        // Decode the JSON response into an associative array
        $data = json_decode($json, true);

        // Get the web URL of the file from the array
        $webUrl = $data["webUrl"];

        // Redirect to the web URL using the header function
        header("Location: $webUrl");
        exit;
    }

    /* delete or undo a lead */
    public function delete()
    {
        $this->validate_submitted_data(array(
            "id" => "required|numeric",
        ));

        $id = $this->request->getPost('id');
        // $this->validate_lead_access($id);
        $row = $this->db->query("SELECT * FROM rise_visitors where id = $id")->getRow();

        if($row->status == 'Approved'){
            echo json_encode(array("success" => false, 'message' => 'Record CAN NOT be Deleted once approved.'));
            exit;
        }

        if ($this->Visitors_model->delete($id)) {
            $this->db->query("DELETE FROM rise_visitors_detail where visitor_id = $id");

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    public function search_results($id= 0) {

        $visitor_info = $this->db->query("SELECT v.*,cb.image as created_avatar,ab.image as approved_avatar,rb.image as rejected_avatar,
                        concat(cb.first_name,' ',cb.last_name) as created_by,concat(rb.first_name,' ',rb.last_name) as rejected_by,
                        concat(ab.first_name,' ',ab.last_name) as approved_by FROM rise_visitors v 

                        LEFT JOIN rise_visitors_detail vd on v.id = vd.visitor_id
                        LEFT JOIN rise_users cb on v.created_by = cb.id
                        LEFT JOIN rise_users ab on v.approved_by = ab.id
                        LEFT JOIN rise_users rb on v.rejected_by = rb.id
                        WHERE v.uuid = '$id'")->getRow();
                    
        $visitor_details = $this->db->query("SELECT * FROM rise_visitors_detail WHERE visitor_id = $visitor_info->id")->getResult();

        $view_data["scroll_to_content"] = true;
        $view_data["visitor_info"] = $visitor_info;
        $view_data["visitor_details"] = $visitor_details;
        return $this->template->rander('visitors/search_result',$view_data);
    }

    public function access_search() {

        $today = date('Y-m-d');
        $visitors = $this->db->query("SELECT *,(select count(*) from rise_visitors_detail vd where v.id = vd.visitor_id) as count 
        FROM rise_visitors v WHERE (end_date >= '$today') order by v.id desc limit 10")->getResult();
        
        $data['visitors'] = $visitors;

        return $this->template->rander('visitors/todays_access_search',$data);
    }

    public function get_visitors_suggestion() {
        $search = $this->request->getPost('search');
        // die('s:'.$search);
        $today = date('Y-m-d');
        $result = $this->db->query("SELECT * FROM rise_visitors WHERE (end_date >= '$today') and id like '%$search%'  order by visit_time desc limit 10")->getResult();

        $result_array = array();
        foreach ($result as $v) {
            $result_array[] = array("value" => $v->uuid, "label" => $v->id .' - '.date("l, h:i a",strtotime(date_format(new DateTime($v->start_date),'Y-m-d').' '.$v->visit_time)).' - '.$v->document_title.' - '.$v->client_type);
        }

        return json_encode($result_array);
    }
    // get visitor details
    public function visitor_details(){

        $id = $this->request->getPost('id');

        $res = $this->check_access('visitor');
        $role = get_array_value($res, 'role');
        $data['can_approve_requests'] = $role == 'Access Controll' || $role == 'Administrator' || $role == 'admin'; 

        $visitor_info = $this->db->query("SELECT v.*,cb.image as created_avatar,ab.image as approved_avatar,rb.image as rejected_avatar,
                        concat(cb.first_name,' ',cb.last_name) as created_by,concat(rb.first_name,' ',rb.last_name) as rejected_by,
                        concat(ab.first_name,' ',ab.last_name) as approved_by FROM rise_visitors v 

                        LEFT JOIN rise_visitors_detail vd on v.id = vd.visitor_id
                        LEFT JOIN rise_users cb on v.created_by = cb.id
                        LEFT JOIN rise_users ab on v.approved_by = ab.id
                        LEFT JOIN rise_users rb on v.rejected_by = rb.id
                        WHERE v.id = $id

                    ")->getRow();

        $visitor_details = $this->db->query("SELECT vd.* FROM rise_visitors v 
        LEFT JOIN rise_visitors_detail vd on v.id = vd.visitor_id
        WHERE visitor_id = $id
        ")->getResult();
        
        $doc = $this->db->query("SELECT d.webUrl FROM rise_visitor_document l left join rise_documents d on l.document_id = d.id where l.visitor_id = $id")->getRow();
        $data['visitor_info'] = $visitor_info;
        $data['visitor_details'] =  $visitor_details;
        $data['webUrl'] =  empty($doc) ? '' : $doc->webUrl;
      

        return $this->template->view('visitors/visitor_details',$data);
    }

    //update status for visitor
    public function update_status(){
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('leave_status_input');
        $user_id = $this->login_user->id;

        if($status == 'Rejected'){
            $this->db->query("UPDATE rise_visitors SET status = '$status',rejected_by = $user_id WHERE id = $id");    
            
            // send whatsapp message:
            $baseUrl = getenv('WHATSAPP_BASE_URL');
            $phoneNumber = getenv('TO_WHATSAPP_PHONE_NUMBER');
            $message = "Access Request Rejected.\n";
            $message .= "\nRequest Number: #$id"; 
            $messageType = "text";
            $apiKey = getenv('WHATSAPP_API_KEY');
                       
            // $vdetails = $this->db->query("SELECT * FROM rise_visitors_detail WHERE visitor_id = $id")->getResult();
            
            $res = sendWhatsappMessage($baseUrl, $phoneNumber, $message,$messageType, $apiKey);

        }elseif($status == 'show-pdf'){
            
            $visitor_info = $this->db->query("SELECT * FROM rise_visitors WHERE id = $id")->getRow();
            // show pdf:
            if($visitor_info){

                $this->access_request_pdf($visitor_info->uuid);
            }else{
                show_404();
            }
        }elseif($status == 'Updated'){
            $this->db->query("UPDATE rise_visitors SET status = '$status',approved_by = $user_id WHERE id = $id");  

            // send whatsapp message:
            $baseUrl = getenv('WHATSAPP_BASE_URL');
            $phoneNumber = getenv('TO_WHATSAPP_PHONE_NUMBER');
            $message = "Access Request Approved.\n";
            $message .= "\n Request Number: #$id";
            $messageType = "text";
            $apiKey = getenv('WHATSAPP_API_KEY');
                       
            // $vdetails = $this->db->query("SELECT * FROM rise_visitors_detail WHERE visitor_id = $id")->getResult();
            
            $res = sendWhatsappMessage($baseUrl, $phoneNumber, $message,$messageType, $apiKey);

        }
        else{
            $this->db->query("UPDATE rise_visitors SET status = '$status',approved_by = $user_id WHERE id = $id");    
        }

        
        echo json_encode(array("success" => true, "data" => null, 'message' => app_lang('record_updated')));
    }

    /* list of leads, prepared for datatable  */
    public function list_data()
    {
        $this->access_only_allowed_members();
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);

        // $show_own_leads_only_user_id = $this->show_own_leads_only_user_id();
        
        $role = $this->get_user_role();
        $department_id = $this->get_user_department_id();

        if($role == 'Access Controll' || $role == 'admin' || $role == 'Administrator'){ //not allowed to others including 'admistrator' role
            $created_by = '%';
            $department_id = '%';
        }elseif($role == 'Director' || $role == 'Secretary'){
            $created_by = '%';
        }
        else{
            app_redirect("forbidden");
        }

        // die($this->login_user->is_admin);
        $options = append_server_side_filtering_commmon_params([]);


        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($options, "server_side")) {
            $order_by = $options['order_by'];
            $order_direction = $options['order_dir'];
            $search_by = $options["search_by"] ;
            $skip = $options["skip"] ;

            
            $limit_offset = "";
            $limit = $options['limit'] ?? 10;
            $where="v.deleted=0";

            if ($limit) {
            
                $offset = $skip ? $skip : 0;
                $limit_offset = " LIMIT $limit OFFSET $offset ";
            }

            if ($order_by) {
                $order_by = "$order_by $order_direction ";
            }

            if ($search_by) {
                $search_by = $this->db->escapeLikeString($search_by);

            // client_type	name	created_by	visit_date	created_at	deleted	remarks
                $where .= " AND (";
                $where .= " v.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR v.client_type LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR v.name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR v.visit_date LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR v.remarks LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.first_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR u.last_name LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR v.created_at LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " )";
            }
        
            // die('dept id: '.$department_id);
            $result = $this->db->query("select v.*,concat(u.first_name,' ',u.last_name) user from rise_visitors v 
            LEFT JOIN rise_users u on v.created_by = u.id 
            where $where and v.created_by LIKE '$created_by' and v.department_id LIKE '$department_id' order by $order_by $limit_offset");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_visitors where created_by LIKE '$created_by' and department_id LIKE '$department_id' and deleted=0")->getRow()->affected;
            $result = array();

        } else {
            $result = $this->db->query("select v.*,concat(u.first_name,' ',u.last_name) user from rise_visitors v 
            LEFT JOIN rise_users u on v.created_by = u.id 
            where v.created_by LIKE '$created_by' and v.department_id LIKE '$department_id' and v.deleted=0");

            $list_data = $result->getResult();
            $total_rows =$this->db->query("select count(*) as affected from rise_visitors where created_by LIKE '$created_by' and department_id LIKE '$department_id' and deleted=0")->getRow()->affected;
            $result = array();
        }


        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_row($data, $custom_fields);
        }

        $result["data"] = $result_data;
        $result["recordsTotal"] = $total_rows;
        $result["recordsFiltered"] = $total_rows;

        // var_dump($result);
        // die();
        echo json_encode($result);
    }

    /* return a row of lead list table */

    private function _row_data($id)
    {
       
        $data = $this->Visitors_model->get_one($id)->getRow();
        return $this->_make_row($data);
    }

    /* prepare a row of lead list table */

    private function _make_row($data)
    {
        
        $role = $this->get_user_role();
        $can_add_requests = $role == 'Access Controll' || $role == 'Secretary' || $role == 'Director'  || $role == 'HRM' || $role == 'admin' || $role == 'Administrator'; 

        //primary contact
        // $image_url = get_avatar($data->contact_avatar);
        // $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        // $primary_contact = get_lead_contact_profile_link($data->primary_contact_id, $contact);

        // `document_title`, `ref_number`, `depertment`, `template`, `item_id`,`created_by`, `created_at`
        //lead owner
        $owner = "-";
        if ($data->created_by) {
            // $owner_image_url = get_avatar($data->owner_avatar);
            // $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->user";
            // $owner = get_team_member_profile_link($data->created_by, $owner_user);
            $owner =$data->user;//$this->db->query("select * from rise_users where id = $data->created_by");
            

        }

        // $lead_labels = make_labels_view_data($data->labels_list, true);
       
        if (strtolower($data->status) === "approved") {
            $status_meta = "<span class='badge badge bg-success'>Approved</span>";
        }else{
            $status_meta = "<span class='badge badge bg-warning'>Pending</span>";
        }


        $visit_date = date('Y-m-d',strtotime($data->visit_date));
         // client_type	name	created_by	visit_date	created_at	deleted	remarks
        $row_data = array(
            $data->id,
            modal_anchor(get_uri("visitors/visitor_details"),$data->name , array("class" => "edit","title" => app_lang('show_info'), "data-post-id" => $data->id)),
            // anchor(get_uri("visitors/view/" . $data->id), ),
            $data->client_type,
            date("h:i a, F d, Y",strtotime($visit_date.' '.$data->visit_time)),
            $owner,
            $this->get_department_name($data->department_id),
            format_to_date($data->created_at, false),
            $status_meta,
        );

        // $row_data[] = js_anchor($data->document_title, array("style" => "background-color: green;",
        // "class" => "badge", "data-id" => $data->id, "data-value" => $data->id, "data-act" => "update-lead-status"));

        // foreach ($custom_fields as $field) {
        //     $cf_id = "cfv_" . $field->id;
        //     $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        // }
        //open doc link:
        $doc = $this->db->query("SELECT d.webUrl FROM rise_visitor_document l left join rise_documents d on l.document_id = d.id where l.visitor_id = $data->id")->getRow();

        $webUrl = empty($doc) ? '' : $doc->webUrl;

        if(($can_add_requests)){

            $link = "<a href='$webUrl' class='btn btn-success' target='_blank' title='Open Document' style='background: #1cc976;color: white'><i data-feather='eye' class='icon-16'></i>";
        }else{
            $webUrl =   get_uri('visitors/access_request_pdf/'.$data->uuid);
            $link = "<a href='$webUrl' class='btn btn-success' target='_blank' title='Show Pdf' style='background: #1cc976;color: white'><i data-feather='eye' class='icon-16'></i>";
        }
        $delLink = $can_add_requests == true ? js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_visitor'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("visitors/delete"), "data-action" => "delete-confirmation")) : '';
        
        $row_data[] = modal_anchor(get_uri("visitors/visitor_details"), "<i data-feather='info' class='icon-16'></i>", array("class" => "edit",
            "title" => app_lang('show_info'), "data-post-id" => $data->id))
            //.modal_anchor(get_uri("visitors/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit",
            //"title" => app_lang('edit_lead'), "data-post-id" => $data->id))
        . $delLink
        . $link;

        return $row_data;
    }

    
    function template_modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['departments'] = $this->Team_model->get_departments_for_select();
        array_unshift($view_data['departments'],'Choose Department');

        $view_data['model_info'] = $this->Templates_model->get_one($this->request->getPost('id'));
        return $this->template->view('templates/modal_form', $view_data);
    }

    function save_template() {
        $this->validate_submitted_data(array(
            "id" => "numeric",
            "name" => "required",
            "destination_folder" => "required",
            "ref_prefix" => "required",
            "path" => "required",
        ));

        $data = array(
            "name" => $this->request->getPost('name'),
            "department" => $this->get_user_department_id(),
            "ref_prefix" => $this->request->getPost('ref_prefix'),
            "destination_folder" => $this->request->getPost('destination_folder'),
            "path" => $this->request->getPost('path'),
            "created_at" => date("Y-m-d H:i:s"),

        );

        $id = $this->request->getPost('id');
        $save_id=null;

        if(!$id){

            $save_id = $this->Templates_model->ci_save($data);
            echo json_encode(array("success" => true, "data" => null, 'id' => $save_id, 'message' => app_lang('record_saved')));
            // $template_info = $this->Templates_model->get_one($id);
        }else{

            $save_id = $this->Templates_model->ci_save($data, $id);
            echo json_encode(array("success" => true, "data" => null, 'id' => $id, 'message' => app_lang('record_updated')));
        }

        if (!$save_id) {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }


    /* load lead details view */

    public function view($client_id = 0, $tab = "")
    {
        $this->check_module_availability("module_lead");
        validate_numeric_value($client_id);

        if ($client_id) {
            $options = array("id" => $client_id);
            $lead_info = $this->Clients_model->get_details($options)->getRow();
            $this->validate_lead_access($client_id);

            if ($lead_info && $lead_info->is_lead) {

                $access_info = $this->get_access_info("estimate");
                $view_data["show_estimate_info"] = (get_setting("module_estimate") && $access_info->access_type == "all") ? true : false;

                $access_info = $this->get_access_info("estimate_request");
                $view_data["show_estimate_request_info"] = (get_setting("module_estimate_request") && $access_info->access_type == "all") ? true : false;

                $access_contract = $this->get_access_info("contract");
                $view_data["show_contract_info"] = (get_setting("module_contract") && $access_contract->access_type == "all") ? true : false;

                $access_info = $this->get_access_info("proposal");
                $view_data["show_proposal_info"] = (get_setting("module_proposal") && $access_info->access_type == "all") ? true : false;

                /*
                $access_info = $this->get_access_info("ticket");
                $view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_info->access_type == "all") ? true : false;
                 */

                $view_data["show_ticket_info"] = false; //don't show tickets for now.

                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $view_data['lead_info'] = $lead_info;

                $view_data["tab"] = clean_data($tab);

                return $this->template->rander("visitors/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
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

            return $this->template->view("visitors/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    public function estimate_requests($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data['client_id'] = $client_id;
            return $this->template->view("visitors/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    public function notes($client_id)
    {
        if ($client_id) {
            validate_numeric_value($client_id);
            $this->validate_lead_access($client_id);
            $view_data['client_id'] = $client_id;
            return $this->template->view("visitors/notes/index", $view_data);
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
        return $this->template->view("visitors/files/index", $view_data);
    }

    /* file upload modal */

    public function file_modal_form()
    {
        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $client_id = $this->request->getPost('client_id') ? $this->request->getPost('client_id') : $view_data['model_info']->client_id;

        $this->validate_lead_access($client_id);

        $view_data['client_id'] = $client_id;
        return $this->template->view('visitors/files/modal_form', $view_data);
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
        js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("visitors/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("visitors/download_file/" . $data->id), "<i data-feather='download-cloud' class='icon-16'></i>", array("title" => app_lang("download")));

        $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("visitors/delete_file"), "data-action" => "delete-confirmation"));

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
            return $this->template->view("visitors/files/view", $view_data);
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
            return $this->template->rander("visitors/contacts/view", $view_data);
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

            return $this->template->view("visitors/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    public function add_new_contact_modal_form()
    {
        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->request->getPost('client_id');
        $this->validate_lead_access($view_data['model_info']->client_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('visitors/contacts/modal_form', $view_data);
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
            return $this->template->view('visitors/contacts/contact_general_info_tab', $view_data);
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

            return $this->template->view('visitors/contacts/company_info_tab', $view_data);
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

        $contact_link = anchor(get_uri("visitors/contact_profile/" . $data->id), $full_name . $primary_contact);
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

        $row_data[] = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("visitors/delete_contact"), "data-action" => "delete"));

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

        return $this->template->rander("visitors/kanban/all_leads", $view_data);
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

        return $this->template->view('visitors/kanban/kanban_view', $view_data);
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

        return $this->template->view('visitors/migration/modal_form', $view_data);
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

            return $this->template->view("visitors/proposals/index", $view_data);
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
            return $this->template->view("visitors/contracts/index", $view_data);
        }
    }

    /* load tasks tab  */

    public function tasks($client_id)
    {
        $this->validate_lead_access($client_id);

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = clean_data($client_id);
        return $this->template->view("visitors/tasks/index", $view_data);
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

        return $this->template->rander("visitors/reports/converted_to_client", $view_data);
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

        return $this->template->view("visitors/reports/converted_to_client_monthly_chart", $view_data);
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

        return $this->template->view("visitors/reports/team_members_summary", $view_data);
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
