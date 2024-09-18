
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
        <label for="training_location_id" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('Training_location'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "training_location_id",
                "name" => "training_location_id",
                "class" => "form-control select2",
                "placeholder" => 'Training Location',
                "autocomplete" => "off"
            ),$training_location,[$model_info->training_location_id]);
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
        <label for="training_participant" class=" <?php echo $label_column; ?>"><?php echo 'Participant'; ?></label>
        <div class=" col-md-9">
            <?php
            $participant = [''=>'-- choose a participant --','Employees'=>'Employees','Units'=>'Units','Sections'=>'Sections','Departments'=>'Departments'];
            echo form_dropdown(array(
                "id" => "training_participant",
                "name" => "training_participant",
                "class" => "form-control select2",
                "placeholder" => 'Participant',
                "autocomplete" => "off"
            ),$participant,[$model_info->participant]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Departments  ------------------------------------>

<div class="form-group" id="departments_section">
    <div class="row">
        <label for="training_department_ids" class=" <?php echo $label_column; ?>"><?php echo 'Departments'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            
            echo form_dropdown(array(
                "id" => "training_department_ids",
                "name" => "training_department_ids[]",
                "class" => "form-control select2",
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Departments -- ',
                "autocomplete" => "off",
            ),$departments,$model_info->department_ids ? explode(',', $model_info->department_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Sections  ------------------------------------>

<div class="form-group" id="sections_section">
    <div class="row">
        <label for="training_section_ids" class=" <?php echo $label_column; ?>"><?php echo 'Sections'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "training_section_ids",
                "name" => "training_section_ids[]",
                "class" => "form-control select2",
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Sections -- ',
                "autocomplete" => "off",
            ),$sections,$model_info->section_ids ? explode(',', $model_info->section_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Units  ------------------------------------>

<div class="form-group" id="units_section">
    <div class="row">
        <label for="training_unit_ids" class=" <?php echo $label_column; ?>"><?php echo 'Units'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "training_unit_ids",
                "name" => "training_unit_ids[]",
                "class" => "form-control select2",
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Units -- ',
                "autocomplete" => "off",
            ),$units,$model_info->unit_ids ? explode(',', $model_info->unit_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Emplyees  ------------------------------------>

<div class="form-group" id="employees_section">
    <div class="row">
        <label for="training_employee_ids" class=" <?php echo $label_column; ?>"><?php echo 'Employees'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "training_employee_ids",
                "name" => "training_employee_ids[]",
                "class" => "form-control select2",
                "placeholder" => ' -- Choose Employees -- ',
                "multiple" => "multiple",
                "autocomplete" => "off"
            ),$employees,$model_info->employee_ids ? explode(',', $model_info->employee_ids) : []); // Handle multiple values
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

    function resetOtherDropdowns(excludeSection) {
        var sections = ['#departments_section', '#sections_section', '#units_section', '#employees_section'];
        
        // Remove the excluded section from the list
        sections = sections.filter(function (item) {
            return item !== excludeSection;
        });

        // Loop through each section and reset the values
        sections.forEach(function (section) {
            $(section + ' select').val(null).trigger('change'); // Clear selection
        });
    }

       // Initially hide all sections
       function hideAllSections() {
        $('#departments_section').hide();
        $('#sections_section').hide();
        $('#units_section').hide();
        $('#employees_section').hide();
    }

    // Call this function whenever the "Meeting With" dropdown changes
    $('#training_participant').on('change', function () {
        var meeting_with = $(this).val();

        // Hide all sections first
        hideAllSections();

        // Show the appropriate section(s) based on the selected meeting_with value and reset others
        switch (meeting_with) {
            case 'Departments':
                $('#departments_section').show();
                resetOtherDropdowns('#departments_section');
                break;
            case 'Sections':
                $('#sections_section').show();
                resetOtherDropdowns('#sections_section');
                break;
            case 'Units':
                $('#units_section').show();
                resetOtherDropdowns('#units_section');
                break;
            case 'Employees':
                $('#employees_section').show();
                resetOtherDropdowns('#employees_section');
                break;
            default:
                hideAllSections(); // If no valid selection is made, hide all sections and reset all dropdowns
                resetOtherDropdowns(null);
        }
    });

    // Trigger change on page load to set the correct visibility based on any pre-selected value
    $('#training_participant').trigger('change');
</script>