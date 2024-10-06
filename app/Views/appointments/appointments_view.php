<div class="modal-body">
<div class="row">
    <div class="table-responsive mb15">
        <table class="table dataTable display b-t">
                <tr>
                    <td class="w100"><?php echo $status_meta; ?></td>
                    <td></td>
                </tr>
            
            <?php if (!empty($model_info->title)) { ?>
                <tr>
                    <td class="w100"><?php echo app_lang('title'); ?></td>
                    <td><?php echo $model_info->title; ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($model_info->date)) { ?>
                <tr>
                    <td><?php echo app_lang('date'); ?></td>
                    <td><?php echo $model_info->date; ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($model_info->time)) { ?>
                <tr>
                    <td><?php echo app_lang('time'); ?></td>
                    <td><?php echo $model_info->time; ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($model_info->room)) { ?>
                <tr>
                    <td><?php echo app_lang('room'); ?></td>
                    <td><?php echo $model_info->room; ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($model_info->note)) { ?>
                <tr>
                    <td><?php echo app_lang('note'); ?></td>
                    <td><?php echo $model_info->note; ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($model_info->HostName)) { ?>
                <tr>
                    <td><?php echo app_lang('host_name'); ?></td>
                    <td><?php echo $model_info->HostName; ?></td>
                </tr>
            <?php } ?>
            
            <?php if (!empty($model_info->meeting_with)) { ?>
                <tr>
                    <td><?php echo app_lang('meeting_with'); ?></td>
                    <td><?php echo $model_info->meeting_with; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

    
</div>

<div class="modal-footer">
    <?php
    if (isset($editable) && $editable === "1") {

        if ($login_user->id == $model_info->created_by || $login_user->is_admin) {

        }
    }
    ?>
    
    <?php echo form_open(get_uri("appointments/update_status"), array("id" => "leave-status-form", "class" => "general-form", "role" => "form")); ?>
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input id="appointment_status_input" type="hidden" name="status" value="" />
        <button type="button" class="btn btn-default close-modal" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <?php
            // echo js_anchor("<i data-feather='x-circle' class='icon-16'></i> " . app_lang('delete'), array("class" => "btn btn-default float-start", "id" => "delete", "data-id" => $encrypted_appointment_id));
            echo modal_anchor(get_uri("appointments/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'), array("class" => "btn btn-info text-white", "data-post-id" => $encrypted_appointment_id, "title" => app_lang('edit_appointment')));
            echo modal_anchor(get_uri("appointments/decline_reason"), "<i data-feather='x-circle' class='icon-16'></i>". app_lang('decline'), array("class" => "btn btn-danger", "title" => app_lang('decline_remarks'), "data-post-id" => $model_info->id));
        ?>
        <button data-status="approved" type="submit" class="btn btn-success update-appointment-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('approve'); ?></button>
    <?php echo form_close(); ?>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        $(".update-appointment-status").click(function () {
            $("#appointment_status_input").val($(this).attr("data-status"));
        });

        
        $("#leave-status-form").appForm({
            onSuccess: function () {
                location.reload();
            }
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