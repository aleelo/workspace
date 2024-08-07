<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix items-page-title">
            <h1> <?php echo app_lang('departments_list'); ?></h1>
            <div class="title-button-group">
                <!-- <?php //echo modal_anchor(get_uri("purchase_items/import_items_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_items'), array("class" => "btn btn-default", "title" => app_lang('import_items'))); ?> -->
                <?php echo modal_anchor(get_uri("departments/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_departments'), array("class" => "btn btn-default", "title" => app_lang('add_departments'))); ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="departments-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#departments-table").appTable({
            source: '<?php echo_uri("departments/list_data") ?>',
            order: [[0, 'desc']],
            columns: [
                {title: "<?php echo app_lang('id') ?> ", "class": "all"},
                {title: "<?php echo app_lang('Department_name_so') ?> ", "class": ""},
                {title: "<?php echo app_lang('short_name_SO') ?> ", "class": ""},
                {title: "<?php echo app_lang('Department_name_en') ?>", "class": " "},
                {title: "<?php echo app_lang('short_name_EN') ?>", "class": " "},
                {title: "<?php echo app_lang('department_email') ?>", "class": " "},
                {title: "<?php echo app_lang('department_head') ?>", "class": ""},
                {title: "<?php echo app_lang('remarks') ?>", "class": "text-right "},
                {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option "}
            ],
            printColumns: [0, 1, 2, 3, 4,5],
            xlsColumns: [0, 1, 2, 3, 4,5]
        });
    });
</script>