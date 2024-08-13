
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Appointment Title  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="appointment_title" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('appointment_title'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "appointment_title",
                "name" => "appointment_title",
                "value" => $model_info->title,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('appointment_title'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

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
                "class" => "form-control company_name_input_section",
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

<!----------------------------------------- Appointment Guest  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="appointment_visitor_id" class=" <?php echo $label_column; ?>"><?php echo 'Visitor'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "appointment_visitor_id",
                "name" => "appointment_visitor_id",
                "class" => "form-control select2",
                "placeholder" => 'Visitor',
                "autocomplete" => "off"
            ),$guests,[$model_info->visitor_id]);
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


    });
</script>