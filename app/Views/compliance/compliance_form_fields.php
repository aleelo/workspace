
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Reporter  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="reporter_id" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('reporter'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "reporter_id",
                "name" => "reporter_id",
                "class" => "form-control select2",
                "placeholder" => 'Reporter',
                "autocomplete" => "off"
            ),$reporter,[$model_info->reporter_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Being Reported  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="being_reported_id" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('being_reported'); ?></label>
        <div class="<?php echo $field_column; ?>">
        <?php
            echo form_dropdown(array(
                "id" => "being_reported_id",
                "name" => "being_reported_id",
                "class" => "form-control select2",
                "placeholder" => 'Being erported',
                "autocomplete" => "off"
            ),$being_reported,[$model_info->being_reported_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Report  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="report" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('report'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "report",
                "name" => "report",
                "value" => $model_info->report,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('report'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Evidence  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="evidence" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('evidence'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "evidence",
                "name" => "evidence",
                "value" => $model_info->evidence,
                "class" => "form-control",
                "placeholder" => app_lang('evidence'),
            ));
            ?>
        </div>
    </div>
</div>




<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 



<script type="text/javascript">
    var k=1;
    $(document).ready(function () {

      

        setDatePicker("#Start_Date")
        setDatePicker("#End_Date")
        
        $('[data-bs-toggle="tooltip"]').tooltip();

<?php if (isset($currency_dropdown)) { ?>
            if ($('#currency').length) {
                $('#currency').select2({data: <?php echo json_encode($currency_dropdown); ?>});
            }
<?php } ?>

<?php if (isset($groups_dropdown)) { ?>
            $("#group_ids").select2({
                multiple: true,
                data: <?php echo json_encode($groups_dropdown); ?>
            });
<?php } ?>

<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
            $('#created_by').select2({data: <?php echo $team_members_dropdown; ?>});
<?php } ?>

<?php if ($login_user->user_type === "staff") { ?>
            $("#client_labels").select2({multiple: true, data: <?php echo json_encode($label_suggestions); ?>});
<?php } ?>
        $('.account_type').click(function () {
            var inputValue = $(this).attr("value");
            if (inputValue === "person") {
                $(".company_name_section").html("Name");
                $(".company_name_input_section").attr("placeholder", "Name");
            } else {
                $(".company_name_section").html("Company name");
                $(".company_name_input_section").attr("placeholder", "Company name");
            }
        });

        $("#client-form .select2").select2();

    });
</script>