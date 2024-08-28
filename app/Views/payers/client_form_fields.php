
<input type="hidden" name="id" id="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Company Name  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="payer_name" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('payer_name_l'); ?></label>
        <div class="<?php echo $field_column_3; ?>">
            <?php
            echo form_input(array(
                "id" => "company_name",
                "name" => "company_name",
                "value" => $model_info->company_name,
                "class" => "form-control company_name_input_section",
                "placeholder" => app_lang('payer_name_p'),
                //"autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<!----------------------------------------- Payer Segment & Company Type  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="type" class="<?php echo $label_column_2; ?>"><?php echo app_lang('payer_type_l'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
            <?php
            
            echo form_dropdown(array(
                "id" => "type",
                "name" => "type",
                "class" => "form-control select2",
                "placeholder" => app_lang('payer_type_p')
            ),$payer_type,[$model_info->type]);
            ?>
        </div>

        <label for="payer_size" class="<?php echo $label_column_2; ?>"><?php echo app_lang('payer_segment_l'); ?></label>

        <div class=" col-md-4">
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


<!-----------------------------------------  Registration Type & Registration NO. ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="Reg_Type" class="<?php echo $label_column_2; ?>"><?php echo app_lang('registration_type_l'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "Reg_Type",
                "name" => "Reg_Type",
                "class" => "form-control select2",
                "placeholder" => app_lang('registration_type_p')
            ),[''=>' - ','New'=>'New','Renew'=>'Renew'],[$model_info->Reg_Type]
            );
            ?>
        </div>

        <label for="Reg_NO" class="<?php echo $label_column_2; ?>"><?php echo app_lang('registration_no_l'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "Reg_NO",
                "name" => "Reg_NO",
                "value" => $model_info->Reg_NO,
                "class" => "form-control",
                "placeholder" => app_lang('registration_no_p')
            ));
            ?>
        </div>

    </div>
</div>


 
<!-----------------------------------------    Start Date & End Date  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="Start_Date" class="<?php echo $label_column_2; ?>"><?php echo app_lang('start_date_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "Start_Date",
                "name" => "Start_Date",
                "value" => $model_info->Start_Date,
                "class" => "form-control date",
                "placeholder" => app_lang('start_date_p')
            ));
            ?>
        </div>

        <label for="End_Date" class="<?php echo $label_column_2; ?>"><?php echo app_lang('end_date_l'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "End_Date",
                "name" => "End_Date",
                "value" => $model_info->End_Date,
                "class" => "form-control date",
                "placeholder" => app_lang('end_date_p')
            ));
            ?>
        </div>

    </div>

</div>

<!-----------------------------------------   District & Turnover Tax  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="district" class="<?php echo $label_column_2; ?>"><?php echo app_lang('district_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "district",
                "name" => "district",
                "class" => "form-control select2",
                "placeholder" => app_lang('district_p')
            ),$districts,[$model_info->district]);
            ?>
        </div>
        
        <label for="turnover_tax" class="<?php echo $label_column_2; ?>"><?php echo app_lang('turnover_tax_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "turnover_tax",
                "name" => "turnover_tax",
                "value" => $model_info->turnover_tax,
                "class" => "form-control",
                "placeholder" => app_lang('turnover_tax_p')
            ));
            ?>
        </div>

        

    </div>
</div>

<!-----------------------------------------  Number of EMployees & Industries ------------------------------------>

<div class="form-group">
    <div class="row">

        <label for="number_of_employees" class="<?php echo $label_column_2; ?>"><?php echo app_lang('number_of_employees_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "number_of_employees",
                "name" => "number_of_employees",
                "value" => $model_info->number_of_employees,
                "class" => "form-control",
                "placeholder" => app_lang('number_of_employees_p')
            ));
            ?>
        </div>
        
        <label for="industries" class="<?php echo $label_column_2; ?>"><?php echo app_lang('industries_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "industries",
                "name" => "industries",
                "value" => $model_info->industries,
                "class" => "form-control",
                "placeholder" => app_lang('industries_p')
            ));
            ?>
        </div>

    </div>
