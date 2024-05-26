<?php

namespace App\Controllers;

use ParagonIE\Sodium\Core\Curve25519\H;

class Signin extends App_Controller
{

    private $signin_validation_errors;

    public function __construct()
    {
        parent::__construct();
        $this->signin_validation_errors = array();
        helper('email');
    }

    public function index()
    {
        if ($this->Users_model->login_user_id()) {
            app_redirect('dashboard/view');
        } else {

            $view_data["redirect"] = "";
            if (isset($_REQUEST["redirect"])) {
                $view_data["redirect"] = $_REQUEST["redirect"];
            }

            // die('si');

            return $this->template->view('signin/index', $view_data);
        }
    }

    public function signin_password()
    {
        if ($this->Users_model->login_user_id()) {
            app_redirect('dashboard/view');
        } else {

            $view_data["redirect"] = "";
            if (isset($_REQUEST["redirect"])) {
                $view_data["redirect"] = $_REQUEST["redirect"];
            }

            $email = $this->request->getGet("email");
// die($email);
            if(!$email){
                array_push($this->signin_validation_errors, 'Login failed, email is required');
                $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
                app_redirect('signin');
            }

            $view_data['email'] = $email;

            return $this->template->view('signin/login_password_page', $view_data);
        }
    }

    public function authenticate_local() {

        $email = $this->request->getPost("email");
        $password = $this->request->getPost("password");

        if(!$email){
            array_push($this->signin_validation_errors, 'Login failed, email is required');
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        //login user with credentials
        if (!$this->Users_model->authenticate($email, $password)) {
            //authentication failed
            array_push($this->signin_validation_errors, app_lang("authentication_failed").', email or password is incorrect');
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        //authentication success
        $redirect = $this->request->getPost("redirect");
        if ($redirect) {
            return redirect()->to($redirect);
        } else {
            app_redirect('dashboard/view');
        }
    }

    private function has_recaptcha_error()
    {
        $recaptcha_post_data = $this->request->getPost("g-recaptcha-response");
        $response = $this->is_valid_recaptcha($recaptcha_post_data);

        if ($response === true) {
            return true;
        } else {
            array_push($this->signin_validation_errors, app_lang("re_captcha_error-" . $response));
            return false;
        }
    }

    private function is_valid_recaptcha($recaptcha_post_data)
    {
        //load recaptcha lib
        require_once APPPATH . "ThirdParty/recaptcha/autoload.php";
        $recaptcha = new \ReCaptcha\ReCaptcha(get_setting("re_captcha_secret_key"));
        $resp = $recaptcha->verify($recaptcha_post_data, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess()) {
            return true;
        } else {

            $error = "";
            foreach ($resp->getErrorCodes() as $code) {
                $error = $code;
            }

            return $error;
        }
    }

    // check authentication
    public function authenticate()
    {
        $validation = $this->validate_submitted_data(array(
            "email" => "required|valid_email",
            // "password" => "required",
        ), true);

        $email = $this->request->getPost("email");

        // die($password);

        if (!$email) {
            //loaded the page directly
            app_redirect('signin');
        }

        if (is_array($validation)) {
            //has validation errors
            $this->signin_validation_errors = $validation;
        }

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {
            //in this function, if any error found in recaptcha, that will be added
            $this->has_recaptcha_error();
        }

        //don't check password if there is any error
        if ($this->signin_validation_errors) {
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        // get login type for user:
        $user = $this->db->query("SELECT * from rise_users where  email = '$email' limit 1")->getRow();

        if($user){
            $login_type = $user->login_type;
        }else{
            array_push($this->signin_validation_errors, app_lang("authentication_failed").", User with email $email not found.");
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }

        if($login_type == 'azure_login'){
            //redirects user to make azure login
            $this->aad_signin($email);
        }else{
            // local user login redirect to password page
            app_redirect('signin/signin_password?email='.$email);           
            
        }

    }

    /**
     * authenticate azure active directory
     * to insure user make login throgh microsoft
     * then microsoft redirects to aad_callback function in signin controller
     */
    public function aad_signin($email)
    {
        $appid = getenv('AZURE_APP_ID'); //"a70c275e-7713-46eb-8a09-6d5a7c3b823d";

        $tennantid = getenv('AZURE_TENANT_ID'); //"695822cd-3aaa-446d-aac2-3ebb02854b8a";

        $secret = getenv('AZURE_SECRET_ID'); //"e54c00ad-6cfd-4113-b46f-5a3de239d13b";
        $env = getenv('ENVIRONMENT'); //ENVIRONMENT

        $login_url = "https://login.microsoftonline.com/" . $tennantid . "/oauth2/v2.0/authorize";//authorize
        \Config\Services::session_destroy();
           
        $session = \Config\Services::session();
       
        $session->set('state', session_id());

        $params = array(
            'client_id' => $appid,
            'redirect_uri' => $env == 'development' ? 'https://localhost/emof/signin/aad_callback' : 'https://workspace.revenuedirectorate.gov.so/signin/aad_callback',
            // 'response_type' => 'code',
            'response_type' => 'token',
            // 'response_mode' => 'form_post',
            'login_hint' => $email, //'admin@presidency@gov.so',
            // 'prompt'=>'consent',
            'scope' => 'https://graph.microsoft.com/User.Read', //User.Read

            'state' => $session->get('state'));
            // die( $login_url . '?' . http_build_query($params));
        // die($login_url . '?' . http_build_query($params));
        header('Location: ' . $login_url . '?' . http_build_query($params));
        exit();

    }

    public function aad_callback()
    {
        // replace # with ?
// die('hesre');
        echo '
            <script>
                        
                url = window.location.href;

                i=url.indexOf("#");

                if(i>0) {

                url=url.replace("#","?");

                window.location.href=url;
            }

            </script>

             ';

          // echo array_key_exists('access_token', $_GET);
        //    var_dump($_GET);
        //    die();    
        // sleep(1);
           $token_exists = array_key_exists('access_token', $_GET);

        if ($token_exists == 1 && $token_exists == true) {

            $this->session->set('aad_token', $_GET['access_token']);

            $t = $this->session->get('aad_token');
            // die('token: '.$t);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $t,

                'Conent-type: application/json'));

            curl_setopt($ch, CURLOPT_URL, "https://graph.microsoft.com/v1.0/me");

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $rez = json_decode(curl_exec($ch), 1);

            curl_close($ch);
            // echo 'result=' . var_dump($rez);
            // die();

            if (array_key_exists('error', $rez)) {

                var_dump($rez['error']);

                die('error=' . $rez['error']);
            } else {
                $email = $rez['userPrincipalName'];
                // var_dump($rez);
                // die('email: '.$email);
                if ($this->Users_model->authenticateAAD($email)) {

                    //authentication success
                    $redirect = $this->request->getPost("redirect");
                    if ($redirect) {
                        return redirect()->to($redirect);
                    } else {
                        app_redirect('dashboard/view');
                    }
                } else {

                    // die('Get: inside ' . var_dump($_GET));

                    //user with email not found ie. authentication failed
                    array_push($this->signin_validation_errors, app_lang("authentication_failed") . ', User Not Found');
                    $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
                    app_redirect('signin');
                }
            }
        } else {

            die();
            //AAD authentication failed
            array_push($this->signin_validation_errors, app_lang("authentication_failed") . ', No access token');
            $this->session->setFlashdata("signin_validation_errors", $this->signin_validation_errors);
            app_redirect('signin');
        }
    }

    public function aad_signout()
    {
        dd('signout');
    }

    public function sign_out()
    {
        $this->Users_model->sign_out();
    }

    //send an email to users mail with reset password link
    public function send_reset_password_mail()
    {
        $this->validate_submitted_data(array(
            "email" => "required|valid_email",
        ));

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {

            $response = $this->is_valid_recaptcha($this->request->getPost("g-recaptcha-response"));

            if ($response !== true) {

                if ($response) {
                    echo json_encode(array('success' => false, 'message' => app_lang("re_captcha_error-" . $response)));
                } else {
                    echo json_encode(array('success' => false, 'message' => app_lang("re_captcha_expired")));
                }

                return false;
            }
        }

        $email = $this->request->getPost("email");

        $existing_user = $this->Users_model->is_email_exists($email);

        //send reset password email if found account with this email
        if ($existing_user) {
            $email_template = $this->Email_templates_model->get_final_template("reset_password", true);

            $user_language = $existing_user->language;
            $parser_data["ACCOUNT_HOLDER_NAME"] = $existing_user->first_name . " " . $existing_user->last_name;
            $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");
            $parser_data["LOGO_URL"] = get_logo_url();
            $parser_data["SITE_URL"] = get_uri();
            $parser_data["RECIPIENTS_EMAIL_ADDRESS"] = $existing_user->email;

            $verification_data = array(
                "type" => "reset_password",
                "code" => make_random_string(),
                "params" => serialize(array(
                    "email" => $existing_user->email,
                    "expire_time" => time() + (24 * 60 * 60),
                )),
            );

            $save_id = $this->Verification_model->ci_save($verification_data);

            $verification_info = $this->Verification_model->get_one($save_id);

            $parser_data['RESET_PASSWORD_URL'] = get_uri("signin/new_password/" . $verification_info->code);

            $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
            $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");

            $message = $this->parser->setData($parser_data)->renderString($message);
            $subject = $this->parser->setData($parser_data)->renderString($subject);

            if (send_app_mail($email, $subject, $message)) {
                echo json_encode(array('success' => true, 'message' => app_lang("reset_info_send")));
            } else {
                echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("no_acount_found_with_this_email")));
            return false;
        }
    }

