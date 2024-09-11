<?php echo form_open(get_uri("team_members/add_team_member"), array("id" => "team_member-form", "class" => "general-form", "role" => "form")); ?>
<div id="team-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">

            <style>
                .app-alert.alert-danger {
                    margin-top: 70px;
                }
            </style>

            <div class="form-widget">
                <div class="widget-title clearfix">
                    <div class="row">
                        <div id="general-info-label" class="col"><i data-feather="circle" class="icon-16"></i><strong> <?php echo app_lang('Profile_info'); ?></strong></div>
                        <div id="education-info-label" class="col"><i data-feather="circle" class="icon-16"></i><strong> <?php echo app_lang('education_info'); ?></strong></div>
                        <div id="job-info-label" class="col"><i data-feather="circle" class="icon-16"></i><strong>  <?php echo app_lang('job_info'); ?></strong></div>
                        <div id="bank-details-label" class="col"><i data-feather="circle" class="icon-16"></i><strong>  <?php echo app_lang('bank_details'); ?></strong></div>
                        <div id="account-info-label" class="col"><i data-feather="circle" class="icon-16"></i><strong>  <?php echo app_lang('account_settings'); ?></strong></div> 
                    </div>
                </div>

                <div class="progress ml15 mr15">
                    <div id="form-progress-bar" class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    </div>
                </div>
            </div>

            <div class="tab-content mt15">

             <!---------------------------------- Profile Info Tab ----------------------------->
                
                <div role="tabpanel" class="tab-pane active" id="general-info-tab">
                    
                        <div class="mb-4">
                            <h4  class="text-muted">Profile Infomation</h4>
                            <hr class="mt-0"/> 
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="first_name" class=" col-md-3"><?php echo app_lang('first_name'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "first_name",
                                        "name" => "first_name",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('first_name'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="last_name" class=" col-md-3"><?php echo app_lang('last_name'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "last_name",
                                        "name" => "last_name",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('last_name'),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="address" class=" col-md-3"><?php echo app_lang('mailing_address'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_textarea(array(
                                        "id" => "address",
                                        "name" => "address",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('mailing_address')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="alternative_address" class=" col-md-3"><?php echo app_lang('alternative_address'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_textarea(array(
                                        "id" => "alternative_address",
                                        "name" => "alternative_address",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('alternative_address')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="phone" class=" col-md-3"><?php echo app_lang('phone'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "phone",
                                        "name" => "phone",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('phone')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>  
                        
                        <div class="form-group">
                            <div class="row">
                                <label for="alternative_phone" class=" col-md-3"><?php echo app_lang('alternative_phone'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "alternative_phone",
                                        "name" => "alternative_phone",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('alternative_phone')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="skype" class=" col-md-3">Skype</label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "skype",
                                        "name" => "skype",
                                        "class" => "form-control",
                                        "placeholder" => "Skype"
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <label for="ssn" class=" col-md-3"><?php echo app_lang('ssn'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "ssn",
                                        "name" => "ssn",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('ssn')
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="gender" class=" col-md-3"><?php echo app_lang('gender'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_radio(array(
                                        "id" => "gender_male",
                                        "name" => "gender",
                                        "class" => "form-check-input",
                                            ), "male", true);
                                    ?>
                                    <label for="gender_male" class="mr15"><?php echo app_lang('male'); ?></label> 
                                    <?php
                                    echo form_radio(array(
                                        "id" => "gender_female",
                                        "name" => "gender",
                                        "class" => "form-check-input",
                                            ), "female", false);
                                    ?>
                                    <label for="gender_female" class="mr15"><?php echo app_lang('female'); ?></label>
                                
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="marital_status_single" class=" col-md-3"><?php echo app_lang('marital_status'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_radio(array(
                                        "id" => "marital_status_single",
                                        "name" => "marital_status",
                                        "class" => "form-check-input",
                                            ), "single", true, "class='form-check-input'");
                                    ?>
                                    <label for="marital_status_maried" class="mr15 p0"><?php echo app_lang('single'); ?></label> 
                                    <?php
                                    echo form_radio(array(
                                        "id" => "marital_status_maried",
                                        "name" => "marital_status",
                                        "class" => "form-check-input",
                                            ), "maried", false, "class='form-check-input'");
                                    ?>
                                    <label for="marital_status_maried" class="p0 mr15"><?php echo app_lang('maried'); ?></label>
                                
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <label for="age_level" class=" col-md-3"><?php echo 'Age Level'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_dropdown(array(
                                        "id" => "age_level",
                                        "name" => "age_level",
                                        "class" => "form-control select2",
                                        "placeholder" => 'Age Level'
                                    ),$age_levels,[$model_info->age_level]);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="birth_date" class=" col-md-3"><?php echo 'Date of Birth'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "birth_date",
                                        "name" => "birth_date",
                                        "class" => "form-control date_input",
                                        "placeholder" => 'Date of Birth'
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <label for="birth_place" class=" col-md-3"><?php echo 'Place of Birth'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "birth_place",
                                        "name" => "birth_place",
                                        "class" => "form-control",
                                        "placeholder" => 'Place of Birth'
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="passport_no" class=" col-md-3"><?php echo 'Passport Number'; ?></label>
                                <div class="col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "passport_no",
                                        "name" => "passport_no",
                                        "class" => "form-control",
                                        "placeholder" => 'Passport Number'
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="relevant_document_url" class=" col-md-3"><?php echo 'Relevant Document Url'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "relevant_document_url",
                                        "name" => "relevant_document_url",
                                        "class" => "form-control",
                                        "placeholder" => 'Relevant Document Url e.g. resume drive url',
                                        "autocomplete" => "off",
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="my-4">
                            <h4 class="text-muted">Emergency Information</h4>
                            <hr class="mt-0"/> 
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="emergency_name" class=" col-md-3"><?php echo 'Emergency Contact Name'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "emergency_name",
                                        "name" => "emergency_name",
                                        "class" => "form-control",
                                        "placeholder" => 'Emergency Contact Name'
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <label for="emergency_phone" class=" col-md-3"><?php echo 'Emergency Phone'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "emergency_phone",
                                        "name" => "emergency_phone",
                                        "class" => "form-control",
                                        "placeholder" => 'Emergency Phone'
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                </div>
            
            <!---------------------------------- Education Info Tab ----------------------------->

                <div role="tabpanel" class="tab-pane" id="education-info-tab">

                <div class="form-group">
                <div class="row">
                    <label for="education_level" class="col-md-3"><?php echo 'Education Level'; ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown(array(
                            "id" => "education_level",
                            "name" => "education_level",
                            "class" => "form-control select2",
                            "value" => $model_info->education_level,
                            "placeholder" => 'Education Level'
                        ),$education_levels, [$model_info->education_level =>$education_levels[$model_info->education_level]]);
                        ?>
                    </div>
                </div>
            </div>

            <div id="primary_school_section">

                <div class="form-group">
                    <div class="row">
                        <label for="primary_school_name" class="col-md-3"><?php echo 'Primary School Name'; ?></label>
                        <div class="col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "primary_school_name",
                                "name" => "primary_school_name",
                                "class" => "form-control",
                                "value" => $model_info->faculty,
                                "placeholder" => 'Primary School Name',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="primary_graduation_date" class="col-md-3"><?php echo 'Primary Graduation Date'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "primary_graduation_date",
                                "name" => "primary_graduation_date",
                                "class" => "form-control",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Primary Graduation Date',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>
            
            <div id="secondary_school_section">

                <div class="form-group">
                    <div class="row">
                        <label for="secondary_school_name" class="col-md-3"><?php echo 'Secondary School Name'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "secondary_school_name",
                                "name" => "secondary_school_name",
                                "class" => "form-control",
                                "value" => $model_info->faculty,
                                "placeholder" => 'Secondary School Name',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="secondary_graduation_date" class=" col-md-3"><?php echo 'Secondary Graduation Date'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "secondary_graduation_date",
                                "name" => "secondary_graduation_date",
                                "class" => "form-control",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Secondary Graduation Date',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>

            <div id="diploma_section">

                <div class="form-group">
                    <div class="row">
                        <label for="university_name_diploma" class=" col-md-3"><?php echo 'University Name Diploma'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "university_name_diploma",
                                "name" => "university_name_diploma",
                                "class" => "form-control",
                                "value" => $model_info->faculty,
                                "placeholder" => 'University Name Diploma',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="field_of_study_diploma" class=" col-md-3"><?php echo 'Field of Study Diploma'; ?></label>
                        <div class=" col-md-9">
                        <?php 
                        echo form_dropdown(array( 
                                'id'=> "field_of_study_diploma",
                                'name'=> "field_of_study_diploma",
                                'class' => "form-control select2",
                                'placeholder' => 'Field of Study Diploma',
                            ),$field_of_study,[$model_info->education_field]); ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="graduation_date_diploma" class=" col-md-3"><?php echo 'Graduation Date Diploma'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "graduation_date_diploma",
                                "name" => "graduation_date_diploma",
                                "class" => "form-control date",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Graduation Date Diploma',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>

            <div id="foculty_1_section">

                <div class="form-group">
                    <div class="row">
                        <label for="university_name_foculty_1" class=" col-md-3"><?php echo 'University Name Foculty'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "university_name_foculty_1",
                                "name" => "university_name_foculty_1",
                                "class" => "form-control",
                                "value" => $model_info->faculty,
                                "placeholder" => 'University Name Foculty',
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="field_of_study_foculty_1" class=" col-md-3"><?php echo 'Field of Study Foculty'; ?></label>
                        <div class=" col-md-9">
                        <?php 
                        echo form_dropdown(array( 
                                'id'=> "field_of_study_foculty_1",
                                'name'=> "field_of_study_foculty_1",
                                'class' => "form-control select2",
                                'placeholder' => 'Field of Study Foculty',
                            ),$field_of_study,[$model_info->education_field]); ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="graduation_date_foculty_1" class=" col-md-3"><?php echo 'Graduation Date Foculty'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "graduation_date_foculty_1",
                                "name" => "graduation_date_foculty_1",
                                "class" => "form-control date",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Graduation Date Foculty',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                </div>

            <div id="foculty_2_section">
            
                <div class="form-group">
                    <div class="row">
                        <label for="un_name_foculty_2" class=" col-md-3"><?php echo 'University Name Faculty 2'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "un_name_foculty_2",
                                "name" => "un_name_foculty_2",
                                "class" => "form-control",
                                "value" => $model_info->faculty2,
                                "placeholder" => 'University Name Faculty 2',
                                "autocomplete" => "off",
                            ));
                            ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="field_of_study_foculty_2" class=" col-md-3"><?php echo 'Field of Study Foculty 2'; ?></label>
                        <div class=" col-md-9">
                        <?php 
                        echo form_dropdown(array( 
                                'id'=> "field_of_study_foculty_2",
                                'name'=> "field_of_study_foculty_2",
                                'class' => "form-control select2",
                                'placeholder' => 'Field of Study Foculty 2',
                            ),$field_of_study,[$model_info->education_field]); ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="graduation_date_foculty_2" class=" col-md-3"><?php echo 'Graduation Date Foculty 2'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "graduation_date_foculty_2",
                                "name" => "graduation_date_foculty_2",
                                "class" => "form-control date",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Graduation Date Foculty 2',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>

            <div id="master_1_section">
            
                <div class="form-group">
                    <div class="row">
                        <label for="un_name_master_1" class=" col-md-3"><?php echo 'University Name Master'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "un_name_master_1",
                                "name" => "un_name_master_1",
                                "class" => "form-control",
                                "value" => $model_info->faculty2,
                                "placeholder" => 'University Name Master',
                                "autocomplete" => "off",
                            ));
                            ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="field_of_study_master_1" class=" col-md-3"><?php echo 'Field of Study Master'; ?></label>
                        <div class=" col-md-9">
                        <?php 
                        echo form_dropdown(array( 
                                'id'=> "field_of_study_master_1",
                                'name'=> "field_of_study_master_1",
                                'class' => "form-control select2",
                                'placeholder' => 'Field of Study Master',
                            ),$field_of_study,[$model_info->education_field]); ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="graduation_date_master_1" class=" col-md-3"><?php echo 'Graduation Date Master'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "graduation_date_master_1",
                                "name" => "graduation_date_master_1",
                                "class" => "form-control date",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Graduation Date Master',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>

            <div id="master_2_section">
            
                <div class="form-group">
                    <div class="row">
                        <label for="un_name_master_2" class=" col-md-3"><?php echo 'University Name Master 2'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "un_name_master_2",
                                "name" => "un_name_master_2",
                                "class" => "form-control",
                                "value" => $model_info->faculty2,
                                "placeholder" => 'University Name Master 2',
                                "autocomplete" => "off",
                            ));
                            ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="field_of_study_master_2" class=" col-md-3"><?php echo 'Field of Study Master 2'; ?></label>
                        <div class=" col-md-9">
                        <?php 
                        echo form_dropdown(array( 
                                'id'=> "field_of_study_master_2",
                                'name'=> "field_of_study_master_2",
                                'class' => "form-control select2",
                                'placeholder' => 'Field of Study Master 2',
                            ),$field_of_study,[$model_info->education_field]); ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="graduation_date_master_1" class=" col-md-3"><?php echo 'Graduation Date Master 2'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "graduation_date_master_2",
                                "name" => "graduation_date_master_2",
                                "class" => "form-control date",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Graduation Date Master 2',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>

            <div id="php_section">
            
                <div class="form-group">
                    <div class="row">
                        <label for="un_name_php" class=" col-md-3"><?php echo 'University Name PHD'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "un_name_php",
                                "name" => "un_name_php",
                                "class" => "form-control",
                                "value" => $model_info->faculty2,
                                "placeholder" => 'University Name PHD',
                                "autocomplete" => "off",
                            ));
                            ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="un_name_php" class=" col-md-3"><?php echo 'Field of Study PHD'; ?></label>
                        <div class=" col-md-9">
                        <?php 
                        echo form_dropdown(array( 
                                'id'=> "un_name_php",
                                'name'=> "un_name_php",
                                'class' => "form-control select2",
                                'placeholder' => 'Field of Study PHD',
                            ),$field_of_study,[$model_info->education_field]); ?>
                        </div>
                    </div>
                </div> 

                <div class="form-group">
                    <div class="row">
                        <label for="graduation_date_phd" class=" col-md-3"><?php echo 'Graduation Date PHD'; ?></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_input(array(
                                "id" => "graduation_date_phd",
                                "name" => "graduation_date_phd",
                                "class" => "form-control date",
                                'value'=> $model_info->date_of_foculty,
                                "placeholder" => 'Graduation Date PHD',
                                "autocomplete" => "off"
                            ));
                            ?>
                        </div>
                    </div>
                </div>

            </div>

                       

                        <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 

                </div>

            <!---------------------------------- Job Info Tab ----------------------------->
                    
                <div role="tabpanel" class="tab-pane" id="job-info-tab">

                    <div class="mb-4">
                        <h4  class="text-muted">Job Infomation</h4>
                        <hr class="mt-0"/> 
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <label for="employee_type" class=" col-md-3"><?php echo 'Employee Type'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_dropdown(array(
                                    "id" => "employee_type",
                                    "name" => "employee_type",
                                    "class" => "form-control select2",
                                    "placeholder" => 'Employee Type',
                                    "autocomplete" => "off"
                                ),['Fixed'=>'Fixed','Temporary'=>'Temporary','Contract'=>'Contract']);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="department_id" class=" col-md-3"><?php echo 'Employee Department'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_dropdown(array(
                                    "id" => "department_id",
                                    "name" => "department_id",
                                    "class" => "form-control select2",
                                    "placeholder" => 'Employee Department',
                                    "autocomplete" => "off",
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ),$departments);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="section_id" class=" col-md-3"><?php echo 'Employee Section'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_dropdown(array(
                                    "id" => "section_id",
                                    "name" => "section_id",
                                    "class" => "form-control select2",
                                    "placeholder" => 'Employee Department',
                                    "autocomplete" => "off",
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ),$Sections);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="unit_id" class=" col-md-3"><?php echo 'Employee Unit'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_dropdown(array(
                                    "id" => "unit_id",
                                    "name" => "unit_id",
                                    "class" => "form-control select2",
                                    "placeholder" => 'Employee Unit',
                                    "autocomplete" => "off",
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ),$Units);
                                ?>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="row">
                            <label for="grade_id" class=" col-md-3"><?php echo 'Grade'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_dropdown(array(
                                    "id" => "grade_id",
                                    "name" => "grade_id",
                                    "class" => "form-control select2",
                                    "placeholder" => 'Grade',
                                    "autocomplete" => "off"
                                ),$grades);
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
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
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
                            <label for="job_description" class=" col-md-3"><?php echo app_lang('job_description'); ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_textarea(array(
                                    "id" => "job_description",
                                    "name" => "job_description",
                                    "class" => "form-control",
                                    "placeholder" => 'Job Descpription'
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="work_experience" class=" col-md-3"><?php echo 'Work Experience'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_textarea(array(
                                    "id" => "work_experience",
                                    "name" => "work_experience",
                                    "class" => "form-control",
                                    "placeholder" => 'Enter Work Experience'
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <label for="job_location" class=" col-md-3"><?php echo 'Job Location'; ?></label>
                            <div class=" col-md-9">
                            <?php 
                            echo form_dropdown(array( 
                                    'id'=> "job_location",
                                    'name'=> "job_location",
                                    'class' => "form-control select2",
                                    'placeholder' => 'Job Location',
                                    'autocomplete'=> "off",
                                    'data-rule-required' => true,
                                    'data-msg-required' =>   app_lang('field_required')
                                ),$job_locations); ?>
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
                                    "class" => "form-control",
                                    "placeholder" => app_lang('date_of_hire'),
                                    "autocomplete" => "off"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <label for="employee_id" class=" col-md-3"><?php echo 'Employee ID'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "employee_id",
                                    "name" => "employee_id",
                                    "class" => "form-control",
                                    "placeholder" => 'eg. Employee Number',
                                    "autocomplete" => "off"
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="row">
                            <label for="event_recurring" class=" col-md-3 col-xs-5 col-sm-4"><?php echo app_lang('has_signature'); ?></label>
                            <div class=" col-md-9 col-xs-7 col-sm-8">
                                <?php
                                echo form_checkbox("recurring", "1", "" ? true : false, "id='event_recurring' class='form-check-input'");
                                ?>                       
                            </div>

                            <div id="recurring_fields" class="<?php echo "hide"; ?>"> 

                                <div class="form-group">
                                    <div class="row">
                                        <label for="repeat_every" class=" col-md-3 col-xs-12"><?php  ?></label>
                                        <div class="col-md-4 col-xs-6">
                                        <button class="btn btn-default upload-file-button float-start me-auto btn-sm round" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?></button>
                                        <?php echo view("includes/dropzone_preview"); ?>
                                        </div>
                                        
                                    </div>
                                </div>    

                            </div>     
                        
                        </div>
                    </div>

                </div>
            
            <!---------------------------------- Bank Details Tab ----------------------------->
                    
                <div role="tabpanel" class="tab-pane" id="bank-details-tab">

                    <div class="mb-4">
                        <h4  class="text-muted">Bank Details</h4>
                        <hr class="mt-0"/> 
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="bank_name" class=" col-md-3"><?php echo 'Bank Name'; ?></label>
                            <div class=" col-md-9">
                            <?php
                            echo form_dropdown(array( 
                                        'id'=> "bank_id",
                                        'name'=> "bank_id",
                                        'class' => "form-control select2",
                                        'placeholder' => 'Bank Name',
                                        'autocomplete'=> "off",
                                    ),$bank_names_dropdown); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="bank_account" class=" col-md-3"><?php echo 'Bank Account'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "bank_account",
                                    "name" => "bank_account",
                                    "class" => "form-control",
                                    "placeholder" => 'Bank Account',
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="bank_registered_name" class=" col-md-3"><?php echo 'Bank Registered Name'; ?></label>
                            <div class=" col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "bank_registered_name",
                                    "name" => "bank_registered_name",
                                    "class" => "form-control",
                                    "placeholder" => 'Bank Registered Name',
                                ));
                                ?>
                            </div>
                        </div>
                    </div>

                </div>

            <!---------------------------------- Account Settings Tab ----------------------------->

                <div role="tabpanel" class="tab-pane" id="account-info-tab">
                                
                    <div class="mb-4">
                            <h4  class="text-muted">Account Details</h4>
                            <hr class="mt-0"/> 
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <label for="email" class=" col-md-3"><?php echo app_lang('email').' (Will Login with this)'; ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "email",
                                        "name" => "email",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('email').': Microsoft 365 azure email',
                                        "autocomplete" => "off",
                                        "data-rule-email" => true,
                                        "data-msg-email" => app_lang("enter_valid_email"),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="private_email" class=" col-md-3"><?php echo app_lang('private_email'); ?></label>
                                <div class=" col-md-9">
                                    <?php
                                    echo form_input(array(
                                        "id" => "private_email",
                                        "name" => "private_email",
                                        "class" => "form-control",
                                        "placeholder" => app_lang('email').': your private email',
                                        "autocomplete" => "off",
                                        "data-rule-email" => true,
                                        "data-msg-email" => app_lang("enter_valid_email"),
                                        "data-rule-required" => true,
                                        "data-msg-required" => app_lang("field_required"),
                                    ));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="display: none">
                            <div class="row">
                                <label for="password" class="col-md-3"><?php echo app_lang('password'); ?></label>
                                <div class=" col-md-8">
                                    <div class="input-group">
                                        <?php
                                        echo form_password(array(
                                            "id" => "password",
                                            "name" => "password",
                                            "class" => "form-control",
                                            "placeholder" => app_lang('password'),
                                            "autocomplete" => "off",
                                            "data-rule-required" => true,
                                            "data-msg-required" => app_lang("field_required"),
                                            "data-rule-minlength" => 6,
                                            "data-msg-minlength" => app_lang("enter_minimum_6_characters"),
                                            "autocomplete" => "off",
                                            "style" => "z-index:auto;"
                                        ),'aleelo',['style' => 'display:none;']);
                                        ?>
                                        <button type="button" class="input-group-text clickable no-border" id="generate_password"><span data-feather="key" class="icon-16"></span> <?php echo app_lang('generate'); ?></button>
                                    </div>
                                </div>
                                <div class="col-md-1 p0">
                                    <a href="#" id="show_hide_password" class="btn btn-default" title="<?php echo app_lang('show_text'); ?>"><span data-feather="eye" class="icon-16"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label for="role" class="col-md-3"><?php echo app_lang('role'); ?></label>
                                <div class="col-md-9">
                                    <?php
                                    echo form_dropdown("role", $role_dropdown, array(), "class='select2' id='user-role'");
                                    ?>
                                    <div id="user-role-help-block" class="help-block ml10 hide"><i data-feather="alert-triangle" class="icon-16 text-warning"></i> <?php echo app_lang("admin_user_has_all_power"); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group " style="display: none;">
                            <div class="col-md-12">  
                                <?php
                                echo form_checkbox("email_login_details", "1", false, "id='email_login_details' class='form-check-input' style='display:none;'");
                                ?> <label for="email_login_details"><?php echo app_lang('email_login_details'); ?></label>
                            </div>
                        </div>
                </div>

            </div>
                
        </div>
    </div>


    <div class="modal-footer">
        <button class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button id="form-previous" type="button" class="btn btn-default hide"><span data-feather="arrow-left-circle" class="icon-16"></span> <?php echo app_lang('previous'); ?></button>
        <button id="form-next" type="button" class="btn btn-info text-white"><span data-feather="arrow-right-circle" class="icon-16"></span> <?php echo app_lang('next'); ?></button>
        <button id="form-submit" type="button" class="btn btn-primary hide"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {

        var uploadUrl = "<?php echo get_uri("team_members/upload_file"); ?>";
        var validationUri = "<?php echo get_uri("team_members/validate_team_file"); ?>";
        var dropzone = attachDropzoneWithForm("#team-dropzone", uploadUrl, validationUri);
       
        $("#team_member-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    $("#team_member-table").appTable({newData: result.data, dataId: result.id});
                }
            },
            onSubmit: function () {
                $("#form-previous").attr('disabled', 'disabled');
            },
            onAjaxSuccess: function () {
                $("#form-previous").removeAttr('disabled');
            }
        });

        $("#team_member-form input").keydown(function (e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                if ($('#form-submit').hasClass('hide')) {
                    $("#form-next").trigger('click');
                } else {
                    $("#team_member-form").trigger('submit');
                }
            }
        });

        // setTimeout(function () {
        //     $("#first_name").focus();
        // }, 200);

        $("#team_member-form .select2").select2();

        setDatePicker("#date_of_hire");
        setDatePicker(".date_input");

        function switchTab(activeTab, nextTab, progressBar, labelIcon, percentage) {
            activeTab.removeClass("active");
            nextTab.addClass("active");
            $("#form-progress-bar").width(percentage);
            labelIcon.find("svg").remove();
            labelIcon.prepend('<i data-feather="check-circle" class="icon-16"></i>');
            feather.replace();
        }

        $("#form-next").click(function () {

        var $generalTab = $("#general-info-tab"),
            $educationTab = $("#education-info-tab"),
            $jobTab = $("#job-info-tab"),
            $bankTab = $("#bank-details-tab"),
            $accountTab = $("#account-info-tab"),

            $previousButton = $("#form-previous"),
            $nextButton = $("#form-next"),
            $submitButton = $("#form-submit");

        if (!$("#team_member-form").valid()) {
            return false;
        }

        if ($generalTab.hasClass("active")) {
            // Move to Education tab
            $generalTab.removeClass("active");
            $educationTab.addClass("active");
            $previousButton.removeClass("hide");

            $("#form-progress-bar").width("20%"); // 20% for the first tab completed
            $("#general-info-label").find("svg").remove();
            $("#general-info-label").prepend('<i data-feather="check-circle" class="icon-16"></i>');
            feather.replace();

        } else if ($educationTab.hasClass("active")) {
            // Move to Job Info tab
            $educationTab.removeClass("active");
            $jobTab.addClass("active");

            $("#form-progress-bar").width("40%"); // 40% after the second tab
            $("#education-info-label").find("svg").remove();
            $("#education-info-label").prepend('<i data-feather="check-circle" class="icon-16"></i>');
            feather.replace();

        } else if ($jobTab.hasClass("active")) {
            // Move to Bank Details tab
            $jobTab.removeClass("active");
            $bankTab.addClass("active");

            $("#form-progress-bar").width("60%"); // 60% after the third tab
            $("#job-info-label").find("svg").remove();
            $("#job-info-label").prepend('<i data-feather="check-circle" class="icon-16"></i>');
            feather.replace();

        } else if ($bankTab.hasClass("active")) {
            // Move to Account Settings tab
            $bankTab.removeClass("active");
            $accountTab.addClass("active");

            $nextButton.addClass("hide");
            $submitButton.removeClass("hide");

            $("#form-progress-bar").width("80%"); // 80% after the fourth tab
            $("#bank-details-label").find("svg").remove();
            $("#bank-details-label").prepend('<i data-feather="check-circle" class="icon-16"></i>');
            feather.replace();

        } else if ($accountTab.hasClass("active")) {
            // All tabs completed
            $("#form-progress-bar").width("100%"); // 100% after the last tab
            $("#account-info-label").find("svg").remove();
            $("#account-info-label").prepend('<i data-feather="check-circle" class="icon-16"></i>');
            feather.replace();
        }
        });

        $("#form-previous").click(function () {

        var $generalTab = $("#general-info-tab"),
            $educationTab = $("#education-info-tab"),
            $jobTab = $("#job-info-tab"),
            $bankTab = $("#bank-details-tab"),
            $accountTab = $("#account-info-tab"),

            $previousButton = $("#form-previous"),
            $nextButton = $("#form-next"),
            $submitButton = $("#form-submit");

        if ($accountTab.hasClass("active")) {
            // Move back to Bank Details tab
            $accountTab.removeClass("active");
            $bankTab.addClass("active");

            $("#form-progress-bar").width("60%"); // Back to 60%
            $nextButton.removeClass("hide");
            $submitButton.addClass("hide");

        } else if ($bankTab.hasClass("active")) {
            // Move back to Job Info tab
            $bankTab.removeClass("active");
            $jobTab.addClass("active");

            $("#form-progress-bar").width("40%"); // Back to 40%

        } else if ($jobTab.hasClass("active")) {
            // Move back to Education Info tab
            $jobTab.removeClass("active");
            $educationTab.addClass("active");

            $("#form-progress-bar").width("20%"); // Back to 20%

        } else if ($educationTab.hasClass("active")) {
            // Move back to General Info tab
            $educationTab.removeClass("active");
            $generalTab.addClass("active");

            $("#form-progress-bar").width("0%"); // Back to 0%
            $previousButton.addClass("hide");
        }
        });


        $("#form-submit").click(function () {
            $("#team_member-form").trigger('submit');
        });

        $("#generate_password").click(function () {
            $("#password").val(getRndomString(8));
        });

        $("#show_hide_password").click(function () {
            var $target = $("#password"),
                    type = $target.attr("type");
            if (type === "password") {
                $(this).attr("title", "<?php echo app_lang("hide_text"); ?>");
                $(this).html("<span data-feather='eye-off' class='icon-16'></span>");
                feather.replace();
                $target.attr("type", "text");
            } else if (type === "text") {
                $(this).attr("title", "<?php echo app_lang("show_text"); ?>");
                $(this).html("<span data-feather='eye' class='icon-16'></span>");
                feather.replace();
                $target.attr("type", "password");
            }
        });

        $("#user-role").change(function () {
            if ($(this).val() === "admin") {
                $("#user-role-help-block").removeClass("hide");
            } else {
                $("#user-role-help-block").addClass("hide");
            }
        });

        $("#email_login_details").click(function () {
            if ($(this).is(":checked")) {
                $("#password").attr("data-rule-required", true);
                $("#password").attr("data-msg-required", "<?php echo app_lang("field_required"); ?>");
            } else {
                $("#password").removeAttr("data-rule-required");
                $("#password").removeAttr("data-msg-required");
            }
        });

        setDatePicker("#primary_graduation_date");
        setDatePicker("#secondary_graduation_date");
        setDatePicker("#graduation_date_diploma");
        setDatePicker("#graduation_date_foculty_1");
        setDatePicker("#graduation_date_foculty_2");
        setDatePicker("#graduation_date_master_1");
        setDatePicker("#graduation_date_master_2");

        // Initially hide all sections
        function hideAllSections() {
            $('#primary_school_section').hide();
            $('#secondary_school_section').hide();
            $('#diploma_section').hide();
            $('#foculty_1_section').hide();
            $('#foculty_2_section').hide();
            $('#master_1_section').hide();
            $('#master_2_section').hide();
            $('#php_section').hide();
        }

    // Call this function whenever the education level changes
    $('#education_level').on('change', function () {
        var educationLevel = $(this).val();

        // Hide all sections first
        hideAllSections();

        // Show the appropriate section(s) based on the selected education level
        switch (educationLevel) {
            case 'Primary':
                $('#primary_school_section').show();
                break;
            case 'Secondary':
                $('#secondary_school_section').show();
                break;
            case 'Diploma':
                $('#diploma_section').show();
                break;
            case 'Bachelor':
                $('#foculty_1_section').show();
                break;
            case 'Bachelor & Master':
                $('#foculty_1_section').show();
                $('#master_1_section').show();
                break;
            case '2 Bachelors':
                $('#foculty_1_section').show();
                $('#foculty_2_section').show();
                break;
            case '2 Bachelors & Master':
                $('#foculty_1_section').show();
                $('#foculty_2_section').show();
                $('#master_1_section').show();
                break;
            case '2 Bachelors & 2 Masters':
                $('#foculty_1_section').show();
                $('#foculty_2_section').show();
                $('#master_1_section').show();
                $('#master_2_section').show();
                break;
            case 'Doctor':
                $('#php_section').show();
                break;
            default:
                hideAllSections(); // If no valid selection is made, hide all sections
        }
    });

    // Trigger change on page load to set the correct visibility based on any pre-selected value
    $('#education_level').trigger('change');
    });
</script>