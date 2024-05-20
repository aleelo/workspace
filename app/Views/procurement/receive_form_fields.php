<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <div class="row">
        <label for="fuel_order" class="col-3"><?php echo app_lang('fuel_order'); ?></label>
        <div class="col-9">
            <?php
            echo form_dropdown(array(
                "id" => "order_id",
                "name" => "order_id",
                "class" => "form-control select2",
                "placeholder" => app_lang('fuel_order'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ),$orders,[$model_info->order_id]);
            ?>
        </div>
    </div>
</div>

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
        <label for="receive_date" class="col-3"><?php echo app_lang('receive_date'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "receive_date",
                "name" => "receive_date",
                "value" => $model_info->receive_date,
                "class" => "form-control date",
                "placeholder" => app_lang('receive_date')
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
        <label for="vehicle_model" class="col-3"><?php echo app_lang('vehicle_model'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "vehicle_model",
                "name" => "vehicle_model",
                "value" => $model_info->vehicle_model,
                "class" => "form-control",
                "placeholder" => app_lang('vehicle_model')
            ));
            ?>
        </div>
    </div> 
</div>

<div class="form-group">
    <div class="row">
        <label for="plate" class="col-3"><?php echo app_lang('plate'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "plate",
                "name" => "plate",
                "value" => $model_info->plate,
                "class" => "form-control",
                "placeholder" => app_lang('plate')
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


<script type="text/javascript">
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".select2").select2();
        setDatePicker("#receive_date");
        // $('#owner_id').select2({data: <?php //echo json_encode($owners_dropdown); ?>});

        // $("#lead_labels").select2({multiple: true, data: <?php //echo json_encode($label_suggestions); ?>});

        $('#barrels').on('input', function (e) {
            $('#litters').val($(this).val() * 220);
        });


        $('#order_id').on('change',function(e){

            e.preventDefault();

            $.ajax({
                    url: 'fuel/get_order_details_json',
                    data: {
                        'order_id': $('#order_id').val(),
                        // 'rise_csrf_token': $('input[name="rise_csrf_token"]').val(),
                    },
                    cache: false,
                    dataType: 'json',
                    type: 'POST',
                    success: function (res) {
                        // console.log(res);
                        $('#fuel_type').val(res.fuel_type).trigger('change');
                        $('#supplier').val(res.supplier).trigger('change');
                        $('#receive_date').val(res.order_date);
                        $('#barrels').val(res.barrels);
                        $('#litters').val(res.barrels * 220);
                        $('#remarks').val(res.remarks);
                                             
                        feather.replace();
                    },
                    statusCode: {
                        403: function () {
                            console.log("403: Session expired.");
                            // location.reload();
                        },
                        404: function () {
                            $("#search-container").find('.modal-body').html("");
                            appAlert.error("404: Page not found.", {container: '.search-container', animate: false});
                        }
                    },
                    error: function () {
                        $("#search-container").find('.modal-body').html("");
                        appAlert.error("500: Internal Server Error.", {container: '.search-container', animate: false});
                    }
                });

                
            });
    });
</script>