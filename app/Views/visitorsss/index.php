<div id="page-content" class="page-wrapper clearfix grid-button leads-view">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab leads-title-section"><h4 class="pl15 pt10 pr15"><?php echo app_lang("access_requests"); ?></h4></li>

        <?php //echo view("visitors/tabs", array("active_tab" => "leads_list")); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">

                <?php //echo modal_anchor(get_uri("visitors/template_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_template'), array("class" => "btn btn-outline-light", "title" => app_lang('manage_labels'), "data-post-type" => "client")); ?>
                <?php //echo modal_anchor(get_uri("visitors/import_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_leads'), array("class" => "btn btn-default", "title" => app_lang('import_leads'))); ?> 
                <?php echo $can_add_requests == true ? modal_anchor(get_uri("visitors/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_request'), array("class" => "btn btn-default", "title" => app_lang('add_access_request'))) : ''; ?>
            </div>
        </div>
    </ul>

    <div class="card">
        <div class="table-responsive">
            <table id="lead-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

    var ignoreSavedFilter = false;
            var hasString = window.location.hash.substring(1);
            if (hasString){
    var ignoreSavedFilter = true;
    }

    
     // client_type	name	created_by	visit_date	created_at	deleted	remarks
    $("#lead-table").appTable({
    source: '<?php echo_uri("visitors/list_data") ?>',
            serverSide: true,
            // smartFilterIdentity: "all_leads_list", //a to z and _ only. should be unique to avoid conflicts
            ignoreSavedFilter: ignoreSavedFilter,
            order: [[0, "desc"]],
            columnDefs:[
                {
                    "targets": "_all",
                    "defaultContent": "-",
                }
            ],
            columns: [
            {title: "<?php echo 'ID' ?>", "class": "all", order_by: "id"},
            {title: "<?php echo app_lang("name") ?>", "class": "all", order_by: "name"},
            {title: "<?php echo app_lang("client_type") ?>", order_by: "client_type"},
            {title: "<?php echo app_lang("visit_date") ?>", order_by: "visit_date"},
            {title: "<?php echo app_lang("created_by") ?>", order_by: "created_by"},
            {title: "<?php echo app_lang("office") ?>"},
            {title: "<?php echo app_lang("created_at") ?>", order_by: "created_at"},
            {title: "<?php echo app_lang("status") ?>", order_by: "status"},
            
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w10p"}
            ],
            filterDropdown: [
                <?php //if (get_array_value($login_user->permissions, "lead") !== "own") { ?>
                 //{name: "owner_id", class: "w200", options: <?php //echo json_encode($owners_dropdown); ?>},
            <?php //} ?>
                //{name: "status", class: "w200", options: <?php //echo view("visitors/lead_statuses"); ?>},
            //{name: "label_id", class: "w200", options: <?php //echo $labels_dropdown; ?>},

            //{name: "source", class: "w200", options: <?php //echo view("visitors/lead_sources"); ?>} ,

            <?php //echo $custom_field_filters; ?>
            ],
            // rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 4, 5], '<?php echo ''; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 4, 5], '<?php echo ''; ?>')
    });
    }
    );
</script>
