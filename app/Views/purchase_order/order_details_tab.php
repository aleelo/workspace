<div class="clearfix default-bg">
    <div class="row">
        <div class="col-md-9 d-flex align-items-stretch">
            <div class="card p15 w-100">
                <div id="page-content" class="clearfix grid-button">
                    <div style="max-width: 1000px; margin: auto;">
                        <div class="clearfix p20">
                            <!-- small font size is required to generate the pdf, overwrite that for screen -->
                            <style type="text/css"> .invoice-meta {
                                    font-size: 100% !important;
                                }</style>

                         <!-- header and purchase ifo -->
                         <table class="header-style" style="font-size: 13.5px;">
                            <tr class="invoice-preview-header-row">
                                <td style="width: 45%; vertical-align: top;">
                                    <?php echo get_company_logo($invoice_info->company_id, "invoice"); ?>
                                </td>
                                <td class="hidden-invoice-preview-row" style="width: 20%;"></td>
                                <td class="invoice-info-container invoice-header-style-one" style="width: 35%; vertical-align: top; text-align: right">
                                
                                    <span class="invoice-info-title" style="font-size:20px; font-weight: bold;background-color: #287ec9; color: #fff;">&nbsp;
                                    <?php echo app_lang("purchase").': #'.$purchase_info->id; ?>&nbsp;</span><br />
                                   
                              
                                    <?php                                        
                                        echo app_lang("order_date") . ": " . format_to_date($purchase_info->order_date, false);?><br /><?php
                                        echo app_lang("type") . ": " . $purchase_info->product_type;
                                        echo app_lang("department") . ": " . $purchase_info?->department;
                                        ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 5px;"></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><?php
                                    echo 'Villa Somalia';
                                    ?>
                                </td>
                                <td></td>
                                <td><?php
                                    // echo view('invoices/invoice_parts/bill_to', $data);
                                    ?>
                                </td>
                            </tr>
                        </table>
                        </div>

                        <div class="table-responsive mt15 pl15 pr15">
                            <table id="invoice-item-table" class="display" width="100%">            
                            </table>
                        </div>

                        <div class="clearfix">
                            <?php if ($can_add_purchase) { ?>
                                <div class="float-start mt20 ml15">
                                    <?php echo modal_anchor(get_uri("purchase_order/item_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_item'), array("class" => "btn btn-info text-white", "title" => app_lang('add_item'), "data-post-invoice_id" => $purchase_info->id)); ?>
                                </div>
                            <?php } ?>
                            <div class="float-end pr15" id="invoice-total-section">
                                <?php //echo view("purchase_order/purchase_total_section", array("purchase_id" => $purchase_info->id, "can_add_purchase" => $can_add_purchase)); ?>
                            </div>
                        </div>           

                        <!-- <p class="b-t b-info pt10 m15"><?php //echo nl2br($purchase_info->note ? process_images_from_content($purchase_info->note) : ""); ?></p> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-stretch">
            <div class="card p15 w-100">
                <div class="clearfix p20">
                    <div class="row">
                        <?php echo view("invoices/invoice_status_bar"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var optionVisibility = false;
        if ("<?php echo $can_edit_purchases ?>") {
            optionVisibility = true;
        }
        var delay;
        var taxableRows = [];

        $("#invoice-item-table").appTable({
            source: '<?php echo_uri("purchase_order/item_list_data/" . $purchase_info->id) ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            stateSave: false,
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("item") ?> ', sortable: false},
                {title: '<?php echo app_lang("quantity") ?>', "class": "text-right w15p", sortable: false},
                {title: '<?php echo app_lang("price") ?>', "class": "text-right w15p", sortable: false},
                {title: '<?php echo app_lang("total") ?>', "class": "text-right w15p", sortable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", sortable: false, visible: optionVisibility}
            ],
            // rowCallback: function (nRow, aData) {
            //     var column = $("#invoice-item-table").DataTable().column(4);
            //     var taxableColumn = "<?php //echo get_setting('taxable_column'); ?>";
            //     if (taxableColumn == "always_show") {
            //         column.visible(true);
            //     } else if (taxableColumn == "never_show") {
            //         column.visible(false);
            //     } else {
            //         taxableRows.push(aData[4]);
            //         clearTimeout(delay);
            //         delay = setTimeout(function () {
            //             var unique = getUniqueArray(taxableRows);

            //             if (unique.length === 2) {
            //                 column.visible(true);
            //             } else {
            //                 column.visible(false);
            //             }
            //             taxableRows = [];
            //         }, 100);
            //     }

            // },
            onInitComplete: function () {
                <?php if ($can_edit_purchases) { ?>
                    //apply sortable
                    $("#invoice-item-table").find("tbody").attr("id", "invoice-item-table-sortable");
                    var $selector = $("#invoice-item-table-sortable");

                    Sortable.create($selector[0], {
                        animation: 150,
                        chosenClass: "sortable-chosen",
                        ghostClass: "sortable-ghost",
                        onUpdate: function (e) {
                            appLoader.show();
                            //prepare sort indexes 
                            var data = "";
                            $.each($selector.find(".item-row"), function (index, ele) {
                                if (data) {
                                    data += ",";
                                }

                                data += $(ele).attr("data-id") + "-" + index;
                            });

                            //update sort indexes
                            $.ajax({
                                url: '<?php echo_uri("invoices/update_item_sort_values") ?>',
                                type: "POST",
                                data: {sort_values: data},
                                success: function () {
                                    appLoader.hide();
                                }
                            });
                        }
                    });

<?php } ?>

            },
            onDeleteSuccess: function (result) {
                $("#invoice-total-section").html(result.invoice_total_view);
                if (typeof updateInvoiceStatusBar == 'function') {
                    updateInvoiceStatusBar(result.invoice_id);
                }
            },
            onUndoSuccess: function (result) {
                $("#invoice-total-section").html(result.invoice_total_view);
                if (typeof updateInvoiceStatusBar == 'function') {
                    updateInvoiceStatusBar(result.invoice_id);
                }
            }
        });
    });
</script>