    //show forgot password recovery form
    public function request_reset_password()
    {
        $view_data["form_type"] = "request_reset_password";
        return $this->template->view('signin/index', $view_data);
    }

    //when user clicks to reset password link from his/her email, redirect to this url
    public function new_password($key)
    {
        $valid_key = $this->is_valid_reset_password_key($key);

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");

            if ($this->Users_model->is_email_exists($email)) {
                $view_data["key"] = clean_data($key);
                $view_data["form_type"] = "new_password";
                return $this->template->view('signin/index', $view_data);
            }
        }

        //else show error
        $view_data["heading"] = "Invalid Request";
        $view_data["message"] = "The key has expaired or something went wrong!";
        return $this->template->view("errors/html/error_general", $view_data);
    }

    //finally reset the old password and save the new password
    public function do_reset_password()
    {
        $this->validate_submitted_data(array(
            "key" => "required",
            "password" => "required",
        ));

        $key = $this->request->getPost("key");
        $password = $this->request->getPost("password");
        $valid_key = $this->is_valid_reset_password_key($key);

        if ($valid_key) {
            $email = get_array_value($valid_key, "email");
            $this->Users_model->update_password($email, password_hash($password, PASSWORD_DEFAULT));

            //user can't reset password two times with the same code
            $options = array("code" => $key, "type" => "reset_password");
            $verification_info = $this->Verification_model->get_details($options)->getRow();
            if ($verification_info->id) {
                $this->Verification_model->delete_permanently($verification_info->id);
            }

            echo json_encode(array("success" => true, 'message' => app_lang("password_reset_successfully") . " " . anchor("signin", app_lang("signin"))));
            return true;
        }

        echo json_encode(array("success" => false, 'message' => app_lang("error_occurred")));
    }

    //check valid key
    private function is_valid_reset_password_key($verification_code = "")
    {

        if ($verification_code) {
            $options = array("code" => $verification_code, "type" => "reset_password");
            $verification_info = $this->Verification_model->get_details($options)->getRow();

            if ($verification_info && $verification_info->id) {
                $reset_password_info = unserialize($verification_info->params);

                $email = get_array_value($reset_password_info, "email");
                $expire_time = get_array_value($reset_password_info, "expire_time");

                if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) && $expire_time && $expire_time > time()) {
                    return array("email" => $email);
                }
            }
        }
    }
}
