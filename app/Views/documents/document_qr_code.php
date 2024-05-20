<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo view('includes/head'); ?>
    </head>
    <body>

    <div class=" d-flex justify-content-center">
        <div class="card col-md-5 col-xs-12 mt-3 shadow-lg">
            <div class="card-title text-center"><h4 class="fw-bold">Document Information #<?php echo $document->id; ?></h4></div>
                
            <div class="modal-body">
                <div class="row">
                    <!-- `client_type`, `access_duration`, `image`, `name`, `created_by`, `visit_date`, `visit_time`, `created_at`, `deleted`, `remarks`, `status` -->
                    <div class="table-responsive mb15">
                        <table class="table dataTable display b-t">
                            <tr>
                                <th class=""> <?php echo app_lang('document_title'); ?></th>
                                <td><?php echo $document->document_title; ?></td>
                            </tr>
                            <tr>
                                <th class=""> <?php echo app_lang('ref_number'); ?></th>
                                <td><?php echo $document->ref_number; ?></td>
                            </tr>
                            <tr>
                                <th class=""> <?php echo app_lang('created_at'); ?></th>
                                <td><?php echo $document->created_at; ?></td>
                            </tr>
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
    </body>
</html>