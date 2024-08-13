<div id="page-content" class="page-wrapper clearfix help-page-container ">
    <div id="search-box-wrapper">
        <div class="help-search-box-container">
            <h2><?php
                  echo app_lang("search_todays_access_requests");
                
                ?></h2>
            <?php echo view("visitors/search_box"); ?>
        </div>
    </div>

    <div class="help-page view-container-large" style="min-height: 300px;">

        <?php
        $count = 0;
        if(count($visitors)){
            foreach ($visitors as $visitor) {
                if ($count % 4 === 0) {
                    echo "<div class='row'>";
                }
                $count++;
                $class = $visitor->status == 'Approved'? 'badge bg-primary' : ($visitor->status == 'Pending'? 'badge bg-warning' : 'badge bg-danger');
                ?>
                <div class="col-md-3 col-sm-12">
                    <a href="<?php echo get_uri("visitors/search_results/" . $visitor->uuid); ?>">
                        <div class="card">
                            <div class="page-body p15 help-category-box">
                                <h4><?php echo $visitor->document_title; ?></h4>
                                <p class="text-off"><?php echo date("l, h:i a",strtotime(date_format(new DateTime($visitor->start_date),'Y-m-d').' '.$visitor->visit_time)); ?></p>
                                <span class="anchor"><?php echo $visitor->count . " " . app_lang("visitors")." <span class='$class'>".$visitor->status."</span>"; ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
                if (($count % 4 === 0) || ($count === count($visitors))) {
                    echo "</div>";
                }
            }
        }
        ?>
    </div>
</div>