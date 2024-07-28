
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Company Name  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="payer_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('payer_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "company_name",
                "name" => "company_name",
                "value" => $model_info->company_name,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('payer_name'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Company Type  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="type" class="<?php echo $label_column; ?>"><?php echo app_lang('payer_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "type",
                "name" => "type",
                "value" => $model_info->type,
                "class" => "form-control select2",
                "placeholder" => app_lang('payer_type')
            ),[''=>'', 'Corporate'=>'Corporate','Limited Liability Company (LLC)'=>'Limited Liability Company (LLC)', 'Partnership'=>'Partnership', 'Non-Profit Organization'=>'Non-Profit Organization', 'Trust'=>'Trust', 
            'Estate'=>'Estate', 'Public Limited Company (PLC)'=>'Public Limited Company (PLC)', 'Private Limited Company (Ltd)'=>'Private Limited Company (Ltd)', 'Cooperative (Co-op)'=>'Cooperative (Co-op)', 'Joint Venture (JV)'=>'Joint Venture (JV)',  ],[$model_info->type]
        );
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Payer Size ------------------------------------>

<div class="form-group">

    <div class="row">
        
        <label for="payer_size" class="<?php echo $label_column; ?>"><?php echo app_lang('payer_size'); ?></label>

        <div class=" col-md-9">

            <?php
            echo form_radio(array(
                "id" => "small",
                "name" => "payer_size",
                "class" => "form-check-input",
                    ), "Small", $model_info->payer_size == 'Small'? true : false, "class='form-check-input'");
            ?>
            <label for="small" class="mr15 p0"><?php echo app_lang('small'); ?></label> 

            <?php
            echo form_radio(array(
                "id" => "medium",
                "name" => "payer_size",
                "class" => "form-check-input",
                    ), "Medium" , $model_info?->id ? ($model_info->payer_size == 'Medium' ? true: false) : true, "class='form-check-input'");
            ?>
            <label for="medium" class="p0 mr15"><?php echo app_lang('medium'); ?></label>

            <?php
            echo form_radio(array(
                "id" => "large",
                "name" => "payer_size",
                "class" => "form-check-input",
                    ), "Large" ,$model_info->payer_size == 'Large'? true : false, "class='form-check-input'");
            ?>
            <label for="large" class="p0 mr15"><?php echo app_lang('large'); ?></label>

        </div>

    </div>

</div>

<!-----------------------------------------  Registration Type  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="Reg_Type" class="<?php echo $label_column; ?>"><?php echo app_lang('Registration_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "Reg_Type",
                "name" => "Reg_Type",
                "value" => $model_info->Reg_Type,
                "class" => "form-control select2",
                "placeholder" => app_lang('Registration_type')
            ),[''=>'','New'=>'New','Renew'=>'Renew)'],[$model_info->Reg_Type]
        );
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Registration NO.  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="Reg_NO" class="<?php echo $label_column; ?>"><?php echo app_lang('Registration_no'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "Reg_NO",
                "name" => "Reg_NO",
                "value" => $model_info->Reg_NO,
                "class" => "form-control",
                "placeholder" => app_lang('Registration_no')
            ));
            ?>
        </div>
    </div>
</div>


<!-----------------------------------------    Start Date  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="Start_Date" class="<?php echo $label_column; ?>"><?php echo app_lang('start_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "Start_Date",
                "name" => "Start_Date",
                "value" => $model_info->Start_Date,
                "class" => "form-control date",
                "placeholder" => app_lang('start_date')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  End Date  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="End_Date" class="<?php echo $label_column; ?>"><?php echo app_lang('end_date'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "End_Date",
                "name" => "End_Date",
                "value" => $model_info->End_Date,
                "class" => "form-control date",
                "placeholder" => app_lang('end_date')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------  Merchant Type  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="merchant_id" class="<?php echo $label_column; ?>"><?php echo app_lang('merchant_type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_dropdown("applicant_id", $team_members_dropdown, "", "class='select2 validate-hidden' id='applicant_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
            echo form_dropdown(array(
                "id" => "merchant_id",
                "name" => "merchant_id",
                "class" => "form-control select2",
                "placeholder" => app_lang('merchant_type')
            ),[$Merchant_types_dropdown],[$model_info?->merchant_id]
        );
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------   Merchant Number ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="merchant_number" class="<?php echo $label_column; ?>"><?php echo app_lang('merchant_number'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "merchant_number",
                "name" => "merchant_number",
                "value" => $model_info->merchant_number,
                "class" => "form-control",
                "placeholder" => app_lang('merchant_number')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------   Turnover Tax  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="turnover_tax" class="<?php echo $label_column; ?>"><?php echo app_lang('turnover_tax'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "turnover_tax",
                "name" => "turnover_tax",
                "value" => $model_info->turnover_tax,
                "class" => "form-control",
                "placeholder" => app_lang('turnover_tax')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------   Number of EMployees ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="number_of_employees" class="<?php echo $label_column; ?>"><?php echo app_lang('number_of_employees'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "number_of_employees",
                "name" => "number_of_employees",
                "value" => $model_info->number_of_employees,
                "class" => "form-control",
                "placeholder" => app_lang('number_of_employees')
            ));
            ?>
        </div>
    </div>
</div>
<!-----------------------------------------  Industries ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="industries" class="<?php echo $label_column; ?>"><?php echo app_lang('industries'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "industries",
                "name" => "industries",
                "value" => $model_info->industries,
                "class" => "form-control",
                "placeholder" => app_lang('industries')
            ));
            ?>
        </div>
    </div>
</div>
<!-----------------------------------------  Segment  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="segment" class="<?php echo $label_column; ?>"><?php echo app_lang('segment'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "segment",
                "name" => "segment",
                "value" => $model_info->segment,
                "class" => "form-control",
                "placeholder" => app_lang('segment')
            ));
            ?>
        </div>
    </div>
</div>

<!-----------------------------------------   Contact Name  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="Contact_Name" class="<?php echo $label_column; ?>"><?php echo app_lang('contact_name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "Contact_Name",
                "name" => "Contact_Name",
                "value" => $model_info->Contact_Name,
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


<!-----------------------------------------  Email  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="email" class="<?php echo $label_column; ?>"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "value" => $model_info->email,
                "class" => "form-control",
                "placeholder" => app_lang('email')
            ));
            ?>
        </div>
    </div>
</div>


<!-----------------------------------------  TIN  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="TIN" class="<?php echo $label_column; ?>"><?php echo app_lang('tin'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "TIN",
                "name" => "TIN",
                "value" => $model_info->TIN,
                "class" => "form-control",
                "placeholder" => app_lang('tin')
            ));
            ?>
        </div>
    </div>
