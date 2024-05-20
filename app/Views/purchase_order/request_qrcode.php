
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
    </head>
    <body>

<?php echo form_open(get_uri("fuel/confirm_dispense"), array("id" => "request-dispense-form", "class" => "general-form", "role" => "form")); ?>
<div class="d-flex justify-content-center">
    <div class="card col-md-4 col-xs-12 mt-3 shadow-lg">
    <div class="card-title text-center"><h4 class="fw-bold">Fuel Request Information #<?php echo $model_info->id; ?></h4></div>

     
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
        <div class="table-responsive ">
            <table class="table dataTable display mb-0">
                <tr>
                    <td class="w25p"> <?php echo app_lang('request_type'); ?></td>
                    <td><?php echo $model_info->request_type; ?></td>
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

                <tr>
                    <td> <?php echo app_lang('driver_name'); ?></td>
                    <td><?php 
                      echo form_input(array(
                        "id" => "driver_name",
                        "name" => "driver_name",
                        "value" => $model_info->driver_name,
                        "class" => "form-control",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "placeholder" => app_lang('driver_name')
                    ));
                    ?></td>
                </tr>
            
            </table>
            <button data-status="dispensed" type="submit" class="btn btn-success  w100p update-request-status">
                <span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('confirm_dispense'); ?>
            </button>

        </div>
    </div>
</div>

<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {

        $("#request-dispense-form").appForm({
            onSuccess: function () {
                location.reload();
            }
        });

    });

    $(document).ready(function () {

        $('#js-init-chat-icon').hide();

    });
</script>    


    
    </body>
</html>