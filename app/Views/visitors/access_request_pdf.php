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
        // echo "<link rel='stylesheet' type='text/css' href='" . base_url($uri) . "?v=3' />";
    }
    // echo file_get_contents("http://localhost/rise/assets/bootstrap/css/bootstrap.min.css");
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

                        ?>
                    <img src="<?php echo $header?>"  style="width:100%;">
             </div>
            <div class="card-title text-center border-bottom mt-3 text-info"><h4 class="fw-bold">Ogolaanshaha soo gelista </h4></div>
              
            <div class="modal-body">
                <div class="row">
              
                    <div class="table-responsive mb15">
                        <div class="text-center mb-4">
                            <?php echo $qrcode;?>
                            <div style="margin-top: -15px; margin-bottom: 20px;">
                                <span class="text-info" style="font-size: 24px; font-weight: bold">#<?php echo $visitor_info->id; ?></span>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center mt-3 font-arial mb-3">     
                                <div class="d-flex justify-content-between mb-4" style="display:flex;justify-content: space-between;text-align: center;margin-left: 70px;">
                                    <span style="margin-right: 100px;"><b>Taariikhda:</b> <?php echo date_format(new DateTime($visitor_info->created_at),'Y-m-d');?></span>
                                    <span style=""><b>Waqtiga:</b> <?php echo date("h:i a",strtotime(date_format(new DateTime($visitor_info->start_date),'Y-m-d').' '.$visitor_info->visit_time)); ?></span>
                                </div>

                        </div>
                        <table class="table dataTable display b-t" style="margin-top: 20px;width:100%;">
                            <thead style="padding: 3px;margin: 0px;">
                                <tr style="padding: 3px;margin: 0px;">
                                    <th style="width:20px;padding: 3px;margin: 0px;">#</th>
                                    <th style="padding: 3px;margin: 0px;">Magaca</th>
                                    <th style="padding: 3px;margin: 0px;">Telefoon</th>
                                    <th style="padding: 3px;margin: 0px;">Xogta Gaadiidka</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;?>

                                <?php foreach ($visitor_details as $d){
                                    
                                    if($d->image){

                                        $file = @unserialize($d->image);
                                        $image = get_array_value($file,'file_name');
                                        // $image =$image;
                                        
                                        $path = $domain.'files/visitors/'.$image;
                                        $type = pathinfo($path, PATHINFO_EXTENSION);
                                        $data = file_get_contents($path);
                                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                    }else{
                                        $image = '';
                                    }
                                    ?>
                                    <tr style="vertical-align: top;">
                                        <td><?php echo $i; ?></td>
                                        <td >
                                        <?php if($image){?>
                                            <img  width="50" src="<?php echo $base64;?>" class="rounded" style="margin-right: 10px;" />
                                            <?php }?>
                                        <span  style="vertical-align: top;"><?php echo $d->visitor_name; ?></span></td>
                                        <td><?php echo $d->mobile;?></td>
                                        <td><?php echo $d->vehicle_details; ?></td>
                                    </tr>
                                <?php
                                    $i++; 
                                } ?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>      

<?php echo view("includes/footer"); ?>
    </body>
</html>