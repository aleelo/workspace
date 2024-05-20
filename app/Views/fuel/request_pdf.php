<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Ogolaanshaha soo gelista</title>
        <?php 
        
    $domain = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    $domain = preg_replace('/index.php.*/', '', $domain);
    $domain = strtolower($domain);
    if(str_contains($domain,'localhost')) {
        $prefix = '/rise/';
        $domain = 'http://' . $domain;
    }else{
        $prefix = '';
        $domain = 'https://' . $domain;
    }

    // die($domain);
    $css_files = array(
        // "/rise/assets/bootstrap/css/bootstrap.min.css",
        "assets/js/select2/select2.css", //don't combine this css because of the images path
        "assets/js/select2/select2-bootstrap.min.css",
        "assets/css/app.all.css",
    );

    array_push($css_files, "assets/css/custom-style.css"); //add to last. custom style should not be merged


    echo "<style type='text/css'>";
    foreach ($css_files as $uri) {
        echo file_get_contents($domain . $uri);
        
    }
    
    echo "</style>";
        ?>
    </head>
    <body style="background: #fff !important;">

    <div class=" d-flex justify-content-center">
        <div class="card col-md-5 col-xs-12 mt-3 shadow-lg">
            <div class="" style="text-align: center">
                    <?php 

                        $path = $domain.'assets/images/ict_header.png';
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $header = 'data:image/' . $type . ';base64,' . base64_encode($data);

                        if ($model_info->status === "pending") {
                            $status_class = "bg-warning";
                        } else if ($model_info->status === "approved") {
                            $status_class = "badge bg-success";//btn-success
                        } else if ($model_info->status === "dispensed") {
                            $status_class = "badge btn-success";//btn-success
                        } else if ($model_info->status === "cancelled") {
                            $status_class = "bg-dark";//btn-success
                        
                        } else if ($model_info->status === "rejected") {
                            $status_class = "bg-danger";
                        } else {
                            $status_class = "bg-dark";
                        }
            
                        $status_meta = "<span style='border-radius:5px;color:white;font-size:12px;font-weight: 600' class='badge $status_class'>" . app_lang($model_info->status) . "</span>";

                        ?>
                    <!-- <img src="<?php echo $header?>"  style="width:100%;"> -->
             </div>
            <div class="card-title text-center border-bottom mt-3 text-info"><h4 class="fw-bold">Request Details </h4></div>
              
            <div class="modal-body">
                <div class="row">
              
                    <div class="table-responsive mb15">

                        <table class="table dataTable display b-t " style="width:100%;">
                            <tr>
                                <td class=""> <?php echo app_lang('request_type'); ?></td>
                                <td><?php echo $model_info->request_type; ?></td>
                            </tr>
                            <tr>
                                <td class=""> <?php echo app_lang('fuel_type'); ?></td>
                                <td><?php echo $model_info->fuel_type; ?></td>
                            </tr>
                            <tr>
                                <td> <?php echo app_lang('request_date'); ?></td>
                                <td><?php echo date_format(new DateTime($model_info->request_date),'F d, Y'); ?></td>
                            </tr>
                            <tr>
                                <td> <?php echo app_lang('litters'); ?></td>
                                <td><?php echo $model_info->litters; ?></td>
                            </tr>
                        
                            <tr>
                                <td> <?php echo app_lang('purpose'); ?></td>
                                <td><?php echo $model_info->purpose; ?></td>
                            </tr>
                        
                            <tr>
                                <td> <?php echo app_lang('requested_by'); ?></td>
                                <td><?php
                                    echo $model_info->user;
                                    ?>
                                </td>
                            </tr>
                            
                            <tr>
                                <td> <?php echo app_lang('vehicle_engine'); ?></td>
                                <td><?php echo $model_info->vehicle_engine; ?></td>
                            </tr>
                            <tr>
                                <td> <?php echo app_lang('plate'); ?></td>
                                <td><?php echo $model_info->plate; ?></td>
                            </tr>
                            <tr>
                                <td> <?php echo app_lang('depertment'); ?></td>
                                <td><?php echo $model_info->department; ?></td>
                            </tr>
                            <tr>
                                <td> <?php echo app_lang('status'); ?></td>
                                <td><?php echo  $status_meta; ?></td>
                            </tr>
                            <tr>
                                <td> <?php echo app_lang('remarks'); ?></td>
                                <td><?php echo $model_info->remarks; ?></td>
                            </tr>
                        </table>
                        <div class="text-center mb-4" style="margin-top: 80px;">
                            <img src="<?php echo $qrcode?>"  style="width:200px;">                         
                        </div>
                     
                    </div>

                </div>
            </div>
        </div>
    </div>      

<?php echo view("includes/footer"); ?>
    </body>
</html>