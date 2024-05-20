<div class="modal-body">
    <div class="row">
     
          <!--   // id	uuid	supplier	fuel_type	barrels	litters	receive_date	received_by	department_id	vehicle_model	plate	remarks	created_at	deleted	
         -->
        <div class="table-responsive mb15">
            <table class="table dataTable display b-t">
                <tr>
                    <td class="w100"> <?php echo app_lang('fuel_type'); ?></td>
                    <td><?php echo $model_info->fuel_type; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('supplier'); ?></td>
                    <td><?php echo $model_info->supplier; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('barrels').'/Fuustooyin'; ?></td>
                    <td><?php echo $model_info->barrels; ?></td>
                </tr>
               
                <tr>
                    <td> <?php echo app_lang('litters'); ?></td>
                    <td><?php echo $model_info->litters; ?></td>
                </tr>
               
                <tr>
                    <td> <?php echo app_lang('receive_date'); ?></td>
                    <td><?php echo date_format(new DateTime($model_info->receive_date),'F d, Y'); ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('received_by'); ?></td>
                    <td><?php
                        $image_url = get_avatar($model_info->avatar);
                        echo "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span><span>" . $model_info->user . "</span>";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('vehicle_model'); ?></td>
                    <td><?php echo $model_info->vehicle_model; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('plate'); ?></td>
                    <td><?php echo $model_info->plate; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('depertment'); ?></td>
                    <td><?php echo $model_info->department; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('remarks'); ?></td>
                    <td><?php echo $model_info->remarks; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php echo form_open(get_uri("leaves/update_status"), array("id" => "leave-status-form", "class" => "general-form", "role" => "form")); ?>
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input id="leave_status_input" type="hidden" name="status" value="" />
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
  
    <?php if (1) { ?>
        <!-- <button data-status="rejected" type="submit" class="btn btn-danger btn-sm update-leave-status"><span data-feather="x-circle" class="icon-16"></span> <?php echo app_lang('reject'); ?></button>
        <button data-status="approved" type="submit" class="btn btn-success btn-sm update-leave-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('approve'); ?></button> -->
    <?php } ?>

    <!-- <?php //if (strtolower($model_info->status) === "approved" && $model_info->leave_type_id !== 3 && $model_info->nolo_status == 1 && $login_user->id === $model_info->applicant_id) { ?>
        <a target="_blank" href="<?php //echo get_uri('visitors_info/show_leave_qrcode_return/'.$model_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="file-text" class="icon-16"></span> <?php echo 'Passport Return'; ?></a>
    <?php // } else if ((strtolower($model_info->status) !== "cancelled" && strtolower($model_info->status) !== "approved" ) && $model_info->leave_type_id !== 3 && $login_user->id === $model_info->applicant_id ) {  ?>
        <a target="_blank" href="<?php //echo get_uri('visitors_info/show_leave_qrcode/'.$model_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="user" class="icon-16"></span> <?php echo 'Nolo Osto'; ?></a>
        <?php //} ?> -->
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