<div class="tab-content">
    <?php echo form_open(get_uri("team_members/save_education_info/" . $user_info->id), array("id" => "education-info-form", "class" => "education-form dashed-row white", "role" => "form")); ?>
    <div class="card">
        <div class=" card-header">
            <h4> <?php echo app_lang('education_info'); ?></h4>
        </div>
        <div class="card-body">

            
            <div class="form-group">
                <div class="row">
                    <label for="education_level" class=" col-md-2"><?php echo 'Education Level'; ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_dropdown(array(
                            "id" => "education_level",
                            "name" => "education_level",
                            "class" => "form-control select2",
                            "value" => $user_info->education_level,
                            "placeholder" => 'Education Level'
                        ),$education_levels, [$user_info->education_level =>$education_levels[$user_info->education_level]]);//$education_levels[$user_info->education_level]
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group" id="div_education_field_of_study">
                <div class="row">
                    <label for="education_field" class=" col-md-2"><?php echo 'Field of Study'; ?></label>
                    <div class=" col-md-10">
                                               
                        <select id= "education_field",
                                name= "education_field",
                                class = "form-control select2",
                                placeholder = 'Field of Study',
                                autocomplete= "off",
                                data-rule-required = 'true',
                                data-msg-required =  "<?= app_lang("field_required")?>">
                                <option value="">Choose Field of Study</option>

                                    <?php
                                    if(count($education_fields)){
                                        foreach($education_fields as $f){
                                        ?>
                                            <option value="<?php echo $f->name?>" <?php echo $f->name == $user_info->education_field ? 'selected' : '' ?>><?php echo $f->name?></option>
                                        <?php
                                    }
                                   }
                                    ?>
                            </select>
                    </div>
                </div>
            </div>
            
            <div class="form-group" id="div_other_field_of_study">
                <div class="row">
                    <label for="other_study" class=" col-md-2"><?php echo 'Other Field of Study'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "other_study",
                            "name" => "other_study",
                            "class" => "form-control",
                            "value" => $user_info->faculty,
                            "placeholder" => 'Other Field of Study',
                        ));
                        ?>
                    </div>
                </div>
            </div>
<!-- 
            <div class="form-group">
                <div class="row">
                    <label for="faculty" class=" col-md-2"><?php echo 'Faculty 1'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        // echo form_input(array(
                        //     "id" => "faculty",
                        //     "name" => "faculty",
                        //     "class" => "form-control",
                        //     "value" => $user_info->faculty,
                        //     "placeholder" => 'Faculty 1 Name',
                        //     "autocomplete" => "off",
                        // ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="faculty2" class=" col-md-2"><?php echo 'Faculty 2'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        // echo form_input(array(
                        //     "id" => "faculty2",
                        //     "name" => "faculty2",
                        //     "class" => "form-control",
                        //     "value" => $user_info->faculty2,
                        //     "placeholder" => 'Faculty 2 Name',
                        //     "autocomplete" => "off",
                        // ));
                        ?>
                    </div>
                </div>
            </div> -->

            <!-- <div class="form-group">
                <div class="row">
                    <label for="education_school" class=" col-md-2"><?php// echo 'School of Study'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        // echo form_input(array(
                        //     "id" => "education_school",
                        //     "name" => "education_school",
                        //     "value" => $user_info->education_school,
                        //     "class" => "form-control",
                        //     "placeholder" => 'School of Study',
                        //     "autocomplete" => "off",
                        // ));
                        ?>
                    </div>
                </div>
            </div>
                   -->
            <!-- <div class="form-group">
                <div class="row">
                    <label for="highest_school" class=" col-md-2"><?php echo 'Highest School of Education'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        // echo form_input(array(
                        //     "id" => "highest_school",
                        //     "name" => "highest_school",
                        //     "value" => $user_info->highest_school,
                        //     "class" => "form-control",
                        //     "placeholder" => 'Highest School of Education',
                        //     "autocomplete" => "off",
                        // ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="bachelor_degree" class=" col-md-2"><?php echo 'Bachelor Degree'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        // echo form_input(array(
                        //     "id" => "bachelor_degree",
                        //     "name" => "bachelor_degree",
                        //     "value" => $user_info->bachelor_degree,
                        //     "class" => "form-control",
                        //     "placeholder" => 'Bachelor Degree Name',
                        //     "autocomplete" => "off",
                        // ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="master_degree" class=" col-md-2"><?php echo 'Master Degree'; ?></label>
                    <div class=" col-md-9">
                        <?php
                        // echo form_input(array(
                        //     "id" => "master_degree",
                        //     "name" => "master_degree",
                        //     "value" => $user_info->master_degree,
                        //     "class" => "form-control",
                        //     "placeholder" => 'Master Degree Name',
                        //     "autocomplete" => "off",
                        // ));
                        ?>
                    </div>
                </div>
            </div> -->

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-2", "field_column" => " col-md-10")); ?> 

        </div>
        <div class="card-footer rounded-0">
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function () {

        $("#education-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                setTimeout(function () {
                    window.location.href = "<?php echo get_uri("team_members/view/" . $user_info->id); ?>" + "/education_info";
                }, 500);
            }
        });
        
        $("#education-info-form .select2").select2();

        setDatePicker("#birth_date");

        // $('#div_education_field_of_study').hide();
        $('#div_other_field_of_study').hide();
        
        // $('#education_level').on('change',function(){
        //     var value = $(this).val();
        //     if(value == 'Bachelor'){
        //         $('#div_education_field_of_study').show();
        //     }
        // });

        $('#education_field').on('change',function(){
            var value = $(this).val();
            if(value == 'Others'){
                $('#div_other_field_of_study').show();
            }
        });

    });
</script>    