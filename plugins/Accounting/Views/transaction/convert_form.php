<?php echo form_open(get_uri("accounting/convert"), array("id" => "convert-form", "class" => "general-form", "role" => "form")); ?>
<div id="invoices-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            
            <?php echo html_entity_decode($html); ?>
            <?php echo form_hidden('id', $id); ?>
            <?php echo form_hidden('type', $type); ?>
            <?php echo form_hidden('amount', $amount); ?>
            
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>
<?php require 'plugins/Accounting/assets/js/transaction/convert_form_js.php'; ?>

