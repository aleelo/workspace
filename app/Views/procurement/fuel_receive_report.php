<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <ul id="project-all-timesheet-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
           <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php //echo app_lang("timesheets"); ?></h4></li>
 
            <li><a id="timesheet-details-button"  role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#timesheet-details"><?php echo app_lang("details"); ?></a></li>
      <!--       <li><a role="presentation" data-bs-toggle="tab" href="<?php //echo_uri("projects/all_timesheet_summary/"); ?>" data-bs-target="#timesheet-summary"><?php //echo app_lang('summary'); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php //echo_uri("projects/timesheet_chart/"); ?>" data-bs-target="#timesheet-chart"><?php //echo app_lang('chart'); ?></a></li> -->
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="timesheet-details">
                <div class="table-responsive">
                    <table id="fuel-receive-report-table" class="display" width="100%">  
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="timesheet-summary"></div>
            <div role="tabpanel" class="tab-pane fade" id="timesheet-chart"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
    
    var optionVisibility = false;

        $("#fuel-receive-report-table").appTable({
            source: '<?php echo_uri("fuel/rec_rpt_list_data") ?>',
            filterDropdown: [
                {name: "received_by", class: "w200", options: <?php echo $members_dropdown; ?>},
                {name: "department_id", class: "w200", options: <?php echo $departments_dropdown; ?>},
                
            ],
            serverSide: true,
            rangeDatepicker: [{startDate: {name: "start_date", value: moment().subtract(7,'days').format("YYYY-MM-DD")}, 
                               endDate: {name: "end_date", value: moment().format("YYYY-MM-DD")}, showClearButton: true, 
                               label: "<?php echo app_lang('date'); ?>", ranges: ['this_month', 'last_month', 'this_year', 'last_year', 'last_30_days', 'last_7_days']}],
            columns: [
                {title: "<?php echo 'ID' ?>", "class": "all", order_by: "id"},
                {title: "<?php echo app_lang('fuel_type') ?>", order_by: "fuel_type"},
                {title: "<?php echo app_lang('supplier') ?>", order_by: "supplier"},
                {title: "<?php echo app_lang('receive_date') ?>", order_by: "receive_date"},
                {title: "<?php echo app_lang('barrels') ?>", order_by: "barrels"},
                {title: "<?php echo app_lang('litters') ?>", order_by: "litters"},
                {title: "<?php echo app_lang('received_by') ?>", "class": ""},
                {title: "<?php echo app_lang('department') ?>", "class": ""},
                {title: "<?php echo app_lang('vehicle_model') ?>", "class": ""},
                {title: "<?php echo app_lang('plate') ?>", "class": ""},
                // {visible: optionVisibility, title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5, 6, 7,8,9],
            xlsColumns: [0, 1, 2, 3, 4, 5, 6, 7,8,9],
            // summation: [{column: 8, dataType: 'time'}]
        });
    });
</script>