<!-- <div class="card bg-white">
    <div class="card-header">
        <i data-feather="users" class="icon-16"></i> &nbsp;<?php echo app_lang("team_members_departments_overview"); ?>
    </div>
    <div class="row rounded-bottom pt-2 pb-3">
        <?php
        $i = 1;
         foreach ($departments as $k=>$v){ 
            if($i%2 == 0 ){?>                
                <div class="col-sm-6  b-r-2">
           
                    <div class="pt-3  px-3 b-b">
                        <div class="d-inline">
                            <div class="color-tag border-circle me-3 wh10" style="background-color: #60a1ff;"></div><?php echo $v?->department; ?>                        
                           
                        </div> 
                        <span class="strong float-end"><?php echo $v?->count; ?></span>
                    </div>
  
                </div>
            <?php }else{?>
                <div class="col-sm-6  b-r-2">
           
                    <div class="pt-3 px-3 b-b">
                        <div class="d-inline">
                            <div class="color-tag border-circle me-3 wh10" style="background-color: #89e762;"></div><?php echo $v?->department; ?>                        
                          
                        </div>  
                        <span class="strong float-end"><?php echo $v?->count; ?></span>
                    </div>

                </div>
            <?php }?>
             
        <?php $i++;}?>
    </div>
</div> -->

<!-- Create a container for the chart -->
<div id="departmentsChart" class="card bg-white" style="width: 100%; height: 290px; padding: 10px; background: white; margin-bottom: 10px;"></div>

<script>
    // Sample data (Replace this with your actual data from the backend)
    var departmentsRowData = [
        { department: 'Hantidhawrka Gudaha', count: 2 },
        { department: 'La-taliyaha Arrimaha Sharciga', count: 1 },
        { department: 'Agaasimaha Guud', count: 48 },
        { department: 'Agaasimaha Adeega Guud', count: 309 },
        { department: 'Shaqada iyo Horumarinta', count: 29 },
        { department: 'Teknoolajiyadda iyo Amniga', count: 53 },
        { department: 'Siyaasadda iyo Qorsheynta', count: 30 },
        { department: 'Waaxda Warfaafinta', count: 25 },
        { department: 'Wasiiru-Dowlaha', count: 18 },
        { department: 'La-taliyaha Dhaqaalaha', count: 2 },
        { department: 'La-taliyaha Arrimaha Bulshada', count: 1 },
        { department: 'La Taliyaasha & Ergada - PEAT', count: 64 },
        { department: 'Xiriirka Dadweynaha', count: 24 },
        { department: 'Amniga Qaranka - ONS', count: 60 },
        { department: 'Arkifiyada iyo Xoghaynada', count: 5 },
        { department: 'Maamulka iyo Maaliyadda', count: 26 },
        { department: 'Habmaamuuska', count: 23 },
        { department: 'Ku Xig. Agaasimaha Guud', count: 13 },
        { department: 'Isbitaalka / Hospital', count: 5 },
        { department: 'Bangiga Hormarinta iyo Dibudhiska', count: 1 }
    ];

      // Function to initialize the ECharts chart
      function initDepartmentChart(departmentData) {
        // Extract the department names and counts
        var departmentNames = departmentData.map(function(item) {
            return item.department.substr(0,30) + '...';
        });
     
        var departmentCounts = departmentData.map(function(item) {
            return item.count;
        });

        // Initialize the ECharts instance
        var chart = echarts.init(document.getElementById('departmentsChart'));

        // Define the chart options
        var options = {
            title: {
                text: 'Employee Departments Overview',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: { type: 'shadow' }
            },
            xAxis: {
                type: 'category',
                data: departmentNames,
                axisLabel: {
                    interval: 0, // Show all labels
                    rotate: 23, // Rotate labels to avoid overlapping
                    fontSize: 9
                }
            },
            yAxis: {
                type: 'value',
                name: 'Employee Count'
            },
            series: [{
                data: departmentCounts,
                type: 'bar',
                barWidth: '70%',
                itemStyle: {
                    color: '#4caf50'
                }
            }]
        };

        // Set the options for the chart
        chart.setOption(options);
    }

    // AJAX call to fetch department data from the server
    function fetchDepartmentData() {
        $.ajax({
            url: '<?php echo get_uri('team_members/get_departments_count_ajax') ?>', // Update the URL to match your route
            method: 'GET',
            success: function(response) {
                // Parse the response JSON
                var departmentData = JSON.parse(response);

                // Initialize the chart with the data
                initDepartmentChart(departmentData);
            },
            error: function(error) {
                console.error('Error fetching department data:', error);
            }
        });
    }

    // Call the function to fetch data and initialize the chart
    fetchDepartmentData();
</script>