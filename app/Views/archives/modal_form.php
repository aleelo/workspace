<?php echo form_open(get_uri("archives/save_file"), array("id" => "file-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">

    <div class="form-group">
            <div class="row">
                <label for="service_type" class="<?php echo 'col-3'; ?>"><?php echo app_lang('document_type'); ?></label>
                <div class="<?php echo 'col-9'; ?>">
                    <?php
                    echo form_dropdown(array(
                        "id" => "service_type",
                        "name" => "service_type",
                        "class" => "form-control select2",
                        "placeholder" => app_lang('document_type'),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ),['Document'=>'Document','Media'=>'Media']);
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group" id="clientDiv">
            <div class="row">
                <label for="department_id" class="<?php echo 'col-3'; ?>" ><?php echo "Choose Department"; ?></label>
                <div class="<?php echo 'col-9'; ?>">
                    <?php
                    echo form_dropdown(array(
                        "id" => "department_id",
                        "name" => "department_id",
                        "class" => "form-control select2",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ),$departments);
                    ?>
                </div>
            </div>
        </div>

    <div class="container-fluid">
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <?php
        echo view("includes/multi_file_uploader", array(
            "upload_url" => get_uri("clients/upload_file"),
            "validation_url" => get_uri("clients/validate_file"),
        ));
        ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default cancel-upload" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" disabled="disabled" class="btn btn-primary start-upload"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#file-form").appForm({
            onSuccess: function (result) {
                $("#client-file-table").appTable({reload: true});
                window.location.reload();
            }
        });

        $('#department_id').select2();
        $('#service_type').select2();
    });

</script>    
