<?php load_js(array("assets/js/push_notification/pusher/pusher.min.js")); ?>

<?php $user = $login_user->id; ?>

<nav class="navbar navbar-expand fixed-top navbar-light navbar-custom shadow-sm" role="navigation" id="default-navbar">
    <div class="container-fluid">
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link sidebar-toggle-btn" aria-current="page" href="#">
                        <i data-feather="menu" class="icon"></i>
                    </a>
                </li>

                <?php
                //get the array of hidden topbar menus
                $hidden_topbar_menus = explode(",", get_setting("user_" . $user . "_hidden_topbar_menus"));

                if (!in_array("to_do", $hidden_topbar_menus)) {
                    echo view("todo/topbar_icon");
                }
                if (!in_array("favorite_projects", $hidden_topbar_menus) && !(get_setting("disable_access_favorite_project_option_for_clients") && $login_user->user_type == "client") && !($login_user->user_type == "staff" && get_array_value($login_user->permissions, "do_not_show_projects"))) {
                    echo view("projects/star/topbar_icon");
                }
                if (!in_array("favorite_clients", $hidden_topbar_menus)) {
                    echo view("clients/star/topbar_icon");
                }
                if (!in_array("dashboard_customization", $hidden_topbar_menus) && (get_setting("disable_new_dashboard_icon") != 1)) {
                    echo view("dashboards/list/topbar_icon");
                }
                ?>

                <?php
                if (has_my_open_timers()) {
                    echo view("projects/open_timers_topbar_icon");
                }

                if ($login_user->user_type === "client") {
                    show_clients_of_this_client_contact($login_user);
                }

                $role = get_user_role();
                $dept_id = get_user_department_id();
                ?>

               
            <li class="nav-item">
                    <a class="nav-link" title="More on Office 365" href="https://www.office.com/?auth=2" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                        <radialGradient id="yRNsYj0k48m_5059Aqtv_a_g7UKWvv49CoI_gr1" cx="-1207.054" cy="544.406" r=".939" gradientTransform="matrix(-11.7002 31.247 54.5012 20.4075 -43776.117 26617.47)" gradientUnits="userSpaceOnUse"><stop offset=".064" stop-color="#bf8af6"></stop><stop offset=".533" stop-color="#3079d6"></stop><stop offset="1" stop-color="#11408c"></stop></radialGradient><path fill="url(#yRNsYj0k48m_5059Aqtv_a_g7UKWvv49CoI_gr1)" d="M20.084,3.026L19.86,3.162c-0.357,0.216-0.694,0.458-1.008,0.722l0.648-0.456H25L26,11l-5,5	l-5,3.475v4.007c0,2.799,1.463,5.394,3.857,6.844l5.264,3.186L14,40h-2.145l-3.998-2.42C5.463,36.131,4,33.535,4,30.736V17.261	c0-2.8,1.464-5.396,3.86-6.845l12-7.259C19.934,3.112,20.009,3.068,20.084,3.026z"></path><radialGradient id="yRNsYj0k48m_5059Aqtv_b_g7UKWvv49CoI_gr2" cx="-1152.461" cy="523.628" r="1" gradientTransform="matrix(30.7198 -4.5183 -2.9847 -20.2925 36976.637 5454.876)" gradientUnits="userSpaceOnUse"><stop offset=".211" stop-color="#bf8af6"></stop><stop offset="1" stop-color="#591c96"></stop></radialGradient><path fill="url(#yRNsYj0k48m_5059Aqtv_b_g7UKWvv49CoI_gr2)" d="M32,19v4.48c0,2.799-1.463,5.394-3.857,6.844l-12,7.264c-2.455,1.486-5.509,1.54-8.007,0.161	l11.722,7.095c2.547,1.542,5.739,1.542,8.285,0l12-7.264C42.537,36.131,44,33.535,44,30.736V27.5L43,26L32,19z"></path><radialGradient id="yRNsYj0k48m_5059Aqtv_c_g7UKWvv49CoI_gr3" cx="-1236.079" cy="516.112" r="1.19" gradientTransform="matrix(-24.1583 -6.1256 -10.3118 40.6682 -24498.48 -28534.523)" gradientUnits="userSpaceOnUse"><stop offset=".059" stop-color="#50e6ff"></stop><stop offset=".68" stop-color="#3079d6"></stop><stop offset="1" stop-color="#11408c"></stop></radialGradient><path fill="url(#yRNsYj0k48m_5059Aqtv_c_g7UKWvv49CoI_gr3)" d="M40.14,10.415l-12-7.259c-2.467-1.492-5.538-1.538-8.043-0.139L19.86,3.162	C17.464,4.611,16,7.208,16,10.007v9.484l3.86-2.335c2.546-1.54,5.735-1.54,8.281,0l12,7.259c2.321,1.404,3.767,3.884,3.855,6.583	C43.999,30.911,44,30.824,44,30.736V17.26C44,14.461,42.536,11.864,40.14,10.415z"></path>
                    </svg>
                    </a>
                </li>
                               
                <li class="nav-item" >
                    <a class="nav-link"  title="Outlook" href="https://outlook.office.com/mail/" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                            <path fill="#103262" d="M43.255,23.547l-6.81-3.967v11.594H44v-6.331C44,24.309,43.716,23.816,43.255,23.547z"></path><path fill="#0084d7" d="M13,10h10v9H13V10z"></path><path fill="#33afec" d="M23,10h10v9H23V10z"></path><path fill="#54daff" d="M33,10h10v9H33V10z"></path><path fill="#027ad4" d="M23,19h10v9H23V19z"></path><path fill="#0553a4" d="M23,28h10v9H23V28z"></path><path fill="#25a2e5" d="M33,19h10v9H33V19z"></path><path fill="#0262b8" d="M33,28h10v9H33V28z"></path><polygon points="13,37 43,37 43,24.238 28.99,32.238 13,24.238" opacity=".019"></polygon><polygon points="13,37 43,37 43,24.476 28.99,32.476 13,24.476" opacity=".038"></polygon><polygon points="13,37 43,37 43,24.714 28.99,32.714 13,24.714" opacity=".057"></polygon><polygon points="13,37 43,37 43,24.952 28.99,32.952 13,24.952" opacity=".076"></polygon><polygon points="13,37 43,37 43,25.19 28.99,33.19 13,25.19" opacity=".095"></polygon><polygon points="13,37 43,37 43,25.429 28.99,33.429 13,25.429" opacity=".114"></polygon><polygon points="13,37 43,37 43,25.667 28.99,33.667 13,25.667" opacity=".133"></polygon><polygon points="13,37 43,37 43,25.905 28.99,33.905 13,25.905" opacity=".152"></polygon><polygon points="13,37 43,37 43,26.143 28.99,34.143 13,26.143" opacity=".171"></polygon><polygon points="13,37 43,37 43,26.381 28.99,34.381 13,26.381" opacity=".191"></polygon><polygon points="13,37 43,37 43,26.619 28.99,34.619 13,26.619" opacity=".209"></polygon><polygon points="13,37 43,37 43,26.857 28.99,34.857 13,26.857" opacity=".229"></polygon><polygon points="13,37 43,37 43,27.095 28.99,35.095 13,27.095" opacity=".248"></polygon><polygon points="13,37 43,37 43,27.333 28.99,35.333 13,27.333" opacity=".267"></polygon><polygon points="13,37 43,37 43,27.571 28.99,35.571 13,27.571" opacity=".286"></polygon><polygon points="13,37 43,37 43,27.81 28.99,35.81 13,27.81" opacity=".305"></polygon><polygon points="13,37 43,37 43,28.048 28.99,36.048 13,28.048" opacity=".324"></polygon><polygon points="13,37 43,37 43,28.286 28.99,36.286 13,28.286" opacity=".343"></polygon><polygon points="13,37 43,37 43,28.524 28.99,36.524 13,28.524" opacity=".362"></polygon><polygon points="13,37 43,37 43,28.762 28.99,36.762 13,28.762" opacity=".381"></polygon><polygon points="13,37 43,37 43,29 28.99,37 13,29" opacity=".4"></polygon><linearGradient id="Qf7015RosYe_HpjKeG0QTa_ut6gQeo5pNqf_gr1" x1="38.925" x2="32.286" y1="24.557" y2="36.024" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#31abec"></stop><stop offset="1" stop-color="#1582d5"></stop></linearGradient><path fill="url(#Qf7015RosYe_HpjKeG0QTa_ut6gQeo5pNqf_gr1)" d="M15.441,42h26.563c1.104,0,1.999-0.889,2-1.994C44.007,35.485,44,24.843,44,24.843	s-0.007,0.222-1.751,1.212S14.744,41.566,14.744,41.566S14.978,42,15.441,42z"></path><linearGradient id="Qf7015RosYe_HpjKeG0QTb_ut6gQeo5pNqf_gr2" x1="13.665" x2="41.285" y1="6.992" y2="9.074" gradientUnits="userSpaceOnUse"><stop offset=".042" stop-color="#076db4"></stop><stop offset=".85" stop-color="#0461af"></stop></linearGradient><path fill="url(#Qf7015RosYe_HpjKeG0QTb_ut6gQeo5pNqf_gr2)" d="M43,10H13V8c0-1.105,0.895-2,2-2h26c1.105,0,2,0.895,2,2V10z"></path><linearGradient id="Qf7015RosYe_HpjKeG0QTc_ut6gQeo5pNqf_gr3" x1="28.153" x2="23.638" y1="33.218" y2="41.1" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#33acee"></stop><stop offset="1" stop-color="#1b8edf"></stop></linearGradient><path fill="url(#Qf7015RosYe_HpjKeG0QTc_ut6gQeo5pNqf_gr3)" d="M13,25v15c0,1.105,0.895,2,2,2h15h12.004c0.462,0,0.883-0.162,1.221-0.425L13,25z"></path><path d="M21.319,13H13v24h8.319C23.352,37,25,35.352,25,33.319V16.681C25,14.648,23.352,13,21.319,13z" opacity=".05"></path><path d="M21.213,36H13V13.333h8.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C24.333,34.603,22.936,36,21.213,36z" opacity=".07"></path><path d="M21.106,35H13V13.667h8.106c1.414,0,2.56,1.146,2.56,2.56V32.44C23.667,33.854,22.52,35,21.106,35z" opacity=".09"></path><linearGradient id="Qf7015RosYe_HpjKeG0QTd_ut6gQeo5pNqf_gr4" x1="3.53" x2="22.41" y1="14.53" y2="33.41" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#1784d8"></stop><stop offset="1" stop-color="#0864c5"></stop></linearGradient><path fill="url(#Qf7015RosYe_HpjKeG0QTd_ut6gQeo5pNqf_gr4)" d="M21,34H5c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C23,33.105,22.105,34,21,34z"></path><path fill="#fff" d="M13,18.691c-3.111,0-4.985,2.377-4.985,5.309S9.882,29.309,13,29.309	c3.119,0,4.985-2.377,4.985-5.308C17.985,21.068,16.111,18.691,13,18.691z M13,27.517c-1.765,0-2.82-1.574-2.82-3.516	s1.06-3.516,2.82-3.516s2.821,1.575,2.821,3.516S14.764,27.517,13,27.517z"></path>
                        </svg>
                    </a>
                </li>
