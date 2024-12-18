<div class="tab-content">
    <?php echo form_open(get_uri("team_members/save_job_info"), array("id" => "job-info-form", "class" => "general-form dashed-row white", "role" => "form")); ?>

    <input name="user_id" type="hidden" value="<?php echo $user_id; ?>" />
    <div class="card">
        <div class=" card-header">
            <h4><?php echo app_lang('job_info'); ?></h4>
        </div>
        
        <div class="card-body">
            <div class="form-group">
                    <div class="row">
                        <label for="employee_type" class=" col-md-3"><?php echo 'Employee Type'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            $types = ['Temporary'=>'Temporary','Fixed'=>'Fixed','Internship'=>'Internship'];
                            echo form_dropdown(array(
                                "id" => "employee_type",
                                "name" => "employee_type",
                                "class" => "form-control select2",
                                "placeholder" => 'Employee Type',
                                "autocomplete" => "off"
                            ),$types,[$job_info->employee_type]);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="department_id" class=" col-md-3"><?php echo 'Employee Department'; ?></label>
                        <div class=" col-md-9">
                          
                            <select id= "department_id",
                                name= "department_id",
                                class = "form-control select2",
                                placeholder = 'Employee Department',
                                autocomplete= "off",
                                data-rule-required = 'true',
                                data-msg-required =  "<?= app_lang("field_required")?>">
                                    <?php
                                        foreach($departments as $k=>$v){
                                    ?>
                                        <option value="<?php echo $k?>" <?php echo $k == $job_info->department_id ? 'selected' : '' ?>><?php echo $v?></option>
                                    <?php
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                </div> 
 
                <div class="form-group">
                    <div class="row">
                        <label for="section_id" class=" col-md-3"><?php echo 'Department Section'; ?></label>
                        <div class=" col-md-9">
                           
                             <select id= "section_id",
                                name= "section_id",
                                class = "form-control select2",
                                placeholder = 'Choose department Section',
                                autocomplete= "off",
                                data-rule-required = 'true',
                                data-msg-required =  "<?= app_lang("field_required")?>">
                                    <?php
                                        foreach($sections as $k=>$v){
                                    ?>
                                        <option value="<?php echo $k?>" <?php echo $k == $job_info->section_id ? 'selected' : '' ?>><?php echo $v?></option>
                                    <?php
                                    }
                                    ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="job_title_en" class=" col-md-3"><?php echo 'Job Title English'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "job_title_en",
                                "name" => "job_title_en",
                                "class" => "form-control",
                                'value'=>  $job_info->job_title_en,
                                "placeholder" => 'Job Title English',
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="job_title_so" class=" col-md-3"><?php echo 'Job Title Somali'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "job_title_so",
                                "name" => "job_title_so",
                                "class" => "form-control",
                                "placeholder" => 'Job Title Somali',
                                'value'=> $job_info->job_title_so,
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group "  style="display:none;">
                    <div class="row">
                        <label for="salary" class=" col-md-3"><?php echo app_lang('salary'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "salary",
                                "name" => "salary",
                                'value'=> $job_info->salary,
                                "class" => "form-control",
                                "placeholder" => app_lang('salary')
                            ),'500',['style' => 'display:none']);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="display:none;">
                    <div class="row">
                        <label for="salary_term" class=" col-md-3"><?php echo app_lang('salary_term'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "salary_term",
                                "name" => "salary_term",
                                "class" => "form-control",
                                'value'=> $job_info->salary_term,
                                "placeholder" => app_lang('salary_term')
                            ),'test',['style' => 'display:none']);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="date_of_hire" class=" col-md-3"><?php echo app_lang('date_of_hire'); ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "date_of_hire",
                                "name" => "date_of_hire",
                                "class" => "form-control date",
                                'value'=> $job_info->date_of_hire,
                                "placeholder" => app_lang('date_of_hire'),
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="row">
                        <label for="employee_id" class=" col-md-3"><?php echo 'Employee Number'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "employee_id",
                                "name" => "employee_id",
                                "class" => "form-control",
                                'value'=> $job_info->employee_id,
                                "placeholder" => 'eg. Employee Card Number',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>
        </div>

        <?php if ($login_user->is_admin || $can_manage_team_members_job_information) { ?>
            <div class="card-footer rounded-0">
                <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            </div>
        <?php } ?>

    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#job-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                window.location.href = "<?php echo get_uri("team_members/view/" . $job_info->user_id); ?>" + "/job_info";
            }
        });
        $("#job-info-form .select2").select2();

        setDatePicker("#date_of_hire");
        setDatePicker(".date");

    });
</script>    