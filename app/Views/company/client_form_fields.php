<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------   Type  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="type" class="<?php echo $label_column; ?>"><?php echo app_lang('type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_radio(array(
                "id" => "type_organization",
                "name" => "account_type",
                "class" => "form-check-input account_type",
                "data-msg-required" => app_lang("field_required"),
                    ), "organization", ($model_info->type === "organization") ? true : (($model_info->type !== "person") ? true : false));
            ?>
            <label for="type_organization" class="mr15"><?php echo app_lang('organization'); ?></label>
            <?php
            echo form_radio(array(
                "id" => "type_person",
                "name" => "account_type",
                "class" => "form-check-input account_type",
                "data-msg-required" => app_lang("field_required"),
                    ), "person", ($model_info->type === "person") ? true : false);
            ?>
            <label for="type_person" class=""><?php echo app_lang('person'); ?></label>
        </div>
    </div>
</div>

<!-----------------------------------------  Company Name  ------------------------------------>

<?php if ($model_info->id) { ?>
    <div class="form-group">
        <div class="row">
            <?php if ($model_info->type == "person") { ?>
                <label for="name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('name'); ?></label>
            <?php } else { ?>
                <label for="company_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('company_name'); ?></label>
            <?php } ?>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => ($model_info->type == "person") ? "name" : "company_name",
                    "name" => "company_name",
                    "value" => $model_info->company_name,
                    "class" => "form-control company_name_input_section",
                    "placeholder" => app_lang('company_name'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="form-group">
        <div class="row">
            <label for="company_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('company_name'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "company_name",
                    "name" => "company_name",
                    "value" => $model_info->company_name,
                    "class" => "form-control company_name_input_section",
                    "placeholder" => app_lang('company_name'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>


<!-----------------------------------------  Registration Type  ------------------------------------>

<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
    <div class="form-group">
        <div class="row">
            <label for="created_by" class="<?php echo $label_column; ?>"><?php echo app_lang('registration_type'); ?>
                <span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('the_person_who_will_manage_this_client') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
            </label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "created_by",
                    "name" => "created_by",
                    "value" => $model_info->created_by ? $model_info->created_by : $login_user->id,
                    "class" => "form-control",
                    "placeholder" => app_lang('owner'),
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required")
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>


<!-----------------------------------------    Start Date  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="start_date" class=" col-md-3"><?php echo app_lang('start_date'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "start_date",
                "name" => "start_date",
                "value" => is_date_exists($model_info->created_by) ? $model_info->created_by : "",
                "class" => "form-control",
                "placeholder" => app_lang('start_date'),
                "autocomplete" => "off"
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------   Contact Name  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="city" class="<?php echo $label_column; ?>"><?php echo app_lang('contact_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "city",
                "name" => "city",
                "value" => $model_info->city,
                "class" => "form-control",
                "placeholder" => app_lang('contact_name')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Address  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="address" class="<?php echo $label_column; ?>"><?php echo app_lang('address'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "address",
                "name" => "address",
                "value" => $model_info->address ? $model_info->address : "",
                "class" => "form-control",
                "placeholder" => app_lang('address')
            ));
            ?>

        </div>
    </div>
</div>

<!-----------------------------------------  Phone Number  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="phone" class="<?php echo $label_column; ?>"><?php echo app_lang('phone_number'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "phone",
                "name" => "phone",
                "value" => $model_info->phone,
                "class" => "form-control",
                "placeholder" => app_lang('phone_number')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  TIN  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="zip" class="<?php echo $label_column; ?>"><?php echo app_lang('tin'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "zip",
                "name" => "zip",
                "value" => $model_info->zip,
                "class" => "form-control",
                "placeholder" => app_lang('tin')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Date  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="start_date" class=" col-md-3"><?php echo app_lang('date'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "start_date",
                "name" => "start_date",
                "value" => is_date_exists($model_info->created_by) ? $model_info->created_by : "",
                "class" => "form-control",
                "placeholder" => app_lang('date'),
                "autocomplete" => "off"
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Large / Medium  ------------------------------------>

<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<?php if ($login_user->is_admin && get_setting("module_invoice")) { ?>
    <div class="form-group">
        <div class="row">
            <label for="disable_online_payment" class="<?php echo $label_column; ?> col-xs-8 col-sm-6"><?php echo app_lang('large_medium'); ?></label>
            <div class="<?php echo $field_column; ?> col-xs-4 col-sm-6">
                <?php
                echo form_checkbox("disable_online_payment", "1", $model_info->disable_online_payment ? true : false, "id='disable_online_payment' class='form-check-input'");
                ?>                       
            </div>
        </div>
    </div>
<?php } ?>

<!-----------------------------------------  Registration NO.  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="zip" class="<?php echo $label_column; ?>"><?php echo app_lang('registration_no'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "zip",
                "name" => "zip",
                "value" => $model_info->zip,
                "class" => "form-control",
                "placeholder" => app_lang('registration_no')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  End Date  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="start_date" class=" col-md-3"><?php echo app_lang('end_date'); ?></label>
        <div class=" col-md-9">
            <?php
            echo form_input(array(
                "id" => "start_date",
                "name" => "start_date",
                "value" => is_date_exists($model_info->created_by) ? $model_info->created_by : "",
                "class" => "form-control",
                "placeholder" => app_lang('end_date'),
                "autocomplete" => "off"
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Email  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="state" class="<?php echo $label_column; ?>"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "state",
                "name" => "state",
                "value" => $model_info->state,
                "class" => "form-control",
                "placeholder" => app_lang('email')
            ));
            ?>
        </div>
    </div>
</div>





<script type="text/javascript">
    $(document).ready(function () {
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

    });
</script>