<div class="modal-body">
    <div class="row">
     
          <!--   // id	uuid	requested_by	department_id	litters	vehicle_engine	plate	request_type	request_date	purpose	status	remarks		created_at	deleted	
         -->
         <?php 
            if ($model_info->status === "pending") {
                $status_class = "bg-warning";
            } else if ($model_info->status === "approved") {
                $status_class = "badge bg-success";//btn-success
            } else if ($model_info->status === "dispensed") {
                $status_class = "badge btn-success";//btn-success
            } else if ($model_info->status === "cancelled") {
                $status_class = "bg-dark";//btn-success
            
            } else if ($model_info->status === "rejected") {
                $status_class = "bg-danger";
            } else {
                $status_class = "bg-dark";
            }

            $status_meta = "<span class='badge $status_class'>" . app_lang($model_info->status) . "</span>";

         ?>
        <div class="table-responsive mb15">
            <table class="table dataTable display b-t">
                <tr>
                    <td class="w100"> <?php echo app_lang('uuid'); ?></td>
                    <td><?php echo $model_info->uuid; ?></td>
                </tr>
                <tr>
                    <td class="w100"> <?php echo app_lang('request_type'); ?></td>
                    <td><?php echo $model_info->request_type; ?></td>
                </tr>
                <tr>
                    <td class="w100"> <?php echo app_lang('fuel_type'); ?></td>
                    <td><?php echo $model_info->fuel_type; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('request_date'); ?></td>
                    <td><?php echo date_format(new DateTime($model_info->request_date),'F d, Y'); ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('litters'); ?></td>
                    <td><?php echo $model_info->litters; ?></td>
                </tr>
               
                <tr>
                    <td> <?php echo app_lang('purpose'); ?></td>
                    <td><?php echo $model_info->purpose; ?></td>
                </tr>
               
                <tr>
                    <td> <?php echo app_lang('requested_by'); ?></td>
                    <td><?php
                        $image_url = get_avatar($model_info->avatar);
                        echo "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span><span>" . $model_info->user . "</span>";
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <td> <?php echo app_lang('vehicle_engine'); ?></td>
                    <td><?php echo $model_info->vehicle_engine; ?></td>
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
                    <td> <?php echo app_lang('status'); ?></td>
                    <td><?php echo $status_meta; ?></td>
                </tr>
                <tr>
                    <td> <?php echo app_lang('remarks'); ?></td>
                    <td><?php echo $model_info->remarks; ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php echo form_open(get_uri("fuel/update_status"), array("id" => "request-status-form", "class" => "general-form", "role" => "form")); ?>
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input id="request_status_input" type="hidden" name="status" value="" />
<div class="modal-footer">
    <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
  
    <?php if (strtolower($model_info->status) == 'pending') { ?>
        <button data-status="cancelled" type="submit" class="btn btn-dark btn-sm update-request-status"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
        <button data-status="rejected" type="submit" class="btn btn-danger btn-sm update-request-status"><span data-feather="x-circle" class="icon-16"></span> <?php echo app_lang('reject'); ?></button>
        <button data-status="approved" type="submit" class="btn btn-success btn-sm update-request-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('approve'); ?></button>
        <?php } ?>

    <a target="_blank" href="<?php echo get_uri('fuel/request_pdf/'.$model_info->uuid);?>" class="btn btn-success btn-sm "><span data-feather="file-text" class="icon-16"></span> <?php echo 'Show PDF'; ?></a>

    <!-- <?php //if (strtolower($model_info->status) === "approved" && $model_info->leave_type_id !== 3 && $model_info->nolo_status == 1 && $login_user->id === $model_info->applicant_id) { ?>
    <?php // } else if ((strtolower($model_info->status) !== "cancelled" && strtolower($model_info->status) !== "approved" ) && $model_info->leave_type_id !== 3 && $login_user->id === $model_info->applicant_id ) {  ?>
        <a target="_blank" href="<?php //echo get_uri('visitors_info/show_leave_qrcode/'.$model_info->uuid);?>" class="btn btn-success btn-sm update-leave-status"><span data-feather="user" class="icon-16"></span> <?php echo 'Nolo Osto'; ?></a>
        <?php //} ?> -->
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {

        $(".update-request-status").click(function () {
            $("#request_status_input").val($(this).attr("data-status"));
        });

        $("#request-status-form").appForm({
            onSuccess: function () {
                location.reload();
            }
        });

    });
</script>    