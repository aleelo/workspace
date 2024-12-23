<div class="table-responsive">
    <table id="active-list-table" class="display" cellspacing="0" width="100%">            
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#active-list-table").appTable({
            source: '<?php echo_uri("leaves/active_list_data") ?>',
            dateRangeType: "monthly",
            rangeDatepicker: [{startDate: {name: "start_date", value: moment().format("YYYY-MM-DD")}, endDate: {name: "end_date", value: moment().format("YYYY-MM-DD")}}],
            columns: [
                {title: '<?php echo 'ID' ?>', "class": "w10p"},
                //{title: '<?php //echo app_lang("department_ame") ?>', "class": "w15p"},
                {title: '<?php echo app_lang("applicant") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("leave_type") ?>', "class": "w15p"},
                {title: '<?php echo app_lang("date") ?>', "class": "w20p"},
                // {title: '<?php // echo app_lang("duration") ?>', "class": "w20p"},
                // {title: '<?php // echo app_lang("unit_name") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("section_name") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("dp_name") ?>', "class": "w20p"},
                {title: '<?php echo app_lang("status") ?>', "class": "w100"},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w10p"}
            ],
            printColumns: [0, 1, 2, 3, 4],
            xlsColumns: [0, 1, 2, 3, 4]
        });
    });
</script>

