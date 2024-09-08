<div id="page-content" class="page-wrapper clearfix grid-button leads-view">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab leads-title-section"><h4 class="pl15 pt10 pr15"><?php echo app_lang("leads"); ?></h4></li>

        <!-- <?php //echo view("documents/tabs", array("active_tab" => "leads_list")); ?> -->

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">

                <?php
                if($can_add_template){
                    echo anchor(get_uri("documents/templates"), "<i data-feather='list' class='icon-16'></i> " . app_lang('templates_list'), array("class" => "btn btn-outline-light", "title" => app_lang('templates_list'), "data-post-type" => "client"));
                    echo modal_anchor(get_uri("documents/template_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_template'), array("class" => "btn btn-outline-light", "title" => app_lang('add_template'), "data-post-type" => "client"));
               }?>

                <!-- <?php //echo modal_anchor(get_uri("labels/modal_form"), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-outline-light", "title" => app_lang('manage_labels'), "data-post-type" => "client")); ?>
                <?php //echo modal_anchor(get_uri("documents/import_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_leads'), array("class" => "btn btn-default", "title" => app_lang('import_leads'))); ?> -->
                <?php 
                if($can_add_document){
                echo modal_anchor(get_uri("documents/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_lead'), array("class" => "btn btn-default", "title" => app_lang('add_lead'))); 
                 }?>
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

    // `document_title`,`created_by`, `ref_number`, `depertment`, `template`, `item_id`, `created_at`
    $("#lead-table").appTable({
    source: '<?php echo_uri("documents/list_data") ?>',
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
            {title: "<?php echo app_lang("document_title") ?>", "class": "all", order_by: "document_title"},
            {title: "<?php echo app_lang("ref_number") ?>", order_by: "ref_number"},
            {title: "<?php echo app_lang("section") ?>", order_by: "section"},
            {title: "<?php echo app_lang("department") ?>", order_by: "department"},
            {title: "<?php echo app_lang("template") ?>", order_by: "template"},
            // {title: "<?php //echo app_lang("item_id") ?>", order_by: "item_id",vissible: false},
            {title: "<?php echo app_lang("created_by") ?>", order_by: "created_by"},
            {title: "<?php echo app_lang("created_at") ?>", order_by: "created_at"}
            <?php echo $custom_field_headers; ?>,
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w10p"}
            ],
            filterDropdown: [
                <?php //if (get_array_value($login_user->permissions, "lead") !== "own") { ?>
                 //{name: "owner_id", class: "w200", options: <?php //echo json_encode($owners_dropdown); ?>},
            <?php //} ?>
                //{name: "status", class: "w200", options: <?php //echo view("documents/lead_statuses"); ?>},
            //{name: "label_id", class: "w200", options: <?php //echo $labels_dropdown; ?>},

            //{name: "source", class: "w200", options: <?php //echo view("documents/lead_sources"); ?>} ,

            <?php //echo $custom_field_filters; ?>
            ],
            // rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 4, 5], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 4, 5], '<?php echo $custom_field_headers; ?>')
    });
    }
    );
</script>

<?php echo view("documents/update_lead_status_script"); ?>