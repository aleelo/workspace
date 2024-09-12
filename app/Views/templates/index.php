<div id="page-content" class="page-wrapper clearfix grid-button leads-view">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab leads-title-section"><h4 class="pl15 pt10 pr15"><?php echo app_lang("templates"); ?></h4></li>

        <!-- <?php //echo view("documents/tabs", array("active_tab" => "leads_list")); ?> -->

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">

                <?php
                if($can_add_template){
                    echo modal_anchor(get_uri("documents/template_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_template'), array("class" => "btn btn-outline-light", "title" => app_lang('add_template'), "data-post-type" => "client"));
               }?>

               </div>
        </div>
    </ul>

    <div class="card">
        <div class="table-responsive">
            <table id="template-table" class="display" cellspacing="0" width="100%">            
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
    $("#template-table").appTable({
    source: '<?php echo_uri("documents/templates_list_data") ?>',
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
            {title: "<?php echo app_lang("ref_prefix") ?>", order_by: "ref_prefix"},
            {title: "<?php echo app_lang("destination_folder") ?>", order_by: "destination_folder"},
            {title: "<?php echo app_lang("unit") ?>", order_by: "unit"},
            {title: "<?php echo app_lang("section") ?>", order_by: "section"},
            {title: "<?php echo app_lang("depertment") ?>", order_by: "department"},
            // {title: "<?php //echo app_lang("description") ?>", order_by: "description"},
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
