<?php echo form_open(get_uri("visitors/save"), array("id" => "lead-form", "class" => "general-form", "role" => "form")); ?>
<div id="leaves-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <?php echo view("visitors/visitor_form_fields"); ?>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    
    var k = 1;
    $(document).ready(function () {
        //hide by default
        $('#add_visitors_table').hide();

        // add visitor table
        $('#add_visitor_btn').on('click', function(){

            $('#add_visitors_table').show();

            //upload button
            var actions = "<div class='d-inline m-2' data-bs-toggle='tooltip' title='<?php echo app_lang("upload"); ?> Image' data-placement='right'>"+
               "<input type='file' name='visitor_image_file_"+ k +"' id='visitor_image_file_upload_"+ k +"' class='no-outline hidden-input-file upload' onChange='$(this).next().show();'>" +
               "<span style='position: absolute;margin-left: 15px;font-weight: bold;margin-top: -3px;border: solid 1px lightseagreen;border-radius: 50px;padding: 1px;width: 8px;height: 8px;background: lightseagreen;display:none;' class='file-indicator'></span>"+
                "<label for='visitor_image_file_upload_"+ k +"' class='clickable'>"+
                    "<span class='btn btn-primary btn-sm ml2 round mt-2'><i data-feather='camera' class='icon-16'></i></span>"+
                "</label>"+
            "</div>";

            //remove button
            actions += "<button type='button' class='btn btn-danger btn-sm mt-2  round ml-2 p-1 ' onclick='$(this).parent().parent().remove();k--;'><i data-feather='minus-circle' class='icon'></i></button>";

            $('#add_visitors_table tbody').append(
                "<tr class=''>"+
                "<td>" + k + "</td>"+
                    "<td><input type='text' class='form-control' data-rule-required data-msg-required='This field is required.' id='visitor_name_" + k + "' placeholder='Visitor Name' name='visitor_name[]'></td>"+
                    "<td><input type='text' class='form-control'  id='visitor_mobile_" + k + "' placeholder='Visitor Mobile'  name='visitor_mobile[]'></td>"+
                    "<td><input type='text' class='form-control'  id='vehicle_details_" + k + "' placeholder='Vehicle Details'  name='vehicle_details[]'></td>"+
                    "<td style='width: 110px;'>" + actions + "</td>"+
                "</tr>"
            );

          

            feather.replace();

            // $('.upload').on('change', function(){
            //     $('#file-indicator_'+k).show();
            // });

            k = k+1;
        });

        
        $('.modal-dialog').removeClass('modal-lg').addClass('modal-xl');

        setDatePicker(".date");
        
        setTimePicker(".time");

        
        

        //read details for visitor:
        
        if($('#id').val() != ''){
            $.ajax({
                url: 'visitors/visitor_details_json/'+$('#id').val(),
                cache: false,
                type: 'GET',
                success: function (data) {

                    $('#add_visitors_table').show();
                    $('#add_visitors_table tbody').html('');
                    data = JSON.parse(data);
                    console.log(data.length);

                    if(data.length > 0 && data[0].visitor_name != null){
                        for(let i=0;i< data.length;i++){
                            $('#add_visitors_table tbody').append(
                                "<tr class=''>"+
                                "<td>" + k + "</td>"+
                                    "<td><input type='text' class='form-control' value='" + data[i].visitor_name + "' data-rule-required data-msg-required='This field is required.' id='visitor_name_" + k + "' placeholder='Visitor Name' name='visitor_name[]'></td>"+
                                    "<td><input type='text' class='form-control' value='" + data[i].mobile + "' data-rule-required data-msg-required='This field is required.' id='visitor_mobile_" + k + "' placeholder='Visitor Mobile'  name='visitor_mobile[]'></td>"+
                                    "<td><input type='text' class='form-control' value='" + data[i].vehicle_details + "' data-rule-required data-msg-required='This field is required.' id='vehicle_details_" + k + "' placeholder='Vehicle Details'  name='vehicle_details[]'></td>"+
                                    "<td style='width: 110px;'><button type='button' class='btn btn-danger btn-sm mt-2 float-end' onclick='$(this).parent().parent().remove();k--;'><i data-feather='minus-circle' class='icon'></i> Remove</button></td>"+
                                "</tr>"
                            );
                            k = k+1;
                        }
                    }

                    feather.replace();
                },
                statusCode: {
                    403: function () {
                        console.log("403: Session expired.");
                        window.location.reload();
                    },
                    404: function () {
                        appLoader.hide();
                        appAlert.error("404: Page not found.");
                    }
                },
                error: function () {
                    appLoader.hide();
                    appAlert.error("500: Internal Server Error.");
                }
            });
        }
        
        $("#lead-form").appForm({
            onSuccess: function (result) {
                if (result.view === "details") {
                    appAlert.success(result.message, {duration: 10000});

                    setTimeout(function () {
                       
                        window.location.reload();

                    }, 500);
                } else {
                    appAlert.success(result.message, {duration: 10000});

                        if(result.webUrl != null) {
                            let newTab = window.open();
                            newTab.location.target = '_blank';
                            newTab.location.href = result.webUrl;
                        }
                        
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);

                    $("#lead-table").appTable({newData: result.data, dataId: result.id});
                    
                }
            }
        });
        setTimeout(function () {
            $("#company_name").focus();
        }, 200);
    });
</script>    