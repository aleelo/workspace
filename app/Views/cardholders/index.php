<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo app_lang('card_holders_list'); ?></h1>
            <div class="title-button-group">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-default btn-sm active me-0"  title="<?php echo app_lang('list_view'); ?>"><i data-feather="menu" class="icon-16"></i></button>
                    <?php echo anchor(get_uri("cardholders/view"), "<i data-feather='grid' class='icon-16'></i>", array("class" => "btn btn-default btn-sm")); ?>
                </div>
                <?php
                    // echo modal_anchor(get_uri("cardholders/invitation_modal"), "<i data-feather='mail' class='icon-16'></i> " . app_lang('send_invitation'), array("class" => "btn btn-default", "title" => app_lang('send_invitation')));
                    echo modal_anchor(get_uri("cardholders/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . 'Add Cardholder', array("class" => "btn btn-default", "title" => 'Add Cardholder'));
               
                ?>
            </div>
        </div>
        <div class="table-responsive">
            <table id="team_member-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var visibleDelete = true;
       
        // `photo`, `CID`, `type`, `fullName`, `department`, `titleEng`, `titleSom`, `cardId`, `user_id`, `expireDate`, 

        $("#team_member-table").appTable({
            source: '<?php echo_uri("cardholders/list_data") ?>',
            order: [[1, "asc"]],
            radioButtons: [{text: '<?php echo app_lang("active_cards") ?>', name: "status", value: "Active", isChecked: true}, {text: '<?php echo app_lang("inactive_cards") ?>', name: "status", value: "Inactive", isChecked: false}, {text: '<?php echo app_lang("lost_cards") ?>', name: "status", value: "Lost", isChecked: false}],
            
            serverSide: true,
            columns: [
                {title: "<?php echo app_lang("CID") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("name") ?>", "class": "w20p all"},
                {title: "<?php echo app_lang("institution") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("office") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("job_title_so") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("job_title_en") ?>", "class": "w15p"},
                {title: "<?php echo app_lang("status") ?>", "class": "w15p"},
                {visible: visibleDelete, title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 3, 4], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3, 4], '<?php echo $custom_field_headers; ?>')

        });
    });
</script>    
