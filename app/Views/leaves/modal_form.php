<?php echo form_open(get_uri("leaves/" . $form_type), array("id" => "leave-form", "class" => "general-form", "role" => "form")); ?>
<div id="leaves-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">

            <?php if ($form_type == "assign_leave") { ?>

                <div class="form-group">
                    <div class="row">
                        <label for="applicant_id" class="col-md-3 text-right"><?php echo app_lang('team_member'); ?></label>
                        <div class=" col-md-9">
                            <?php
                                echo form_dropdown("applicant_id", $team_members_dropdown, "", "class='select2 validate-hidden' id='applicant_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <div class="form-group">
                <div class="row">
                    <label for="leave_type" class=" col-md-3 text-right"><?php echo app_lang('leave_type'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("leave_type_id", $leave_types_dropdown, "", "class='select2 validate-hidden' id='leave_type_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>

            
         
            <div class="form-group">
                <div class="row">
                    <label for="flight_included_no" class="col-md-3 text-right"><?php echo app_lang('is_flight_included'); ?></label>
                    <div class=" col-md-9">
                    <?php
                        echo form_radio(array(
                            "id" => "flight_included_no",
                            "class" => "form-check-input",
                            "name" => "flight_included",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                                ), "0", false);
                        ?>
                        <label for="flight_included_no" class="mr15" ><?php echo app_lang('no'); ?></label>

                        <?php
                        echo form_radio(array(
                            "id" => "flight_included_yes",
                            "class" => "form-check-input",
                            "name" => "flight_included",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                                ), "1", false);
                        ?>
                        <label for="flight_included_yes" class="mr15" ><?php echo app_lang('yes'); ?></label>

                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="reason" class=" col-md-3 text-right"><?php echo app_lang('reason'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "reason",
                            "name" => "reason",
                            "class" => "form-control",
                            "placeholder" => app_lang('reason'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class=" form-group">
                <div class="row">
                    <label for="duration" class="col-md-3 text-right"><?php echo app_lang('duration'); ?></label>
                    <div class="col-md-9">

                        <?php
                        echo form_radio(array(
                            "id" => "duration_single_day",
                            "class" => "duration form-check-input",
                            "name" => "duration",
                                ), "single_day", true);
                        ?>
                        <label for="duration_single_day" class="mr15" ><?php echo app_lang('single_day'); ?></label>

                        <?php
                        echo form_radio(array(
                            "id" => "duration_mulitple_days",
                            "class" => "duration form-check-input",
                            "name" => "duration",
                                ), "multiple_days", false);
                        ?>
                        <label for="duration_mulitple_days" class="mr15" ><?php echo app_lang('mulitple_days'); ?></label>

                        <?php
                        echo form_radio(array(
                            "id" => "duration_hours",
                            "class" => "duration form-check-input",
                            "name" => "duration",
                                ), "hours", false);
                        ?>
                        <label for="duration_hours" ><?php echo app_lang('hours'); ?></label>
                    </div>
                </div>
            </div>

            <div id="single_day_section"  class="form-group date_section">
                <div class="row">
                    <label id="date_label" for="single_date" class="col-md-3 text-right"><?php echo app_lang('date'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "single_date",
                            "name" => "single_date",
                            "class" => "form-control",
                            "placeholder" => app_lang('date'),
                            "autocomplete" => "off",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                            // "value" => date('d F Y')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div id="multiple_days_section" class="hide date_section">
                <div class="form-group">
                    <div class="row">
                        <label for="start_date" class="col-md-3 text-right"><?php echo app_lang('start_date'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "start_date",
                                "name" => "start_date",
                                
                                "class" => "form-control",
                                "placeholder" => app_lang('start_date'),
                                "autocomplete" => "off",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            // "value" => date('d F Y')

                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="end_date" class="col-md-3 text-right"><?php echo app_lang('end_date'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "end_date",
                                "name" => "end_date",
                                "class" => "form-control",
                                "placeholder" => app_lang('end_date'),
                                "autocomplete" => "off",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                                "data-rule-greaterThanOrEqual" => "#start_date",
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div id="total_days_section" class="date_section">

                <div class="form-group">
                     <div class="row">
                        <div class="col-md-3 text-right allowed-days-label">
                        </div>
                        <div class="col-md-3 allowed-days-display">
                        </div>
                        <div class="col-md-2 text-right taken-days-display-label">
                        </div>
                        <div class="col-md-2 taken-days-display">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                    <div class="col-md-3 text-right total-days-label">
                        </div>
                        <div class="col-md-9 total-days">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                     <div class="row">
                        <div class="col-md-3 text-right remaining-days-label">
                        </div>
                        <div class="col-md-9 remaining-days">
                        </div>
                    </div>
                </div>


            </div>

            <div id="hours_section" class="hide date_section">
                <div class="clearfix">
                    <div class="row">
                        <label for="hour_date" class=" col-md-3"><?php echo app_lang('date'); ?></label>
                        <div class="col-md-4 form-group">
                            <?php
                            echo form_input(array(
                                "id" => "hour_date",
                                "name" => "hour_date",
                                "class" => "form-control",
                                "placeholder" => app_lang('date'),
                                "autocomplete" => "off",
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>

                        <label for="hours" class=" col-md-2"><?php echo app_lang('hours'); ?></label>
                        <div class=" col-md-3">
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
                                    ), "", "class='select2 validate-hidden' id='hours' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
            </div>


            <?php echo view("includes/dropzone_preview"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default upload-file-button float-start me-auto btn-sm round" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" id="submit_button" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang($form_type); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
   $(document).ready(function () {
    var allowed_days = 0;  // Initialize allowed days globally
    var taken_days = 0;    // Initialize taken days globally
    var unlimited_days = false;  // Flag for unlimited days

    function getCurrentDate() {
        var today = new Date();
        var day = ("0" + today.getDate()).slice(-2);
        var month = ("0" + (today.getMonth() + 1)).slice(-2);
        var year = today.getFullYear();
        return year + "-" + month + "-" + day;
    }

    // Set the default date to today's date
    var currentDate = getCurrentDate();
    $('#single_date').val(currentDate);
    $('#start_date').val(currentDate);

    // Handle form success
    $("#leave-form").appForm({
        onSuccess: function (result) {
            appAlert.success(result.message, {duration: 15000});

            if (result.webUrl != null && result.flight_included == 1) {
                let newTab = window.open();
                newTab.location.target = '_blank';
                newTab.location.href = result.webUrl;
            }

            location.reload();
        }
    });

    // Fetch allowed days and taken days dynamically when leave type changes
    $('#leave_type_id').change(function () {
        get_allowed_days();
    });

    function get_allowed_days() {
        var leave_type_id = $('#leave_type_id').val();
        var applicant_id = $('#applicant_id').val();

        $.ajax({
            url: "<?php echo get_uri('leaves/get_allowed_days'); ?>",
            type: "POST",
            data: {
                leave_type_id: leave_type_id,
                form_type: "<?php echo $form_type ?>",
                applicant_id: applicant_id,
            },
            success: function (response) {
                var data = JSON.parse(response);
                allowed_days = data.allowed_days;
                taken_days = data.taken_days;
                unlimited_days = $.trim(allowed_days) === 'Unlimited';  // Check if leave type has unlimited days

                 // Update the displayed allowed days
                updateAllowedDays();
            }
        });
    }

     // Update the displayed allowed days
    function updateAllowedDays() {
       
        if (unlimited_days) { 
                    $('div.allowed-days-label').html('Allowed Days: ').css('color', 'green');
                    $('div.allowed-days-display').html('Unlimited').css('color', 'green');
                    $('div.taken-days-display-label').html('Taken Days: ').css('color', 'purple');
                    $('div.taken-days-display').html(taken_days + ' - Days').css('color', 'purple');
                    $('#submit_button').prop('disabled', false);  // Enable submit button for unlimited days
                    // Hide remaining and requested days, since they don't apply
                    $('div.total-days-label').html('');
                    $('div.total-days').html('');
                    $('div.remaining-days').html('');
                    $('div.remaining-days-label').html('');
                    
                } else {
                    $('div.allowed-days-label').html('Allowed Days: ').css('color', 'blue');
                    $('div.allowed-days-display').html(allowed_days + ' - Days').css('color', 'blue');
                    $('div.taken-days-display-label').html('Taken Days: ').css('color', 'purple');
                    $('div.taken-days-display').html(taken_days + ' - Days').css('color', 'purple');
                    handleDurationChange();  // Recalculate based on the selected duration
                }
    }

    // When start, end, or single day is changed, recalculate remaining days
    $('#start_date, #end_date, #single_date').change(function () { //
       
        if (!unlimited_days) {
            get_allowed_days();  // Call the function to recalculate based on the selected duration
        }
    });

    // Function to handle duration change and trigger the correct calculation
    function handleDurationChange() {
        var selectedDuration = $('input[name="duration"]:checked').val();

        $('#total_days_section').removeClass("hide");

        if (selectedDuration === "single_day") {
            calculateSingleDay();  // Calculate remaining days for a single day leave
        } else if (selectedDuration === "multiple_days") {
            calculateRemainingDays();  // Calculate remaining days for multiple days leave
        }
    }

    // Function to calculate the remaining days for multiple days leave
    function calculateRemainingDays() {
        if (!unlimited_days) {  // Only calculate remaining days if not unlimited
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();

            if (start_date && end_date) {
                var total_days = moment(end_date).diff(moment(start_date), 'days') + 1;

                $('div.total-days-label').html('Requested Days: ');
                $('div.total-days').html(total_days + ' - Days');

                var remaining_days = allowed_days - taken_days - total_days;

                updateRemainingDays(remaining_days);
            }
        }
    }

    // Function to calculate the remaining days for a single day leave
    function calculateSingleDay() {
        if (!unlimited_days) {  // Only calculate remaining days if not unlimited
            var single_date = $('#single_date').val();

            if (single_date) {
                var total_days = 1;

                $('div.total-days-label').html('Requested Days: ');
                $('div.total-days').html(total_days + ' - Day');

                var remaining_days = allowed_days - taken_days - total_days;

                updateRemainingDays(remaining_days);
            }
        }
    }

    // Helper function to update the remaining days and enable/disable the submit button
    function updateRemainingDays(remaining_days) {
        if (remaining_days >= 0) {
            $('div.remaining-days-label').html('Remaining Days: ').css('color', 'green');
            $('div.remaining-days').html(remaining_days + ' - Days').css('color', 'green');
            $('#submit_button').prop('disabled', false);  // Enable submit button
        } else {
            $('div.remaining-days-label').html('Remaining Days: ').css('color', 'red');
            $('div.remaining-days').html(remaining_days + ' - Days' + ' (You have exceeded the allowed days)').css('color', 'red');
            $('#submit_button').prop('disabled', true);  // Disable submit button
        }
    }

    // Set date pickers for the dates
    setDatePicker("#start_date, #end_date, #single_date, #hour_date");

    $("#leave-form .select2").select2();

    // Handle duration type radio buttons
    $(".duration").click(function () {
        var value = $(this).val();
        $(".date_section").addClass("hide");
        if (value === "multiple_days") {
            $("#multiple_days_section").removeClass("hide");
            handleDurationChange();  // Recalculate for multiple days
        } else if (value === "hours") {
            $("#hours_section").removeClass("hide");
        } else {
            $("#single_day_section").removeClass("hide");
            handleDurationChange();  // Recalculate for single day
        }
    });
});
</script>





