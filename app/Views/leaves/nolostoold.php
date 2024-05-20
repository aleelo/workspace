<div class=" d-flex justify-content-center">
        <div class="card col-md-4 col-xs-12 mt-3 shadow-lg qrcode-style">
            <div class="card-title text-center">
                <h4 class="fw-bold">Leave Information #<?php echo $leave_info->id; ?></h4>
                <h4 class="fw-bold mt-1">NOLO OSTO</h4>
        </div>
                
            <div class="modal-body">
                <div class="row">
                    <div class="p10 clearfix">
                        <div class="d-flex  justify-content-center">
                            <div class="flex-shrink-0">
                                <span class="avatar avatar-sm">
                                    <img src="<?php echo get_avatar($leave_info->applicant_avatar); ?>" alt="..." />
                                </span>
                            </div>
                            <div class="ps-2 pt5">
                                <div class="m0">
                                    <?php echo $leave_info->applicant_name; ?>
                                </div>
                                <p><span class='badge bg-primary'><?php echo $leave_info->job_title; ?></span> </p>
                            </div>
                        </div>
                    </div>
                    <!-- `client_type`, `access_duration`, `image`, `name`, `created_by`, `visit_date`, `visit_time`, `created_at`, `deleted`, `remarks`, `status` -->
                    <div class="table-responsive mb15">
                        <table class="table dataTable display b-t">
                        <tr>
                            <th class=""> <?php echo app_lang('leave_type'); ?></th>
                            <td><?php echo $leave_info->leave_type; ?></td>
                        </tr>
                        
                        <tr>
                            <th> <?php echo app_lang('duration'); ?></th>
                            <td><?php echo $leave_info->duration.' days'; ?></td>
                        </tr>

                        <?php if($leave_info->duration == 1){ ?>
                        <tr>
                            <th> <?php echo app_lang('date'); ?></th>
                            <td><?php echo date_format(new DateTime($leave_info->start_date),'M d,Y'); ?></td>
                        </tr>
                        <?php }else{ ?>
                            <tr>
                                <th> <?php echo app_lang('start_date'); ?></th>
                                <td><?php echo date_format(new DateTime($leave_info->start_date),'M d,Y'); ?></td>
                            </tr>
                            <tr>
                                <th> <?php echo app_lang('end_date'); ?></th>
                                <td><?php echo date_format(new DateTime($leave_info->end_date),'M d,Y'); ?></td>
                            </tr>
                        <?php }?>
                        <tr>
                            <th> <?php echo app_lang('reason'); ?></th>
                            <td><?php echo nl2br($leave_info->reason ? $leave_info->reason : ""); ?></td>
                        </tr>
                        <tr>
                            <th> <?php echo app_lang('status'); ?></th>
                            <td><?php echo $leave_info->status; ?></td>
                        </tr>
                        <?php if ($leave_info->status === "rejected") { ?>
                            <tr>
                                <th> <?php echo app_lang('rejected_by'); ?></th>
                                <td><?php
                                    $image_url = get_avatar($leave_info->checker_avatar);
                                    echo "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span><span>" . $leave_info->checker_name . "</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($leave_info->status === "approved") { ?>
                            <tr>
                                <th> <?php echo app_lang('approved_by'); ?></th>
                                <td><?php
                                    $image_url = get_avatar($leave_info->checker_avatar);
                                    echo "<span class='avatar avatar-xs mr10'><img src='$image_url' alt=''></span><span>" . $leave_info->checker_name . "</span>";
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mb-3">
                        <?php
                        
                            $options = new chillerlan\QRCode\QROptions([
                                'eccLevel' => chillerlan\QRCode\Common\EccLevel::H,
                                'outputBase64' => true,
                                // 'cachefile' => APPPATH . 'Views/documents/qrcode.png',
                                // 'outputType'=>QROutputInterface::GDIMAGE_PNG,
                                'logoSpaceHeight' => 17,
                                'logoSpaceWidth' => 17,
                                'scale' => 20,
                                'version' => chillerlan\QRCode\Common\Version::AUTO,

                            ]);
                            echo "<img style='border-radius: 7px;border: 1px solid #1f8bf2;' width='150' src=". (new chillerlan\QRCode\QRCode($options))->render(get_uri('visitors_info/show_leave_qrcode/'.$leave_info->uuid))." alt='Scan to see' />";?>
                    </div>

                    <div class="d-flex justify-content-end mb-3 hprint">
                        <a class="btn btn-warning text-white mr10 hprint" href="<?php echo get_uri('visitors_info/show_leave_qrcode_return/'.$leave_info->uuid);?>">
                        <i data-feather='file' class='icon-16 '></i> Passport Celin</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

