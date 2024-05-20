<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />

<div class="form-group">
    <div class="row">
        <label for="request_type" class="col-3"><?php echo app_lang('request_type'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_dropdown(array(
                "id" => "request_type",
                "name" => "request_type",
                "class" => "form-control select2",
                "placeholder" => app_lang('request_type')
            ),['Peronal Request'=>'Peronal Request','For Other Person'=>'For Other Person'],[$model_info->request_type]);
            ?>
        </div>
    </div> 
</div>

<div class="form-group" id="team_member" style="display: none">
    <div class="row">
        <label for="requested_by" class="col-3"><?php echo app_lang('team_member'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_dropdown(array(
                "id" => "requested_by",
                "name" => "requested_by",
                "class" => "form-control select2",
                "placeholder" => app_lang('requested_by')
            ),$employees,[$model_info->requested_by],"style='display:none;'");
            ?>
        </div>
    </div> 
</div>

<!-- request_type	request_date	litters	purpose	vehicle_engine	plate	remarks	 -->
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
        <label for="request_date" class="col-3"><?php echo app_lang('request_date'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "request_date",
                "name" => "request_date",
                "value" => $model_info->request_date,
                "class" => "form-control date",
                "placeholder" => app_lang('request_date')
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
        <label for="purpose" class="col-3"><?php echo app_lang('purpose'); ?></label>
        <div class="col-9">
            <?php
            echo form_dropdown(array(
                "id" => "purpose",
                "name" => "purpose",
                "class" => "form-control select2",
                "placeholder" => app_lang('purpose'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ),['Routine'=>'Routine','Travel'=>'Travel'],[$model_info->purpose]);
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="vehicle_engine" class="col-3"><?php echo app_lang('vehicle_engine'); ?>
        </label>
        <div class="col-9">
            <?php
            echo form_input(array(
                "id" => "vehicle_engine",
                "name" => "vehicle_engine",
                "value" => $model_info->vehicle_engine,
                "class" => "form-control",
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
                "placeholder" => app_lang('vehicle_engine')
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
        setDatePicker("#request_date");
        // $('#owner_id').select2({data: <?php //echo json_encode($owners_dropdown); ?>});

        // $("#lead_labels").select2({multiple: true, data: <?php //echo json_encode($label_suggestions); ?>});

        get_employee_changes();

        $('#request_type').on('change', function() {
           get_employee_changes();
        });

        $('#barrels').on('input', function() {
            $('#litters').val($(this).val() * 159);
        })

        function get_employee_changes()
        {
            if($("#request_type").val() == 'For Other Person'){
                $('#team_member').show();
                $('#requested_by').show();

                $("#requested_by").select2();
            }else{
                $('#team_member').hide();
                $('#requested_by').hide();
                $('#requested_by').val('');
            }
        }
    });
</script>