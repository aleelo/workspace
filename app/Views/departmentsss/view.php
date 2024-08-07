<div class="modal-body clearfix general-form">
    <div class="container-fluid">

        <div class="clearfix">
            <div class="col-md-12">
                <strong class="font-18"><?php echo $model_info->name; ?></strong>              
                
            </div>
        </div>

        <div class="col-md-12 mb15">
            <span class="badge item-rate-badge font-18 strong"><?php echo to_currency($model_info->price); ?></span> <?php echo $model_info->unit_type ? "/" . $model_info->unit_type : ""; ?>
        </div>

        <div class="col-md-12 mb15">
            <?php echo $model_info->description ? nl2br(link_it(process_images_from_content($model_info->description))) : "-"; ?>
        </div>

      
    </div>
</div>

<div class="modal-footer">
    <?php
    if (isset($login_user->id) && $login_user->user_type == "staff") {
        echo modal_anchor(get_uri("purchase_items/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_item'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "title" => app_lang('edit_item')));
    }
    ?>
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <?php
    //show add to cart button on client portal
    // if (!$model_info->added_to_cart && (!isset($login_user->id) || (isset($login_user->id) && $login_user->user_type == "client"))) {
    //     echo js_anchor("<i data-feather='shopping-cart' class='icon-16'></i> " . app_lang("add_to_cart"), array("class" => "btn btn-info text-white item-add-to-cart-btn", "data-item_id" => $model_info->id));
    // }
    ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>