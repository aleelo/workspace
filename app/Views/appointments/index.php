<div id="page-content" class="page-wrapper clearfix">
    <div class="clearfix grid-button">
        <ul id="appointment-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
            <!-- <li><a role="presentation" data-bs-toggle="tab" href="<?php // echo_uri("appointments/appointments_calendar/"); ?>" data-bs-target="#appointments_calendar"><?php // echo app_lang('appointments_calendar'); ?></a></li> -->
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("appointments/appointments_list/"); ?>" data-bs-target="#appointment_list"><?php echo app_lang('appointments_list'); ?></a></li>
            <!-- <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#overview"><?php echo app_lang('overview'); ?></a></li> -->
            <!-- <li><a role="presentation" data-bs-toggle="tab" href="<?php //echo_uri("appointments/contacts/"); ?>" data-bs-target="#contacts"><?php// echo app_lang('contacts'); ?></a></li> -->
            <div class="tab-title clearfix no-border">
                <div class="title-button-group">
                    <?php
                        echo modal_anchor(get_uri("appointments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_appointment'), array("class" => "btn btn-default", "title" => app_lang('add_appointment')));
                    ?>
                </div>
            </div>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="overview">
                <?php echo view("appointments/overview/index"); ?>
            </div>

            <!-- <div role="tabpanel" class="tab-pane fade" id="appointments_calendar"></div> -->
            <div role="tabpanel" class="tab-pane fade" id="appointment_list"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "appointment_list" || tab === "appointment_list-has_open_projects") {
                $("[data-bs-target='#appointment_list']").trigger("click");

                window.selectedClientQuickFilter = window.location.hash.substring(1);
            } else if (tab === "contacts") {
                $("[data-bs-target='#contacts']").trigger("click");

                window.selectedContactQuickFilter = window.location.hash.substring(1);
            }
        }, 210);
    });
</script>