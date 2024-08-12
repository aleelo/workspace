<div id="page-content" class="clearfix page-content">
    <div class="container-fluid  full-width-button">
        <div class="row clients-view-button">
            <div class="col-md-12">
                <div class="page-title clearfix no-border no-border-top-radius no-bg">
                    <h1 class="pl0">
                        <?php echo app_lang('unit_details') . " - " . $section_info->nameSo ?>
                        <span id="star-mark">
                            <?php
                            if ($is_starred) {
                                echo view('units/star/starred', array("client_id" => $section_info->id));
                            } else {
                                echo view('units/star/not_starred', array("client_id" => $section_info->id));
                            }
                            ?>
                        </span>

                        <?php if ($section_info->lead_status_id) { ?>
                            <?php $lead_information = app_lang("past_lead_information") . "<br />"; ?>
                            <?php if ($section_info->created_date) { ?>
                                <?php $lead_information .= app_lang("lead_created_at") . ": " . format_to_date($section_info->created_date, false) . "<br />"; ?>
                            <?php } ?>
                            <?php if ($section_info->client_migration_date && is_date_exists($section_info->client_migration_date)) { ?>
                                <?php $lead_information .= app_lang("migrated_to_client_at") . ": " . format_to_date($section_info->client_migration_date, false) . "<br />"; ?>
                            <?php } ?>
                            <?php if ($section_info->last_lead_status) { ?>
                                <?php $lead_information .= app_lang("last_status") . ": " . $section_info->last_lead_status . "<br />"; ?>
                            <?php } ?>
                            <?php if ($section_info->owner_id) { ?>
                                <?php $lead_information .= app_lang("owner") . ": " . $section_info->owner_name; ?>
                            <?php } ?>

                            <span data-bs-toggle="tooltip" data-bs-html="true" title="<?php echo $lead_information; ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                        <?php } ?>

                    </h1>

                    <?php if (can_access_reminders_module()) { ?>
                        <div class="title-button-group mr0 clients-view">
                            <?php echo modal_anchor(get_uri("events/reminders"), "<i data-feather='clock' class='icon-16'></i> " . app_lang('reminders'), array("class" => "btn btn-default mr0", "id" => "reminder-icon", "data-post-client_id" => $section_info->id, "data-post-reminder_view_type" => "client", "title" => app_lang('reminders') . " (" . app_lang('private') . ")")); ?>
                        </div>
                    <?php } ?>
                </div>

                <!-- <div>
                    <?php// echo view("units/info_widgets/index"); ?>
                </div> -->

                <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs" role="tablist">
                    
                    <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/company_info_tab/" . $section_info->id); ?>" data-bs-target="#client-info"> <?php echo app_lang('unit_info'); ?></a></li>

                    <!-- <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/contacts/" . $section_info->id); ?>" data-bs-target="#client-contacts"> <?php echo app_lang('contacts'); ?></a></li>

                    <?php if ($show_project_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/projects/" . $section_info->id); ?>" data-bs-target="#client-projects"><?php echo app_lang('projects'); ?></a></li>
                    <?php } ?>

                    <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/tasks/" . $section_info->id); ?>" data-bs-target="#client-tasks"><?php echo app_lang('tasks'); ?></a></li> -->

                    <!-- <?php if ($show_invoice_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/invoices/" . $section_info->id); ?>" data-bs-target="#client-invoices"> <?php echo app_lang('invoices'); ?></a></li>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/payments/" . $section_info->id); ?>" data-bs-target="#client-payments"> <?php echo app_lang('payments'); ?></a></li>
                    <?php } ?> -->

                    <?php if ($show_estimate_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/estimates/" . $section_info->id); ?>" data-bs-target="#client-estimates"> <?php echo app_lang('estimates'); ?></a></li>
                    <?php } ?>

                    <!-- <?php if ($show_order_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/orders/" . $section_info->id); ?>" data-bs-target="#client-orders"> <?php echo app_lang('orders'); ?></a></li>
                    <?php } ?> -->

                    <!-- <?php if ($show_estimate_request_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/estimate_requests/" . $section_info->id); ?>" data-bs-target="#client-estimate-requests"> <?php echo app_lang('estimate_requests'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_contract_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/contracts/" . $section_info->id); ?>" data-bs-target="#client-contracts"> <?php echo app_lang('contracts'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_proposal_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/proposals/" . $section_info->id); ?>" data-bs-target="#client-proposals"> <?php echo app_lang('proposals'); ?></a></li>
                    <?php } ?> -->

                    <!-- <?php if ($show_ticket_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/tickets/" . $section_info->id); ?>" data-bs-target="#client-tickets"> <?php echo app_lang('tickets'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_note_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/notes/" . $section_info->id); ?>" data-bs-target="#client-notes"> <?php echo app_lang('notes'); ?></a></li>
                    <?php } ?>
                    <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/files/" . $section_info->id); ?>" data-bs-target="#client-files"><?php echo app_lang('files'); ?></a></li> -->

                    <!-- <?php if ($show_event_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/events/" . $section_info->id); ?>" data-bs-target="#client-events"> <?php echo app_lang('events'); ?></a></li>
                    <?php } ?>

                    <?php if ($show_expense_info) { ?>
                        <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("units/expenses/" . $section_info->id); ?>" data-bs-target="#client-expenses"> <?php echo app_lang('expenses'); ?></a></li>
                    <?php } ?> -->

                    <?php
                    $hook_tabs = array();
                    $hook_tabs = app_hooks()->apply_filters('app_filter_client_details_ajax_tab', $hook_tabs, $section_info->id);
                    $hook_tabs = is_array($hook_tabs) ? $hook_tabs : array();
                    foreach ($hook_tabs as $hook_tab) {
                        ?>
                        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo get_array_value($hook_tab, 'url') ?>" data-bs-target="#<?php echo get_array_value($hook_tab, 'target') ?>"><?php echo get_array_value($hook_tab, 'title') ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="client-projects"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-tasks"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-files"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-info"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-contacts"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-invoices"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-payments"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-estimates"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-orders"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-estimate-requests"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-contracts"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-proposals"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-tickets"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-notes"></div>
                    <div role="tabpanel" class="tab-pane" id="client-events" style="min-height: 300px"></div>
                    <div role="tabpanel" class="tab-pane fade" id="client-expenses"></div>
                    <?php foreach ($hook_tabs as $hook_tab) { ?>
                        <div role="tabpanel" class="tab-pane fade" id="<?php echo get_array_value($hook_tab, 'target') ?>"></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "info") {
                $("[data-bs-target='#client-info']").trigger("click");
            } else if (tab === "projects") {
                $("[data-bs-target='#client-projects']").trigger("click");
            } else if (tab === "invoices") {
                $("[data-bs-target='#client-invoices']").trigger("click");
            } else if (tab === "payments") {
                $("[data-bs-target='#client-payments']").trigger("click");
            }
        }, 210);

        $('[data-bs-toggle="tooltip"]').tooltip();

    });
</script>
