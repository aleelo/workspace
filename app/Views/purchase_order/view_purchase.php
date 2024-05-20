<div class="page-content invoice-details-view clearfix">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="invoice-title-section">
                    <div class="page-title no-bg clearfix mb5 no-border">
                        <h1 class="pl0">
                            <span><i data-feather="cart" class='icon'></i></span>
                          
                            <?php echo $purchase_info->id; ?>
                           
                        </h1>

                        <div class="title-button-group mr0">
                            
                            <?php if ($can_add_purchase) { ?>
                                <?php //echo modal_anchor(get_uri("purchase_order/order_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_purchase'), array("class" => "btn btn-default", "title" => app_lang('add_payment'), "data-post-invoice_id" => $purchase_info->id)); ?>
                            <?php } ?>

                            <span class="dropdown inline-block">
                                <button class="btn btn-info text-white dropdown-toggle caret" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                                    <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php if ($purchase_status !== "cancelled" && $can_add_purchase) { ?>
                                    
                                    <li role="presentation"><?php echo modal_anchor(get_uri("purchase_order/send_invoice_modal_form/" . $purchase_info->id), "<i data-feather='mail' class='icon-16'></i> " . app_lang('email_invoice_to_client'), array("title" => app_lang('email_invoice_to_client'), "data-post-id" => $purchase_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>
                                       
                                    <li role="presentation"><?php echo anchor(get_uri("purchase_order/download_pdf/" . $purchase_info->id), "<i data-feather='download' class='icon-16'></i> " . app_lang('download_pdf'), array("title" => app_lang('download_pdf'), "class" => "dropdown-item")); ?> </li>
                                    <li role="presentation"><?php echo anchor(get_uri("purchase_order/download_pdf/" . $purchase_info->id . "/view"), "<i data-feather='file-text' class='icon-16'></i> " . app_lang('view_pdf'), array("title" => app_lang('view_pdf'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                                    <li role="presentation"><?php echo anchor(get_uri("purchase_order/preview/" . $purchase_info->id . "/1"), "<i data-feather='search' class='icon-16'></i> " . app_lang('preview'), array("title" => app_lang('preview'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>
                                    <li role="presentation"><?php echo js_anchor("<i data-feather='printer' class='icon-16'></i> " . app_lang('print'), array('title' => app_lang('print'), 'id' => 'print-invoice-btn', "class" => "dropdown-item")); ?> </li>

                                        <li role="presentation" class="dropdown-divider"></li>
                                      
                                        <li role="presentation"><?php echo modal_anchor(get_uri('purchase_order/modal_form'), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit_invoice'), array("title" => app_lang('edit_invoice'), "data-post-id" => $purchase_info->id, "role" => "menuitem", "tabindex" => "-1", "class" => "dropdown-item")); ?> </li>

                                        <?php if ($purchase_status == "draft" && $purchase_status !== "cancelled") { ?>
                                            <li role="presentation"><?php echo ajax_anchor(get_uri("purchase_order/update_purchase_status/" . $purchase_info->id . "/not_paid"), "<i data-feather='check' class='icon-16'></i> " . app_lang('mark_invoice_as_not_paid'), array("data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                                        <?php } else if ($purchase_status == "not_paid" || $purchase_status == "overdue" || $purchase_status == "partially_paid") { ?>
                                            <li role="presentation"><?php echo js_anchor("<i data-feather='x' class='icon-16'></i> " . app_lang('mark_invoice_as_cancelled'), array('title' => app_lang('mark_invoice_as_cancelled'), "data-action-url" => get_uri("purchase_order/update_purchase_status/" . $purchase_info->id . "/cancelled"), "data-action" => "delete-confirmation", "data-reload-on-success" => "1", "class" => "dropdown-item")); ?> </li>
                                        <?php } ?>

                                       
                                    <?php } ?>

                                </ul>
                            </span>
                        </div>
                    </div>

                    <ul id="invoice-tabs" data-bs-toggle="ajax-tab" class="nav nav-pills rounded classic mb20 scrollable-tabs border-white" role="tablist">
                        <li><a role="presentation" data-bs-toggle="tab"  href="<?php echo_uri("purchase_order/order_details_tab/" . $purchase_info->id); ?>" data-bs-target="#order-details-tab"><?php echo app_lang("order_details"); ?></a></li>
                     
                        <!-- <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("purchase_order/payments/" . $purchase_info->id); ?>" data-bs-target="#invoice-payments-section"><?php echo app_lang('payments'); ?></a></li>
                         
                        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("purchase_order/tasks/" . $purchase_info->id); ?>" data-bs-target="#invoice-tasks-section"><?php echo app_lang('tasks'); ?></a></li> -->
                    </ul>
                </div>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active" id="order-details-tab"></div>
<!--                   
                    <div role="tabpanel" class="tab-pane fade grid-button" id="invoice-payments-section"></div>
                       
                    <div role="tabpanel" class="tab-pane fade grid-button" id="invoice-tasks-section"></div> -->
                </div>
            </div>
        </div>
    </div>    
</div>
<script type="text/javascript">
    $(document).ready(function () {
        //modify the delete confirmation texts
        $("#confirmationModalTitle").html("<?php echo app_lang('cancel') . "?"; ?>");
        $("#confirmDeleteButton").html("<i data-feather='x' class='icon-16'></i> <?php echo app_lang("cancel"); ?>");
    });

    updateInvoiceStatusBar = function (invoiceId) {
        $.ajax({
            url: "<?php echo get_uri("purchase_order/get_invoice_status_bar"); ?>/" + invoiceId,
            success: function (result) {
                if (result) {
                    $("#invoice-status-bar").html(result);
                }
            }
        });
    };

    //print invoice
    $("#print-invoice-btn").click(function () {
        appLoader.show();

        $.ajax({
            url: "<?php echo get_uri('purchase_order/print_invoice/' . $purchase_info->id) ?>",
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    document.body.innerHTML = result.print_view; //add invoice's print view to the page
                    $("html").css({"overflow": "visible"});

                    setTimeout(function () {
                        window.print();
                    }, 200);
                } else {
                    appAlert.error(result.message);
                }

                appLoader.hide();
            }
        });
    });

    //reload page after finishing print action
    window.onafterprint = function () {
        location.reload();
    };

</script>

<?php
//required to send email 

load_css(array(
    "assets/js/summernote/summernote.css",
));
load_js(array(
    "assets/js/summernote/summernote.min.js",
));
?>