</div>

<!------------------------------------------------------------------------------------------>









<!-- 
<div class="form-group">
    <div class="row">
        <label for="city" class="<?php echo $label_column; ?>"><?php echo app_lang('city'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "city",
                "name" => "city",
                "value" => $model_info->city,
                "class" => "form-control",
                "placeholder" => app_lang('city')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="state" class="<?php echo $label_column; ?>"><?php echo app_lang('state'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "state",
                "name" => "state",
                "value" => $model_info->state,
                "class" => "form-control",
                "placeholder" => app_lang('state')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="zip" class="<?php echo $label_column; ?>"><?php echo app_lang('zip'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "zip",
                "name" => "zip",
                "value" => $model_info->zip,
                "class" => "form-control",
                "placeholder" => app_lang('zip')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="country" class="<?php echo $label_column; ?>"><?php echo app_lang('country'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "country",
                "name" => "country",
                "value" => $model_info->country,
                "class" => "form-control",
                "placeholder" => app_lang('country')
            ));
            ?>
        </div>
    </div>
</div>


<div class="form-group">
    <div class="row">
        <label for="website" class="<?php echo $label_column; ?>"><?php echo app_lang('website'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "website",
                "name" => "website",
                "value" => $model_info->website,
                "class" => "form-control",
                "placeholder" => app_lang('website')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="vat_number" class="<?php echo $label_column; ?>"><?php echo app_lang('vat_number'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "vat_number",
                "name" => "vat_number",
                "value" => $model_info->vat_number,
                "class" => "form-control",
                "placeholder" => app_lang('vat_number')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="gst_number" class="<?php echo $label_column; ?>"><?php echo app_lang('gst_number'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "gst_number",
                "name" => "gst_number",
                "value" => $model_info->gst_number,
                "class" => "form-control",
                "placeholder" => app_lang('gst_number')
            ));
            ?>
        </div>
    </div>
</div>

<?php if ($login_user->user_type === "staff") { ?>
    <div class="form-group">
        <div class="row">
            <label for="groups" class="<?php echo $label_column; ?>"><?php echo app_lang('client_groups'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "group_ids",
                    "name" => "group_ids",
                    "value" => $model_info->group_ids,
                    "class" => "form-control",
                    "placeholder" => app_lang('client_groups')
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>


<?php if ($login_user->is_admin && get_setting("module_invoice")) { ?>
    <div class="form-group">
        <div class="row">
            <label for="currency" class="<?php echo $label_column; ?>"><?php echo app_lang('currency'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "currency",
                    "name" => "currency",
                    "value" => $model_info->currency,
                    "class" => "form-control",
                    "placeholder" => app_lang('keep_it_blank_to_use_default') . " (" . get_setting("default_currency") . ")"
                ));
                ?>
            </div>
        </div>
    </div>    
    <div class="form-group">
        <div class="row">
            <label for="currency_symbol" class="<?php echo $label_column; ?>"><?php echo app_lang('currency_symbol'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "currency_symbol",
                    "name" => "currency_symbol",
                    "value" => $model_info->currency_symbol,
                    "class" => "form-control",
                    "placeholder" => app_lang('keep_it_blank_to_use_default') . " (" . get_setting("currency_symbol") . ")"
                ));
                ?>
            </div>
        </div>
    </div>

<?php } ?>
<?php if ($login_user->user_type === "staff") { ?>
    <div class="form-group">
        <div class="row">
            <label for="client_labels" class="<?php echo $label_column; ?>"><?php echo app_lang('labels'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "client_labels",
                    "name" => "labels",
                    "value" => $model_info->labels,
                    "class" => "form-control",
                    "placeholder" => app_lang('labels')
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>

            -->

<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 



<script type="text/javascript">
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