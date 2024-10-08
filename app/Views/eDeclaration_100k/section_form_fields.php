
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Section Name SO  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="section_name_so" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('section_name_so'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "section_name_so",
                "name" => "section_name_so",
                "value" => $model_info->nameSo,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('section_name_so'),
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

<!----------------------------------------- Section Name EN  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="section_name_en" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('section_name_en'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "section_name_en",
                "name" => "section_name_en",
                "value" => $model_info->nameEn,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('section_name_en'),
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

<!----------------------------------------- Email  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="email" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "value" => $model_info->email,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('email'),
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Department  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="section_department" class=" <?php echo $label_column; ?>"><?php echo 'Section Department'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "section_department",
                "name" => "section_department",
                "class" => "form-control select2",
                "placeholder" => 'Section Department',
                "autocomplete" => "off"
            ),$departments,[$model_info->department_id]);
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Section Head  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="section_head" class=" <?php echo $label_column; ?>"><?php echo 'Section Head'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "section_head",
                "name" => "section_head",
                "class" => "form-control select2",
                "placeholder" => 'Section Head',
                "autocomplete" => "off"
            ),$Section_heads,[$model_info->section_head_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Secretary  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="secretary" class=" <?php echo $label_column; ?>"><?php echo 'Secretary'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "secretary",
                "name" => "secretary",
                "class" => "form-department_head select2",
                "placeholder" => 'Secretary',
                "autocomplete" => "off"
            ),$secretary,[$model_info->secretary_id]);
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