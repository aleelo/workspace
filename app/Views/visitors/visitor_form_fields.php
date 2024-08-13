
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Visitor Name  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="visitor_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('visitor_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "visitor_name",
                "name" => "visitor_name",
                "value" => $model_info->name,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('visitor_name'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Visitor Type  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="visitor_type" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "visitor_type",
                "name" => "visitor_type",
                "value" => $model_info->type,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('type'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Visitor Email  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="visitor_email" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "visitor_email",
                "name" => "visitor_email",
                "value" => $model_info->email,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('email'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Visitor Phone  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="visitor_phone" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('phone'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "visitor_phone",
                "name" => "visitor_phone",
                "value" => $model_info->phone,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('phone'),
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Visitor Description  ------------------------------------>

<div class="form-group">
    <div class="row">
    <label for="visitor_description" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('description'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "visitor_description",
                "name" => "visitor_description",
                "value" => $model_info?->description,
                "class" => "form-control",
                "placeholder" => app_lang('description')
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