</div>

<!-----------------------------------------  TIN ------------------------------------>

<div class="form-group">
    <div class="row">

        <label for="TIN" class="<?php echo $label_column_2; ?>"><?php echo app_lang('tin_l'); ?></label>
        <div class="<?php echo $field_column_3; ?>">
            <?php
            echo form_input(array(
                "id" => "TIN",
                "name" => "TIN",
                "value" => $model_info->TIN,
                "class" => "form-control",
                "placeholder" => app_lang('tin_P')
            ));
            ?>
        </div>
    </div>
</div>


<!-----------------------------------------   Contact Name & Gender  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="Contact_Name" class="<?php echo $label_column_2; ?>"><?php echo app_lang('contact_name_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "Contact_Name",
                "name" => "Contact_Name",
                "value" => $model_info->Contact_Name,
                "class" => "form-control",
                "placeholder" => app_lang('contact_name_p')
            ));
            ?>
        </div>

        <label for="gender" class="<?php echo $label_column_2; ?>"><?php echo app_lang('gender_l'); ?></label>

        <div class=" col-md-4">
            <?php
            echo form_radio(array(
                "id" => "male",
                "name" => "gender",
                "class" => "form-check-input",
                    ), "male", $model_info->gender == 'male'? true : false, "class='form-check-input'");
            ?>
            <label for="male" class="mr15 p0"><?php echo app_lang('male'); ?></label> 

            <?php
            echo form_radio(array(
                "id" => "female",
                "name" => "gender",
                "class" => "form-check-input",
                    ), "female" , $model_info?->id ? ($model_info->gender == 'female' ? true: false) : true, "class='form-check-input'");
            ?>
            <label for="female" class="p0 mr15"><?php echo app_lang('female'); ?></label>

        </div>

    </div>

</div>

<!-----------------------------------------  Nationality & Date of Birth  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="nationality" class="<?php echo $label_column_2; ?>"><?php echo app_lang('nationality_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_dropdown(array(
                "id" => "nationality",
                "name" => "nationality",
                "class" => "form-control select2",
                "placeholder" => app_lang('nationality_p')
            ),$nationalities,[$model_info->nationality]);
            
            ?>
        </div>

        <label for="date_of_birth" class="<?php echo $label_column_2; ?>"><?php echo app_lang('date_of_birth_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "date_of_birth",
                "name" => "date_of_birth",
                "value" => $model_info->date_of_birth,
                "class" => "form-control date",
                "placeholder" => app_lang('date_of_birth_p')
            ));
            ?>
        </div>

    </div>

</div>

<!-----------------------------------------  Phone & Email  ------------------------------------>

<div class="form-group">

    <div class="row">

        

        <label for="phone" class="<?php echo $label_column_2; ?>"><?php echo app_lang('phone_number_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "phone",
                "name" => "phone",
                "value" => $model_info->phone,
                "class" => "form-control",
                "placeholder" => app_lang('phone_number_p')
            ));
            ?>
        </div>

        <label for="email" class="<?php echo $label_column_2; ?>"><?php echo app_lang('email_l'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "value" => $model_info->email,
                "class" => "form-control",
                "placeholder" => app_lang('email_p')
            ));
            ?>
        </div>

    </div>

</div>

<!-----------------------------------------  Address  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="address" class="<?php echo $label_column_2; ?>"><?php echo app_lang('address_l'); ?></label>
        <div class="<?php echo $field_column_3; ?>">
            <?php
            echo form_textarea(array(
                "id" => "address",
                "name" => "address",
                "value" => $model_info->address ? $model_info->address : "",
                "class" => "form-control",
                "placeholder" => app_lang('address_p')
            ));
            ?>

        </div>
    </div>
</div>


<!-----------------------------------------  Email  ------------------------------------>

<div class="form-group">
    <div class="row">
        
    </div>
</div>


<!------------------------------------------------------------------------------------------>

