<?php echo form_open(get_uri("bank_names/save"), array("id" => "bank-names-form", "class" => "general-form", "role" => "form")); ?>

<div class="modal-body clearfix">

    <div class="container-fluid">

        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="bank_name" class=" col-md-3"><?php echo app_lang('bank_name'); ?></label>
                <div class=" col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "bank_name",
                        "name" => "bank_name",
                        "value" => $model_info->bank_name,
                        "class" => "form-control",
                        "placeholder" => app_lang('bank_name'),
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
        $("#bank-names-form").appForm({
            onSuccess: function (result) {
                $("#leave-type-table").appTable({newData: result.data, dataId: result.id});
            }
        });
        setTimeout(function () {
            $("#name").focus();
        }, 200);

    });
</script>    