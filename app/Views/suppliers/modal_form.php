<?php echo form_open(get_uri("suppliers/save"), array("id" => "suppliers-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    
            <div class="form-group">
                <div class="row">
                    <label for="supplier_name" class=" col-md-3"><?php echo app_lang('supplier_name'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "supplier_name",
                            "name" => "supplier_name",
                            "value" => $model_info->supplier_name,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('supplier_name'),
                            "autofocus" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="address" class="col-md-3"><?php echo app_lang('address'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "address",
                            "name" => "address",
                            "value" => $model_info->address,
                            "class" => "form-control",
                            "placeholder" => app_lang('address'),
                            "data-rich-text-editor" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="contact_person" class=" col-md-3"><?php echo app_lang('contact_person'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "contact_person",
                            "name" => "contact_person",
                            "value" => $model_info->contact_person,
                            "class" => "form-control",
                            "placeholder" => app_lang('contact_person')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="phone" class=" col-md-3"><?php echo app_lang('phone'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "phone",
                            "name" => "phone",
                            "value" => $model_info->phone ,
                            "class" => "form-control",
                            "placeholder" => app_lang('phone')
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-3"><?php echo app_lang('email'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "email",
                            "name" => "email",
                            "value" => $model_info->email ,
                            "class" => "form-control",
                            "placeholder" => app_lang('email')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <div class="row">
                    <label for="remarks" class=" col-md-3"><?php echo app_lang('remarks'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "remarks",
                            "name" => "remarks",
                            "value" => $model_info->remarks ,
                            "class" => "form-control",
                            "placeholder" => app_lang('remarks')
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <!-- <button class="btn btn-default upload-file-button float-start btn-sm round me-auto" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_image"); ?></button> -->
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {

        $("#suppliers-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#suppliers-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#suppliers-form .select2").select2();
    });
</script>