<div class="form-group ">
    <hr class="mt-4 mb-4">
    <button type="button" class="btn btn-success float-end" id="add_payer_btn"><i data-feather="plus-circle" class='icon'></i> Add Merchant</button>

</div>
<div class="form-group mt-4" style="clear: both;">
    <div class="row">
        <table class="table" id="add_payers_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Merchant Type</th>
                    <th>Merchant Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>       
    </div>
</div>



<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 



<script type="text/javascript">

    var k=1;
    $(document).ready(function () {

        // Instert new Data 

        $('#add_payers_table').hide();

        $('.modal-dialog').removeClass('modal-lg').addClass('modal-xl');
        // add Payers table
        $('#add_payer_btn').on('click', function(){

            $('#add_payers_table').show();

            //remove button
            var actions = "<button type='button' class='btn btn-danger btn-sm mt-2  round ml-2 p-1 ' onclick='$(this).parent().parent().remove();k--;'><i data-feather='minus-circle' class='icon'></i></button>";

            $('#add_payers_table tbody').append(
                "<tr class=''>"+
                "<td>" + k + "</td>"+
                    "<td><input type='text' class='form-control' data-rule-required data-msg-required='This field is required.' id='merchant_type" + k + "' placeholder='Merchant Type' name='merchant_type[]'></td>"+
                    "<td><input type='text' class='form-control'  id='merchant_number" + k + "' placeholder='Merchant Number'  name='merchant_number[]'></td>"+
                    "<td style='width: 110px;'>" + actions + "</td>"+
                "</tr>"
            );

            $("#merchant_type"+k).select2({data: <?php echo json_encode($Merchant_types_dropdown_js); ?>});
        

            feather.replace();

            // $('.upload').on('change', function(){
            //     $('#file-indicator_'+k).show();
            // });

            k = k+1;
        });

        // End Insert Data

        // Reading Data

        var payer_id = $('#id').val();
        if (payer_id != '') {
            var actions = "<button type='button' class='btn btn-danger btn-sm mt-2  round ml-2 p-1 ' onclick='$(this).parent().parent().remove();k--;'><i data-feather='minus-circle' class='icon'></i></button>";
            var host = "<?php echo base_url() ?>";

            $('#add_payers_table').show();
            $.ajax({
                url: host + 'payers/merchant_details/' + payer_id,
                cache: false,
                type: 'GET',
                success: function(data) {

                    $('#add_members_table').show();
                    $('#add_members_table tbody').html('');
                    data = JSON.parse(data);

                    if (data.length > 0 && data[0].name != null) {
                        for (let i = 0; i < data.length; i++) {
                            $('#add_payers_table tbody').append(
                                "<tr class=''>"+
                                "<td>" + k + "</td>"+
                                    "<td><input type='text' class='form-control' value='" + data[i].name + "' data-rule-required data-msg-required='This field is required.' id='merchant_type" + k + "' placeholder='Merchant Type' name='merchant_type[]'></td>"+
                                    "<td><input type='text' class='form-control' value='" + data[i].merchant_number + "'  id='merchant_number" + k + "' placeholder='Merchant Number'  name='merchant_number[]'></td>"+
                                    "<td style='width: 110px;'>" + actions + "</td>"+
                                "</tr>"
                            );
                            
                            $("#merchant_type"+k).select2({data: <?php echo json_encode($Merchant_types_dropdown_js); ?>});
                            $("#merchant_type"+k).val(data[i].merchant_id).trigger('change');
           
                            k = k + 1;
                        }
                    }

                    feather.replace();
                },
                statusCode: {
                    403: function() {
                        console.log("403: Session expired.");
                        window.location.reload();
                    },
                    404: function() {
                        appLoader.hide();
                        appAlert.error("404: Page not found.");
                    }
                },
                error: function() {
                    appLoader.hide();
                    appAlert.error("500: Internal Server Error.");
                }
            });
        }
       

        setDatePicker("#Start_Date")
        setDatePicker("#End_Date")
        setDatePicker("#date_of_birth")
        
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