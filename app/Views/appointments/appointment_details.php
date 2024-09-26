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

<?php echo form_open(get_uri("appointments/update_status"), array("id" => "leave-status-form", "class" => "general-form", "role" => "form")); ?>
<input type="hidden" name="id" value="<?php echo $appointment_info->id; ?>" />
<input id="appointment_status_input" type="hidden" name="status" value="" />

<div class="modal-footer">
    <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <?php
        echo modal_anchor(get_uri("appointments/decline_reason"), "<i data-feather='x-circle' class='icon-16'></i>". app_lang('decline'), array("class" => "btn btn-danger", "title" => app_lang('decline_reason'), "data-post-id" => $appointment_info->id));
    ?>
    <!-- <button data-status="rejected" type="submit" class="btn btn-danger btn-sm update-appointment-status"><span data-feather="x-circle" class="icon-16"></span> <?php echo app_lang('decline'); ?></button> -->
    <button data-status="approved" type="submit" class="btn btn-success btn-sm update-appointment-status"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('approve'); ?></button>

</div>
    <?php echo form_close(); ?>

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

    });
</script>    