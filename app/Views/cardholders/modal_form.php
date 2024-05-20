<?php echo form_open(get_uri("cardholders/save"), array("id" => "cardholder-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

        <div class="form-group b-b pb-3">
            <div class="row justify-content-center">
                <div class="col-3 p-0" style="height: 200px;border: 1px dashed #ccc;background-size: cover;background-position: center;background-repeat: no-repeat;" id="avatar-preview" >
                    <!-- <img src="" alt="" style="width: 100%; height: 100%;"> -->
                </div>
            <div class='col-4 d-flex justify-content-center' data-bs-toggle='tooltip' title='<?php echo app_lang("upload"); ?> Image' data-placement='right'>
               <input type='file' name='avatar_image_file' onchange="getPreview(event)" id='avatar_image_file' class='no-outline hidden-input-file upload' onChange='$(this).next().show();'>
               <span style='position: absolute;margin-left: 15px;font-weight: bold;margin-top: -3px;border: solid 1px lightseagreen;border-radius: 50px;padding: 1px;width: 8px;height: 8px;background: lightseagreen;display:none;' class='file-indicator'></span>
                <label for='avatar_image_file' class='clickable'>
                    <span class='btn btn-primary btn-lg ml2 round mt-2'><i data-feather='camera' class='icon-32'></i></span>
                </label>
            </div>

            </div>
        </div>
        <?php //if($model_info->id){ ?>
        <div class="form-group">
            <div class="row">
                <label for="uuid" class="col-3"><?php echo app_lang('uuid'); ?></label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "uuid",
                        "name" => "uuid",
                        "class" => "form-control",
                        "value" =>$model_info->uid,
                        "placeholder" => app_lang('uuid'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
        <?php //} ?>

        <div class="form-group">
            <div class="row">
                <label for="fuel_type" class="col-3"><?php echo app_lang('full_name'); ?></label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "fullName",
                        "name" => "fullName",
                        "class" => "form-control",
                        "value" =>$model_info->fullName,
                        "placeholder" => app_lang('full_name'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
            </div>
        </div>
             <!-- photoId,  CID,    fullName,   institution,    office, titleSom,   titleEng -->
        <div class="form-group">
            <div class="row">
                <label for="institution" class="col-3"><?php echo 'Institution Type'; ?></label>
                <div class="col-9">
                    <?php
                    echo form_dropdown(array(
                        "id" => "type",
                        "name" => "type",
                        "class" => "form-control select2",
                        "placeholder" => app_lang('type'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ),$types,[$model_info->type]);
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="institution" class="col-3"><?php echo app_lang('institution'); ?></label>
                <div class="col-9">
                    <?php
                    echo form_dropdown(array(
                        "id" => "institution",
                        "name" => "institution",
                        "class" => "form-control select2",
                        "placeholder" => app_lang('institution'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ),$institutions,[$model_info->institution]);
                    ?>
                </div>
            </div>
        </div>
       
        <div class="form-group">
            <div class="row">
                <label for="office" class="col-3"><?php echo  app_lang('office'); ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "office",
                        "name" => "office",
                        "value" =>  $model_info->office,
                        "class" => "form-control",
                        "placeholder" => app_lang('office')
                    ));
                    ?>
                </div>
            </div> 
        </div>

        <div class="form-group">
            <div class="row">
                <label for="titleSom" class="col-3"><?php echo app_lang('job_title_so'); ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "titleSom",
                        "name" => "titleSom",
                        "value" => $model_info->titleSom,
                        "class" => "form-control",
                        "placeholder" => app_lang('job_title_so')
                    ));
                    ?>
                </div>
            </div> 
        </div>

        <div class="form-group">
            <div class="row">
                <label for="titleEng" class="col-3"><?php echo app_lang('job_title_en'); ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "titleEng",
                        "name" => "titleEng",
                        "value" => $model_info->titleEng,
                        "class" => "form-control",
                        "placeholder" => app_lang('job_title_en')
                    ));
                    ?>
                </div>
            </div> 
        </div>

        <div class="form-group">
            <div class="row">
                <label for="status" class="col-3"><?php echo app_lang('status'); ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_dropdown(array(
                        "id" => "status",
                        "name" => "status",
                        "class" => "form-control select2",
                        "placeholder" => app_lang('status')
                    ),['Active'=>'Active','Inactive'=>'Inactive','Lost'=>'Lost'],$model_info->status);
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
      var getPreview = function(event) {
            var reader = new FileReader();
            reader.onload = function(){
            var output = document.getElementById('avatar-preview');
            // output.src = reader.result;
            var path = reader.result;
            output.style.backgroundImage = 'url(' + path + ')';
            output.style.backgroundPosition = 'center';
            output.style.backgroundSize = 'contain';
            output.style.backgroundRepeat = 'no-repeat';
            };
            reader.readAsDataURL(event.target.files[0]);
        };

    $(document).ready(function () {
        var url = "<?php 
            if(file_exists(ROOTPATH.'files/IdImages/'.$model_info->uid.'.png')){
                $url = get_uri('files/IdImages/'.$model_info->uid.'.png');
            }else{
                $uid = str_replace('-','',$model_info->uid);
                $url = get_uri('files/IdImages/'.$uid.'.png');
            }
            echo $url;
            ?>";
        document.getElementById('avatar-preview').style.backgroundImage = 'url(' + url + ')';
        
        $("#cardholder-form").appForm({
            onSuccess: function (result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});

                    setTimeout(function () {
                       
                        window.location.reload();

                    }, 500);
                } else {
                    appAlert.success(result.message, {duration: 10000});

                        setTimeout(function () {
                            window.location.reload();
                        }, 500);

                    $("#order-table").appTable({newData: result.data, dataId: result.id});
                    $("#reload-kanban-button:visible").trigger("click");
                }
            }
        });

        setTimeout(function () {
            $("#fullName").focus();
        }, 200);

        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();
        setDatePicker("#order_date");
        setDatePicker("#arrival_date");
      
      

    });
</script>    