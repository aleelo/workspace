<div class="card bg-white mb15">
    <div class="card-header text-center">
        <?php if (get_setting("show_logo_in_signin_page") === "yes") { ?>
            <img class="p20 mw100p" src="<?php echo get_logo_url(); ?>" />
        <?php } else { ?>
            <img src="<?php echo base_url().'assets/images/logo.png'; ?>" alt="">
        <?php } ?>
    </div>
    <div class="card-body p30 rounded-bottom">
        
    <div class="form-group">
      
        <?php echo form_open("signin/authenticate", array("id" => "signin-form", "class" => "general-form", "role" => "form")); ?>
        
        <?php
        $session = \Config\Services::session();
        $signin_validation_errors = $session->getFlashdata("signin_validation_errors");
        if ($signin_validation_errors && is_array($signin_validation_errors)) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($signin_validation_errors as $validation_error) { ?>
                    <i data-feather="alert-circle" class="icon-16"></i>
                    <?php echo $validation_error; ?>
                    <br />
                <?php } ?>
            </div>
        <?php } ?>
        
        <div class="w-100 mb-3 d-flex p-3">
            <div class="w-45" style="height: 1px;background: #bec3d0;width: 35%;"></div>
            <div class="mx-3  fs-5 d-flex fs-3" style="margin-top: -15px;">
                
                Login
            </div>
            <div class="w-45 " style="height: 1px;background: #bec3d0;width: 35%;"></div>
        </div>
           
        <!-- <div class="form-group">
            <label for="login_type">Login Type</label>
            <?php
            // echo form_dropdown(array(
            //     "id" => "login_type",
            //     "name" => "login_type",
            //     "class" => "form-control",
            //     "data-rule-required" => true,
            //     "data-msg-required" => app_lang("field_required")
            // ),['Azure Login' => 'Azure Login','Normal Login'=>'Normal Login'],['Azure Login' => isset($_GET['login_type']) ? $_GET['login_type'] : 'Azure Login']);
            ?>
        </div>
        <hr> -->
        <div class="form-group">
            <!-- or line -->                    
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "class" => "form-control p10",
                "placeholder" => 'Enter Microsoft Email', //app_lang('email')
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
                "data-rule-email" => true,
                "data-msg-email" => app_lang("enter_valid_email")
            ));
            ?>
        </div>
       
        <input type="hidden" name="redirect" value="<?php
        if (isset($redirect)) {
            echo $redirect;
        }
        ?>" />


        <?php echo view("signin/re_captcha"); ?>
        
        <button type = "submit" name="azure-login-btn" id="azure-login-btn" class="btn btn-primary btn-lg w-100 mb-4">
                <svg width="30" height="30" class="" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="e399c19f-b68f-429d-b176-18c2117ff73c" x1="-1032.172" x2="-1059.213" y1="145.312" y2="65.426" gradientTransform="matrix(1 0 0 -1 1075 158)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#114a8b"></stop>
                                <stop offset="1" stop-color="#0669bc"></stop>
                            </linearGradient>
                            <linearGradient id="ac2a6fc2-ca48-4327-9a3c-d4dcc3256e15" x1="-1023.725" x2="-1029.98" y1="108.083" y2="105.968" gradientTransform="matrix(1 0 0 -1 1075 158)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-opacity=".3"></stop>
                                <stop offset=".071" stop-opacity=".2"></stop>
                                <stop offset=".321" stop-opacity=".1"></stop>
                                <stop offset=".623" stop-opacity=".05"></stop>
                                <stop offset="1" stop-opacity="0"></stop>
                            </linearGradient>
                            <linearGradient id="a7fee970-a784-4bb1-af8d-63d18e5f7db9" x1="-1027.165" x2="-997.482" y1="147.642" y2="68.561" gradientTransform="matrix(1 0 0 -1 1075 158)" gradientUnits="userSpaceOnUse">
                                <stop offset="0" stop-color="#3ccbf4"></stop>
                                <stop offset="1" stop-color="#2892df"></stop>
                            </linearGradient>
                        </defs>
                        <path fill="url(#e399c19f-b68f-429d-b176-18c2117ff73c)" d="M33.338 6.544h26.038l-27.03 80.087a4.152 4.152 0 0 1-3.933 2.824H8.149a4.145 4.145 0 0 1-3.928-5.47L29.404 9.368a4.152 4.152 0 0 1 3.934-2.825z"></path>
                        <path fill="#0078d4" d="M71.175 60.261h-41.29a1.911 1.911 0 0 0-1.305 3.309l26.532 24.764a4.171 4.171 0 0 0 2.846 1.121h23.38z"></path>
                        <path fill="url(#ac2a6fc2-ca48-4327-9a3c-d4dcc3256e15)" d="M33.338 6.544a4.118 4.118 0 0 0-3.943 2.879L4.252 83.917a4.14 4.14 0 0 0 3.908 5.538h20.787a4.443 4.443 0 0 0 3.41-2.9l5.014-14.777 17.91 16.705a4.237 4.237 0 0 0 2.666.972H81.24L71.024 60.261l-29.781.007L59.47 6.544z"></path>
                        <path fill="url(#a7fee970-a784-4bb1-af8d-63d18e5f7db9)" d="M66.595 9.364a4.145 4.145 0 0 0-3.928-2.82H33.648a4.146 4.146 0 0 1 3.928 2.82l25.184 74.62a4.146 4.146 0 0 1-3.928 5.472h29.02a4.146 4.146 0 0 0 3.927-5.472z"></path>
                </svg>
                Continue
            </button>

          
        <!-- <button class="w-100 btn btn-lg btn-primary" type="submit">

            <?php //echo app_lang('signin'); ?>
        </button> -->

        <?php echo form_close(); ?>

        <div class="mt5"><?php echo anchor("signin/request_reset_password", app_lang("forgot_password")); ?></div>

        <?php if (!get_setting("disable_client_signup")) { ?>
            <!-- <div class="mt20"><?php //echo app_lang("you_dont_have_an_account") ?> &nbsp; <?php //echo anchor("signup", app_lang("signup")); ?></div> -->
        <?php } ?>

        <?php
        app_hooks()->do_action('app_hook_signin_extension');
        ?>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        // function get_login_defaults() {
        //     if($('#login_type').val() == 'Azure Login'){
        //         $('#password').hide();
        //         $('#password').val('aleelo');
        //         $('#email').attr('placeholder','Enter Microsoft Email');
        //         $('#azure-login-btn').show();
        //         $('#normal-login-btn').hide();

        //     }else{
        //         $('#password').show();
        //         $('#password').val('');
        //         $('#email').attr('placeholder','Enter Email');
        //         $('#normal-login-btn').show();
        //         $('#azure-login-btn').hide();

        //     }
        // }
        // get_login_defaults();
        // $('#login_type').on('change', function(){
        //     get_login_defaults();
        // });

        $("#signin-form").appForm({ajaxSubmit: false, isModal: false});
    });
</script>    