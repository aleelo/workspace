<input type="hidden" name="id" id='id' value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<!-- `document_title`, `ref_number`, `depertment`, `template`, `item_id`, `created_at` -->

<div class="form-group">
    <div class="row">
        <label for="type" class="<?php echo 'col-2'; ?>"><?php echo app_lang('visitor_type'); ?></label>
        <div class="col-10">
            <?php
            echo form_radio(array(
                "id" => "type_organization",
                "name" => "client_type",
                "class" => "form-check-input client_type",
                "data-msg-required" => app_lang("field_required"),
                    ), "organization", ($model_info->client_type === "organization") ? true : (($model_info->client_type !== "person") ? true : false));
            ?>
            <label for="type_organization" class="mr15"><?php echo app_lang('organization'); ?></label>
            <?php
            echo form_radio(array(
                "id" => "type_person",
                "name" => "client_type",
                "class" => "form-check-input client_type",
                "data-msg-required" => app_lang("field_required"),
                    ), "person", ($model_info->client_type === "person") ? true : false);
            ?>
            <label for="type_person" class=""><?php echo app_lang('person'); ?></label>
        </div>
    </div>
</div>

<?php if ($model_info->id) { ?>
    <div class="form-group">
        <div class="row">
            <?php if ($model_info->client_type == "person") { ?>
                <label for="name" class="<?php echo 'col-2'; ?> company_name_section"><?php echo app_lang('name'); ?></label>
            <?php } else { ?>
                <label for="company_name" class="<?php echo 'col-2'; ?> company_name_section"><?php echo app_lang('company_name'); ?></label>
            <?php } ?>
            <div class="<?php echo 'col-10'; ?>">
                <?php
                echo form_input(array(
                    "id" => ($model_info->client_type == "person") ? "name" : "company_name",
                    "name" => "name",
                    "value" => $model_info->name,
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
            <label for="company_name" class="<?php echo 'col-2'; ?> company_name_section"><?php echo app_lang('company_name'); ?></label>
            <div class="<?php echo 'col-10'; ?>">
                <?php
                echo form_input(array(
                    "id" => "company_name",
                    "name" => "name",
                    "value" => $model_info->name,
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

<div class=" form-group">
    <div class="row">
        <label for="duration" class=" col-2"><?php echo app_lang('access_duration'); ?></label>
        <div class="col-10">

        <?php
            echo form_radio(array(
                "id" => "duration_hours",
                "class" => "duration form-check-input ",
                "name" => "access_duration",
                    ), "hours", false);
            ?>
            <label for="duration_hours" class="mr15"><?php echo app_lang('hours'); ?></label>
            
            <?php
            echo form_radio(array(
                "id" => "duration_single_day",
                "class" => "duration form-check-input",
                "name" => "access_duration",
                    ), "single_day", true);
            ?>
            <label for="duration_single_day" class="mr15" ><?php echo app_lang('single_day'); ?></label>
            
            <?php
            echo form_radio(array(
                "id" => "duration_mulitple_days",
                "class" => "duration form-check-input",
                "name" => "access_duration",
                    ), "multiple_days", false);
            ?>
            <label for="duration_mulitple_days" class="mr15" ><?php echo app_lang('mulitple_days'); ?></label>

        </div>
    </div>
</div>

<div id="single_day_section"  class="form-group date_section">
    <div class="row">
        <label id="date_label" for="single_date" class=" col-2"><?php echo app_lang('date'); ?></label>
        <div class="col-10">
            <?php
            echo form_input(array(
                "id" => "single_date",
                "name" => "single_date",
                "class" => "form-control date",
                "placeholder" => app_lang('date'),
                "autocomplete" => "off",                
                "value" => $model_info->start_date ? $model_info->start_date : "",
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>
<div id="multiple_days_section" class="hide date_section">
    <div class="form-group">
        <div class="row">
            <label for="start_date" class=" col-2"><?php echo app_lang('start_date'); ?></label>
            <div class=" col-10">
                <?php
                echo form_input(array(
                    "id" => "start_date",
                    "name" => "start_date",
                    "class" => "form-control date",
                    "placeholder" => app_lang('start_date'),
                    "value" => $model_info->start_date ? $model_info->start_date : "",
                    "autocomplete" => "off",
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required")
                ));
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <label for="end_date" class=" col-2"><?php echo app_lang('end_date'); ?></label>
            <div class=" col-10">
                <?php
                echo form_input(array(
                    "id" => "end_date",
                    "name" => "end_date",
                    "class" => "form-control date",
                    "placeholder" => app_lang('end_date'),
                    "autocomplete" => "off",
                    "value" => $model_info->end_date ? $model_info->end_date : "",
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                    "data-rule-greaterThanOrEqual" => "#start_date",
                    "data-msg-greaterThanOrEqual" => app_lang("end_date_must_be_equal_or_greater_than_start_date"),
                    "data-rule-mustBeSameYear" => "#start_date"
                ));
                ?>
            </div>
        </div>
    </div>
</div>

<div id="total_days_section" class="hide date_section">
    <div class="form-group">
        <div class="row">
            <label for="total_days" class="col-2"><?php echo app_lang('total_days'); ?></label>
            <div class="col-10 total-days">

            </div>
        </div>
    </div>
</div>

<div id="hours_section" class="hide date_section">
    <div class="clearfix">
        <div class="row">
            <label for="hour_date" class=" col-2"><?php echo app_lang('date'); ?></label>
            <div class="col-md-4 form-group">
                <?php
                echo form_input(array(
                    "id" => "hour_date",
                    "name" => "hour_date",
                    "class" => "form-control date",
                    "placeholder" => app_lang('date'),
                    "value" => $model_info->start_date ? $model_info->start_date : "",
                    "autocomplete" => "off",
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>

            <label for="hours" class=" col-md-2"><?php echo app_lang('hours'); ?></label>
            <div class=" col-2">
                <?php
                echo form_dropdown("hours", array(
                    "01" => "01",
                    "02" => "02",
                    "03" => "03",
                    "04" => "04",
                    "05" => "05",
                    "06" => "06",
                    "07" => "07",
                    "08" => "08",
                        ), [$model_info->total_hours ? $model_info->total_hours : "",], "class='select2 validate-hidden' id='hours' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                ?>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="visit_date" class="<?php echo 'col-2'; ?>"><?php echo app_lang('visit_time'); ?></label>
       
        <div class="col-10">
            <?php
            echo form_input(array(
                "id" => "visit_time",
                "name" => "visit_time",
                "value" => $model_info->visit_time ? $model_info->visit_time : "",
                "class" => "form-control time",
                "placeholder" => app_lang('visit_time'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ),);
            ?>

        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="document_title" class="<?php echo 'col-2'; ?>"><?php echo app_lang('document_title'); ?></label>
       
        <div class="col-10">
            <?php
            echo form_input(array(
                "id" => "document_title",
                "name" => "document_title",
                "value" => $model_info->document_title ? $model_info->document_title : "",
                "class" => "form-control",
                "placeholder" => 'Ujeedo: Sodeeyn Marti Gaar ah: - '.app_lang('document_title'),
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ),);
            ?>

        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="department_id" class="<?php echo 'col-2'; ?>"><?php echo app_lang('office').'/Xafiiska'; ?></label>
       
        <div class="col-10">
            <?php
            echo form_dropdown(array(
                "id" => "department_id",
                "name" => "department_id",
                "value" => $model_info->department_id ? $model_info->department_id : "",
                "class" => "form-control select2",
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ),$departments,[$model_info->department_id]);
            ?>

        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="allowed_gates" class="<?php echo 'col-2'; ?>"><?php echo app_lang('allowed_gates'); ?></label>
       
        <div class="col-10">
            <?php
            echo form_dropdown(array(
                "id" => "allowed_gates",
                "name" => "allowed_gates",
                "value" => $model_info->allowed_gates ? $model_info->allowed_gates : "",
                "class" => "form-control select2",
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ),[''=>'Choose allowed gates','Gate 1'=>'Gate 1','Gate 1 iyo Gate 2'=>'Gate 1 iyo Gate 2'],[$model_info->allowed_gates]);
            ?>

        </div>
    </div>
</div>

<div class="form-group ">
    <hr class="mt-4 mb-4">
    <button type="button" class="btn btn-success float-end" id="add_visitor_btn"><i data-feather="plus-circle" class='icon'></i> Add Visitors</button>

</div>
<div class="form-group mt-4" style="clear: both;">
    <div class="row">
         <table class="table" id="add_visitors_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Visitor Name</th>
                    <th>Mobile</th>
                    <th>Vehicle Details</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
         </table>       
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="remarks" class="<?php echo app_lang('remarks'); ?>"><?php echo app_lang('remarks'); ?></label>
        <div class="">
            <?php
            echo form_textarea(array(
                "id" => "remarks",
                "name" => "remarks",
                "value" => $model_info->remarks ? $model_info->remarks : "",
                "class" => "form-control",
                'rows' => '5',
                'cols' => '7',
                "placeholder" => 'Additional Information'
            ),);
            ?>

        </div>
    </div>
</div>

<?php //echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();

        // $('#owner_id').select2({data: <?php //echo json_encode($owners_dropdown); ?>});

        // $("#lead_labels").select2({multiple: true, data: <?php //echo json_encode($label_suggestions); ?>});

        $('.client_type').click(function () {
            var inputValue = $(this).attr("value");
            if (inputValue === "person") {
                $(".company_name_section").html("Name");
                $(".company_name_input_section").attr("placeholder", "Name");
                $('#company_name').val('Person')
                $('#company_name').parent().parent().hide();
            } else {
                $(".company_name_section").html("Company name");
                $(".company_name_input_section").attr("placeholder", "Company name");

                $('#company_name').val('');
                $('#company_name').parent().parent().show();
                
            }
        });

        
        $(".duration").click(function () {
            var value = $(this).val();
            $(".date_section").addClass("hide");
            if (value === "multiple_days") {
                $("#multiple_days_section").removeClass("hide");
            } else if (value === "hours") {
                $("#hours_section").removeClass("hide");
            } else {
                $("#single_day_section").removeClass("hide");
            }
        });


        $("#multiple_days_section").change(function () {
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            if (start_date && end_date) {
                $("#total_days_section").removeClass("hide");

                var start_date = moment($('#start_date').val(), getJsDateFormat().toUpperCase());
                var end_date = moment($('#end_date').val(), getJsDateFormat().toUpperCase());
                var total_days = end_date.diff(start_date, 'days');

                $('div.total-days').html((total_days * 1) + 1); //count the starting day too
            } else {
                $("#total_days_section").addClass("hide");
            }
        });

    });

    feather.replace();
</script>