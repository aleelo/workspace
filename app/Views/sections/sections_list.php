<div class="card">
    <div class="table-responsive">
        <table id="section-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    loadClientsTable = function (selector) {
    var showInvoiceInfo = true;
    if (!"<?php echo $show_invoice_info; ?>") {
    showInvoiceInfo = false;
    }

    var showOptions = true;
    if (!"<?php echo $can_edit_clients; ?>") {
    showOptions = false;
    }

    var ignoreSavedFilter = false;
    var quick_filters_dropdown = <?php echo view("sections/quick_filters_dropdown"); ?>;
    if (window.selectedClientQuickFilter){
    var filterIndex = quick_filters_dropdown.findIndex(x => x.id === window.selectedClientQuickFilter);
    if ([filterIndex] > - 1){
    //match found
    ignoreSavedFilter = true;
    quick_filters_dropdown[filterIndex].isSelected = true;
    }
    }

    $(selector).appTable({
    source: '<?php echo_uri("sections/list_data") ?>',
            serverSide: true,
            smartFilterIdentity: "all_clients_list", //a to z and _ only. should be unique to avoid conflicts
            ignoreSavedFilter: ignoreSavedFilter,
            filterDropdown: [
            {name: "quick_filter", class: "w200", options: quick_filters_dropdown},
        <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
                {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>},
<?php } ?>
            {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>},
            {name: "label_id", class: "w200", options: <?php echo $labels_dropdown; ?>},
<?php echo $custom_field_filters; ?>
            ],
            columns: [
            {title: "<?php echo app_lang("id") ?>", "class": "text-center w50 all", order_by: "id"},
            {title: "<?php echo app_lang("section_name_so") ?>", "class": "all", order_by: "nameSo"},
            {title: "<?php echo app_lang("short_name_so") ?>", order_by: "short_name_SO"},
            {title: "<?php echo app_lang("section_name_en") ?>", order_by: "nameEn"},
            {title: "<?php echo app_lang("short_name_en") ?>", order_by: "short_name_EN"},
            {title: "<?php echo app_lang("section_email") ?>", order_by: "email"},
            {title: "<?php echo app_lang("section_department") ?>", order_by: "DepNameSo"},
            {title: "<?php echo app_lang("section_head") ?>", order_by: "SectionHead"},
            {title: "<?php echo app_lang("secretary") ?>", order_by: "secretary"},
            {title: "<?php echo app_lang("section_remarks") ?>", order_by: "remarks"}

            
<?php echo $custom_field_headers; ?>,
            {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions}
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6, 7], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6, 7], '<?php echo $custom_field_headers; ?>')
    });
    };
    $(document).ready(function () {
    loadClientsTable("#section-table");
    });
</script>