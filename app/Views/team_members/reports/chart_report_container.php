<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Employee Data Charts</title>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <style>
        /* Modern styling for charts */
        .chart-container {
            width: 1000%; /* Ensure charts fit the available space */
            height: 400px;
            margin: 40px;
        }
    </style>
</head>
<body>
<?php echo get_reports_topbar(); ?>
    <!-- Div for User Type Bar Chart -->

    <div id="page-content" class="page-wrapper clearfix grid-button">
        <div class="card clearfix">
            <ul id="tickets-reports-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white inner" role="tablist">
                <!-- <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("tickets"); ?></h4></li> -->
                <li><a role="presentation" data-bs-toggle="tab"  href="javascript:;" data-bs-target="#marital-status-chart"><?php echo 'Marital Status'; ?></a></li>
                <!--<li><a role="presentation" data-bs-toggle="tab" href="<?php // echo_uri("tickets/team_members_summary"); ?>" data-bs-target="#team-members-summary-tab"><?php //echo app_lang('team_members_summary'); ?></a></li>-->
            </ul>

            <div class="tab-content">

                <div role="tabpanel" class="tab-pane fade" id="marital-status-chart">
                    
                    <div class="chart-container" id="marital_status_chart"></div>

                </div>

                <div role="tabpanel" class="tab-pane fade" id="team-members-summary-tab">
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="chart-container" id="user_type_chart"></div>

    <!-- Div for Marital Status Pie Chart -->

    <!-- Div for Age vs Work Experience Scatter Plot -->
    <div class="chart-container" id="age_work_chart"></div>

    <script type="text/javascript">
        // Data from the server (ensure this outputs proper JSON format)
        var userTypeData = <?php echo json_encode($user_type_data); ?>;
        var maritalStatusData = <?php echo json_encode($marital_status_data); ?>;
 
        // Modernized Bar Chart for User Types
        var userTypeChart = echarts.init(document.getElementById('user_type_chart'));
        var userTypeOption = {
            title: {
                text: 'User Types',
                left: 'center',
                textStyle: { fontSize: 18, color: '#4A4A4A' }
            },
            tooltip: { trigger: 'axis' },
            xAxis: {
                type: 'category',
                data: userTypeData.map(item => item.user_type),
                axisLabel: { rotate: 30 }
            },
            yAxis: { type: 'value' },
            series: [{
                type: 'bar',
                data: userTypeData.map(item => item.count),
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#83bff6' },
                        { offset: 0.5, color: '#188df0' },
                        { offset: 1, color: '#188df0' }
                    ])
                },
                barWidth: '60%',
                animationDuration: 1500
            }]
        };
        userTypeChart.setOption(userTypeOption);

        // Modernized Pie (Donut) Chart for Marital Status
        var maritalStatusChart = echarts.init(document.getElementById('marital_status_chart'));
        var maritalStatusOption = {
            title: {
                text: 'Marital Status Distribution',
                left: 'center',
                textStyle: { fontSize: 18, color: '#4A4A4A' }
            },
            tooltip: { trigger: 'item' },
            series: [{
                name: 'Marital Status',
                type: 'pie',
                radius: ['40%', '70%'], // Donut chart
                avoidLabelOverlap: false,
                label: {
                    formatter: '{b}: {d}%',
                    position: 'outside',
                    textStyle: { color: '#4A4A4A' }
                },
                labelLine: { show: true },
                data: maritalStatusData.map(function(item) {
                    return { value: item.count, name: item.marital_status };
                }),
                animationDuration: 1500
            }]
        };
        maritalStatusChart.setOption(maritalStatusOption);

        // Modernized Scatter Plot for Age Level vs Work Experience
        

        // Responsive resizing of charts
      
    </script>
</body>
</html>
