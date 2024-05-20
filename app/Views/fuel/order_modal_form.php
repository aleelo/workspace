<?php echo form_open(get_uri("fuel/order_save"), array("id" => "order-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
       
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
        <div class="form-group">
            <div class="row">
                <label for="fuel_type" class="col-3"><?php echo app_lang('fuel_type'); ?></label>
                <div class="col-9">
                    <?php
                    echo form_dropdown(array(
                        "id" => "fuel_type",
                        "name" => "fuel_type",
                        "class" => "form-control select2",
                        "placeholder" => app_lang('fuel_type'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ),['Gasoline (Baasiin)'=>'Gasoline (Baasiin)','Naphtha'=>'Naphtha'],[$model_info->fuel_type]);
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <label for="supplier" class="col-3"><?php echo app_lang('supplier'); ?></label>
                <div class="col-9">
                    <?php
                    echo form_dropdown(array(
                        "id" => "supplier",
                        "name" => "supplier",
                        "class" => "form-control select2",
                        "placeholder" => app_lang('supplier'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ),['NISA'=>'NISA','SNA'=>'SNA','UNSOS'=>'UNSOS','HAQABTIRE'=>'HAQABTIRE'],[$model_info->supplier]);
                    ?>
                </div>
            </div>
        </div>
        <!-- fuel_type supplier receive_date barrels	litters	received_by	vehicle_model	plate	 -->

        <div class="form-group">
            <div class="row">
                <label for="order_date" class="col-3"><?php echo 'Order/EAD Date'; ?>
                </label>
                <div class="col-5">
                    <?php
                    echo form_input(array(
                        "id" => "order_date",
                        "name" => "order_date",
                        "value" => empty($model_info->order_date) ? date("Y-m-d") : $model_info->order_date,
                        "class" => "form-control date",
                        "placeholder" => app_lang('order_date')
                    ));
                    ?>
                </div>
            
                <div class="col-4">
               
                    <?php
                    echo form_input(array(
                        "id" => "arrival_date",
                        "name" => "arrival_date",
                        "value" => $model_info->arrival_date,
                        "class" => "form-control date",
                        "placeholder" => app_lang('ead_arrival_date')
                    ));
                    ?>
                </div>
            </div> 
        </div>

        <div class="form-group">
            <div class="row">
                <label for="barrels" class="col-3"><?php echo app_lang('barrels').'/Fuustoyinka'; ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "barrels",
                        "name" => "barrels",
                        "value" => $model_info->barrels,
                        "class" => "form-control",
                        "placeholder" => app_lang('barrels')
                    ));
                    ?>
                </div>
            </div> 
        </div>

        <div class="form-group">
            <div class="row">
                <label for="litters" class="col-3"><?php echo app_lang('litters'); ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_input(array(
                        "id" => "litters",
                        "name" => "litters",
                        "readonly" => "readonly",
                        "value" => $model_info->litters,
                        "class" => "form-control",
                        "placeholder" => app_lang('litters')
                    ));
                    ?>
                </div>
            </div> 
        </div>

        <div class="form-group">
            <div class="row">
                <label for="remarks" class="col-3"><?php echo app_lang('remarks'); ?>
                </label>
                <div class="col-9">
                    <?php
                    echo form_textarea(array(
                        "id" => "remarks",
                        "name" => "remarks",
                        "rows" => "10",
                        "cols" => "10",
                        "class" => "form-control",
                        "placeholder" => app_lang('remarks')
                    ),$model_info->remarks);
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
    $(document).ready(function () {
        $("#order-form").appForm({
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
            $("#fuel_type").focus();
        }, 200);

        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();
        setDatePicker("#order_date");
        setDatePicker("#arrival_date");
        // $('#owner_id').select2({data: <?php //echo json_encode($owners_dropdown); ?>});

        // $("#lead_labels").select2({multiple: true, data: <?php //echo json_encode($label_suggestions); ?>});

        $('#barrels').on('input', function (e) {
            $('#litters').val($(this).val() * 220);
        });

    });
</script>    