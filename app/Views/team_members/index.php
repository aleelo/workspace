<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('team_members'); ?></h1>
            <div class="title-button-group">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default btn-sm active me-0"  title="<?php echo app_lang('list_view'); ?>"><i data-feather="menu" class="icon-16"></i></button>
                    <?php echo anchor(get_uri("team_members/view"), "<i data-feather='grid' class='icon-16'></i>", array("class" => "btn btn-default btn-sm")); ?>
                </div>
                <?php
                if ($login_user->is_admin || get_array_value($login_user->permissions, "can_add_or_invite_new_team_members")) {
                    echo modal_anchor(get_uri("team_members/invitation_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_invitation'), array("class" => "btn btn-default", "title" => app_lang('send_invitation')));
                    echo modal_anchor(get_uri("team_members/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_team_member'), array("class" => "btn btn-default", "title" => app_lang('add_team_member')));
                }
                ?>
            </div>
        </div>
            <div class="table-responsive">
                <div class="table-scroll">
                    <table id="team_member-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var visibleContact = false;
        if ("<?php echo $show_contact_info; ?>") {
            visibleContact = true;
        }

        var visibleDelete = false;
        if ("<?php echo $login_user->is_admin; ?>") {
            visibleDelete = true;
        }

        $("#team_member-table").appTable({
            source: '<?php echo_uri("team_members/list_data") ?>',
            order: [[1, "asc"]],
            radioButtons: [{text: '<?php echo app_lang("active_members") ?>', name: "status", value: "active", isChecked: true}, {text: '<?php echo app_lang("inactive_members") ?>', name: "status", value: "inactive", isChecked: false}],
            filterDropdown: [
                <?php echo $custom_field_filters; ?>
                {name: "department_id", class: "w200", options: <?php echo $departments_dropdown; ?>},
                
            ],
            // serverSide: true,
            columns: [
                {title: '', "class": "w50 text-center all"},
                {title: "<?php echo app_lang("name") ?>", "class": "w200 all"},
                {title: "<?php echo app_lang("job_title") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("company_name") ?>", "class": "w200 all"},
                {visible: visibleContact, title: "<?php echo app_lang("email") ?>", "class": "w200 all"},
                {visible: visibleContact, title: "<?php echo app_lang("phone") ?>", "class": "w200 all"}
                <?php echo $custom_field_headers; ?>,
                {visible: visibleDelete, title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], '<?php echo $custom_field_headers; ?>')

        });
    });
</script>    
