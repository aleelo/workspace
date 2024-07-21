<div class="tab-content">
    <?php echo form_open(get_uri("team_members/save_bank_details"), array("id" => "job-info-form", "class" => "general-form dashed-row white", "role" => "form")); ?>

    <input name="user_id" type="hidden" value="<?php echo $user_id; ?>" />

    <div class="card">
        
        <div class=" card-header">
            <h4><?php echo app_lang('bank_details'); ?></h4>
        </div>
        
        <div class="card-body">

                <div class="form-group">
                    <div class="row">
                        <label for="job_title_en" class=" col-md-3"><?php echo 'Bank Name'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "bank_name",
                                "name" => "bank_name",
                                "class" => "form-control",
                                'value'=>  $job_info->bank_name,
                                "placeholder" => 'Bank Name',
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="job_title_so" class=" col-md-3"><?php echo 'Bank Account'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "bank_account",
                                "name" => "bank_account",
                                "class" => "form-control",
                                "placeholder" => 'Bank Account',
                                'value'=> $job_info->bank_account,
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="employee_id" class=" col-md-3"><?php echo 'Registered Name'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "registered_name",
                                "name" => "registered_name",
                                "class" => "form-control",
                                'value'=> $job_info->registered_name,
                                "placeholder" => 'Registered Name',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>
        </div>

        <?php if ($login_user->is_admin || $can_manage_team_members_job_information) { ?>
            <div class="card-footer rounded-0">
                <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            </div>
        <?php } ?>

    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#job-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                window.location.href = "<?php echo get_uri("team_members/view/" . $job_info->user_id); ?>" + "/job_info";
            }
        });
        $("#job-info-form .select2").select2();

        setDatePicker("#date_of_hire");
        setDatePicker(".date");

    });
</script>    