<!-- 
                <li class="nav-item">
                    <a class="nav-link" href="https://tasks.office.com/presidency.gov.so/en-US/Home/Planner/" title="Planner" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                            <path fill="#33c481" d="M41,8v19c0,1.105-0.895,2-2,2H29V6h10C40.105,6,41,6.895,41,8z"></path><path fill="#107c41" d="M19,17h10v16c0,1.105-0.895,2-2,2h-8V17z"></path><path fill="#185c37" d="M17,42H9c-1.105,0-2-0.895-2-2V24h12v16C19,41.105,18.105,42,17,42z"></path><path fill="#107c41" d="M19,24H7V8c0-1.105,0.895-2,2-2h10V24z"></path><rect width="10" height="11" x="19" y="6" fill="#21a366"></rect>
                        </svg>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" title="Sharepoint" href="https://villasomaliafrs.sharepoint.com/_layouts/15/sharepoint.aspx" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                            <linearGradient id="PtC3nmzn5K~Q855MdLFzFa_bVAf0kiXtJhO_gr1" x1="16.145" x2="26.377" y1="6.428" y2="20.715" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#048489"></stop><stop offset="1" stop-color="#03676a"></stop></linearGradient><path fill="url(#PtC3nmzn5K~Q855MdLFzFa_bVAf0kiXtJhO_gr1)" d="M23,4c-6.627,0-12,5.373-12,12s5.373,12,12,12s12-5.373,12-12S29.627,4,23,4z"></path><linearGradient id="PtC3nmzn5K~Q855MdLFzFb_bVAf0kiXtJhO_gr2" x1="27.122" x2="41.023" y1="18.616" y2="35.799" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#18939a"></stop><stop offset=".41" stop-color="#168c93"></stop><stop offset="1" stop-color="#117981"></stop></linearGradient><path fill="url(#PtC3nmzn5K~Q855MdLFzFb_bVAf0kiXtJhO_gr2)" d="M33.5,16C27.701,16,23,20.701,23,26.5S27.701,37,33.5,37S44,32.299,44,26.5S39.299,16,33.5,16	z"></path><linearGradient id="PtC3nmzn5K~Q855MdLFzFc_bVAf0kiXtJhO_gr3" x1="16" x2="32" y1="36" y2="36" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#37c6d0"></stop><stop offset="1" stop-color="#37c6d0"></stop></linearGradient><path fill="url(#PtC3nmzn5K~Q855MdLFzFc_bVAf0kiXtJhO_gr3)" d="M24,28c-4.418,0-8,3.582-8,8s3.582,8,8,8s8-3.582,8-8S28.418,28,24,28z"></path><path d="M22.319,13H11.393C11.146,13.961,11,14.962,11,16c0,6.627,5.373,12,12,12	c0.04,0,0.078-0.006,0.118-0.006c0.003,0.019,0.006,0.037,0.009,0.056C19.12,28.485,16,31.877,16,36c0,0.339,0.028,0.672,0.069,1	h6.25C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path><path d="M16,36h6.213c1.724,0,3.121-1.397,3.121-3.121V16.454c0-1.724-1.397-3.121-3.121-3.121H11.308	C11.112,14.192,11,15.082,11,16c0,6.627,5.373,12,12,12c0.04,0,0.078-0.006,0.118-0.006c0.003,0.019,0.006,0.037,0.009,0.056	C19.12,28.485,16,31.877,16,36z" opacity=".07"></path><path d="M22.106,13.667H11.231C11.082,14.422,11,15.201,11,16c0,6.627,5.373,12,12,12	c0.04,0,0.078-0.006,0.118-0.006c0.003,0.019,0.006,0.037,0.009,0.056c-3.677,0.4-6.6,3.291-7.057,6.95h6.037	c1.414,0,2.56-1.146,2.56-2.56V16.227C24.667,14.813,23.52,13.667,22.106,13.667z" opacity=".09"></path><linearGradient id="PtC3nmzn5K~Q855MdLFzFd_bVAf0kiXtJhO_gr4" x1="4.586" x2="23.414" y1="14.586" y2="33.414" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#058f92"></stop><stop offset="1" stop-color="#027074"></stop></linearGradient><path fill="url(#PtC3nmzn5K~Q855MdLFzFd_bVAf0kiXtJhO_gr4)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path><path fill="#fff" d="M10.432,28.75v-2.298c0.426,0.349,2.391,1.048,2.9,1.048c0.299,0,2.138,0.088,2.138-1.041	c0-1.633-5.082-1.494-5.082-4.725c0-0.536,0.066-3.059,4.133-3.059c1.041,0,2.271,0.261,2.628,0.395v2.147	c-0.176-0.12-0.89-0.718-2.496-0.718c-1.877,0-2.04,0.883-2.04,1.041c0,1.359,4.998,1.544,4.998,4.711	c0,3.172-3.614,3.074-4.177,3.074C12.857,29.325,10.814,28.942,10.432,28.75z"></path>
                        </svg>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" title="Teams" href="https://teams.microsoft.com/_#/?lm=deeplink&lmsrc=officeWaffle" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                        <path fill="#5059c9" d="M44,22v8c0,3.314-2.686,6-6,6s-6-2.686-6-6V20h10C43.105,20,44,20.895,44,22z M38,16	c2.209,0,4-1.791,4-4c0-2.209-1.791-4-4-4s-4,1.791-4,4C34,14.209,35.791,16,38,16z"></path><path fill="#7b83eb" d="M35,22v11c0,5.743-4.841,10.356-10.666,9.978C19.019,42.634,15,37.983,15,32.657V20h18	C34.105,20,35,20.895,35,22z M25,17c3.314,0,6-2.686,6-6s-2.686-6-6-6s-6,2.686-6,6S21.686,17,25,17z"></path><circle cx="25" cy="11" r="6" fill="#7b83eb"></circle><path d="M26,33.319V20H15v12.657c0,1.534,0.343,3.008,0.944,4.343h6.374C24.352,37,26,35.352,26,33.319z" opacity=".05"></path><path d="M15,20v12.657c0,1.16,0.201,2.284,0.554,3.343h6.658c1.724,0,3.121-1.397,3.121-3.121V20H15z" opacity=".07"></path><path d="M24.667,20H15v12.657c0,0.802,0.101,1.584,0.274,2.343h6.832c1.414,0,2.56-1.146,2.56-2.56V20z" opacity=".09"></path><linearGradient id="DqqEodsTc8fO7iIkpib~Na_zQ92KI7XjZgR_gr1" x1="4.648" x2="23.403" y1="14.648" y2="33.403" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#5961c3"></stop><stop offset="1" stop-color="#3a41ac"></stop></linearGradient><path fill="url(#DqqEodsTc8fO7iIkpib~Na_zQ92KI7XjZgR_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path><path fill="#fff" d="M18.068,18.999H9.932v1.72h3.047v8.28h2.042v-8.28h3.047V18.999z"></path>
                    </svg>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" title="Outlook Calendar" href="https://outlook.office.com/calendar/view/month" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                        <path fill="#1976d2" d="M28,13h14.533C43.343,13,44,13.657,44,14.467v19.066C44,34.343,43.343,35,42.533,35H28V13z"></path><rect width="14" height="15.542" x="28" y="17.958" fill="#fff"></rect><polygon fill="#1976d2" points="27,44 4,39.5 4,8.5 27,4"></polygon><path fill="#fff" d="M15.25,16.5c-3.176,0-5.75,3.358-5.75,7.5s2.574,7.5,5.75,7.5S21,28.142,21,24	S18.426,16.5,15.25,16.5z M15,28.5c-1.657,0-3-2.015-3-4.5s1.343-4.5,3-4.5s3,2.015,3,4.5S16.657,28.5,15,28.5z"></path><rect width="2.7" height="2.9" x="28.047" y="29.737" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="31.448" y="29.737" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="34.849" y="29.737" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="28.047" y="26.159" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="31.448" y="26.159" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="34.849" y="26.159" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="38.25" y="26.159" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="28.047" y="22.706" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="31.448" y="22.706" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="34.849" y="22.706" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="38.25" y="22.706" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="31.448" y="19.112" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="34.849" y="19.112" fill="#1976d2"></rect><rect width="2.7" height="2.9" x="38.25" y="19.112" fill="#1976d2"></rect>
                    </svg>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" title="MS Word" href="https://www.microsoft365.com/launch/word?auth=2" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                        <linearGradient id="Q7XamDf1hnh~bz~vAO7C6a_pGHcje298xSl_gr1" x1="28" x2="28" y1="14.966" y2="6.45" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#42a3f2"></stop><stop offset="1" stop-color="#42a4eb"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6a_pGHcje298xSl_gr1)" d="M42,6H14c-1.105,0-2,0.895-2,2v7.003h32V8C44,6.895,43.105,6,42,6z"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6b_pGHcje298xSl_gr2" x1="28" x2="28" y1="42" y2="33.054" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#11408a"></stop><stop offset="1" stop-color="#103f8f"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6b_pGHcje298xSl_gr2)" d="M12,33.054V40c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2v-6.946H12z"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6c_pGHcje298xSl_gr3" x1="28" x2="28" y1="-15.46" y2="-15.521" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3079d6"></stop><stop offset="1" stop-color="#297cd2"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6c_pGHcje298xSl_gr3)" d="M12,15.003h32v9.002H12V15.003z"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6d_pGHcje298xSl_gr4" x1="12" x2="44" y1="28.53" y2="28.53" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#1d59b3"></stop><stop offset="1" stop-color="#195bbc"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6d_pGHcje298xSl_gr4)" d="M12,24.005h32v9.05H12V24.005z"></path><path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path><path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path><path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path><linearGradient id="Q7XamDf1hnh~bz~vAO7C6e_pGHcje298xSl_gr5" x1="4.744" x2="23.494" y1="14.744" y2="33.493" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#256ac2"></stop><stop offset="1" stop-color="#1247ad"></stop></linearGradient><path fill="url(#Q7XamDf1hnh~bz~vAO7C6e_pGHcje298xSl_gr5)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path><path fill="#fff" d="M18.403,19l-1.546,7.264L15.144,19h-2.187l-1.767,7.489L9.597,19H7.641l2.344,10h2.352l1.713-7.689	L15.764,29h2.251l2.344-10H18.403z"></path>
                    </svg>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" title="MS Excel" href="https://www.microsoft365.com/launch/excel?auth=2" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 48 48" style="width: 30px;height: 30px;">
                        <rect width="16" height="9" x="28" y="15" fill="#21a366"></rect><path fill="#185c37" d="M44,24H12v16c0,1.105,0.895,2,2,2h28c1.105,0,2-0.895,2-2V24z"></path><rect width="16" height="9" x="28" y="24" fill="#107c42"></rect><rect width="16" height="9" x="12" y="15" fill="#3fa071"></rect><path fill="#33c481" d="M42,6H28v9h16V8C44,6.895,43.105,6,42,6z"></path><path fill="#21a366" d="M14,6h14v9H12V8C12,6.895,12.895,6,14,6z"></path><path d="M22.319,13H12v24h10.319C24.352,37,26,35.352,26,33.319V16.681C26,14.648,24.352,13,22.319,13z" opacity=".05"></path><path d="M22.213,36H12V13.333h10.213c1.724,0,3.121,1.397,3.121,3.121v16.425	C25.333,34.603,23.936,36,22.213,36z" opacity=".07"></path><path d="M22.106,35H12V13.667h10.106c1.414,0,2.56,1.146,2.56,2.56V32.44C24.667,33.854,23.52,35,22.106,35z" opacity=".09"></path><linearGradient id="flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1" x1="4.725" x2="23.055" y1="14.725" y2="33.055" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#18884f"></stop><stop offset="1" stop-color="#0b6731"></stop></linearGradient><path fill="url(#flEJnwg7q~uKUdkX0KCyBa_UECmBSgBOvPT_gr1)" d="M22,34H6c-1.105,0-2-0.895-2-2V16c0-1.105,0.895-2,2-2h16c1.105,0,2,0.895,2,2v16	C24,33.105,23.105,34,22,34z"></path><path fill="#fff" d="M9.807,19h2.386l1.936,3.754L16.175,19h2.229l-3.071,5l3.141,5h-2.351l-2.11-3.93L11.912,29H9.526	l3.193-5.018L9.807,19z"></path>
                    </svg>
                    </a>
                </li>
                  -->
            </ul>



            <div class="d-flex w-auto">
                <ul class="navbar-nav">

                    <?php
                    if ($login_user->user_type == "staff") {
                        load_js(array("assets/js/awesomplete/awesomplete.min.js"));
                        ?>
                        <li class="nav-item hidden-sm" title="<?php echo app_lang('search') . ' (/)'; ?>">
                            <?php echo modal_anchor(get_uri("search/search_modal_form"), "<i data-feather='search' class='icon'></i>", array("class" => "nav-link", "data-modal-title" => app_lang('search') . ' (/)', "data-post-hide-header" => true, "data-modal-close" => "1", "id" => "global-search-btn")); ?>
                        </li>
                    <?php } ?>

                    <?php
                    if (!in_array("quick_add", $hidden_topbar_menus)) {
                        echo view("settings/topbar_parts/quick_add");
                    }
                    ?>

                    <?php if (!in_array("language", $hidden_topbar_menus) && (($login_user->user_type == "staff" && !get_setting("disable_language_selector_for_team_members")) || ($login_user->user_type == "client" && !get_setting("disable_language_selector_for_clients")))) { ?>

                        <li class="nav-item dropdown">
                            <?php echo js_anchor("<i data-feather='globe' class='icon'></i>", array("id" => "personal-language-icon", "class" => "nav-link dropdown-toggle", "data-bs-toggle" => "dropdown")); ?>

                            <ul class="dropdown-menu dropdown-menu-end language-dropdown">
                                <li>
                                    <?php
                                    $user_language = $login_user->language;
                                    $system_language = get_setting("language");

                                    foreach (get_language_list() as $language) {
                                        $language_status = "";
                                        $language_text = $language;

                                        if ($user_language == strtolower($language) || (!$user_language && $system_language == strtolower($language))) {
                                            $language_status = "<span class='float-end checkbox-checked m0'></span>";
                                            $language_text = "<strong>" . $language . "</strong>";
                                        }

                                        if ($login_user->user_type == "staff") {
                                            echo ajax_anchor(get_uri("team_members/save_personal_language/$language"), $language_text . $language_status, array("class" => "dropdown-item clearfix", "data-reload-on-success" => "1"));
                                        } else {
                                            echo ajax_anchor(get_uri("clients/save_personal_language/$language"), $language_text . $language_status, array("class" => "dropdown-item clearfix", "data-reload-on-success" => "1"));
                                        }
                                    }
                                    ?>
                                </li>
                            </ul>
                        </li>

                    <?php } ?>

                    <?php if (can_access_reminders_module()) { ?>
                        <li class="nav-item dropdown">
                            <?php echo modal_anchor(get_uri("events/reminders"), "<i data-feather='clock' class='icon'></i>", array("class" => "nav-link", "id" => "reminder-icon", "data-post-reminder_view_type" => "global", "title" => app_lang('reminders') . " (" . app_lang('private') . ")")); ?>
                        </li>
                        <?php reminders_widget(); ?>
                    <?php } ?>

                    <li class="nav-item dropdown">
                        <?php echo js_anchor("<i data-feather='bell' class='icon'></i>", array("id" => "web-notification-icon", "class" => "nav-link dropdown-toggle", "data-bs-toggle" => "dropdown")); ?>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown w400">
                            <div class="dropdown-details card bg-white m0">
                                <div class="list-group">
                                    <span class="list-group-item inline-loader p10"></span>                          
                                </div>
                            </div>
                            <div class="card-footer text-center mt-2">
                                <?php echo anchor("notifications", app_lang('see_all')); ?>
                            </div>
                        </div>
                    </li>

                    <?php if (get_setting("module_message") && can_access_messages_module()) { ?>
                        <li class="nav-item dropdown hidden-sm <?php echo ($login_user->user_type === "client" && !get_setting("client_message_users")) ? "hide" : ""; ?>">
                            <?php echo js_anchor("<i data-feather='mail' class='icon'></i>", array("id" => "message-notification-icon", "class" => "nav-link dropdown-toggle", "data-bs-toggle" => "dropdown")); ?>
                            <div class="dropdown-menu dropdown-menu-end w300">
                                <div class="dropdown-details card bg-white m0">
                                    <div class="list-group">
                                        <span class="list-group-item inline-loader p10"></span>                          
                                    </div>
                                </div>
                                <div class="card-footer text-center">
                                    <?php echo anchor("messages", app_lang('see_all')); ?>
                                </div>
                            </div>
                        </li>
                    <?php } ?>

                    <li class="nav-item dropdown">
                        <a id="user-dropdown" href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="avatar-xs avatar me-1" >
                                <img alt="..." src="<?php echo get_avatar($login_user->image); ?>">
                            </span>
                            <span class="user-name ml10"><?php echo $login_user->first_name . " " . $login_user->last_name; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end w200 user-dropdown-menu">
                            <?php if ($login_user->user_type == "client") { ?>
                                <div class="company-switch-option d-none"><?php show_clients_of_this_client_contact($login_user, true); ?></div>
                                <li><?php echo get_client_contact_profile_link($login_user->id . '/general', "<i data-feather='user' class='icon-16 me-2'></i>" . app_lang('my_profile'), array("class" => "dropdown-item")); ?></li>
                                <li><?php echo get_client_contact_profile_link($login_user->id . '/account', "<i data-feather='key' class='icon-16 me-2'></i>" . app_lang('change_password'), array("class" => "dropdown-item")); ?></li>
                                <li><?php echo get_client_contact_profile_link($login_user->id . '/my_preferences', "<i data-feather='settings' class='icon-16 me-2'></i>" . app_lang('my_preferences'), array("class" => "dropdown-item")); ?></li>
                            <?php } else { ?>
                                <li><?php echo get_team_member_profile_link($login_user->id . '/general', "<i data-feather='user' class='icon-16 me-2'></i>" . app_lang('my_profile'), array("class" => "dropdown-item")); ?></li>
                                <li><?php echo get_team_member_profile_link($login_user->id . '/account', "<i data-feather='key' class='icon-16 me-2'></i>" . app_lang('change_password'), array("class" => "dropdown-item")); ?></li>
                                <li><?php echo get_team_member_profile_link($login_user->id . '/my_preferences', "<i data-feather='settings' class='icon-16 me-2'></i>" . app_lang('my_preferences'), array("class" => "dropdown-item")); ?></li>
                            <?php } ?>

                            <?php if (get_setting("show_theme_color_changer") === "yes") { ?>

                                <li class="dropdown-divider"></li>    
                                <li class="pl10 ms-2 mt10 theme-changer">
                                    <?php echo get_custom_theme_color_list(); ?>
                                </li>

                            <?php } ?>

                            <li class="dropdown-divider"></li>
                            <li><a href="<?php echo_uri('signin/sign_out'); ?>" class="dropdown-item"><i data-feather="log-out" class='icon-16 me-2'></i> <?php echo app_lang('sign_out'); ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<script type="text/javascript">
    //close navbar collapse panel on clicking outside of the panel
    $(document).click(function (e) {
        if (!$(e.target).is('#navbar') && isMobile()) {
            $('#navbar').collapse('hide');
        }
    });

    var notificationOptions = {};

    $(document).ready(function () {
        //load message notifications
        var messageOptions = {},
                messageIcon = "#message-notification-icon",
                notificationIcon = "#web-notification-icon";

        //check message notifications
        messageOptions.notificationUrl = "<?php echo_uri('messages/count_notifications'); ?>";
        messageOptions.notificationStatusUpdateUrl = "<?php echo_uri('messages/update_notification_checking_status'); ?>";
        messageOptions.checkNotificationAfterEvery = "<?php echo get_setting('check_notification_after_every'); ?>";
        messageOptions.icon = "mail";
        messageOptions.notificationSelector = $(messageIcon);
        messageOptions.isMessageNotification = true;

        checkNotifications(messageOptions);

        window.updateLastMessageCheckingStatus = function () {
            checkNotifications(messageOptions, true);
        };

        $('body').on('show.bs.dropdown', messageIcon, function () {
            messageOptions.notificationUrl = "<?php echo_uri('messages/get_notifications'); ?>";
            checkNotifications(messageOptions, true);
        });




        //check web notifications
        notificationOptions.notificationUrl = "<?php echo_uri('notifications/count_notifications'); ?>";
        notificationOptions.notificationStatusUpdateUrl = "<?php echo_uri('notifications/update_notification_checking_status'); ?>";
        notificationOptions.checkNotificationAfterEvery = "<?php echo get_setting('check_notification_after_every'); ?>";
        notificationOptions.icon = "bell";
        notificationOptions.notificationSelector = $(notificationIcon);
        notificationOptions.notificationType = "web";
        notificationOptions.pushNotification = "<?php echo get_setting("enable_push_notification") && $login_user->enable_web_notification && !get_setting('user_' . $login_user->id . '_disable_push_notification') ? true : false ?>";

        checkNotifications(notificationOptions); //start checking notification after starting the message checking 

        if (isMobile()) {
            //for mobile devices, load the notifications list with the page load
            notificationOptions.notificationUrlForMobile = "<?php echo_uri('notifications/get_notifications'); ?>";
            checkNotifications(notificationOptions);
        }

        $('body').on('show.bs.dropdown', notificationIcon, function () {
            notificationOptions.notificationUrl = "<?php echo_uri('notifications/get_notifications'); ?>";
            checkNotifications(notificationOptions, true);
        });

        $('body').on('click', "#reminder-icon", function () {
            $("#ajaxModal").addClass("reminder-modal");
        });

        $("body").on("click", ".notification-dropdown a[data-act='ajax-modal'], #js-quick-add-task, #js-quick-add-multiple-task, #task-details-edit-btn, #task-modal-view-link", function () {
            if ($(".task-preview").length) {
                //remove task details view when it's already opened to prevent selector duplication
                $("#page-content").remove();
                $('#ajaxModal').on('hidden.bs.modal', function () {
                    location.reload();
                });
            }
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    });

</script>