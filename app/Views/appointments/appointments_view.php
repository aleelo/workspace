<div class="modal-body">
    <div class="container-fluid">
        <div class="row mb15">
            <div class="col-md-12 clearfix">
                <h4 class="mt0 float-start">
                    <?php
                
                    echo "<span style='color: #6690f4' class='float-start mr10'><i data-feather='$event_icon' class='icon-16'></i></span> " . $model_info->title;
                    ?>
                </h4>
            </div>

            <?php if ($status) { ?>
                <div class="col-md-12 pb10">
                    <?php echo $status; ?>
                </div>
            <?php } ?>

            <div class="col-md-12 pb10 ">
                <i data-feather="clock" class="icon-16"></i>
                <?php
                //echo view("appointments/event_time");
                ?>
            </div>

            <div class="col-md-12 pb10">
                <?php //echo $labels; ?>
            </div>

            <?php if ($model_info->note) { ?>
                <div class="col-md-12">
                    <blockquote class="font-14 text-justify" style="<?php echo "border-color: #6690f4" ; ?>"><?php echo nl2br(process_images_from_content($model_info->note)); ?></blockquote>
                </div>
            <?php } ?>

            <?php if ($model_info->room) { ?>
                <div class="col-md-12 mt5">
                    <div class="font-14"><i data-feather="map-pin" class="icon-16"></i> <?php echo nl2br($model_info->room); ?></div>
                </div>
            <?php }
            ?>



            <?php if ($confirmed_by) { ?>
                <div class="col-md-12 clearfix">
                    <div class="pl10 pr10">
                        <div class="row">
                            <div class="col-md-1 p0">
                                <span title="<?php echo app_lang("confirmed"); ?>" class='confirmed-by-logo'><span data-feather="check-circle"></span></span>
                            </div>
                            <div class="col-md-11 pt10 pl0">
                                <?php echo $confirmed_by; ?>
                            </div>
                        </div> 
                    </div>
                </div>
            <?php } ?>

            <?php if ($rejected_by) { ?>
                <div class="col-md-12 clearfix">
                    <div class="pl10 pr10">
                        <div class="row">
                            <div class="col-md-1 p0">
                                <span title="<?php echo app_lang("rejected"); ?>" class="rejected-by-logo"><i data-feather="x-circle"></i></span>
                            </div>
                            <div class="col-md-11 pt10 pl0">
                                <?php echo $rejected_by; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>


            <!-- <?php
            // $files = @unserialize($model_info->files);
            // if ($files && is_array($files) && count($files)) {
                ?>
                <div class="clearfix">
                    <div class="col-md-12 mt10">
                        <div class="mb5 strong"><?php //echo app_lang("files"); ?></div>
                        <?php
                        // foreach ($files as $key => $value) {
                        //     $file_name = get_array_value($value, "file_name");
                        //     echo "<div>";
                        //     echo js_anchor(remove_file_prefix($file_name), array("data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("events/file_preview/" . $model_info->id . "/" . $key)));
                        //     echo "</div>";
                        // }
                        ?>
                    </div>
                </div>
            <?php //} ?> -->

        </div>
    </div>
</div>

<div class="modal-footer">
    <?php
    if (isset($editable) && $editable === "1") {

        if ($login_user->id == $model_info->created_by || $login_user->is_admin) {

            echo js_anchor("<i data-feather='x-circle' class='icon-16'></i> " . app_lang('delete_event'), array("class" => "btn btn-default float-start", "id" => "delete_event", "data-id" => $encrypted_appointment_id));

            echo modal_anchor(get_uri("appointments/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_appointment'), array("class" => "btn btn-default", "data-post-id" => $encrypted_appointment_id, "title" => app_lang('edit_appointment')));
        }
    }
    ?>
    
    <?php echo form_open(get_uri("appointments/update_status"), array("id" => "leave-status-form", "class" => "general-form", "role" => "form")); ?>
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input id="appointment_status_input" type="hidden" name="status" value="" />

        <div class="modal-footer">
            <?php
                echo modal_anchor(get_uri("appointments/decline_reason"), "<i data-feather='x-circle' class='icon-16'></i>". app_lang('decline'), array("class" => "btn btn-danger", "title" => app_lang('decline_remarks'), "data-post-id" => $model_info->id));
            ?>
            <!-- <button data-status="rejected" type="submit" class="btn btn-danger btn-sm update-appointment-status"><span data-feather="x-circle" class="icon-16"></span> <?php echo app_lang('decline'); ?></button> -->
            <button data-status="approved" type="submit" class="btn btn-success update-appointment-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('approve'); ?></button>

        </div>
    <?php echo form_close(); ?>

    <button type="button" class="btn btn-info text-white close-modal" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        
        $(".update-appointment-status").click(function () {
            $("#appointment_status_input").val($(this).attr("data-status"));
        });


        $('#delete_event').click(function () {
            var encrypted_appointment_id = $(this).attr("data-encrypted_appointment_id");
            $(this).appConfirmation({
                title: "<?php echo app_lang('are_you_sure'); ?>",
                btnConfirmLabel: "<?php echo app_lang('yes'); ?>",
                btnCancelLabel: "<?php echo app_lang('no'); ?>",
                onConfirm: function () {
                    appLoader.show();
                    $('.close-modal').trigger("click");

                    $.ajax({
                        url: "<?php echo get_uri('appointments/delete') ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {id: encrypted_appointment_id},
                        success: function (result) {
                            if (result.success) {
                                window.fullCalendar.refetchEvents();
                                setTimeout(function () {
                                    feather.replace();
                                }, 100);

                                if (typeof getReminders === 'function') {
                                    getReminders();
                                }

                                appAlert.warning(result.message, {duration: 10000});
                            } else {
                                appAlert.error(result.message);
                            }

                            appLoader.hide();
                        }
                    });

                }
            });

            return false;
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

    });
</script>    