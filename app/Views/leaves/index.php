<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card">
        <div class="page-title clearfix leaves-page-title">
            <h1><?php echo app_lang('leaves'); ?></h1>
            <div class="title-button-group">
                <?php
                if ($can_manage_all_leaves) {
                    echo modal_anchor(get_uri("leaves/import_leaves_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_leaves'), array("class" => "btn btn-default", "title" => app_lang('import_leaves')));
                }
                ?>
                <?php echo modal_anchor(get_uri("leaves/apply_leave_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('apply_leave'), array("class" => "btn btn-default", "title" => app_lang('apply_leave'))); ?>

                <?php  echo $can_assign_leaves == true ? modal_anchor(get_uri("leaves/assign_leave_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('assign_leave'), array("class" => "btn btn-default", "title" => app_lang('assign_leave'))) : '' ;?>
            </div>
        </div>
        <ul id="leaves-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white inner" role="tablist">
            <li>
                <a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/active_list/"); ?>" data-bs-target="#leave-active-applications">
                <span class="badge " style="background-color: #a7abbf" title="Newly applied leaves">
                Active</span> 
                <!-- <?php //echo app_lang("active"); ?> -->
            </a>
            </li>
            
            <li>
                <a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/pending_list/"); ?>" data-bs-target="#leave-pending-applications">
                <span class="badge bg-warning" title="Leaves approved by Director">Pending</span> 
                <!-- <?php //echo app_lang("pending"); ?> -->
            </a>
            </li>
            <!-- <li><a role="presentation" data-bs-toggle="tab" href="<?php //echo_uri("leaves/pending_approval/"); ?>" data-bs-target="#leave-pending-approval"><?php //echo app_lang("pending_approval"); ?></a></li> -->
            <li>
                <a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/approved_list/"); ?>" data-bs-target="#leave-approved-applications">
                <span class="badge bg-success" title="Leaves approved by HRM">Approved</span> 
                </a>
            </li>
            <li>
                <a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/rejected_list/"); ?>" data-bs-target="#leave-rejected-applications">
                <span class="badge bg-danger" title="Leaves rejected by HRM or Director">Rejected</span>
                </a>
            </li>
            <li>
                <a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/canceled_list/"); ?>" data-bs-target="#leave-canceled-applications">
                    <span class="badge bg-dark" title="Leaves cancelled by user">Cancelled</span> 
                </a>
            </li>
            <li>
                <a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/all_applications/"); ?>" data-bs-target="#leave-all-applications">
                <spanspan class="badge bg-info" title="All Applications">All Applications</span>
                </a>
            </li>
            <!-- <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/summary/"); ?>" data-bs-target="#leave-summary"><?php echo app_lang("summary"); ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/leave_nolosto_search/"); ?>" data-bs-target="#leave-nolo"><?php echo 'NOLO OSTO'; ?></a></li>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leaves/leave_return_search/"); ?>" data-bs-target="#leave-return"><?php echo 'PASSPORT CELIN'; ?></a></li> -->
            
            <!-- <li class="d-flex align-items-center gap-4 ml30">
                <span><i data-feather='info' class='icon-16 text-info'></i>  Status Description: </span> 
                <span class="badge " style="background-color: #a7abbf" title="Newly applied leaves">Active</span> 
                <span class="badge bg-warning" title="Leaves approved by Director">Pending</span> 
                <span class="badge bg-success" title="Leaves approved by HRM">Approved</span> 
                <span class="badge bg-dark" title="Leaves cancelled by user">Cancelled</span> 
                <span class="badge bg-danger" title="Leaves rejected by HRM or Director">Rejected</span>
            </li> -->
            
        </ul>
        <div class="tab-content">
            <!-- <div role="tabpanel" class="tab-pane fade active" id="leave-pending-approval"></div> -->
            <div role="tabpanel" class="tab-pane fade active" id="leave-active-applications"></div>
            <div role="tabpanel" class="tab-pane fade active" id="leave-pending-applications"></div>
            <div role="tabpanel" class="tab-pane fade active" id="leave-approved-applications"></div>
            <div role="tabpanel" class="tab-pane fade active" id="leave-rejected-applications"></div>
            <div role="tabpanel" class="tab-pane fade active" id="leave-canceled-applications"></div>
            <div role="tabpanel" class="tab-pane fade" id="leave-all-applications"></div>
            <div role="tabpanel" class="tab-pane fade" id="leave-summary"></div>
            <div role="tabpanel" class="tab-pane fade" id="leave-nolo"></div>
            <div role="tabpanel" class="tab-pane fade" id="leave-return"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "all_applications") {
                $("[data-bs-target='#leave-all-applications']").trigger("click");
            }
        }, 210);
    });
</script>