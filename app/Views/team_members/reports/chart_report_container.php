<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Combined Profile Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }

        .chart-container {
            width: 1200%;
            height: 600px;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }

        h2 {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card clearfix">
        <ul id="tickets-reports-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white inner" role="tablist">
            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#tickets-chart-tab"><?php echo 'Employee List'; ?></a></li>
        </ul>

        <div class="tab-content">

            <div role="tabpanel" class="tab-pane fade" id="tickets-chart-tab">

                <!-- Combined Chart Container -->
                <div class="chart-container" id="combined_chart">
                    <h2>Modern Combined Profile Chart</h2>
                </div>

                <div class="bg-white">
                    <div id="tickets-chart-filters"></div>
                </div>

                <div id="load-tickets-chart"></div>

            </div>
            <div role="tabpanel" class="tab-pane fade" id="team-members-summary-tab"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Use data passed from the controller
    var genderData = <?php echo $gender_data; ?>;
    var maritalStatusData = <?php echo $marital_status_data; ?>;
    var ageLevelData = <?php echo $age_level_data; ?>;

    // Prepare data for each category (Gender, Marital Status, Age Level)
    var categories = ['Gender', 'Marital Status', 'Age Level'];

    var genderValues = genderData.map(item => item.count); // Extract count for gender
    var maritalStatusValues = maritalStatusData.map(item => item.count); // Extract count for marital status
    var ageLevelValues = ageLevelData.map(item => item.count); // Extract count for age level

    // Modern Combined Bar Chart with Stacked Bars and Smooth Transitions
    var combinedChart = echarts.init(document.getElementById('combined_chart'));
    var combinedOption = {
        tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
        legend: { 
            data: ['Male', 'Female', 'Single', 'Married', 'Young', 'Middle-aged', 'Older'],
            bottom: '0' // Legend at the bottom
        },
        xAxis: {
            type: 'category',
            data: categories,
            axisLabel: { fontSize: 14, color: '#666' }
        },
        yAxis: { 
            type: 'value',
            axisLabel: { fontSize: 14, color: '#666' }
        },
        series: [
            {
                name: 'Male',
                type: 'bar',
                stack: 'Gender',
                data: [genderValues[0], 0, 0], // Only for "Gender" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#3498db' },
                        { offset: 1, color: '#2980b9' }
                    ])
                }
            },
            {
                name: 'Female',
                type: 'bar',
                stack: 'Gender',
                data: [genderValues[1], 0, 0], // Only for "Gender" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#e74c3c' },
                        { offset: 1, color: '#c0392b' }
                    ])
                }
            },
            {
                name: 'Single',
                type: 'bar',
                stack: 'Marital Status',
                data: [0, maritalStatusValues[0], 0], // Only for "Marital Status" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#2ecc71' },
                        { offset: 1, color: '#27ae60' }
                    ])
                }
            },
            {
                name: 'Married',
                type: 'bar',
                stack: 'Marital Status',
                data: [0, maritalStatusValues[1], 0], // Only for "Marital Status" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#f39c12' },
                        { offset: 1, color: '#e67e22' }
                    ])
                }
            },
            {
                name: 'Young',
                type: 'bar',
                stack: 'Age Level',
                data: [0, 0, ageLevelValues[0]], // Only for "Age Level" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#9b59b6' },
                        { offset: 1, color: '#8e44ad' }
                    ])
                }
            },
            {
                name: 'Middle-aged',
                type: 'bar',
                stack: 'Age Level',
                data: [0, 0, ageLevelValues[1]], // Only for "Age Level" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#34495e' },
                        { offset: 1, color: '#2c3e50' }
                    ])
                }
            },
            {
                name: 'Older',
                type: 'bar',
                stack: 'Age Level',
                data: [0, 0, ageLevelValues[2]], // Only for "Age Level" category
                itemStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#e67e22' },
                        { offset: 1, color: '#d35400' }
                    ])
                }
            }
        ],
        animationDuration: 2000
    };
    combinedChart.setOption(combinedOption);

    // Responsive resizing of the chart
    window.addEventListener('resize', function() {
        combinedChart.resize();
    });
</script>
</body>
</html>
