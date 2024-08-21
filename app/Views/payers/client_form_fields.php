
<input type="hidden" name="id" id="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-----------------------------------------  Company Name  ------------------------------------>


<div class="form-group">
    <div class="row">
        <label for="payer_name" class="<?php echo $label_column_2; ?> company_name_section"><?php echo app_lang('payer_name'); ?></label>
        <div class="<?php echo $field_column_3; ?>">
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

<!----------------------------------------- Payer Segment & Company Type  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="type" class="<?php echo $label_column_2; ?>"><?php echo app_lang('payer_type'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
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

        <label for="payer_size" class="<?php echo $label_column_2; ?>"><?php echo app_lang('payer_segment'); ?></label>

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

        <label for="Reg_Type" class="<?php echo $label_column_2; ?>"><?php echo app_lang('Registration_type'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
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

        <label for="Reg_NO" class="<?php echo $label_column_2; ?>"><?php echo app_lang('Registration_no'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
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


 
<!-----------------------------------------    Start Date & End Date  ------------------------------------>

<div class="form-group">

    <div class="row">

        <label for="Start_Date" class="<?php echo $label_column_2; ?>"><?php echo app_lang('start_date'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

        <label for="End_Date" class="<?php echo $label_column_2; ?>"><?php echo app_lang('end_date'); ?></label>

        <div class="<?php echo $field_column_2; ?>">
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

<!-----------------------------------------   Turnover Tax & Number of EMployees  ------------------------------------>

<div class="form-group">

    <div class="row">
        
        <label for="turnover_tax" class="<?php echo $label_column_2; ?>"><?php echo app_lang('turnover_tax'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

        <label for="number_of_employees" class="<?php echo $label_column_2; ?>"><?php echo app_lang('number_of_employees'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

<!-----------------------------------------  Industries & TIN ------------------------------------>

<div class="form-group">
    <div class="row">

        <label for="industries" class="<?php echo $label_column_2; ?>"><?php echo app_lang('industries'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

        <label for="TIN" class="<?php echo $label_column_2; ?>"><?php echo app_lang('tin'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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


<!-----------------------------------------   Contact Name & Phone Number  ------------------------------------>

<div class="form-group">
    <div class="row">

        <label for="Contact_Name" class="<?php echo $label_column_2; ?>"><?php echo app_lang('contact_name'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

        <label for="phone" class="<?php echo $label_column_2; ?>"><?php echo app_lang('phone_number'); ?></label>
        <div class="<?php echo $field_column_2; ?>">
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

<!-----------------------------------------  Address  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="address" class="<?php echo $label_column_2; ?>"><?php echo app_lang('address'); ?></label>
        <div class="<?php echo $field_column_3; ?>">
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


<!-----------------------------------------  Email  ------------------------------------>

<div class="form-group">
    <div class="row">
        <label for="email" class="<?php echo $label_column_2; ?>"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column_3; ?>">
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
                    console.log(data[0].name);
                    console.log(data[1].name);

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