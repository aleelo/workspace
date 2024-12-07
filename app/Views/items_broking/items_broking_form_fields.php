
<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />


<div class="form-group">
    <div class="row">
        <label for="item_id" class=" <?php echo $label_column; ?>"><?php echo 'Item Name'; ?></label>
        <div class=" col-md-9">
            <?php
            echo form_dropdown(array(
                "id" => "item_id",
                "name" => "item_id",
                "class" => "form-department_head select2",
                "placeholder" => 'Item Name',
                "autocomplete" => "off"
            ),$Items_Lists,[$model_info->item_id]);
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="broken" class="<?php echo $label_column; ?> company_name_section"><?php echo 'Broken'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "broken",
                "name" => "broken",
                "value" => $model_info->broken,
                "class" => "form-control company_name_input_section",
                "placeholder" => 'Broken',
            ));
            ?>
        </div>
    </div>
</div> 

<div class="form-group">
    <div class="row">
        <label for="description" class="<?php echo $label_column; ?> company_name_section"><?php echo 'Description'; ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "description",
                "name" => "description",
                "value" => $model_info->description,
                "class" => "form-control company_name_input_section",
                "placeholder" => 'Description',
            ));
            ?>
        </div>
    </div>
</div> 


<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?> 

<script type="text/javascript">
    var k=1;
    $(document).ready(function () {

      

        setDatePicker("#Start_Date")
        setDatePicker("#End_Date")
        
        $('[data-bs-toggle="tooltip"]').tooltip();

<?php if (isset($currency_dropdown)) { ?>
            if ($('#currency').length) {
                $('#currency').select2({data: <?php echo json_encode($currency_dropdown); ?>});
            }
<?php } ?>

<?php if (isset($groups_dropdown)) { ?>
            $("#group_ids").select2({
                multiple: true,
                data: <?php echo json_encode($groups_dropdown); ?>
            });
<?php } ?>

<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
            $('#created_by').select2({data: <?php echo $team_members_dropdown; ?>});
<?php } ?>

<?php if ($login_user->user_type === "staff") { ?>
            $("#client_labels").select2({multiple: true, data: <?php echo json_encode($label_suggestions); ?>});
<?php } ?>
        $('.account_type').click(function () {
            var inputValue = $(this).attr("value");
            if (inputValue === "person") {
                $(".company_name_section").html("Name");
                $(".company_name_input_section").attr("placeholder", "Name");
            } else {
                $(".company_name_section").html("Company name");
                $(".company_name_input_section").attr("placeholder", "Company name");
            }
        });

        $("#client-form .select2").select2();

    });
</script>