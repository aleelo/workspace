<?php echo form_open(get_uri("documents/save_template"), array("id" => "upload-template-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <?php echo view("templates/template_form_fields"); ?>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#upload-template-form").appForm({
            onSuccess: function (result) {
            
                appAlert.success(result.message, {duration: 10000});

                // location.reload();

            }
        });
        setTimeout(function () {
            $("#document_title").focus();
        }, 200);
    });
</script>    
   