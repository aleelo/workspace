<?php echo form_open(get_uri("job_locations/save"), array("id" => "job-location-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">

    <div class="container-fluid">

        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="Job_location" class=" col-md-3"><?php echo app_lang('job_location'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "Job_location",
                        "name" => "Job_location",
                        "value" => $model_info->name,
                        "class" => "form-control",
                        "placeholder" => app_lang('job_location'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#job-location-form").appForm({
            onSuccess: function (result) {
                $("#leave-type-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        setTimeout(function () {
            $("#name").focus();
        }, 200);

    });
</script>    