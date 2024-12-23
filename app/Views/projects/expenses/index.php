<div class="card">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('expenses'); ?></h4>
        <div class="title-button-group">
            <?php echo modal_anchor(get_uri("expenses/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_expense'), array("class" => "btn btn-default mb0", "title" => app_lang('add_expense'), "data-post-project_id" => $project_id)); ?>
        </div>
    </div>
    <div class="table-responsive">
        <table id="expense-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $EXPENSE_TABLE = $("#expense-table");

        $EXPENSE_TABLE.appTable({
            source: '<?php echo_uri("expenses/list_data/") ?>',
            filterParams: {project_id: "<?php echo $project_id; ?>"},
            order: [[0, "asc"]],
            columns: [
            {visible: false, searchable: false},
            {title: '<?php echo app_lang("date") ?>', "iDataSort": 0, "class": "all"},
            {title: '<?php echo "Created by" ?>'},
            {title: '<?php echo app_lang("title") ?>', "class": "all"},
            {title: '<?php echo app_lang("description") ?>'},
            {title: '<?php echo app_lang("category") ?>'},
            {title: '<?php echo app_lang("amount") ?>', "class": "text-right"},
            {title: '<?php echo app_lang("status") ?>', "class": "text-center"}
            <?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 3, 4, 6, 7, 8, 9], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3, 4, 6, 7, 8, 9], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 6, dataType: 'currency'}]
        });
    });
</script>