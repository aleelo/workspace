
        <style>
          
            @media print {
            
                #default-navbar,#leaves-tabs,#leave-return-form,#js-init-chat-icon,.page-title,.sidebar {
                    display: none;
                }
                .print-area{
                    display: flex;
                }
               
                #search-card2{
                    width: 400px;
                    margin-left:auto;margin-right: auto;
                    margin-left: -150px;
                    background-color: #fcab73 !important;
                    color: white;
                }
            
                body,.page-wrapper,.sidebar-menu,.sidebar-scroll,.sidebar,#left-menu-toggle-mask{
                    background: #fff
                }
            }
           
            #searchTerm::placeholder {
                color: #a8aaae;
                /* color: #b2b3b4; */
                /* background-color: #fff; */
            }

            #search-container {
                width: 440px;
            }

            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

* {
    margin: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}
.main {
    background-color: rgba(128, 128, 128, 0.315);
}

.search-container .container {
    background-color: #e5e5e5;
    width: 440px;
    border-top-left-radius:  7px;
    border-top-right-radius:  7px;
    padding: 0px;
    color: #603007;
}

.search-container  .ticket-header {
    background-color: #f4a82d;
    color: white;
    border-top-left-radius:  7px;
    border-top-right-radius:  7px;
    text-align: center;
    padding: 15px 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-container  .ticket-header h2{
    font-size: 20px;
}

#logo {
    /* width: 45px; */
    /* border: 2px solid white; */
    /* border-radius: 100%; */
}

.search-container   .ticket-body {
    padding: 20px 15px;
}

.search-container   .ticket-name p {
    font-size: 0.9rem;
    color: #b55334;
    font-weight: 300;
}

.search-container   .ruler {
    margin: 1rem 0;
    height: 1px;
    background: #b36f00;
}

.search-container  .ticket-number-date {
    display: flex;
    justify-content: space-between;
    margin: 0 10%;
}

.search-container   .ticket-from-and-to {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* margin: 0 15%; */
    /* border-bottom: 2.5px dashed #20a3f6;
    padding-bottom: 20px; */
}

.plane-body {
    display: flex;
    align-items: center;
    justify-content:center;
    width: 250px;
}

.plane {
    width: 50px;
    margin-top: 15px;
    /* display: flex; */
}


.ter-gat-set {
    margin-top: 10px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    margin: 10px 0px;
    gap: 10px;
    text-align: center;
}

.ter-gat-set  h2,h4{

    font-size: 16px;
}

.bording {
    margin-top: 10px;
    display: flex;
    justify-content: center;
}

.search-container   .bording-content {
    border: 2px dashed #603007;
    padding: 10px 73px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.qrcode {
    margin-top: 20px;
    display: felx;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.code {
    height:200px;
    width: 200px;
}

p{
    font-weight: 500;
}
h2,h3,h4,h5,h6,p{
    margin-bottom: 0;
    margin-top: 0;
}

.ticket-from-and-to .avatar{
    width: 100px;
    height: 100px;
}

.circle-container img{
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    text-align: center;
    border-radius: 50%;
    margin: auto;
}

@media screen and (max-width: 414px) {
    
    
    .search-container   .container {
    background-color: #e5e5e5;
    width: 100%;
    margin: auto;
    border-top-left-radius:  7px;
    border-top-right-radius:  7px;
    padding: 0px;
    color: #603007;
}
 h2{
    font-size: 20px;
}
.search-container   .ticket-header {
    background-color: #f4a82d;
    color: white;
    border-top-left-radius:  7px;
    border-top-right-radius:  7px;
    text-align: center;
    padding: 15px 5px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.search-container  .ticket-header h2{
    font-size: 20px;
}

#logo {
    /* width: 45px; */
    /* border: 2px solid white; */
    /* border-radius: 100%; */
}

.ticket-body {
    padding: 30px 15px;
}

.search-container  .ticket-name p {
    font-size: 0.9rem;
    color: #b55334;
    font-weight: 500;
}

.search-container  .ruler {
    margin: 1rem 0;
    height: 1px;
    background: #b36f00;
}

.ticket-number-date {
    display: flex;
    justify-content: space-between;
    margin: 0 5%;
}

.ticket-from-and-to {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* margin: 0 15%; */
    /* border-bottom: 2.5px dashed #20a3f6; */
    /* padding-bottom: 20px; */
}

.plane-body {
    display: flex;
    align-items: center;
    justify-content:center;
    width: 250px;
}

.plane {
    width: 50px;
    margin-top: 15px;
    /* display: flex; */
}


.ter-gat-set {
    margin-top: 10px;
    display: flex;
    justify-content: space-around;
    align-items: center;
    margin: 10px 0px;
    gap: 10px;
    text-align: center;
}

.ter-gat-set  h2,h4{

    font-size: 16px;
}


.bording {
    margin-top: 10px;
    display: flex;
    justify-content: center;
}

.search-container   .bording-content {
    border: 2px dashed #603007;
    padding: 10px 73px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.qrcode {
    margin-top: 20px;
    display: felx;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.code {
    height:200px;
    width: 200px;
}

.circle-container img{
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    text-align: center;
    border-radius: 50%;
    margin: auto;
}
}
        </style>

    <div class=" col-md-4 search-container" style="margin-left:auto;margin-right: auto;" id="search-container">

        <?php echo form_open(get_uri("leaves/leave_return_search"), array("id" => "leave-return-form", "class" => "general-form", "role" => "form","method"=>"POST")); ?>
            <div class="shadow d-flex align-item-center col-xs-12 mt-3" style="">
                <input type="text" id="searchTerm" name="searchTerm" value="<?php echo $search == 0? '' : $search; ?>" class="form-control" placeholder="Search by id,name,mobile or any unique property" >
                <button type="submit" class="btn btn-primary rounded-0 rounded-end"><i class="search"></i> Search</button>
                
            </div>
        <?php echo form_close() ?>

        <div class="shadow d-flex justify-content-center col-xs-12 mt-3 mb-3 " id="search-card2">
            <p class="p10 m10 fs-3">No result to show</p>
        </div>
    </div>

<script>

$('#leave-return-form').on('submit', function(e){

    e.preventDefault();


    $.ajax({
            url: 'leaves/leave_return_search_form',
            data: {
                'searchTerm': $('#searchTerm').val(),
                // 'rise_csrf_token': $('input[name="rise_csrf_token"]').val(),
            },
            cache: false,
            dataType: 'json',
            type: 'POST',
            success: function (res) {
                //    console.log(result.success);
                $("#search-card2").html('');
                $("#search-card2").html(res.result);


                feather.replace();
            },
            statusCode: {
                403: function () {
                    console.log("403: Session expired.");
                    // location.reload();
                },
                404: function () {
                    $("#search-container").find('.modal-body').html("");
                    appAlert.error("404: Page not found.", {container: '.search-container', animate: false});
                }
            },
            error: function () {
                $("#search-container").find('.modal-body').html("");
                appAlert.error("500: Internal Server Error.", {container: '.search-container', animate: false});
            }
        });

        
});
</script>

