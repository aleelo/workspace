<div class="tab-content">
    <?php echo form_open(get_uri("team_members/save_general_info/" . $user_info->id), array("id" => "general-info-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
    <div class="card">
        <div class=" card-header">
            <h4> <?php echo app_lang('general_info'); ?></h4>
        </div>
        <div class="card-body">
            
            <div class="form-group">
                <div class="row">
                    <label for="first_name" class=" col-md-2"><?php echo app_lang('first_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "first_name",
                            "name" => "first_name",
                            "value" => $user_info->first_name,
                            "class" => "form-control",
                            "placeholder" => app_lang('first_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="last_name" class=" col-md-2"><?php echo app_lang('last_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "last_name",
                            "name" => "last_name",
                            "value" => $user_info->last_name,
                            "class" => "form-control",
                            "placeholder" => app_lang('last_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="address" class=" col-md-2"><?php echo app_lang('mailing_address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_textarea(array(
                            "id" => "address",
                            "name" => "address",
                            "value" => $user_info->address,
                            "class" => "form-control",
                            "placeholder" => app_lang('mailing_address')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="alternative_address" class=" col-md-2"><?php echo app_lang('alternative_address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_textarea(array(
                            "id" => "alternative_address",
                            "name" => "alternative_address",
                            "value" => $user_info->alternative_address,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_address')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="phone" class=" col-md-2"><?php echo app_lang('phone'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "phone",
                            "name" => "phone",
                            "value" => $user_info->phone,
                            "class" => "form-control",
                            "placeholder" => app_lang('phone')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="alternative_phone" class=" col-md-2"><?php echo app_lang('alternative_phone'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "alternative_phone",
                            "name" => "alternative_phone",
                            "value" => $user_info->alternative_phone,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_phone')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="skype" class=" col-md-2">Skype</label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "skype",
                            "name" => "skype",
                            "value" => $user_info->skype ? $user_info->skype : "",
                            "class" => "form-control",
                            "placeholder" => "Skype"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <label for="ssn" class=" col-md-2"><?php echo app_lang('ssn'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "ssn",
                            "name" => "ssn",
                            "value" => $user_info->ssn,
                            "class" => "form-control",
                            "placeholder" => app_lang('ssn')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="gender" class=" col-md-2"><?php echo app_lang('gender'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_radio(array(
                            "id" => "gender_male",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "male", ($user_info->gender === "male") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> 
                        <?php
                        echo form_radio(array(
                            "id" => "gender_female",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "female", ($user_info->gender === "female") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_female" class="p0 mr15"><?php echo app_lang('female'); ?></label>
                       
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="marital_status_single" class=" col-md-2"><?php echo app_lang('marital_status'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_radio(array(
                            "id" => "marital_status_single",
                            "name" => "marital_status",
                            "class" => "form-check-input",
                                ), "single", ($user_info->marital_status === "single") ? true : (empty($user_info->id) ? true : false), "class='form-check-input'");
                        ?>
                        <label for="marital_status_maried" class="mr15 p0"><?php echo app_lang('single'); ?></label> 
                        <?php
                        echo form_radio(array(
                            "id" => "marital_status_maried",
                            "name" => "marital_status",
                            "class" => "form-check-input",
                                ), "maried", ($user_info->marital_status === "maried") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="marital_status_maried" class="p0 mr15"><?php echo app_lang('maried'); ?></label>
                       
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <label for="passport_no" class=" col-md-2"><?php echo 'Passport Number'; ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "passport_no",
                            "name" => "passport_no",
                            "value" => $user_info->passport_no,
                            "class" => "form-control",
                            "placeholder" => 'Passport Number'
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="emergency_name" class=" col-md-2"><?php echo 'Emergency Contact Name'; ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "emergency_name",
                            "name" => "emergency_name",
                            "value" => $user_info->emergency_name,
                            "class" => "form-control",
                            "placeholder" => 'Emergency Contact Name'
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="emergency_phone" class=" col-md-2"><?php echo 'Emergency Contact Phone'; ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "emergency_phone",
                            "name" => "emergency_phone",
                            "value" => $user_info->emergency_phone,
                            "class" => "form-control",
                            "placeholder" => 'Emergency Contact Phone'
                        ));
                        ?>
                    </div>
                </div>
            </div>
                <!-- `marital_status`, `emergency_name`, `emergency_phone`, `birth_date`, `birth_place`, `education_level`, `education_field`, `education_school` -->

            <div class="form-group">
                <div class="row">
                    <label for="birth_date" class=" col-md-2"><?php echo 'Date of Birth'; ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "birth_date",
                            "name" => "birth_date",
                            "value" => $user_info->birth_date,
                            "class" => "form-control",
                            "placeholder" => 'Date of Birth'
                        ));
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <label for="birth_place" class=" col-md-2"><?php echo 'Place of Birth'; ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "birth_place",
                            "name" => "birth_place",
                            "value" => $user_info->birth_place,
                            "class" => "form-control",
                            "placeholder" => 'Place of Birth'
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="relevant_document_url" class=" col-md-2"><?php echo 'Relevant Document Url'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "relevant_document_url",
                            "name" => "relevant_document_url",
                            "value" => $user_info->relevant_document_url,
                            "class" => "form-control",
                            "placeholder" => 'Relevant Document Url e.g. resume drive url',
                            "autocomplete" => "off",
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-2", "field_column" => " col-md-10")); ?> 

        </div>

        <?php if($can_edit_profile){?>
            <div class="card-footer rounded-0">
                <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            </div>
        <?php }?>
        
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#general-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                setTimeout(function () {
                    window.location.href = "<?php echo get_uri("team_members/view/" . $user_info->id); ?>" + "/general";
                }, 500);
            }
        });
        $("#general-info-form .select2").select2();

        setDatePicker("#birth_date");

    });
</script>    