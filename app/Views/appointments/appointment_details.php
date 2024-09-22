<div class="modal-body">
    <div class="row">
        <div class="table-responsive mb15">
            <table class="table dataTable display b-t">
                <tr>
                    <td class="w100"> <?php echo app_lang('title'); ?></td>
                    <td><?php echo $appointment_info->title; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('date'); ?></td>
                    <td><?php echo $appointment_info->date; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('time'); ?></td>
                    <td><?php echo $appointment_info->time; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('room'); ?></td>
                    <td><?php echo $appointment_info->room; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('status'); ?></td>
                    <td><?php echo $appointment_info->status_meta; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?php echo form_open(get_uri("leaves/update_status"), array("id" => "leave-status-form", "class" => "general-form", "role" => "form")); ?>
<input type="hidden" name="id" value="<?php echo $appointment_info->id; ?>" />
<input id="leave_status_input" type="hidden" name="status" value="" />

<div class="modal-footer">

    <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

    <!-- Cancel -->

    <?php if (($appointment_info->status === "active" || $appointment_info->status === "pending" ) && $login_user->id === $appointment_info->applicant_id) { ?>

        <button data-status="canceled" type="submit" class="btn btn-danger btn-sm update-leave-status"><span data-feather="x-circle" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>

    <?php } ?>   
    
    <!-- Reject, Verify & Approve -->

    <?php if (($appointment_info->status === "active" || $appointment_info->status === "pending" ) && $show_approve_reject) { ?>

        <button data-status="rejected" type="submit" class="btn btn-danger btn-sm update-leave-status"><span data-feather="x-circle" class="icon-16"></span> <?php echo app_lang('reject'); ?></button>

        <?php if ($role === 'admin' || $role === 'HRM' || $role === 'Administrator') { ?>
            <button data-status="pending" type="submit" class="btn btn-warning btn-sm update-leave-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('verify'); ?></button>
            <button data-status="approved" type="submit" class="btn btn-success btn-sm update-leave-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('approve'); ?></button>
        <?php }else if ($role == 'Section Head' ) { ?>
            <button data-status="pending" type="submit" class="btn btn-warning btn-sm update-leave-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('verify'); ?></button>
        <?php }?>

    <?php } ?>

        
    
        <?php if(strtolower($appointment_info->status) === "approved"){?>

            <?php if ($appointment_info->flight_included === '1') {  //&& $appointment_info->nolo_status == 1 && $login_user->id === $appointment_info->applicant_id ?>
                <a target="_blank" href="<?php echo get_uri('visitors_info/show_leave_qrcode_return/'.$appointment_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="file-text" class="icon-16"></span> <?php echo 'Passport Return'; ?></a>

                <a target="_blank" href="<?php echo get_uri('visitors_info/show_leave_qrcode/'.$appointment_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="user" class="icon-16"></span> <?php echo 'Fasax Dhoof'; ?></a>
                <?php }?>            
            
        <?php }?>

        <?php if(strtolower($appointment_info->status) === "verified"){?>

            <?php if ($appointment_info->flight_included === '1') {  //&& $appointment_info->nolo_status == 1 && $login_user->id === $appointment_info->applicant_id ?>
                <a target="_blank" href="<?php echo get_uri('visitors_info/show_leave_qrcode_return/'.$appointment_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="file-text" class="icon-16"></span> <?php echo 'Passport Return'; ?></a>

                <a target="_blank" href="<?php echo get_uri('visitors_info/show_leave_qrcode/'.$appointment_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="user" class="icon-16"></span> <?php echo 'Fasax Dhoof'; ?></a>
                <?php }?>            

        <?php }?>
    </div>
    <?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {

        $(".update-leave-status").click(function () {
            $("#leave_status_input").val($(this).attr("data-status"));
        });

        $("#leave-status-form").appForm({
            onSuccess: function () {
                location.reload();
            }
        });

    });
</script>    