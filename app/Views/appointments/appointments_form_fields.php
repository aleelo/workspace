
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!----------------------------------------- Appointment Date  ------------------------------------>


<div class="form-group">
    <div class="row">

        <label for="appointment_date" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('appointment_date'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "appointment_date",
                "name" => "appointment_date",
                "value" => $model_info->date,
                "class" => "form-control",
                "placeholder" => app_lang('appointment_date'),
            ));
            ?>
        </div>

        <label for="appointment_time" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('appointment_time'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            if (is_date_exists($model_info->time) && $model_info->time == "00:00:00") {
                $time = "";
            } else {
                $time = $model_info->time;
            }

            if ($time_format_24_hours) {
                $time = $time ? date("H:i", strtotime($time)) : "";
            } else {
                $time = $time ? convert_time_to_12hours_format(date("H:i:s", strtotime($time))) : "";
            }
            echo form_input(array(
                "id" => "appointment_time",
                "name" => "appointment_time",
                "value" => $time,
                "class" => "form-control",
                "placeholder" => app_lang('appointment_time'),
            ));
            ?>
        </div>
        
    </div>
</div>


<!----------------------------------------- Appointment Room  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="appointment_room" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('appointment_room'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "appointment_room",
                "name" => "appointment_room",
                "value" => $model_info->room,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('appointment_room'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Note  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="appointment_note" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('appointment_note'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "appointment_note",
                "name" => "appointment_note",
                "value" => $model_info->note,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('appointment_note'),
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Appointment Host  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="appointment_host_id" class=" <?php echo $label_column; ?>"><?php echo 'Host'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_host_id",
                "name" => "appointment_host_id",
                "class" => "form-control select2",
                "placeholder" => 'Host Person',
                "autocomplete" => "off"
            ),$host,[$model_info->host_id]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Meeting With  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="meeting_with" class="<?php echo $label_column; ?>"><?php echo 'Meeting With'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            $meetingWith = [''=>' - ','Payers'=>'Payers',
            'Partners'=>'Partners','Visitors'=>'Visitors','Employees'=>'Employees'];
            echo form_dropdown(array(
                "id" => "meeting_with",
                "name" => "meeting_with",
                "class" => "form-control select2",
                "placeholder" => 'Meeting With',
                "autocomplete" => "off"
            ),$meetingWith,[$model_info->meeting_with]);
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Departments  ------------------------------------>

<div class="form-group" id="departments_section">
    <div class="row">
        <label for="appointment_department_ids" class=" <?php echo $label_column; ?>"><?php echo 'Departments'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            
            echo form_dropdown(array(
                "id" => "appointment_department_ids",
                "name" => "appointment_department_ids[]",
                "class" => "form-control select2",
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Departments -- ',
                "autocomplete" => "off",
            ),$departments,$model_info->department_ids ? explode(',', $model_info->department_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Sections  ------------------------------------>

<div class="form-group" id="sections_section">
    <div class="row">
        <label for="appointment_section_ids" class=" <?php echo $label_column; ?>"><?php echo 'Sections'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_section_ids",
                "name" => "appointment_section_ids[]",
                "class" => "form-control select2",
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Sections -- ',
                "autocomplete" => "off",
            ),$Sections,$model_info->section_ids ? explode(',', $model_info->section_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Units  ------------------------------------>

<div class="form-group" id="units_section">
    <div class="row">
        <label for="appointment_unit_ids" class=" <?php echo $label_column; ?>"><?php echo 'Units'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_unit_ids",
                "name" => "appointment_unit_ids[]",
                "class" => "form-control select2",
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Units -- ',
                "autocomplete" => "off",
            ),$Units,$model_info->unit_ids ? explode(',', $model_info->unit_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Payers  ------------------------------------>

<div class="form-group" id="payers_section">
    <div class="row">
        <label for="appointment_payer_ids" class=" <?php echo $label_column; ?>"><?php echo 'Payers'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_payer_ids",
                "name" => "appointment_payer_ids[]",
                "class" => "form-control select2",
                "placeholder" => 'Payers',
                "multiple" => "multiple",
                "placeholder" => ' -- Choose Payers -- ',
                "autocomplete" => "off",
            ),$payers,$model_info->payer_ids ? explode(',', $model_info->payer_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Partners  ------------------------------------>

<div class="form-group" id="partners_section">
    <div class="row">
        <label for="appointment_partner_ids" class=" <?php echo $label_column; ?>"><?php echo 'Partners'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_partner_ids",
                "name" => "appointment_partner_ids[]",
                "class" => "form-control select2",
                "placeholder" => ' -- Choose Partners -- ',
                "multiple" => "multiple",
                "autocomplete" => "off"
            ),$partners,$model_info->partner_ids ? explode(',', $model_info->partner_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Visitors  ------------------------------------>

<div class="form-group" id="visitors_section">
    <div class="row">
        <label for="appointment_visitor_ids" class=" <?php echo $label_column; ?>"><?php echo 'Visitors'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_visitor_ids",
                "name" => "appointment_visitor_ids[]",
                "class" => "form-control select2",
                "placeholder" => ' -- Choose Visitors -- ',
                "multiple" => "multiple",
                "autocomplete" => "off"
            ),$guests,$model_info->visitor_ids ? explode(',', $model_info->visitor_ids) : []); // Handle multiple values
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Emplyees  ------------------------------------>

<div class="form-group" id="employees_section">
    <div class="row">
        <label for="appointment_employee_ids" class=" <?php echo $label_column; ?>"><?php echo 'Employees'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_employee_ids",
                "name" => "appointment_employee_ids[]",
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

        setDatePicker("#appointment_date");
        setTimePicker("#appointment_time");


        // Function to reset other dropdowns
        function resetOtherDropdowns(excludeSection) {
            var sections = ['#departments_section', '#sections_section', '#units_section', '#payers_section', '#partners_section', '#visitors_section', '#employees_section'];
        
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
        $('#payers_section').hide();
        $('#partners_section').hide();
        $('#visitors_section').hide();
        $('#employees_section').hide();
    }

    // Call this function whenever the "Meeting With" dropdown changes
    $('#meeting_with').on('change', function () {
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
            case 'Payers':
                $('#payers_section').show();
                resetOtherDropdowns('#payers_section');
                break;
            case 'Partners':
                $('#partners_section').show();
                resetOtherDropdowns('#partners_section');
                break;
            case 'Visitors':
                $('#visitors_section').show();
                resetOtherDropdowns('#visitors_section');
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
    $('#meeting_with').trigger('change');


    });
</script>