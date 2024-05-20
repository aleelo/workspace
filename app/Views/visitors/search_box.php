<?php
load_js(array(
    "assets/js/awesomplete/awesomplete.min.js"
));
?>

<div class="input-group d-block search-box">
    <?php
    echo form_input(array(
        "id" => "help-search-box",
        "name" => "search",
        "value" => "",
        "autocomplete" => "false",
        "class" => "form-control help-search-box",
        "placeholder" => app_lang('search_your_question')
    ));
    ?>
    <span class="spinning-btn"></span>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        var $searchBox = $("#help-search-box");
        var $spinningBtn = $(".spinning-btn");
        var awesomplete = new Awesomplete($searchBox[0], {
                            minChars: 1,
                            autoFirst: true,
                            maxItems: 10
                        });

        $searchBox.on("keyup", function (e) {
            if (!(e.which >= 37 && e.which <= 40)) {

                //show/hide loder icon in searchbox
                if (this.value) {
                    $spinningBtn.addClass("spinning");
                } else {
                    $spinningBtn.removeClass("spinning");
                }
                // getAwesompleteList();
                //witi 200 ms to request ajax cll
                clearTimeout($.data(this, 'timer'));
                var wait = setTimeout(getAwesompleteList, 200);
                $(this).data('timer', wait);
            }

            //hide the no result found message
            // if (!this.value) {
            //     $(".awesomplete").find("ul").html("").attr("hidden");
            // }

        });

        function getAwesompleteList() {

            $.ajax({
                url: "<?php echo get_uri('visitors/get_visitors_suggestion'); ?>",
                data: {search: $searchBox.val()},
                cache: false,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    //show a loader icon in search box
                    $spinningBtn.removeClass("spinning");

                    //set the results
                    awesomplete.list = response;
                    awesomplete.replace = function (suggestion){
                                            $searchBox.val(suggestion.value);
                                            // console.log(suggestion);
                                    };

                    //show no result found 
                    if (!response.length && $searchBox.val()) {
                        $(".awesomplete").find("ul").html("<li aria-selected='false'> <?php echo app_lang('no_result_found'); ?> </li>").removeAttr("hidden");
                    }
                }
            });
        }


        $searchBox.on('awesomplete-selectcomplete', function (suggestion) {
            //serch result selected, redirect to the article view
            // console.log(this.value);
            console.log( this.value);
            if (this.value) {
                window.location.href = "<?php echo get_uri("visitors/search_results"); ?>/" + this.value;
            }
            this.value = "";
        });
    });

</script>