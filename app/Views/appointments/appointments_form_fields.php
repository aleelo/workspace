
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Appointment Title  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="title" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('title'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "title",
                "name" => "title",
                "value" => $model_info->title,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('title'),
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
        <label for="date" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "date",
                "name" => "date",
                "value" => $model_info->date,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('date'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Time  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="time" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('time'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "time",
                "name" => "time",
                "value" => $model_info->time,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('time'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Room  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="room" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('room'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "room",
                "name" => "room",
                "value" => $model_info->room,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('room'),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Appointment Note  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="note" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('note'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "note",
                "name" => "note",
                "value" => $model_info->note,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('note'),
            ));
            ?>
        </div>
    </div>
</div>


<!----------------------------------------- Appointment Host  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="host_id" class=" <?php echo $label_column; ?>"><?php echo 'Host'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "host_id",
                "name" => "host_id",
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
        <label for="guest_id" class=" <?php echo $label_column; ?>"><?php echo 'Guest'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "guest_id",
                "name" => "guest_id",
                "class" => "form-control select2",
                "placeholder" => 'Guests',
                "autocomplete" => "off"
            ),$guests,[$model_info->guest_id]);
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