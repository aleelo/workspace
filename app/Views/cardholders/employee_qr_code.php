<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
        <style>
            .text-header{
                font-size: 18px;
                margin: 0;
                text-transform: capitalize;
            }
            .text-body{
                font-size: 22px;
                text-transform: capitalize;
            }
            .card-title{
                background-color: #4789d9;
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
            }
            .border-bottom{
                border-color: #c8d8e5 !important;
            }
            .b-t{
                border-color: #c8d8e5 !important;
            }
            body{
                overflow: auto;
            }
        </style>
    </head>
    <body>

    <div class=" d-flex justify-content-center">
        <div class="card col-12 mt-3 shadow-lg" style="width: 97%;border-top-left-radius: 8px;border-top-right-radius: 8px;background-color: #dcf4ff;">
        <?php  if($model_info){ ?>
       
            <div class="card-title text-center text-white"><h4 class="fw-bold"> <span class="text-white " style="text-transform: uppercase;"><?php echo $model_info->institution; ?></span> </h4></div>
                
            <div class="modal-body" style="padding: 15px">
                <div class="row">
                    <!-- `client_type`, `access_duration`, `image`, `name`, `created_by`, `visit_date`, `visit_time`, `created_at`, `deleted`, `remarks`, `status` -->
                    <div class="table-responsive mb15">
                    <?php 
                        if(file_exists(ROOTPATH.'files/IdImages/'.$model_info->uid.'.png')){
                            $url = get_uri('files/IdImages/'.$model_info->uid.'.png');
                        }else{
                            $uid = str_replace('-','',$model_info->uid);
                            $url = get_uri('files/IdImages/'.$uid.'.png');
                        }
                        
                        ?>

                    <div class="d-flex justify-content-center">
                        <div class="clearfix">
                                <div class=" text-center justify-content-center">
                                    <div class="flex-shrink-0">
                                        <span class="avatar" style="width: 165px;height: 225px;">
                                            <img src="<?php echo $url; ?>" alt="..."  style="border-radius: 15px !important; margin-bottom: 15px;"/>
                                        </span>
                                    </div>
                                    <!-- <div class="ps-2 pt5">
                                        <div class="m0">
                                            <?php //echo $model_info->fullName; ?>
                                        </div>
                                        <p><span class='badge bg-primary'><?php //echo $model_info->titleSom; ?></span> </p>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="table dataTable display mt-3">
                           <div class="row border-bottom b-t mb-3 mt-2">
                                <h4 class="text-header mt-3"> Magaca/Name:</h4>
                                <p class="text-body"><?php echo $model_info->fullName; ?></p>
                            </div>
                            
                            <div class="row border-bottom mb-3">
                                <h4 class="text-header"> Xilka/Designation</h4>
                                <p class="text-body"><?php echo ucwords(strtolower($model_info->titleSom)); ?></p>
                            </div>
                        
                            <div class="row border-bottom mb-3">
                                <h4 class="text-header"> Xafiiska/Office </h4>
                                <p class="text-body"><?php echo ucwords(strtolower($model_info->office)); ?></p>
                            </div>
                                                 
                        </div>
                    </div>


                </div>
            </div>
            <?php  }else{ ?>
                <h3 class="text-center p-5">Not Data Found.</h3>
            <?php  } ?>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function () {

        $('#js-init-chat-icon').hide();

        });
        
    </script>    

<?php echo view("includes/footer"); ?>
    </body>
</html>