
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
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Trainer  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="trainer_id" class=" <?php echo $label_column; ?>"><?php echo 'Trainer Name'; ?></label>
        <div class=" col-md-10">
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

<!----------------------------------------- Training Type  ------------------------------------>

<div class="form-group">
    <div class="row">
        
        <label for="Training_Type" class=" <?php echo $label_column; ?>"><?php echo 'Training Type'; ?></label>
        <div class=" col-md-10">
            <?php
            $Training_Type = [''=>' -- choose training type -- ','Technical Skills'=>'Technical Skills','Soft Skills'=>'Soft Skills','Others'=>'Others'];
            echo form_dropdown(array(
                "id" => "Training_Type",
                "name" => "Training_Type",
                "class" => "form-control select2",
                "placeholder" => 'Training Type',
                "autocomplete" => "off"
            ),$Training_Type,[$model_info->type]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Technical Skills  ------------------------------------>


<div class="form-group" id="technical_skills_section">
    <div class="row">
        <label for="technical_skills" class=" <?php echo $label_column; ?>"><?php echo 'Technical Skills'; ?></label>
        <div class=" col-md-10">
            <?php
            $technical_skills = [''=>' -- choose technical skills -- ','Data Analysis'=>'Data Analysis','Programming & Coding'=>'Programming & Coding','IT Security'=>'IT Security',
            'Software Training'=>'Software Training','Digital Marketing'=>'Digital Marketing'];
            echo form_dropdown(array(
                "id" => "technical_skills",
                "name" => "technical_skills",
                "class" => "form-control select2",
                "placeholder" => 'Technical Skills',
                "autocomplete" => "off"
            ),$technical_skills,[$model_info->technical_skills]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Soft Skills  ------------------------------------>


<div class="form-group" id="soft_skills_section">
    <div class="row">
        <label for="soft_skills" class=" <?php echo $label_column; ?>"><?php echo 'Soft Skills'; ?></label>
        <div class=" col-md-10">
            <?php
            $soft_skills = [''=>' -- choose soft skills -- ','Communication Skills'=>'Communication Skills','Time Management'=>'Time Management',
            'Team Building'=>'Team Building','Team Building'=>'Team Building'];
            echo form_dropdown(array(
                "id" => "soft_skills",
                "name" => "soft_skills",
                "class" => "form-control select2",
                "placeholder" => 'Soft Skills',
                "autocomplete" => "off"
            ),$soft_skills,[$model_info->soft_skills]);
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Delivery Mode  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="delivery_mode" class=" <?php echo $label_column; ?>"><?php echo 'Delivery Mode'; ?></label>
        <div class=" col-md-10">
            <?php
            $delivery_mode = [''=>' -- choose delivery mode -- ','Virtual'=>'Virtual','Face 2 Face'=>'Face 2 Face','Workshop'=>'Workshop'];
            echo form_dropdown(array(
                "id" => "delivery_mode",
                "name" => "delivery_mode",
                "class" => "form-control select2",
                "placeholder" => 'Delivery Mode',
                "autocomplete" => "off"
            ),$delivery_mode,[$model_info->delivery_mode]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Platfroms  ------------------------------------>

<div class="form-group" id="platform_section">
    <div class="row">
        <label for="platform" class=" <?php echo $label_column; ?>"><?php echo 'Platform'; ?></label>
        <div class=" col-md-10">
            <?php
            $platform = [''=>' -- choose platform -- ','Zoom'=>'Zoom','Microsoft Teams'=>'Microsoft Teams','Google Meet'=>'Google Meet'
            ,'Cisco WebEx'=>'Cisco WebEx','Skype for Business'=>'Skype for Business'];
            echo form_dropdown(array(
                "id" => "platform",
                "name" => "platform",
                "class" => "form-control select2",
                "placeholder" => 'Platform',
                "autocomplete" => "off"
            ),$platform,[$model_info->platform]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Training Start Date  ------------------------------------>


<div class="form-group">
    <div class="row">

        <label for="training_start_date" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('training_start_date'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

        <label for="training_end_date" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('training_end_date'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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


<!----------------------------------------- Duration & Training Location  ------------------------------------ >


<div class="form-group">
    <div class="row">

        <label for="training_duration" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('duration'); ?></label>
        <div class=" col-md-4" style="position: relative;">
        <span style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%);">hrs</span>
            <?php
            echo form_input(array(
                "id" => "training_duration",
                "name" => "training_duration",
                "value" => $model_info->training_duration,
                "class" => "form-control company_name_input_section",
                "style" => "padding-left: 35px;", // Adjust padding to make room for the dollar sign
                "placeholder" => app_lang('duration'),
            ));
            ?>
            
        </div>
        
        <label for="training_location" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('Training_location'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "training_location",
                "name" => "training_location",
                "class" => "form-control select2",
                "placeholder" => 'Training Location',
                "autocomplete" => "off"
            ),$locations,[$model_info->training_location]);
            ?>
        </div>

    </div>
</div>

<!----------------------------------------- Objectives  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="objectives" class=" <?php echo $label_column; ?>"><?php echo 'Objectives'; ?></label>
        <div class=" col-md-10">
            <?php
            echo form_textarea(array(
                "id" => "objectives",
                "name" => "objectives",
                "class" => "form-control",
                "placeholder" => 'Objectives',
                "value" => $model_info->objectives

            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Training Funders  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="funder" class=" <?php echo $label_column; ?>"><?php echo 'Funder'; ?></label>
        <div class=" col-md-10">
            <?php
            echo form_dropdown(array(
                "id" => "funder",
                "name" => "funder",
                "class" => "form-control select2",
                "placeholder" => 'Funder',
                "autocomplete" => "off"
            ),$funders,[$model_info->funder_id]);
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Budget  ------------------------------------>


<div class="form-group" id="budget_section">
    <div class="row">
        <label for="budget" class="<?php echo $label_column; ?> company_name_section"><?php echo 'Budget'; ?></label>
        <div class="<?php echo $field_column; ?>" style="position: relative;">
            <!-- Dollar sign span -->
            <span style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%);">$</span>

            <?php
            echo form_input(array(
                "id" => "budget",
                "name" => "budget",
                "value" => $model_info->budget,
                "class" => "form-control company_name_input_section",
                "style" => "padding-left: 25px;", // Adjust padding to make room for the dollar sign
                "placeholder" => 'Budget',
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Training Participant  ------------------------------------>



<div class="form-group">
    <div class="row">
        <label for="training_participant" class=" <?php echo $label_column; ?>"><?php echo 'Participant'; ?></label>
        <div class=" col-md-10">
            <?php
            // $participant = [''=>'-- choose a participant --','Employees'=>'Employees','Units'=>'Units','Sections'=>'Sections','Departments'=>'Departments'];
            $participant = [''=>'-- choose a participant --','Employees'=>'Employees'];
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

<!----------------------------------------- Files  ------------------------------------>

<div class="form-group">
    <div class="col-md-12">
        <?php
        echo view("includes/file_list", array("files" => $model_info->files));
        ?>
    </div>
</div>

<?php echo view("includes/dropzone_preview"); ?>



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

// ------------------------------- Delivery Mode ----------------------------------------------------------------------------------------------- //

    function resetOtherDropdownsDeliveryMode(excludeSection) {
        var sections = ['#platform_section'];
        
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
      function hideAllSectionsDeliveryMode() {
        $('#platform_section').hide();
    }

    // Call this function whenever the "Meeting With" dropdown changes
    $('#delivery_mode').on('change', function () {
        var delivery_mode = $(this).val();

        // Hide all sections first
        hideAllSectionsDeliveryMode();

        // Show the appropriate section(s) based on the selected meeting_with value and reset others
        switch (delivery_mode) {
            case 'Virtual':
                $('#platform_section').show();
                resetOtherDropdownsDeliveryMode('#platform_section');
                break;
            default:
                hideAllSectionsDeliveryMode(); // If no valid selection is made, hide all sections and reset all dropdowns
                resetOtherDropdownsDeliveryMode(null);
        }
    });

    // Trigger change on page load to set the correct visibility based on any pre-selected value
    $('#delivery_mode').trigger('change');

// ---------------------------- Funder  --------------------------------------------------------------------------------------------- //

function resetOtherDropdownsFunder(excludeSection) {
        var sections = ['#budget_section'];
        
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
      function hideAllSectionsFunder() {
        $('#budget_section').hide();
    }

    // Call this function whenever the "Meeting With" dropdown changes
    $('#funder').on('change', function () {
        var funder = $(this).val();

        // Hide all sections first
        hideAllSectionsFunder();

        // Show the appropriate section(s) based on the selected meeting_with value and reset others
        switch (funder) {
            case '3':
                $('#budget_section').show();
                resetOtherDropdownsFunder('#budget_section');
                break;
            default:
            hideAllSectionsFunder(); // If no valid selection is made, hide all sections and reset all dropdowns
            resetOtherDropdownsFunder(null);
        }
    });

    // Trigger change on page load to set the correct visibility based on any pre-selected value
    $('#funder').trigger('change');

// ------------------------------  Training Type  ------------------------------------------------------------------------------------------- //

    function resetOtherDropdownsTrainingType(excludeSection) {
        var sections = ['#technical_skills_section', '#soft_skills_section'];
        
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
      function hideAllSectionsTrainingType() {
        $('#technical_skills_section').hide();
        $('#soft_skills_section').hide();
    }

    // Call this function whenever the "Meeting With" dropdown changes
    $('#Training_Type').on('change', function () {
        var training_Type = $(this).val();

        // Hide all sections first
        hideAllSectionsTrainingType();

        // Show the appropriate section(s) based on the selected meeting_with value and reset others
        switch (training_Type) {
            case 'Technical Skills':
                $('#technical_skills_section').show();
                resetOtherDropdownsTrainingType('#technical_skills_section');
                break;
            case 'Soft Skills':
                $('#soft_skills_section').show();
                resetOtherDropdownsTrainingType('#soft_skills_section');
                break;
            default:
                hideAllSectionsTrainingType(); // If no valid selection is made, hide all sections and reset all dropdowns
                resetOtherDropdownsTrainingType(null);
        }
    });

    // Trigger change on page load to set the correct visibility based on any pre-selected value
    $('#Training_Type').trigger('change');

// -------------------------------------------------------------------------------------------------------------------------------------- //


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
        var training_participant = $(this).val();

        // Hide all sections first
        hideAllSections();

        // Show the appropriate section(s) based on the selected meeting_with value and reset others
        switch (training_participant) {
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