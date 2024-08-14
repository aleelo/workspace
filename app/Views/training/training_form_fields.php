
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Training Name  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="training_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('training_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "training_name",
                "name" => "training_name",
                "value" => $model_info->training_name,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('training_name'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Training Start Date  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="training_start_date" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('training_start_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "training_start_date",
                "name" => "training_start_date",
                "value" => $model_info->start_date,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('training_start_date'),
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Training End Date  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="training_end_date" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('training_end_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "training_end_date",
                "name" => "training_end_date",
                "value" => $model_info->end_date,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('training_end_date'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Training Location  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="Training_location" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('Training_location'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "Training_location",
                "name" => "Training_location",
                "value" => $model_info->training_location,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('Training_location'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Training Type  ------------------------------------>



<div class="form-group">
    <div class="row">
        <label for="Training_Type" class=" <?php echo $label_column; ?>"><?php echo 'Training Type'; ?></label>
        <div class=" col-md-9">
            <?php
            $training = [''=>'-- choose a type --','Face 2 Face'=>'Face 2 Face','Virtual'=>'Virtual'];
            echo form_dropdown(array(
                "id" => "Training_Type",
                "name" => "Training_Type",
                "class" => "form-control select2",
                "placeholder" => 'Training Type',
                "autocomplete" => "off"
            ),$training,[$model_info->type]);
            ?>
        </div>
    </div>
</div>



<!----------------------------------------- Number of Employee  ------------------------------------>

<!-- 
<div class="form-group">
    <div class="row">
        <label for="num_employee" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('num_employee'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "num_employee",
                "name" => "num_employee",
                "value" => $model_info->num_employee,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('num_employee'),
            ));
            ?>
        </div>
    </div>
</div> -->


<!----------------------------------------- Trainer  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="trainer_id" class=" <?php echo $label_column; ?>"><?php echo 'Trainer Name'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "trainer_id",
                "name" => "trainer_id",
                "class" => "form-control select2",
                "placeholder" => 'Trainer Name',
                "autocomplete" => "off"
            ),$Trainers,[$model_info->trainer_id]);
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Training Participant  ------------------------------------>



<div class="form-group">
    <div class="row">
        <label for="training_participant" class=" <?php echo $label_column; ?>"><?php echo 'Related to'; ?></label>
        <div class=" col-md-9">
            <?php
            $participant = [''=>'-- choose a participant --','Employees'=>'Employees','Units'=>'Units','Sections'=>'Sections','Departments'=>'Departments'];
            echo form_dropdown(array(
                "id" => "training_participant",
                "name" => "training_participant",
                "class" => "form-control select2",
                "placeholder" => 'Related to',
                "autocomplete" => "off"
            ),$participant,[$model_info->participant]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Department  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="department_id" class=" <?php echo $label_column; ?>"><?php echo 'Department'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "department_id",
                "name" => "department_id",
                "class" => "form-control select2",
                "placeholder" => 'Department',
                "autocomplete" => "off"
            ),$Departments,[$model_info->department_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Section  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="section_id" class=" <?php echo $label_column; ?>"><?php echo 'Section'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "section_id",
                "name" => "section_id",
                "class" => "form-control select2",
                "placeholder" => 'Section',
                "autocomplete" => "off"
            ),$Sections,[$model_info->section_id]);
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Unit  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="unit_id" class=" <?php echo $label_column; ?>"><?php echo 'Unit'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "unit_id",
                "name" => "unit_id",
                "class" => "form-control select2",
                "placeholder" => 'Unit',
                "autocomplete" => "off"
            ),$Units,[$model_info->unit_id]);
            ?>
        </div>
    </div>
</div>



<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 



<script type="text/javascript">
    var k=1;
    $(document).ready(function () {

      

        setDatePicker("#training_start_date")
        setDatePicker("#training_end_date")
        
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