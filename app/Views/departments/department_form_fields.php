
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Department Name SO  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="department_name_so" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('department_name_so'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "department_name_so",
                "name" => "department_name_so",
                "value" => $model_info->nameSo,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('department_name_so'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Short Name SO  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="short_name_so" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('short_name_so'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "short_name_so",
                "name" => "short_name_so",
                "value" => $model_info->short_name_SO,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('short_name_so'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Department Name EN  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="department_name_en" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('department_name_en'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "department_name_en",
                "name" => "department_name_en",
                "value" => $model_info->nameEn,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('department_name_en'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Short Name EN  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="short_name_en" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('short_name_en'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "short_name_en",
                "name" => "short_name_en",
                "value" => $model_info->short_name_EN,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('short_name_en'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Department Email  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="department_email" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('department_email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "department_email",
                "name" => "department_email",
                "value" => $model_info->email,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('department_email'),
            ));
            ?>
        </div>
    </div>
</div>



<!----------------------------------------- Department Head  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="department_head" class=" <?php echo $label_column; ?>"><?php echo 'Department Head'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "department_head",
                "name" => "department_head",
                "class" => "form-department_head select2",
                "placeholder" => 'Department Head',
                "autocomplete" => "off"
            ),$department_heads,[$model_info->dep_head_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Section Remarks  ------------------------------------>

<div class="form-group">
    <div class="row">
    <label for="section_remarks" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('section_remarks'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "section_remarks",
                "name" => "section_remarks",
                "value" => $model_info?->remarks ,
                "class" => "form-control",
                "placeholder" => app_lang('section_remarks')
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