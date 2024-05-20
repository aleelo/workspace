<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
    </head>
    <body>

    <div class=" d-flex justify-content-center">
        <div class="card col-md-5 col-xs-12 mt-3 shadow-lg">
        <div class="" style="text-align: center">
                    <?php 

                        $path = get_uri('assets/images/ict_header.png');
                       
                        ?>
                    <img src="<?php echo $path?>" style="width:100%;">
             </div>
            <div class="card-title text-center mt-3 text-info"><h4 class="fw-bold">Xogta Ogolaashiyaha soo gelista #<?php echo $visitor_info->id; ?></h4></div>
                
            <div class="modal-body">
                <div class="row">
                    <!-- `client_type`, `access_duration`, `image`, `name`, `created_by`, `visit_date`, `visit_time`, `created_at`, `deleted`, `remarks`, `status` -->
                    <div class="table-responsive mb15">
                        <table class="table dataTable display b-t">
                            
                            <?php if($visitor_info->access_duration == 'multiple_days'){ ?>
                            <tr>
                                <th> <?php echo 'Taariikh Bilaaw'; ?></th>
                                <td><?php echo $visitor_info->start_date; ?></td>
                            </tr>
                            <tr>
                                <th> <?php echo 'Taariikh Dhamaad'; ?></th>
                                <td><?php echo $visitor_info->end_date; ?></td>
                            </tr>
                            <?php }else{?>
                                <tr>
                                    <th> <?php echo 'Taarikhda'; ?></th>
                                    <td><?php echo date("F d, Y",strtotime(date_format(new DateTime($visitor_info->start_date),'Y-m-d'))); ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th> <?php echo 'Waqtiga'; ?></th>
                                <td><?php echo date("h:i a",strtotime(date_format(new DateTime($visitor_info->start_date),'Y-m-d').' '.$visitor_info->visit_time)); ?></td>
                            </tr>

                        </table>
                    </div>

                    <div class="table-responsive mb15">
                        <h3 class="text-info m-3 mb-1 text-center mb-4">Xogta xubnaha Soo Deynta</h3>
                        <div class="d-flex flex-column justify-content-center mt-3 font-arial mb-3">
                            <div class=" col-9 mx-auto" style="font-size: 16px;letter-spacing: 0.8px;line-height: 1.8;">
                                    
                                <!-- <div class="d-flex justify-content-between mb-4">
                                    <span class=""><b>Tix:</b> <?php //echo $visitor_info->ref_number?></span>
                                    <span class=""><b>Date:</b> <?php //echo date_format(new DateTime($visitor_info->created_at),'Y-m-d');?></span>
                                </div> -->
                                
                            </div>
                        </div>
                        <table class="table dataTable display b-t">
                            <thead>
                                <tr>
                                    <th style="width:20px">#</th>
                                    <th>Magaca</th>
                                    <th>Telefoon</th>
                                    <th>Xogta Gaadiidka</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;?>

                                <?php foreach ($visitor_details as $d){?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><img width="50" src="<?php echo get_visitor_avatar($d->image);?>" class="rounded" style="margin-right: 10px;" /><span><?php echo $d->visitor_name; ?></span></td>
                                        <td><?php echo $d->mobile; ?></td>
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


    <script type="text/javascript">
        $(document).ready(function () {

        $('#js-init-chat-icon').hide();

        });
        
    </script>    

<?php echo view("includes/footer"); ?>
    </body>
</html>