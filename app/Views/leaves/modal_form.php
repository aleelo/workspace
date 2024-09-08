<?php echo form_open(get_uri("leaves/" . $form_type), array("id" => "leave-form", "class" => "general-form", "role" => "form")); ?>
<div id="leaves-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">

            <?php if ($form_type == "assign_leave") { ?>

                <div class="form-group">
                    <div class="row">
                        <label for="applicant_id" class=" col-md-3"><?php echo app_lang('team_member'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            if (isset($team_members_info)) {
                                $image_url = get_avatar($team_members_info->image);
                                echo "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span>" . $team_members_info->first_name . " " . $team_members_info->last_name;
                                ?>
                                <input type="hidden" name="applicant_id" value="<?php echo $team_members_info->id; ?>" />
                                <?php
                            } else {
                                echo form_dropdown("applicant_id", $team_members_dropdown, "", "class='select2 validate-hidden' id='applicant_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            }
                            ?>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <div class="form-group">
                <div class="row">
                    <label for="leave_type" class=" col-md-3"><?php echo app_lang('leave_type'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_dropdown("leave_type_id", $leave_types_dropdown, "", "class='select2 validate-hidden' id='leave_type_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>
            <div class=" form-group">
                <div class="row">
                    <label for="duration" class=" col-md-3"><?php echo app_lang('duration'); ?></label>
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
                    <label id="date_label" for="single_date" class=" col-md-3"><?php echo app_lang('date'); ?></label>
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
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div id="multiple_days_section" class="hide date_section">
                <div class="form-group">
                    <div class="row">
                        <label for="start_date" class=" col-md-3"><?php echo app_lang('start_date'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "start_date",
                                "name" => "start_date",
                                
                                "class" => "form-control",
                                "placeholder" => app_lang('start_date'),
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
                        <label for="end_date" class=" col-md-3"><?php echo app_lang('end_date'); ?></label>
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
                        <label for="total_days" class="col-md-3"><?php echo app_lang('total_days'); ?></label>
                        <div class="col-md-9 total-days">

                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remaining_days"><?php echo app_lang('remaining_days'); ?></label>
                    <div class="remaining-days"> <!-- Halkan waxaa lagu soo bandhigi doonaa remaining days -->

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
         
            <div class="form-group">
                <div class="row">
                    <label for="flight_included_no" class=" col-md-3"><?php echo app_lang('is_flight_included'); ?></label>
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
                    <label for="reason" class=" col-md-3"><?php echo app_lang('reason'); ?></label>
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

            <?php echo view("includes/dropzone_preview"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-default upload-file-button float-start me-auto btn-sm round" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?></button>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang($form_type); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        var uploadUrl = "<?php echo get_uri("leaves/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("leaves/validate_leaves_file"); ?>";

        var dropzone = attachDropzoneWithForm("#leaves-dropzone", uploadUrl, validationUri);

        $("#leave-form").appForm({
            onSuccess: function (result) {
                
                appAlert.success(result.message, {duration: 15000});

                if(result.webUrl != null && result.flight_included == 1) {
                    let newTab = window.open();
                    newTab.location.target = '_blank';
                    newTab.location.href = result.webUrl;
                }
                
                location.reload();
            }
        });

    // Marka leave type la doorto
        $('#leave_type_id').change(function () {
            var leave_type_id = $(this).val();

            // U dir codsi AJAX ah si loo helo allowed days ee fasaxa la doortay
            $.ajax({
                url: "<?php echo get_uri('leaves/get_allowed_days'); ?>",
                type: "POST",
                data: {leave_type_id: leave_type_id},
                success: function (response) {
                    var data = JSON.parse(response);
                    var allowed_days = data.allowed_days;  // allowed days from backend
                    
                    // Marka la dooranayo maalmaha fasaxa (start date & end date)
                    $('#start_date, #end_date').change(function () {
                        var start_date = $('#start_date').val();
                        var end_date = $('#end_date').val();

                        // Hubi haddii labada taariikh la doortay
                        if (start_date && end_date) {
                            // Xisaabi total days (faraqa u dhexeeya start date iyo end date)
                            var total_days = moment(end_date).diff(moment(start_date), 'days') + 1; // +1 to include start day

                            // Ku soo bandhig total days
                            $('div.total-days').html('Total Days: ' + total_days);

                            // Xisaabi maalmaha haray
                            var remaining_days = allowed_days - total_days;
                            console.log('Remaining Days: ', remaining_days);

                            // Ku soo bandhig maalmaha haray iyo validation
                            if (remaining_days >= 0) {
                                $('div.remaining-days').html('Remaining Days: ' + remaining_days).css('color', 'green');
                                $('#submit_button').prop('disabled', false);  // Enable submit button
                            } else {
                                $('div.remaining-days').html('Remaining Days: ' + remaining_days + ' (You have exceeded the allowed days)').css('color', 'red');
                                $('#submit_button').prop('disabled', true);  // Disable submit button
                            }
                        }
                    });
                }
            });
        });

        setDatePicker("#start_date, #end_date");

        setDatePicker("#single_date, #hour_date");


        $("#leave-form .select2").select2();

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
</script>