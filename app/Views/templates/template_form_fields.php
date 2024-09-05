<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <div class="row">
        <label for="name" class="col-3"><?php echo app_lang('template_name'); ?></label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "name",
                "name" => "name",
                "value" => $model_info->name,
                "class" => "form-control",
                "placeholder" => app_lang('template_name'),
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
        <label for="ref_prefix" class="col-3"><?php echo app_lang('template_prefix'); ?> <span title="Waa Summadda sida JSF/XM/XAG"> <span data-feather="info" class="icon-16 text-info"></span></span></label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "ref_prefix",
                "name" => "ref_prefix",
                "value" => $model_info->ref_prefix,
                "class" => "form-control",
                "placeholder" => 'Waa Summadda sid: JSF/XM/XAG',
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
        <label for="destination_folder" class="col-3"><?php echo app_lang('template_destination'); ?> <span title="Waa folder ka sharepoint aad ka abuurtay"> 
            <span data-feather="info" class="icon-16 text-info"></span></span></label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "destination_folder",
                "name" => "destination_folder",
                "value" => $model_info->destination_folder,
                "class" => "form-control",
                "placeholder" => 'Geli Magaca folder ka sharepoint aad ka abuurtay',
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group" style="display:block;">
    <div class="row">
        <label for="department" class="col-3"><?php echo app_lang('depertment'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_dropdown(array(
                "id" => "department",
                "name" => "department",
                "class" => "form-control select2",
                "placeholder" => app_lang('depertment')
            ),$departments,[$model_info->department_id],"style='display:block';");
            ?>
        </div>
    </div>
</div>

<div class="form-group" style="display:block;">
    <div class="row">
        <label for="section" class="col-3"><?php echo app_lang('section'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_dropdown(array(
                "id" => "section",
                "name" => "section",
                "class" => "form-control select2",
                "placeholder" => app_lang('section')
            ),$sections,[$model_info->section_id],"style='display:block';");
            ?>
        </div>
    </div>
</div>


<div class="form-group">
    <div class="row">
        <label for="ref_prefix" class=""><?php echo app_lang('upload_template'); ?> 
        (<span class="text-info">Please upload one document at a time.</span>)</label>
        <div class="col-12">
            <div class="container-fluid">
                <?php
                echo view("includes/multi_file_uploader", array(
                    "upload_url" => get_uri("clients/upload_file"),
                    "validation_url" => get_uri("clients/validate_file"),
                ));
                ?>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();

        feather.replace();
        // $('#owner_id').select2({data: <?php //echo json_encode($owners_dropdown); ?>});

        // $("#lead_labels").select2({multiple: true, data: <?php //echo json_encode($label_suggestions); ?>});

    });
</script>
