<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <div class="row">
        <label for="document_title" class="col-3"><?php echo app_lang('document_title'); ?></label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "document_title",
                "name" => "document_title",
                // "value" => $model_info->document_title,
                "class" => "form-control",
                "placeholder" => app_lang('document_title'),
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
        <label for="ref_number" class="col-3"><?php echo app_lang('ref_number'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "ref_number",
                "name" => "ref_number",
                "class" => "form-control",
                "placeholder" => app_lang('ref_number')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="depertment" class="col-3"><?php echo app_lang('depertment'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "depertment",
                "name" => "depertment",
                "class" => "form-control",
                "placeholder" => app_lang('depertment')
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="template" class="col-3"><?php echo app_lang('template'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "template",
                "name" => "template",
                "class" => "form-control",
                "placeholder" => app_lang('template')
            ));
            ?>
        </div>
    </div>
</div>

<!-- `document_title`, `ref_number`, `depertment`, `template`, `item_id`, `created_at` -->


<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();

        // $('#owner_id').select2({data: <?php //echo json_encode($owners_dropdown); ?>});

        // $("#lead_labels").select2({multiple: true, data: <?php //echo json_encode($label_suggestions); ?>});

    });
</script>