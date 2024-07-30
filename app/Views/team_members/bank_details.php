<div class="tab-content">
    <?php echo form_open(get_uri("team_members/save_bank_details/" . $user_info->id), array("id" => "bank-details-form", "class" => "education-form dashed-row white", "role" => "form")); ?>
    <div class="card">
        <div class=" card-header">
            <h4> <?php echo app_lang('education_info'); ?></h4>
        </div>
        <div class="card-body">

            
            <div class="form-group">
                <div class="row">
                    <label for="bank_name" class=" col-md-2"><?php echo 'Bank Name'; ?></label>
                    <div class=" col-md-10">
                    <?php
                    echo form_dropdown(array( 
                                'id'=> "bank_id",
                                'name'=> "bank_id",
                                'class' => "form-control select2",
                                'placeholder' => 'Bank Name',
                                'autocomplete'=> "off",
                                'data-rule-required' => true,
                                'data-msg-required' =>   app_lang('field_required')
                            ),$bank_names_dropdown,[$user_info->bank_id]); ?>
                       
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="bank_account" class=" col-md-2"><?php echo 'Bank Account'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "bank_account",
                            "name" => "bank_account",
                            "class" => "form-control",
                            "value" => $user_info->bank_account,
                            "placeholder" => 'Bank Account',
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="bank_registered_name" class=" col-md-2"><?php echo 'Bank Registered Name'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "bank_registered_name",
                            "name" => "bank_registered_name",
                            "class" => "form-control",
                            "value" => $user_info->registered_name,
                            "placeholder" => 'Bank Registered Name',
                        ));
                        ?>
                    </div>
                </div>
            </div>



            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-2", "field_column" => " col-md-10")); ?> 

        </div>
        <div class="card-footer rounded-0">
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#bank-details-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                setTimeout(function () {
                    window.location.href = "<?php echo get_uri("team_members/view/" . $user_info->id); ?>" + "/Bank-Details";
                }, 500);
            }
        });
        $("#bank-details-form .select2").select2();

        setDatePicker("#birth_date");

    });
</script>    