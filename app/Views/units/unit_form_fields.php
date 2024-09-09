
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Unit Name SO  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="unit_name_so" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('unit_name_so'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "unit_name_so",
                "name" => "unit_name_so",
                "value" => $model_info->nameSo,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('unit_name_so'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Unit Short Name SO  ------------------------------------>


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

<!----------------------------------------- Unit Name EN  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="unit_name_en" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('unit_name_en'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "unit_name_en",
                "name" => "unit_name_en",
                "value" => $model_info->nameEn,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('unit_name_en'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Unit Short Name EN  ------------------------------------>


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

<!----------------------------------------- Email  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="unit_email" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('unit_email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "unit_email",
                "name" => "unit_email",
                "value" => $model_info->email,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('unit_email'),
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Department  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="unit_department" class=" <?php echo $label_column; ?>"><?php echo 'Unit Department'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "unit_department",
                "name" => "unit_department",
                "class" => "form-control select2",
                "placeholder" => 'Section Department',
                "autocomplete" => "off"
            ),$Departments,[$model_info->department_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Section  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="unit_section" class=" <?php echo $label_column; ?>"><?php echo 'Unit Section'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "unit_section",
                "name" => "unit_section",
                "class" => "form-control select2",
                "placeholder" => 'Section Department',
                "autocomplete" => "off"
            ),$Sections,[$model_info->section_id]);
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Unit Head  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="unit_head" class=" <?php echo $label_column; ?>"><?php echo 'Unit Head'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "unit_head",
                "name" => "unit_head",
                "class" => "form-control select2",
                "placeholder" => 'Unid Head',
                "autocomplete" => "off"
            ),$Unit_heads,[$model_info->unit_head_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Unit Remarks  ------------------------------------>

<div class="form-group">
    <div class="row">
    <label for="unit_remarks" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('unit_remarks'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "unit_remarks",
                "name" => "unit_remarks",
                "value" => $model_info?->remarks ,
                "class" => "form-control",
                "placeholder" => app_lang('unit_remarks')
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

        $("#unit-form .select2").select2();

    });
</script>