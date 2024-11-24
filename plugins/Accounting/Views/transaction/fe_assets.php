<div class="row general-form">
  <div class="col-md-3">
    <?php $status = [ 
          1 => ['id' => 'converted', 'name' => _l('acc_converted')],
          2 => ['id' => 'has_not_been_converted', 'name' => _l('has_not_been_converted')],
        ]; 
        ?>
        <?php echo render_select('status',$status,array('id','name'),'status', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
  </div>
  <div class="col-md-3">
    <?php echo render_date_input('from_date','from_date'); ?>
  </div>
  <div class="col-md-3">
    <?php echo render_date_input('to_date','to_date'); ?>
  </div>
</div>
<a href="#" onclick="assets_transaction_bulk_actions(); return false;" data-toggle="modal" data-target="#assets_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-assets"><?php echo _l('bulk_actions'); ?></a>
<table class="table table-assets">
  <thead>
    <th><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="assets" class="form-check-input"><label></label></div></th>
    <th><?php echo  _l('fe_serial'); ?></th>
    <th><?php echo  _l('fe_asset_name'); ?></th>
    <th><?php echo  _l('fe_image'); ?></th>
    <th><?php echo  _l('fe_model'); ?></th>
    <th><?php echo  _l('fe_model_no'); ?></th>
    <th><?php echo  _l('fe_category'); ?></th>
    <th><?php echo  _l('fe_purchase_date'); ?></th>
    <th><?php echo  _l('fe_purchase_cost'); ?></th>
    <th><?php echo  _l('fe_status'); ?></th>

    <th><?php echo _l('mapping_status'); ?></th>
    <th><?php echo _l('acc_convert'); ?></th>
  </thead>
  <tbody>
    
  </tbody>
</table>

<div class="modal fade bulk_actions" id="assets_bulk_actions" tabindex="-1" role="dialog" data-table=".table-assets">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
          <?php echo form_hidden('bulk_actions_type', 'fe_asset'); ?>
            <?php if(acc_has_permission('acc_can_create_transaction')){ ?>
               <div class="checkbox checkbox-info">
                  <input type="checkbox" name="mass_convert" id="mass_convert" checked class="form-check-input">
                  <label for="mass_convert"><?php echo _l('mass_convert'); ?></label>
               </div>
            <?php } ?>
            <?php if(acc_has_permission('acc_can_delete_transaction')){ ?>
               <div class="checkbox checkbox-danger">
                  <input type="checkbox" name="mass_delete_convert" id="mass_delete_convert" class="form-check-input">
                  <label for="mass_delete_convert"><?php echo _l('mass_delete_convert'); ?></label>
               </div>
            <?php } ?>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>
          <a type="submit" onclick="assets_bulk_action(this); return false;" class="btn btn-info btn-submit text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo app_lang('confirm'); ?></a>
      </div>
   </div>
   <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php require 'plugins/Accounting/assets/js/transaction/fe_assets_js.php';?>