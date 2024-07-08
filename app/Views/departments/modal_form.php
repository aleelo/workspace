<?php echo form_open(get_uri("departments/save"), array("id" => "departments-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $model_info?->id; ?>" />
    
            <div class="form-group">
                <div class="row">
                    <label for="nameSo" class=" col-md-3"><?php echo app_lang('name_so'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "nameSo",
                            "name" => "nameSo",
                            "value" => $model_info?->nameSo,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('name_so'),
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
                    <label for="nameEn" class=" col-md-3"><?php echo app_lang('name_en'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "nameEn",
                            "name" => "nameEn",
                            "value" => $model_info?->nameEn,
                            "class" => "form-control validate-hidden",
                            "placeholder" => app_lang('name_en'),
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
                    <label for="head_id" class=" col-md-3"><?php echo app_lang('department_head'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown(array(
                            "id" => "head_id",
                            "name" => "head_id",
                            "class" => "form-control select2",
                            "placeholder" => app_lang('department_head')
                        ),[$employees_dropdown],[$model_info?->head_id]);
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="email" class=" col-md-3"><?php echo app_lang('department_email'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "email",
                            "name" => "email",
                            "value" => $model_info?->email ,
                            "class" => "form-control",
                            "placeholder" => app_lang('department_email')
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
                            "value" => $model_info?->remarks ,
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

        $("#departments-form").appForm({
            onSuccess: function (result) {
                if (window.refreshAfterUpdate) {
                    window.refreshAfterUpdate = false;
                    location.reload();
                } else {
                    $("#departments-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });

        $("#departments-form .select2").select2();
    });
</script>