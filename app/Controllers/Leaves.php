<?php

namespace App\Controllers;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Common\Version;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use PhpOffice\PhpWord\TemplateProcessor;

class Leaves extends Security_Controller {

    function __construct() {
        parent::__construct();
        $this->access_only_team_members();

        $this->init_permission_checker("leave");
    }

    protected function can_delete_leave_application() {
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "can_delete_leave_application") == "1") {
            return true;
        }
    }

    function index($tab = "") {

        $this->check_module_availability("module_leave");
        $this->access_only_allowed_members();

        $role = $this->get_user_role();

        $view_data['can_assign_leaves'] = $role == 'admin' || $role == 'Administrator' || $role != 'Employee';

        $view_data["can_manage_all_leaves"] = $this->login_user->is_admin || $this->access_type === "all";
        $view_data['tab'] = clean_data($tab);

        return $this->template->rander("leaves/index", $view_data);
    }


     //update leave status
     function update_status() {

        $this->validate_submitted_data(array(
            "id" => "required|numeric",
            "status" => "required"
        ));

        $applicaiton_id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $now = get_current_utc_time();

        $role = $this->get_user_role();
        
        if($role === "HRM" && $status === "approved"){
            $status = 'approved';
        }elseif(($role == "Section Head" || $role === "HRM") && $status === "pending"){
            $status = 'pending';
        }

        $leave_data = array(
            "checked_by" => $this->login_user->id,
            "checked_at" => $now,
            "status" => $status
        );

        if ($status === "pending") {
            $leave_data["pended_by"] = $this->login_user->id;
            $leave_data["pended_at"] = $now;
        } else if ($status === "approved") {
            $leave_data["approved_by"] = $this->login_user->id;
            $leave_data["approved_at"] = $now;
        } else if ($status === "rejected") {
            $leave_data["rejected_by"] = $this->login_user->id;
            $leave_data["rejected_at"] = $now;
        } else if ($status === "canceled") {
            $leave_data["canceled_by"] = $this->login_user->id;
            $leave_data["canceled_at"] = $now;
        }

        //only allow to updte the status = accept or reject for admin or specefic user
        //otherwise user can cancel only his/her own application
        $applicatoin_info = $this->Leave_applications_model->get_one($applicaiton_id);

        if ($status === "approved" || $status === "rejected") {
            $this->access_only_allowed_members($applicatoin_info->applicant_id);
        } else if ($status === "canceled" && $applicatoin_info->applicant_id != $this->login_user->id) {
            //any user can't cancel other user's leave application
            app_redirect("forbidden");
        }

            
        $save_id = $this->Leave_applications_model->ci_save($leave_data, $applicaiton_id);
            
            if ($save_id) {
                
                $notification_options = array("leave_id" => $applicaiton_id, "to_user_id" => $applicatoin_info->applicant_id);
                
                if ($status == "approved") {
                    log_notification("leave_approved_HR", $notification_options);//leave_approved
                } else if ($status == "pending") {
                    log_notification("leave_verified_Director", $notification_options);
                } else if ($status == "rejected") {
                    log_notification("leave_rejected", $notification_options);
                } else if ($status == "canceled") {
                    log_notification("leave_canceled", $notification_options);
                }               
                        
                $leave_info = $this->db->query("SELECT l.*,t.title,t.status FROM rise_leave_applications l left join rise_leave_types t on t.id=l.leave_type_id where l.id = $save_id")->getRow();

                $hrm_info = $this->db->query("SELECT us.id,us.private_email FROM rise_users us LEFT JOIN rise_roles rl ON us.role_id = rl.id WHERE rl.title = 'HRM'")->getRow();

                $head_section_info = $this->db->query("SELECT la.id, au.id, hsu.first_name, hsu.private_email FROM rise_leave_applications la LEFT JOIN rise_users au on au.id = la.applicant_id LEFT JOIN rise_team_member_job_info tj ON tj.user_id = au.id LEFT JOIN rise_sections sc on sc.id = tj.section_id LEFT JOIN rise_users hsu on sc.section_head_id = hsu.id WHERE la.id = $save_id")->getRow();
                
                // $head_department_info = $this->db->query("SELECT la.id, hdu.private_email FROM rise_leave_applications la LEFT JOIN rise_users au on la.applicant_id = au.id LEFT JOIN rise_departments dp on au.department_id = dp.id LEFT JOIN rise_users hdu on dp.dep_head_id = hdu.id WHERE la.id = $save_id")->getRow();
                
                
                $user_info = $this->db->query("SELECT u.*,j.job_title_so,j.signature,j.department_id FROM rise_users u left join rise_team_member_job_info j on u.id=j.user_id where u.id = $leave_info?->applicant_id")->getRow();

                if ($status === "approved" ) {
                    
                     //send email to the user for leave status
                    $leave_email_data = [
                        'LEAVE_ID'=>$save_id,
                        'EMPLOYEE_NAME'=>$user_info->first_name.' '.$user_info->last_name,
                        'LEAVE_TITLE' => $leave_info->title,
                        // 'LEAVE_REASON' => $leave_info->reason,
                        // 'LEAVE_DATE' => $duration == 1 ? $leave_data['start_date']: $leave_data['start_date'] .' - '.$leave_data['end_date'],
                        // 'TOTAL_DAYS'=>(int)$leave_info->total_days,
                        'LEAVE_STATUS'=>$status,  
                        'HRM_EMAIL'=>$hrm_info->private_email,                 
                        // 'HEAD_DEPARTMENT_EMAIL'=>$head_department_info->private_email,                 
                        'HEAD_SECTION_EMAIL'=>$head_section_info->private_email,                 
                        'PRIVATE_EMAIL'=>$user_info->private_email,                 
                        'MOF_EMAIL'=>$user_info->email,                 
                    ];

                    $r = $this->send_notify_leave_status_email($leave_email_data);

                    /** update leave signature start */
                        //get document row
                        // $doc = $this->db->query("select d.* from rise_documents d
                        // LEFT JOIN rise_leave_document ld on d.id = ld.document_id
                        // where d.deleted=0 and ld.leave_id =$applicaiton_id")->getRow();

                        // // $drive_info = unserialize($doc->drive_info);
                        // $itemID = $doc->item_id;
                        // $siteId = getenv('SITE_ID');
                        // $driveId = getenv('DRIVE_ID');
                        // $accessToken = $this->AccesToken();
                        // $imageArr = unserialize($user_info->signature);
                        // $signatureImageUrl = get_array_value($imageArr[0],'file_name');
                        // //   print_r($imageArr);die;

                        // if($signatureImageUrl){
                        //     $resultArr = $this->downloadWordDocument($accessToken,$siteId,$driveId,$itemID);

                        //     if($resultArr['success'] == true) {
                        //         $localFilePath = $resultArr['result'];
                        //         $updatedFilePath = $this->updateWordDocument($localFilePath, $signatureImageUrl);
                        //         $respose = $this->uploadUpdatedDocument($accessToken,$siteId,$driveId,$itemID,$updatedFilePath);
                            
                        //     }else{                
                                
                        //         $result = $resultArr['result'];
                        //         echo json_encode(array("success" => false, "data" => null, 'message' => $result));
                        //         die;
                        //     }
                        // }
                        
                        // print_r($respose);
                        // print_r($s);
                        // die;


                }elseif($status === "rejected"){

                     //send email to the user for leave status:
                        $leave_email_data = [
                            'LEAVE_ID'=>$save_id,
                            'EMPLOYEE_NAME'=>$user_info->first_name.' '.$user_info->last_name,
                            'LEAVE_TITLE' => $leave_info->title,
                            // 'LEAVE_REASON' => $leave_info->reason,
                            // 'LEAVE_DATE' => $duration == 1 ? $leave_data['start_date']: $leave_data['start_date'] .' - '.$leave_data['end_date'],
                            // 'TOTAL_DAYS'=>(int)$leave_info->total_days,
                            'LEAVE_STATUS'=>$status, 
                            'HRM_EMAIL'=>$hrm_info->private_email,                 
                            // 'HEAD_DEPARTMENT_EMAIL'=>$head_department_info->private_email,
                            'HEAD_SECTION_EMAIL'=>$head_section_info->private_email,                 
                            'PRIVATE_EMAIL'=>$user_info->private_email, 
                            'MOF_EMAIL'=>$user_info->email,                 

                        ];
    

                    $r = $this->send_notify_leave_status_email($leave_email_data);


                }elseif($status == 'pending'){
                    
                     //send email to the user for leave status:
                        $leave_email_data = [
                            'LEAVE_ID'=>$save_id,
                            'EMPLOYEE_NAME'=>$user_info->first_name.' '.$user_info->last_name,
                            'LEAVE_TITLE' => $leave_info->title,
                            // 'LEAVE_REASON' => $leave_info->reason,
                            // 'LEAVE_DATE' => $duration == 1 ? $leave_data['start_date']: $leave_data['start_date'] .' - '.$leave_data['end_date'],
                            // 'TOTAL_DAYS'=>(int)$leave_info->total_days,
                            'LEAVE_STATUS'=>$status, 
                            'HRM_EMAIL'=>$hrm_info->private_email,                 
                            // 'HEAD_DEPARTMENT_EMAIL'=>$head_department_info->private_email, 
                            'HEAD_SECTION_EMAIL'=>$head_section_info->private_email,                 
                            'PRIVATE_EMAIL'=>$user_info->private_email,   
                            'MOF_EMAIL'=>$user_info->email,                 

                        ];
    

                    $r = $this->send_notify_leave_status_email($leave_email_data);

                }



            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /** start word update */
    function downloadWordDocument($accessToken, $siteId, $driveId, $itemId) {
        $url = "https://graph.microsoft.com/v1.0/drives/$driveId/items/$itemId/content";
        
        $headers = [
            "Authorization: Bearer $accessToken"
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Set the maximum number of redirects

        $response = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $redirect_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        curl_close($ch);
    
        // print_r('response: '.$accessToken);
        // print_r('redirect: '.$url);
        // die;
        // Debugging output
        if ($curlError) {
            echo "cURL Error: " . $curlError;
           return array('success' => false, 'result' => $curlError); 
        }
    
        if ($httpStatusCode != 200) {
            echo "HTTP Status Code: " . $httpStatusCode;          
        }
    
        if (empty($response)) {
            echo "No response received!";            
           return array('success' => false, 'result' => 'No response received'); 
           
        }
        curl_close($ch);

        $localFilePath = APPPATH . 'Views/documents/local_copy_'.date('hs').'.docx';  
        file_put_contents($localFilePath, $response);

        return array('success' => true, 'result' => $localFilePath); 
    }

    function updateWordDocument($localFilePath, $signatureImageUrl) {
        // $localFilePath = APPPATH . 'Views/documents/'.$localFilePath;  
        // $phpWord = IOFactory::load($localFilePath);

        $template = new TemplateProcessor($localFilePath);

        $template->setImageValue('signature',
        [
            'path' => ROOTPATH . 'files/signature_file_path/'.$signatureImageUrl,
            'width' => '300',
            'height' => '150',
            'ratio' => true,
        ]);

        $template->saveAs($localFilePath);

        // $section = $phpWord->addSection();
        // $section->addText('This is new content added to the document.');
        // $phpWord->save($localFilePath, 'Word2007');

        // echo $localFilePath;
        return $localFilePath;
    }

    function uploadUpdatedDocument($accessToken, $siteId, $driveId, $itemId, $updatedFilePath) {
        $url = "https://graph.microsoft.com/v1.0/drives/$driveId/items/$itemId/content";
        
        $headers = [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document"
        ];

        $fileContents = file_get_contents($updatedFilePath);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContents);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Follow redirects
        // curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Set the maximum number of redirects

        $response = curl_exec($ch);
        
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        //  print_r('updatedFilePath: '.$updatedFilePath);
        //  print_r('fileContents: '.$fileContents);
        //  print_r('response: '.$response);
        //  print_r('httpStatusCode: '.$httpStatusCode);
        // print_r('curlError: '.$curlError);
        // die;

        //delete local file:
        if(file_exists($updatedFilePath)){
            unlink($updatedFilePath);
        }

        return json_decode($response, true);
    }

    /** end word update */


    public function send_leave_request_email($data = array()) {
        
        $email_template = $this->Email_templates_model->get_final_template("new_leave_request", true);

        $hrm_email = $data['HRM_EMAIL'];
        // $head_department_email = $data['HEAD_DEPARTMENT_EMAIL'];
        $head_section_email = $data['HEAD_SECTION_EMAIL'];
        $private_email = $data['PRIVATE_EMAIL'];
        $mof_email = $data['MOF_EMAIL'];
        $info_email = 'info@revenuedirectorate.gov.so';//$data['EMAIL'];

        $parser_data["EMPLOYEE_NAME"] = $data['EMPLOYEE_NAME'];
        $parser_data["LEAVE_ID"] = $data['LEAVE_ID'];
        $parser_data["LEAVE_TITLE"] = $data['LEAVE_TITLE'];
        $parser_data["LEAVE_REASON"] = $data['LEAVE_REASON'];
        $parser_data["LEAVE_DATE"] = $data['LEAVE_DATE'];
        $parser_data["TOTAL_DAYS"] = $data['TOTAL_DAYS'];

        $parser_data["LEAVE_URL"] = get_uri('leaves');
        $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["EMAIL_HEADER_URL"] = get_uri('assets/images/email_header.jpg');
        $parser_data["EMAIL_FOOTER_URL"] = get_uri('assets/images/email_footer.png');

        $message =  get_array_value($email_template, "message_default");
        $subject =  get_array_value($email_template, "subject_default");

        $message = $this->parser->setData($parser_data)->renderString($message);
        $subject = $this->parser->setData($parser_data)->renderString($subject);

        //$info_email = send_app_mail($info_email, $subject, $message);
        //$mof_email = send_app_mail($mof_email, $subject, $message);

        if(!empty($hrm_email)){

            $hrm_email =  send_app_mail($hrm_email, $subject, $message);
        }

        // if(!empty($head_department_email)){

        //     $head_department_email =  send_app_mail($head_department_email, $subject, $message);
        // }

        if(!empty($head_section_email)){

            $head_section_email =  send_app_mail($head_section_email, $subject, $message);
        }

        if(!empty($private_email)){

            $private_email = send_app_mail($private_email, $subject, $message);
        }


    }

    public function send_notify_leave_status_email($data = array()) {

       // $head_department_email = get_array_value($data,'HEAD_DEPARTMENT_EMAIL') ? get_array_value($data,'HEAD_DEPARTMENT_EMAIL') : '%';
        
        $hrm_email = $data['HRM_EMAIL'];
        // $head_department_email = $data['HEAD_DEPARTMENT_EMAIL'];
        $head_section_email = $data['HEAD_SECTION_EMAIL'];
        $private_email = $data['PRIVATE_EMAIL'];
        $mof_email = $data['MOF_EMAIL'];

        $status = $data['LEAVE_STATUS'];

        if($status == 'approved'){
            $email_template = $this->Email_templates_model->get_final_template("leave_request_approved", true);
           // $info_email = 'info@revenuedirectorate.gov.so';//$data['EMAIL'];
        }else if($status == 'rejected'){
            $email_template = $this->Email_templates_model->get_final_template("leave_request_rejected", true);
           // $info_email = 'info@revenuedirectorate.gov.so';//$data['EMAIL'];
        }elseif($status == 'pending'){
            $email_template = $this->Email_templates_model->get_final_template("leave_request_verified", true);
           // $info_email = 'info@revenuedirectorate.gov.so';//$data['EMAIL'];
        }

        $parser_data["EMPLOYEE_NAME"] = $data['EMPLOYEE_NAME'];
        $parser_data["LEAVE_ID"] = $data['LEAVE_ID'];
        $parser_data["LEAVE_TITLE"] = $data['LEAVE_TITLE'];
        // $parser_data["LEAVE_REASON"] = $data['LEAVE_REASON'];
        // $parser_data["LEAVE_DATE"] = $data['LEAVE_DATE'];
        // $parser_data["TOTAL_DAYS"] = $data['TOTAL_DAYS'];

        $parser_data["LEAVE_URL"] = get_uri('leaves');
        $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["EMAIL_HEADER_URL"] = get_uri('assets/images/email_header.jpg');
        $parser_data["EMAIL_FOOTER_URL"] = get_uri('assets/images/email_footer.png');

        $message =  get_array_value($email_template, "message_default");
        $subject =  get_array_value($email_template, "subject_default");

        $message = $this->parser->setData($parser_data)->renderString($message);
        $subject = $this->parser->setData($parser_data)->renderString($subject);

        // $info_email = send_app_mail($info_email, $subject, $message);
        // $mof_email =  send_app_mail($mof_email, $subject, $message);

        if(!empty($hrm_email)){

            $hrm_email =  send_app_mail($hrm_email, $subject, $message);
        }

        if(!empty($head_section_email)){

            $head_section_email =  send_app_mail($head_section_email, $subject, $message);
        }

        if(!empty($private_email)){

            $private_email = send_app_mail($private_email, $subject, $message);
        }

    }


    //load assign leave modal 

    function assign_leave_modal_form($applicant_id = 0) {

        $view_data = $this->team_members_dropdown();

        if ($applicant_id) {
            $view_data['team_members_info'] = $this->Users_model->get_one($applicant_id);
        } else {

            //show all members list to only admin and other members who has permission to manage all member's leave
            //show only specific members list who has limited access
            if ($this->access_type === "all") {
                $where = array("user_type" => "staff");
            } else {
                $where = array("user_type" => "staff", "id !=" => $this->login_user->id, "where_in" => array("id" => $this->allowed_members));
            }
            // $view_data['team_members_dropdown'] = array("" => "-") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", $where);
        }

        $view_data['leave_types_dropdown'] = array("" => "-") + $this->Leave_types_model->get_dropdown_list(array("title"), "id", array("status" => "active"));
        $view_data['form_type'] = "assign_leave";

        return $this->template->view('leaves/modal_form', $view_data);
    }

    //all team members can apply for leave
    function apply_leave_modal_form() {
        $view_data['leave_types_dropdown'] = array("" => "-") + $this->Leave_types_model->get_dropdown_list(array("title"), "id", array("status" => "active"));
        $view_data['form_type'] = "apply_leave";
        
        $view_data['applicant_id'] = $this->login_user->id;
        return $this->template->view('leaves/modal_form', $view_data);
    }


    
    private function team_members_dropdown()
    {

        $options = array(
            "status" => $this->request->getPost("status"),
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "user_type" => "staff",
        );

        $team_members = $this->Leave_applications_model->get_team_members_dropdown_permission($options);
        
        $team_members = get_array_value($team_members,'data') ? get_array_value($team_members,'data') : $team_members->getResult(); 
        $recordsTotal =  get_array_value($team_members,'recordsTotal');
        $recordsFiltered =  get_array_value($team_members,'recordsFiltered');

        $temp_array = ['-'];
        
        $result = array();
        foreach ($team_members as $t) {
            $temp_array[$t->id] = $t->name;
        }


        $view_data["team_members_dropdown"] = $temp_array;
        return $view_data;
    }


    // save: assign leave 
    function assign_leave() {
        $leave_data = $this->_prepare_leave_form_data();
        $applicant_id = $this->request->getPost('applicant_id');
        $leave_data['applicant_id'] = $applicant_id;
        $leave_data['created_by'] = $this->login_user->id;
        $leave_data['checked_by'] = $this->login_user->id;
        $leave_data['checked_at'] = $leave_data['created_at'];
        // $leave_data['status'] = "approved";

        $webUrl = null;

        //hasn't full access? allow to update only specific member's record, excluding loged in user's own record
        $this->access_only_allowed_members($leave_data['applicant_id']);

        
        
        $save_id = $this->Leave_applications_model->ci_save($leave_data);

        $hrm_info = $this->db->query("SELECT us.id,us.private_email FROM rise_users us LEFT JOIN rise_roles rl ON us.role_id = rl.id WHERE rl.title = 'HRM'")->getRow();
        // $head_department_info = $this->db->query("SELECT la.id, hdu.private_email FROM rise_leave_applications la LEFT JOIN rise_users au on la.applicant_id = au.id LEFT JOIN rise_departments dp on au.department_id = dp.id LEFT JOIN rise_users hdu on dp.dep_head_id = hdu.id WHERE la.id = $save_id")->getRow();
        $head_section_info = $this->db->query("SELECT la.id, au.id, hsu.first_name, hsu.private_email FROM rise_leave_applications la LEFT JOIN rise_users au on au.id = la.applicant_id LEFT JOIN rise_team_member_job_info tj ON tj.user_id = au.id LEFT JOIN rise_sections sc on sc.id = tj.section_id LEFT JOIN rise_users hsu on sc.section_head_id = hsu.id WHERE la.id = $save_id")->getRow();
        $user_info = $this->db->query("SELECT u.*,j.job_title_so,j.department_id FROM rise_users u left join rise_team_member_job_info j on u.id=j.user_id where u.id = $applicant_id")->getRow();
        $leave_info = $this->db->query("SELECT l.*,t.title,t.status FROM rise_leave_applications l 
                        left join rise_leave_types t on t.id=l.leave_type_id where l.id = $save_id")->getRow();

        if(!$user_info){
            
            echo json_encode(array("success" => false, 'message' => 'Information is missing, Please fill your User & Job information'));
        }

        $template = $this->db->query("SELECT * FROM rise_templates where destination_folder = 'Leave'")->getRow();
        $this->db->query("update rise_templates set sqn = sqn + 1 where id = $template->id");
        $sqn = $this->db->query("SELECT lpad(max(sqn),4,0) as sqn FROM rise_templates where id = $template->id")->getRow()->sqn;

        // var_dump($leave_info);
        // var_dump($save_id);
        // die();

        $duration = (int)$leave_info->total_days;
        $flight_included = $leave_info->flight_included;

        $doc_leave_data = [
            'uuid' => $leave_info->uuid,
            'id'=>$save_id,
            'employee'=>$user_info->first_name.' '.$user_info->last_name,
            'jobtitle'=>$user_info->job_title_so,
            'passport'=>$user_info->passport_no,
            'leavetype'=>$leave_info->title,
            'ref_number'=> $template->ref_prefix.$sqn.'/'.date('m').'/'.date('y'),
            'template' => $template->path,
            'folder' => $template->destination_folder,
            'date' => $leave_data['start_date'],
        ];
        
        $doc_data = [
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            'document_title' =>'Leave - '.$user_info->first_name.' '.$user_info->last_name,
            'ref_number' =>$template->ref_prefix.'/'.$sqn.'/'.date('m').'/'.date('y'),
            "department" => $user_info->department_id,
            "template" => $template->id,
            "created_by" => $this->login_user->id,
            "created_at" => date('Y-m-d H:i:s')
        ];

        $doc_id = $this->Documents_model->ci_save($doc_data);

        $doc = $this->db->query("SELECT * FROM rise_documents where id = $doc_id")->getRow();
        $this->db->query("insert into rise_leave_document(leave_id,document_id) values($save_id,$doc_id)");
        $doc_leave_data['document_id'] = $doc->id;

        $path = $this->createDoc($doc_leave_data);
        $token = $this->AccesToken();  
        $data = $this->uploadDoc($token,$doc_leave_data,$path);   
        
        
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
            // $driveId = $drive_ref['driveId'];

            //update item id and web url for document
            $u_data= array('item_id' => $itemId,'webUrl' => $webUrl,'ref_number'=>$doc_leave_data['ref_number'],'drive_info'=>@serialize($drive_ref));
            
            $this->Documents_model->ci_save($u_data, $doc->id);

            //send email to the HRM for new leave notification:
            $leave_email_data = [
                'LEAVE_ID'=>$save_id,
                'UUID' => $leave_info->uuid,
                'LEAVE_REASON' => $leave_info->reason,
                'LEAVE_TITLE' => $leave_info->title,
                'EMPLOYEE_NAME'=>$user_info->first_name.' '.$user_info->last_name,
                'JOB_TITLE'=>$user_info->job_title_so,
                'HRM_EMAIL'=>$hrm_info->private_email,                 
                // 'HEAD_DEPARTMENT_EMAIL'=>$head_department_info->private_email,
                'HEAD_SECTION_EMAIL'=>$head_section_info->private_email,
                'PRIVATE_EMAIL'=>$user_info->private_email,
                'MOF_EMAIL'=>$user_info->email,                 
                'PASSPORT'=>$user_info->passport_no,
                'LEAVE_TYPE'=>$leave_info->title,            
                'TOTAL_DAYS'=>(int)$leave_info->total_days,            
                'LEAVE_DATE' => $duration == 1 ? $leave_data['start_date']: $leave_data['start_date'] .' - '.$leave_data['end_date'],
            ];

            $r = $this->send_leave_request_email($leave_email_data);

        }


        if ($save_id) {
            log_notification("leave_assigned", array("leave_id" => $save_id, "to_user_id" => $applicant_id));
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id,'flight_included'=>$flight_included,'webUrl'=>$webUrl, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }


    function apply_leave() {

        $leave_data = $this->_prepare_leave_form_data();
        $leave_data['applicant_id'] = $this->login_user->id;
        $leave_data['created_by'] = 0;
        $leave_data['checked_at'] = "0000:00:00";

        $leave_type_id = $this->request->getPost('leave_type_id');
        $start_date = $leave_data['start_date'];
        $end_date = $leave_data['end_date'];
        // $leave_data['status'] = "pending";

        $applicant_id = $this->login_user->id;

        $allowed_days = $this->Leave_applications_model->get_allowed_days_by_type($leave_type_id);
        $taken_days = $this->Leave_applications_model->get_taken_days_by_type($applicant_id, $leave_type_id);

        // // Calculate the number of requested days
        // $requested_days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1; // +1 to include the start day

        // // Calculate remaining days
        // $remaining_days = $allowed_days - $taken_days;

        // // Check if the requested days are more than the remaining days
        // if ($requested_days > $remaining_days) {
        //     // Show an error message if the requested days exceed the remaining days
        //     $this->session->set_flashdata('error', 'You only have ' . $remaining_days . ' days remaining for this leave type.');
        //     redirect('leaves/apply');
        // }

        // // Check if the start date is before or equal to the end date
        // if (strtotime($start_date) > strtotime($end_date)) {
        //     $this->session->set_flashdata('error', 'Start date cannot be later than end date.');
        //     redirect('leaves/apply');
        // }

        $leave_data = clean_data($leave_data);
        
        $webUrl = null;
        $user_info = $this->db->query("SELECT u.*,j.job_title_so,j.department_id FROM rise_users u left join rise_team_member_job_info j on u.id=j.user_id where u.id = $applicant_id")->getRow();
      
        if(!$user_info){
            
            echo json_encode(array("success" => false, 'message' => 'Information is missing'.', Please fill your User & Job information'));
        }

        //hasn't full access? allow to update only specific member's record, excluding loged in user's own record
        // $this->access_only_allowed_members($leave_data['applicant_id']);

        $save_id = $this->Leave_applications_model->ci_save($leave_data);
        
        $hrm_info = $this->db->query("SELECT us.id,us.private_email FROM rise_users us LEFT JOIN rise_roles rl ON us.role_id = rl.id WHERE rl.title = 'HRM'")->getRow();
        // $head_department_info = $this->db->query("SELECT la.id, hdu.private_email FROM rise_leave_applications la LEFT JOIN rise_users au on la.applicant_id = au.id LEFT JOIN rise_departments dp on au.department_id = dp.id LEFT JOIN rise_users hdu on dp.dep_head_id = hdu.id WHERE la.id = $save_id")->getRow();
        $head_section_info = $this->db->query("SELECT la.id, au.id, hsu.first_name, hsu.private_email FROM rise_leave_applications la LEFT JOIN rise_users au on au.id = la.applicant_id LEFT JOIN rise_team_member_job_info tj ON tj.user_id = au.id LEFT JOIN rise_sections sc on sc.id = tj.section_id LEFT JOIN rise_users hsu on sc.section_head_id = hsu.id WHERE la.id = $save_id")->getRow();
        $leave_info = $this->db->query("SELECT l.*,t.title FROM rise_leave_applications l 
                        left join rise_leave_types t on t.id=l.leave_type_id where l.id = $save_id")->getRow();

        $template = $this->db->query("SELECT * FROM rise_templates where destination_folder = 'Leave'")->getRow();
        $this->db->query("update rise_templates set sqn = sqn + 1 where id = $template->id");
        $sqn = $this->db->query("SELECT lpad(max(sqn),4,0) as sqn FROM rise_templates where id = $template->id")->getRow()->sqn;

        
        $flight_included = $leave_info->flight_included;

        $doc_leave_data = [
            'uuid' => $leave_info->uuid,
            'id'=>$save_id,
            'employee'=>$user_info->first_name.' '.$user_info->last_name,
            'jobtitle'=>$user_info->job_title_so,
            'passport'=>$user_info->passport_no,
            'leavetype'=>$leave_info->title,
            'ref_number'=> $template->ref_prefix.$sqn.'/'.date('m').'/'.date('y'),
            'template' => $template->path,
            'folder' => $template->destination_folder,
            'date' => $leave_data['start_date'],
        ];

        $doc_data = [
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            'document_title' =>'Leave - '.$user_info->first_name.' '.$user_info->last_name,
            'ref_number' =>$template->ref_prefix.'/'.$sqn.'/'.date('m').'/'.date('y'),
            "department" => $user_info->department_id,
            "template" => $template->id,
            "created_by" => $this->login_user->id,
            "created_at" => date('Y-m-d H:i:s')
        ];

        $doc_id = $this->Documents_model->ci_save($doc_data);
        $doc = $this->db->query("SELECT * FROM rise_documents where id = $doc_id")->getRow();
        $this->db->query("insert into rise_leave_document(leave_id,document_id) values($save_id,$doc_id)");
        $doc_leave_data['document_id'] = $doc->id;

        $path = $this->createDoc($doc_leave_data);
        $token = $this->AccesToken();  
        $data = $this->uploadDoc($token,$doc_leave_data,$path);   
        
        
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

            //update item id and web url for document
            $u_data= array('item_id' => $itemId,'webUrl' => $webUrl,'ref_number'=>$doc_leave_data['ref_number']);
            
            $this->Documents_model->ci_save($u_data, $doc->id);

            $duration = (int)$leave_info->total_days;
            
            //send email to the HRM for new leave notification:
                $leave_email_data = [
                    'LEAVE_ID'=>$save_id,
                    'UUID' => $leave_info->uuid,
                    'LEAVE_TITLE' => $leave_info->title,
                    'LEAVE_REASON' => $leave_info->reason,
                    'EMPLOYEE_NAME'=>$user_info->first_name.' '.$user_info->last_name,
                    'JOB_TITLE'=>$user_info->job_title_so,
                    'HRM_EMAIL'=>$hrm_info->private_email,                 
                    // 'HEAD_DEPARTMENT_EMAIL'=>$head_department_info->private_email,
                    'HEAD_SECTION_EMAIL'=>$head_section_info->private_email,
                    'PRIVATE_EMAIL'=>$user_info->private_email,
                    'MOF_EMAIL'=>$user_info->email,                 
                    'PASSPORT'=>$user_info->passport_no,            
                    'TOTAL_DAYS'=>$duration,
                    'LEAVE_TYPE'=>$leave_info->title,            
                    'LEAVE_DATE' => $duration == 1 ? $leave_data['start_date']: $leave_data['start_date'] .' - '.$leave_data['end_date'],
                ];
    
                $r = $this->send_leave_request_email($leave_email_data);
    
    
        }



        if ($save_id) {
            log_notification("leave_application_submitted", array("leave_id" => $save_id));
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }


    public function get_allowed_days() {
        $leave_type_id = $this->request->getPost('leave_type_id');
        $form_type = $this->request->getPost('form_type');
        $applicant_id = $this->request->getPost('applicant_id');

        $user_id = $form_type == 'apply_leave' ? $this->login_user->id : $applicant_id;  // Assuming the user ID is stored in session
    
        // Get allowed days for the selected leave type
        $allowed_days = $this->Leave_applications_model->get_allowed_days_by_type($leave_type_id);
    
        // Get the total days already taken by the user for the selected leave type
        $taken_days = $this->Leave_applications_model->get_taken_days_by_type($user_id, $leave_type_id);
    
        // Return both allowed_days and taken_days
        echo json_encode(array('allowed_days' => $allowed_days, 'taken_days' => $taken_days));
    }
    
  

    /**
     * start document functions
     */
     public function saveAsPDF($driveId,$itemId) {

    
        $pdfApi = "https://graph.microsoft.com/v1.0/drive/items/$itemId/content?format=pdf";///drives/$driveId
        $curl = curl_init();
        $accessToken = $this->AccesToken();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $pdfApi,
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

        // var_dump($itemId);
        // var_dump($driveId);
        // var_dump($accessToken);
        // die();
        
        // Decode the JSON response into an associative array
        $data = json_decode($json, true);

        return $data;
    }

     
    public function get_leave_pdf($path,$data,$mode='view'){

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

    
    // nolo osto search
    public function leave_nolosto_search() {
        $search = $this->request->getPost('searchTerm') ?? 0;
        // die($search);
        $leave_info = '';
        $view_data['leave_info'] = $leave_info;
        $view_data['search'] = $search;

        return  $this->template->view('leaves/leave_nolosto_search',$view_data);

    }


    public function leave_nolosto_search_form() {
        $search = $this->request->getPost('searchTerm') ?? 0;
        // die($search);
        $leave_info = empty($search) ? '' :  $this->db->query("SELECT t.title as leave_type,t.color,l.start_date,l.end_date,l.total_days as duration,l.id,l.uuid,CONCAT(a.first_name, ' ',a.last_name) as applicant_name ,e.job_title_so as job_title,
        a.image as applicant_avatar,CONCAT(cb.first_name, ' ',cb.last_name) AS checker_name,cb.image as checker_avatar,l.status,l.reason,a.passport_no,l.nolo_status FROM rise_leave_applications l 
        
        LEFT JOIN rise_users a on l.applicant_id = a.id
        LEFT JOIN rise_users cb on l.applicant_id = cb.id
        LEFT JOIN rise_team_member_job_info e on e.user_id = a.id
        left join rise_leave_types t on t.id=l.leave_type_id 
        where ( l.id = '$search' or l.uuid = '$search' or concat(a.first_name,' ',a.last_name) = '$search'  or e.employee_id = '$search' or  a.phone = '$search' or  a.email = '$search'  or  a.passport_no = '$search') limit 1")->getRow();
        
        $view_data['leave_info'] = $leave_info;
        $view_data['search'] = $search;

        $view =  $this->template->view('leaves/leave_nolosto_search_form',$view_data);

        // die($view);
        // die($search);
        echo json_encode(array('success' => true,'result' =>$view,'search' => $search ));
    }

    // leave return search
    public function leave_return_search() {
        $search = $this->request->getPost('searchTerm') ?? 0;
        // die($search);
        $leave_info = '';
        $view_data['leave_info'] = $leave_info;
        $view_data['search'] = $search;

        return  $this->template->view('leaves/leave_return_search',$view_data);

    }


    public function leave_return_search_form() {
        $search = $this->request->getPost('searchTerm') ?? 0;
        // die($search);
        $leave_info = empty($search) ? '' : $this->db->query("SELECT t.title as leave_type,t.color,l.start_date,l.end_date,l.total_days as duration,l.id,l.uuid,CONCAT(a.first_name, ' ',a.last_name) as applicant_name ,e.job_title_so as job_title,
        a.image as applicant_avatar,CONCAT(cb.first_name, ' ',cb.last_name) AS checker_name,cb.image as checker_avatar,l.status,l.reason,a.passport_no,l.nolo_status  FROM rise_leave_applications l 
        
        LEFT JOIN rise_users a on l.applicant_id = a.id
        LEFT JOIN rise_users cb on l.applicant_id = cb.id
        LEFT JOIN rise_team_member_job_info e on e.user_id = a.id
        left join rise_leave_types t on t.id=l.leave_type_id 
        where ( l.id = '$search' or l.uuid = '$search' or concat(a.first_name,' ',a.last_name) = '$search' or e.employee_id = '$search' or  a.phone = '$search' or  a.email = '$search'  or  a.passport_no = '$search') limit 1")->getRow() ;
        
        $view_data['leave_info'] = $leave_info;
        $view_data['search'] = $search;

        $view =  $this->template->view('leaves/leave_return_search_form',$view_data);

        // die($view);
        // die($search);
        
        echo json_encode(array('success' => true,'result' =>$view,'search' => $search ));
    }

     // get access token
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
        //  var_dump($data);
        //  die();
         // Get the web URL of the file from the array
         $accessToken = $data["access_token"];
 
         curl_close($curl);
         return $accessToken;
 
     }

    // Creates the Document Using the Provided Template
    public function createDoc($data =array())
    {

        require_once ROOTPATH . 'vendor/autoload.php';

        // Creating the new document...

        $template = new TemplateProcessor(APPPATH . 'Views/documents/'.$data['template']);

        $ext = pathinfo(APPPATH.'Views/documents/'.$data['template'],PATHINFO_EXTENSION);
        $employee = $data['employee'];
        $save_as_name = toSnakeCase($employee).'_'.$data['id'].'_'.date('m').'_'.date('Y').'.'.$ext;
        

        $path_absolute = APPPATH . 'Views/documents/'.$save_as_name;
        $doc_id = $data['document_id'];
        
        $doc = $this->db->query("SELECT * FROM rise_documents where id = $doc_id")->getRow();
        $webUrl = $doc->webUrl;
        // var_dump($data);
        // var_dump($save_as_name);
        // die();
        
        $template->setValues([

            'id' => $data['id'],
            'employee' => $data['employee'],
            'jobtitle' => $data['jobtitle'],
            'leavetype' => $data['leavetype'],
            'passport' => $data['passport'],
            'ref' => $data['ref_number'],
            'date' => date('F d, Y',strtotime($data['date'])),

        ]);

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

        //   $options->outputType = ;

        $qrcode = (new QRCode($options))->render(get_uri('visitors_info/show_leave_qrcode/'.$data['uuid']));//->getQRMatrix(current_url())

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

        //save as docx
        $template->saveAs($path_absolute);
        //save as pdf
        // \PhpOffice\PhpWord\Settings::setPdfRendererPath('vendor/dompdf/dompdf');
        // \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $phpWord = \PhpOffice\PhpWord\IOFactory::load($path_absolute);
        // $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord,'PDF');
        // $xmlWriter->save(APPPATH . 'Views/documents/' . $data['id'].'_'.date('m').'_'.date('Y').'.pdf');

        $save_as_name2 = $data['id'].'_'.date('m').'_'.date('Y').'.pdf';

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

        if(file_exists(APPPATH.'Views/documents/'.$path)){
            unlink(APPPATH.'Views/documents/'.$path);
        }

        //delete .docx file also
        $file_name = pathinfo($path,PATHINFO_FILENAME);
        if(file_exists(APPPATH.'Views/documents/'.$file_name.'.docx')){
            unlink(APPPATH.'Views/documents/'.$file_name.'.docx');
        }

        // send whatsapp message:
        $id = $data['id'];
        $employee = $data['employee'];
        $jobTitle = $data['jobtitle'];

        $baseUrl = getenv('WHATSAPP_BASE_URL');
        $phoneNumber = getenv('TO_WHATSAPP_PHONE_NUMBER');
        $message = "New Leave Requested.\n";
        $message .= "\nLeave Number: #$id"; 
        $message .= "\nRequested by: #$employee"; 
        $message .= "\nJob Title: #$jobTitle"; 
        $messageType = "text";
        $apiKey = getenv('WHATSAPP_API_KEY');
                
        // $vdetails = $this->db->query("SELECT * FROM rise_visitors_detail WHERE visitor_id = $id")->getResult();
        
        // $resw = sendWhatsappMessage($baseUrl, $phoneNumber, $message,$messageType, $apiKey);


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

    /**
     * end
    */

    /* prepare common data for a leave application both for apply a leave or assign a leave */

    private function _prepare_leave_form_data() {

        $this->validate_submitted_data(array(
            "leave_type_id" => "required|numeric",
            "reason" => "required"
        ));

        $duration = $this->request->getPost('duration');
        $hours_per_day = 8;
        $hours = 0;
        $days = 0;

        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "leave");
        $new_files = unserialize($files_data);

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

        $now = get_current_utc_time();
        $leave_data = array(
            'uuid' => $this->db->query("select replace(uuid(),'-','') as uuid;")->getRow()->uuid,
            "leave_type_id" => $this->request->getPost('leave_type_id'),
            "start_date" => $start_date,
            "end_date" => $end_date,
            "reason" => $this->request->getPost('reason'),
            "flight_included" => $this->request->getPost('flight_included'),
            "created_by" => $this->login_user->id,
            "created_at" => $now,
            "department_id" => $this->get_user_department_id(),
            "total_hours" => $hours,
            "total_days" => $days,
            "files" => serialize($new_files)
        );

        return $leave_data;
    }


    // load leave summary tab
    function summary() {
        $view_data['team_members_dropdown'] = json_encode($this->_get_members_dropdown_list_for_filter());
        $view_data['leave_types_dropdown'] = json_encode($this->_get_leave_types_dropdown_list_for_filter());
        return $this->template->view("leaves/summary", $view_data);
    }

    // // load pending approval tab
    // function pending_approval() {
    //     return $this->template->view("leaves/pending_approval");
    // }

    // // list of pending leave application. prepared for datatable
    // function pending_approval_list_data() {

    //     $options = array(
    //         "status" => "pending",
    //         'view_type' => 'pending_list', 
    //         "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
    //         "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
    //         "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
    //         "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
    //         "access_type" => $this->access_type, 
    //         "allowed_members" => $this->allowed_members
    //     );

    //     $list_data = $this->Leave_applications_model->get_list($options)->getResult();

    //     $result = array();
    //     foreach ($list_data as $data) {
    //         $result[] = $this->_make_row($data);
    //     }
    //     echo json_encode(array("data" => $result));
        
    // }

    // load pending approval tab
    function active_list() {
        return $this->template->view("leaves/active_list");
    }

    // list of pending leave application. prepared for datatable
    function active_list_data() {

        $options = array(
            "status" => "active",
            'view_type' => 'active_list', 
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "access_type" => $this->access_type, 
            "allowed_members" => $this->allowed_members
        );

        $list_data = $this->Leave_applications_model->get_list($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
        
    }

    // load pending approval tab
    function pending_list() {
        return $this->template->view("leaves/pending_list");
    }

    // list of pending leave application. prepared for datatable
    function pending_list_data() {

        $options = array(
            "status" => "active",
            'view_type' => 'pending_list', 
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "access_type" => $this->access_type, 
            "allowed_members" => $this->allowed_members
        );

        $list_data = $this->Leave_applications_model->get_list($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
        
    }

     // load pending approval tab
     function approved_list() {
        return $this->template->view("leaves/approved_list");
    }

    // list of pending leave application. prepared for datatable
    function approved_list_data() {

        $options = array(
            "status" => "approved",
            'view_type' => 'approved_list', 
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "access_type" => $this->access_type, 
            "allowed_members" => $this->allowed_members
        );

        $list_data = $this->Leave_applications_model->get_list($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
        
    }

     // load pending approval tab
     function rejected_list() {
        return $this->template->view("leaves/rejected_list");
    }

    // list of pending leave application. prepared for datatable
    function rejected_list_data() {

        $options = array(
            "status" => "rejected",
            'view_type' => 'rejected_list', 
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "access_type" => $this->access_type, 
            "allowed_members" => $this->allowed_members
        );

        $list_data = $this->Leave_applications_model->get_list($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
        
    }

     // load pending approval tab
     function canceled_list() {
        return $this->template->view("leaves/canceled_list");
    }

    // list of pending leave application. prepared for datatable
    function canceled_list_data() {

        $options = array(
            "status" => "canceled",
            'view_type' => 'canceled_list', 
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "access_type" => $this->access_type, 
            "allowed_members" => $this->allowed_members
        );

        $list_data = $this->Leave_applications_model->get_list($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
        
    }

      // load all applications tab 
      function all_applications() {
        return $this->template->view("leaves/all_applications");
    }

    // list of all leave application. prepared for datatable 
    function all_application_list_data() {

        $this->validate_submitted_data(array(
            "applicant_id" => "numeric"
        ));

        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        $applicant_id = $this->request->getPost('applicant_id');

        // print_r($applicant_id);die;


        $options = array(
            "start_date" => $start_date, 
            "end_date" => $end_date, 
            "applicant_id" => $applicant_id, 
            "login_user_id" => $this->login_user->id, 
            "show_own_leaves_only_user_id" => $this->show_own_leaves_only_user_id(),
            "show_own_unit_leaves_only_user_id" => $this->show_own_unit_leaves_only_user_id(),
            "show_own_section_leaves_only_user_id" => $this->show_own_section_leaves_only_user_id(),
            "show_own_department_leaves_only_user_id" => $this->show_own_department_leaves_only_user_id(),
            "access_type" => $this->access_type, 
            "allowed_members" => $this->allowed_members
        );

        $list_data = $this->Leave_applications_model->get_list($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    // list of leave summary. prepared for datatable
    function summary_list_data() {
        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        $applicant_id = $this->request->getPost('applicant_id');
        $leave_type_id = $this->request->getPost('leave_type_id');

        $options = array("start_date" => $start_date, "end_date" => $end_date, "access_type" => $this->access_type, "allowed_members" => $this->allowed_members, "applicant_id" => $applicant_id, "leave_type_id" => $leave_type_id);
        $list_data = $this->Leave_applications_model->get_summary($options)->getResult();

        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row_for_summary($data);
        }
        echo json_encode(array("data" => $result));
    }

    // reaturn a row of leave application list table
    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Leave_applications_model->get_list($options)->getRow();
        return $this->_make_row($data);
    }

    // prepare a row of leave application list table
    private function _make_row($data) {
        $meta_info = $this->_prepare_leave_info($data);
        $option_icon = "info";
        if ($data->status === "pending") {
            $option_icon = "cloud-lightning";
        }

        $doc = $this->db->query("SELECT d.webUrl FROM rise_leave_document l left join rise_documents d on l.document_id = d.id where l.leave_id = $data->id")->getRow();

        //checking the user permissiton to show/hide reject and approve button
        $actions= '';
        $role = $this->get_user_role();
        $can_approve_leaves = $role != 'Employee';

        $actions .= modal_anchor(get_uri("leaves/application_details"), "<i data-feather='$option_icon' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('application_details'), "data-post-id" => $data->id));

        $can_manage_application = false;
        if ($this->access_type === "own_section" || $this->access_type === "all" && $can_approve_leaves) {
            $can_manage_application = true;
            // $actions .= modal_anchor(get_uri("clients/application_details"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_client'), "data-post-id" => $data->id))
            
        } else if (array_search($data->applicant_id, $this->allowed_members) && $data->applicant_id !== $this->login_user->id && ($can_approve_leaves)) {
            $can_manage_application = true;
        }

        $leave_types = $meta_info->leave_type_title === "Fassax Shaqo" || $meta_info->leave_type_title === "Fasax Caafimaad" || $meta_info->leave_type_title === "Fasax Waxbarasho"; 
        $flight_included = $meta_info->flight_included === "1";

        // ------------- Developer ---------------------------- Feysal --------------------------------- Abdirizack ------------------------ Info -----------
        $loginuser = $this->login_user->id === "1" || $this->login_user->id === "2621" || $this->login_user->id === "2552" || $this->login_user->id === "1547";

        if ($this->can_delete_leave_application() && $can_manage_application && $can_approve_leaves) {
            $webUrl = empty($doc) ? '' : $doc->webUrl;
            if($leave_types && $flight_included && $meta_info->status === "approved" && !empty($webUrl) && $loginuser){
                $actions .= "<a href='$webUrl' class='btn btn-success' target='_blank' title='Open Document' style='background: #1cc976;color: white'><i data-feather='eye' class='icon-16'></i>";
            }   
            $actions .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("leaves/delete"), "data-action" => "delete-confirmation"));
        }

        return array(
            $data->id,
            //$data->dp_name,
            get_team_member_profile_link($data->applicant_id, $meta_info->applicant_meta),
            $meta_info->leave_type_meta,
            $meta_info->date_meta,
            // $meta_info->duration_meta,
            // $meta_info->unit_name,
            $meta_info->section_name,
            $meta_info->dp_name,
            $meta_info->status_meta,
            $actions
        );
    }

    // prepare a row of leave application list table
    private function _make_row_for_summary($data) {
        $meta_info = $this->_prepare_leave_info($data);

        return array(
            get_team_member_profile_link($data->applicant_id, $meta_info->applicant_meta),
            $meta_info->leave_type_meta,
            $meta_info->duration_meta
        );
    }

    //return required style/format for a application
    private function _prepare_leave_info($data) {
        $image_url = get_avatar($data->applicant_avatar);
        $data->applicant_meta = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span>" . $data->applicant_name;
        $style = '';

        if (isset($data->status)) {
            if ($data->status === "pending") {
                $status_class = "bg-warning";
            } else if ($data->status === "approved") {
                $status_class = "badge bg-success";//btn-success
            } else if ($data->status === "active") {
                $status_class = "btn-dark";//btn-success
                $style = "background-color:#a7abbf;";
            } else if ($data->status === "rejected") {
                $status_class = "bg-danger";
            } else {
                $status_class = "bg-dark";
            }
            $data->status_meta = "<span style='$style' class='badge $status_class'>" . app_lang($data->status) . "</span>";
        }

        if (isset($data->start_date)) {
            $date = format_to_date($data->start_date, FALSE);
            if ($data->start_date != $data->end_date) {
                $date = sprintf(app_lang('start_date_to_end_date_format'), format_to_date($data->start_date, FALSE), format_to_date($data->end_date, FALSE));
            }
            $data->date_meta = $date;
        }

        // if (isset($data->start_date)) {
        //     $date = format_to_date($data->start_date, FALSE);
        //     if ($data->start_date != $data->end_date) {
        //         $date = sprintf('%s to %s', format_to_date($data->start_date, FALSE), format_to_date($data->end_date, FALSE));
        //     }
        //     $data->date_meta = $date;
        // }

        if ($data->total_days > 1) {
            $duration = $data->total_days . " " . app_lang("days");
        } else {
            $duration = $data->total_days . " " . app_lang("day");
        }

        if ($data->total_hours > 1) {
            $duration = $duration . " (" . $data->total_hours . " " . app_lang("hours") . ")";
        } else {
            $duration = $duration . " (" . $data->total_hours . " " . app_lang("hour") . ")";
        }
        $data->duration_meta = $duration;
        $data->leave_type_meta = "<span style='background-color:" . $data->leave_type_color . "' class='color-tag float-start'></span>" . $data->leave_type_title;
        return $data;
    }

    // reaturn a row of leave application list table
    function application_details() {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        
        $applicaiton_id = $this->request->getPost('id');
        $info = $this->Leave_applications_model->get_details_info($applicaiton_id);
        if (!$info) {
            show_404();
        }



        //checking the user permissiton to show/hide reject and approve button
        $can_manage_application = false;
        if ($this->access_type === "own_section" || $this->access_type === "all") {
            $can_manage_application = true;
        } else if (array_search($info->applicant_id, $this->allowed_members) && $info->applicant_id !== $this->login_user->id) {
            $can_manage_application = true;
        }

        $role = $this->get_user_role();
        $view_data['show_approve_reject'] = $role === 'admin' || $role === 'HRM' || $role === 'Director' || $role === 'Section Head'|| $role === 'Administrator';

        //has permission to manage the appliation? or is it own application?
        if (!$can_manage_application && $info->applicant_id !== $this->login_user->id) {
            app_redirect("forbidden");
        }

        $view_data['leave_info'] = $this->_prepare_leave_info($info);
        $view_data['role']=$role;
        return $this->template->view("leaves/application_details", $view_data);
    }

    public function approve_nolosto($id){
        if(!$id){
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred').', Incorrect ID'));
        }

       $res = $this->db->query("update rise_leave_applications set nolo_status = 1 where id = $id");

       if($res){
            echo json_encode(array("success" => true, 'message' => 'Successfully approved.'));
        } else {
            echo json_encode(array("success" => false, 'message' => 'Data not saved, contact support.'));
        }

    }

   

    //    delete a leave application

    function delete() {

        $id = $this->request->getPost('id');

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        if (!$this->can_delete_leave_application()) {
            app_redirect("forbidden");
        }

        $applicatoin_info = $this->Leave_applications_model->get_one($id);
        $this->access_only_allowed_members($applicatoin_info->applicant_id);

        if ($this->Leave_applications_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    //view leave list of login user
    function leave_info() {
        $this->check_module_availability("module_leave");

        $view_data['applicant_id'] = $this->login_user->id;
        if ($this->request->isAJAX()) {
            return $this->template->view("team_members/leave_info", $view_data);
        } else {
            $view_data['page_type'] = "full";
            return $this->template->rander("team_members/leave_info", $view_data);
        }
    }

    //summary dropdown list of team members

    private function _get_members_dropdown_list_for_filter() {

        if ($this->access_type === "all") {
            $where = array("user_type" => "staff");
        } else {
            if (!count($this->allowed_members)) {
                $where = array("user_type" => "nothing");
            } else {
                $allowed_members = $this->allowed_members;
                $allowed_members[] = $this->login_user->id;

                $where = array("user_type" => "staff", "where_in" => array("id" => $allowed_members));
            }
        }

        $members = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", $where);

        $members_dropdown = array(array("id" => "", "text" => "- " . app_lang("team_member") . " -"));
        foreach ($members as $id => $name) {
            $members_dropdown[] = array("id" => $id, "text" => $name);
        }
        return $members_dropdown;
    }

    //summary dropdown list of leave type 

    private function _get_leave_types_dropdown_list_for_filter() {

        $leave_type = $this->Leave_types_model->get_dropdown_list(array("title"), "id", array("status" => "active"));

        $leave_type_dropdown = array(array("id" => "", "text" => "- " . app_lang("leave_type") . " -"));
        foreach ($leave_type as $id => $name) {
            $leave_type_dropdown[] = array("id" => $id, "text" => $name);
        }
        return $leave_type_dropdown;
    }

    /* upload a file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for leaves */

    function validate_leaves_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    function file_preview($id = "", $key = "") {
        if ($id) {
            validate_numeric_value($id);
            $leave_info = $this->Leave_applications_model->get_one($id);
            $files = unserialize($leave_info->files);
            $file = get_array_value($files, $key);

            $file_name = get_array_value($file, "file_name");
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");

            $view_data["file_url"] = get_source_url_of_file($file, get_setting("timeline_file_path"));
            $view_data["is_image_file"] = is_image_file($file_name);
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_name);
            $view_data["is_google_drive_file"] = ($file_id && $service_type == "google") ? true : false;
            $view_data["is_iframe_preview_available"] = is_iframe_preview_available($file_name);

            return $this->template->view("leaves/file_preview", $view_data);
        } else {
            show_404();
        }
    }

    function import_leaves_modal_form() {
        $this->access_only_allowed_members();

        return $this->template->view("leaves/import_leaves_modal_form");
    }

    function download_sample_excel_file() {
        $this->access_only_allowed_members();
        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-leaves-sample.xlsx"))));
    }

    function upload_excel_file() {
        $this->access_only_allowed_members();
        upload_file_to_temp(true);
    }

    function validate_import_leaves_file() {
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

    function save_leave_from_excel_file() {
        $this->access_only_allowed_members();

        if (!$this->validate_import_leaves_file_data(true)) {
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
            if ($key === 0) { //first line is headers, continue to the next loop
                continue;
            }

            $leave_data_array = $this->_prepare_leave_data($value, $allowed_headers);
            $leave_data = get_array_value($leave_data_array, "leave_data");

            //couldn't prepare valid data
            if (!($leave_data && count($leave_data))) {
                continue;
            }

            $leave_data["created_at"] = $now;
            $leave_data["created_by"] = $this->login_user->id;

            //save leave data
            $leave_save_id = $this->Leave_applications_model->ci_save($leave_data);
            if (!$leave_save_id) {
                continue;
            }
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _get_applicant_id($applicant = "") {
        $applicant = trim($applicant);
        if (!$applicant) {
            return false;
        }

        $existing_user = $this->Users_model->get_user_from_full_name($applicant, "staff");
        if ($existing_user) {
            return $existing_user->id;
        } else {
            return false;
        }
    }

    private function _get_leave_type_id($leave_type = "") {
        if (!$leave_type) {
            return false;
        }

        $existing_leave_type = $this->Leave_types_model->get_one_where(array("title" => $leave_type, "deleted" => 0));
        if ($existing_leave_type->id) {
            //leave leave_type exists, add the leave_type id
            return $existing_leave_type->id;
        } else {
            //leave leave_type doesn't exists, create a new one and add leave_type id
            $leave_type_data = array("title" => $leave_type, "color" => "#83c340");
            return $this->Leave_types_model->ci_save($leave_type_data);
        }
    }

    private function _get_allowed_headers() {
        return array(
            "applicant",
            "leave_type",
            "start_date",
            "end_date",
            "total_hours",
            "total_days",
            "reason",
            "status"
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

    function validate_import_leaves_file_data($check_on_submit = false) {
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

        //all field is required
        if ($header_value && !$data) {
            return sprintf(app_lang("import_error_field_required"), app_lang($header_value));
        }

        //check dates
        if (($header_value == "start_date" || $header_value == "end_date") && !$this->_check_valid_date($data)) {
            return app_lang("import_date_error_message");
        }

        //check user names
        if ($header_value == "applicant" && !$this->_get_applicant_id($data)) {
            return sprintf(app_lang("import_error_field_required"), app_lang($header_value));
        }

        //check valid statuses
        $valid_statuses = array('pending', 'approved', 'rejected', 'canceled');
        if ($header_value == "status" && !in_array(strtolower($data), $valid_statuses)) {
            $status_error = "";
            foreach ($valid_statuses as $valid_status) {
                if ($status_error) {
                    $status_error .= ", ";
                }
                $status_error .= ucfirst($valid_status);
            }

            return app_lang("import_leave_status_error_message") . $status_error . ".";
        }
    }

    private function _prepare_leave_data($data_row, $allowed_headers) {
        //prepare leave data
        $leave_data = array();

        foreach ($data_row as $row_data_key => $row_data_value) { //row values
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if ($header_key_value == "applicant") {
                $leave_data["applicant_id"] = $this->_get_applicant_id($row_data_value);
            } else if ($header_key_value == "leave_type") { //we've to make leave type data differently
                $leave_data["leave_type_id"] = $this->_get_leave_type_id($row_data_value);
            } else if ($header_key_value == "start_date") {
                $leave_data["start_date"] = $this->_check_valid_date($row_data_value);
            } else if ($header_key_value == "end_date") {
                $leave_data["end_date"] = $this->_check_valid_date($row_data_value);
            } else if ($header_key_value == "status") {
                $leave_data["status"] = strtolower($row_data_value);
            } else {
                $leave_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "leave_data" => $leave_data
        );
    }

}

/* End of file leaves.php */
    /* Location: ./app/controllers/leaves.php */    