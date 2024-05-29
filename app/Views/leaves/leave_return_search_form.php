
    <div class=" search-container" style="margin-left:auto;margin-right: auto;" id="search-container">

        <?php if(!empty($leave_info)){ ?>
        <div class="" >            
        <div class="container">
            <div class="ticket-header"  style="padding: 3px !important;background-color: #3a3835 !important;">
                <img id="logo" src="<?php echo base_url().'assets/images/logo-emblem.png';?>" style="width: 90px;">
                        &nbsp;&nbsp;
                    <h2>Ministry of Finance Somalia</h2>
                </div>
                <!-- <div class="ps">
                    <h2>Somalia Revenue Directorate</h2>
                </div> -->
                <div class="ticket-body">
                    <div class="ticket-name">
                    <h2 class="text-center mb-3"><?php echo strtoupper($leave_info->leave_type) ?></h2>
                    </div>
                    <hr class="ruler" />

                    <div class="circle-container">
                        <img src="<?php echo get_avatar($leave_info->applicant_avatar); ?>" alt="..." />
                    </div>

                    <div class="ter-gat-set">
                    <div style="">
                        <p class="fw-semibold"><?php echo $leave_info->applicant_name;?></p>
                        <h2 style="font-size: 12px;"><?php echo $leave_info->job_title; ?></h2>
                    </div>
                    </div>
                    
                    <hr class="ruler" />
                    <div class="ticket-number-date">
                    <div>
                        <p class="fw-semibold">passport No</p>
                        <h3><?php echo $leave_info->passport_no;?></h3>
                    </div>
                    <div>
                        <p class="fw-semibold">DATE</p>
                        <h3><?php echo date_format(new DateTime(date('Y-m-d')),'d M, Y');?></h3>
                    </div>
                    </div>

                    <hr class="ruler" />

                    <div class="bording">
                    <div class="bording-content">
                        <p class="fw-semibold">Leave Date</p>
                        <h2 style="font-size: 14px;"><?php echo date_format(new DateTime($leave_info->start_date),'F d, Y').' - '.date_format(new DateTime($leave_info->end_date),'F d, Y');?></h2>
                    </div>
                    </div>
                    <div class="qrcode">
                    <?php
                                    
                        $options = new chillerlan\QRCode\QROptions([
                            'eccLevel' => chillerlan\QRCode\Common\EccLevel::L,
                            'outputBase64' => true,
                            // 'cachefile' => APPPATH . 'Views/documents/qrcode.png',
                            // 'outputType'=>QROutputInterface::GDIMAGE_PNG,
                            'logoSpaceHeight' => 17,
                            'logoSpaceWidth' => 17,
                            'scale' => 20,
                            'version' => chillerlan\QRCode\Common\Version::AUTO,

                        ]);
                        echo "<img style='border-radius: 7px;border: 1px solid #603007;' width='150' src=". (new chillerlan\QRCode\QRCode($options))->render(get_uri('visitors_info/show_leave_qrcode/'.$leave_info->uuid))." alt='Scan to see' />";?>
                
                    </div>
                </div>
            </div>
            
        </div>
        <?php }else{ ?>
            <div class=" d-flex justify-content-center col-xs-12 mt-3 mb-3 ">
                <p class="p10 m10 fs-3">No result to show</p>
            </div>
            <?php } ?>
    </div>
