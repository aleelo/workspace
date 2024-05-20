<div id="page-content" class="page-wrapper clearfix grid-button leads-view">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab leads-title-section"><h4 class="pl15 pt10 pr15"><?php echo app_lang("fuel_order"); ?></h4></li>

        <!-- <?php //echo view("fuel/tabs", array("active_tab" => "leads_list")); ?> -->

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                
                <!-- <?php //echo modal_anchor(get_uri("labels/modal_form"), "<i data-feather='tag' class='icon-16'></i> " . app_lang('manage_labels'), array("class" => "btn btn-outline-light", "title" => app_lang('manage_labels'), "data-post-type" => "client")); ?>
                <?php //echo modal_anchor(get_uri("fuel/import_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_leads'), array("class" => "btn btn-default", "title" => app_lang('import_leads'))); ?> -->
                <?php echo modal_anchor(get_uri("fuel/order_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_fuel_order'), array("class" => "btn btn-default", "title" => app_lang('add_fuel_order'))); ?>
            </div>
        </div>
    </ul>

    <div class="card">
        <div class="table-responsive">
            <table id="order-table" class="display" cellspacing="0" width="100%">            
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

    // fuel_type supplier receive_date barrels	litters	received_by	vehicle_model	plate	
    $("#order-table").appTable({
    source: '<?php echo_uri("fuel/order_list_data") ?>',
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
            {title: "<?php echo app_lang("fuel_type") ?>", "class": "all", order_by: "fuel_type"},
            {title: "<?php echo app_lang("supplier") ?>", order_by: "supplier"},
            {title: "<?php echo app_lang("order_date") ?>", order_by: "order_date"},
            {title: "<?php echo app_lang("arrival_date") ?>", order_by: "arrival_date"},
            {title: "<?php echo app_lang("barrels") ?>", order_by: "barrels"},
            {title: "<?php echo app_lang("litters") ?>", order_by: "litters"},
            {title: "<?php echo app_lang("ordered_by") ?>", order_by: "ordered_by"},
            {title: "<?php echo app_lang("status") ?>", order_by: "status"},
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w10p"}
            ],
            filterDropdown: [
                <?php //if (get_array_value($login_user->permissions, "lead") !== "own") { ?>
                 //{name: "owner_id", class: "w200", options: <?php //echo json_encode($owners_dropdown); ?>},
            <?php //} ?>
                //{name: "status", class: "w200", options: <?php //echo view("fuel/lead_statuses"); ?>},
            //{name: "label_id", class: "w200", options: <?php //echo $labels_dropdown; ?>},

            //{name: "source", class: "w200", options: <?php //echo view("fuel/lead_sources"); ?>} ,

            <?php //echo $custom_field_filters; ?>
            ],
            // rangeDatepicker: [{startDate: {name: "start_date", value: ""}, endDate: {name: "end_date", value: ""}, showClearButton: true}],
            printColumns: [0, 1, 2, 4, 5],
            xlsColumns: [0, 1, 2, 4, 5],
    });
    }
    );
</script>
