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
            width: 100%;
            height: 400px;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 40px;
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

            <!-- Profile Info Chart -->
            <div class="chart-container" id="profile_chart">
                <h2>Profile Info Chart</h2>
            </div>

            <!-- Bank Usage Chart -->
            <div class="chart-container" id="bank_chart">
                <h2>Bank Usage Chart</h2>
            </div>

            <!-- Job Info Chart -->
            <div class="chart-container" id="job_chart">
                <h2>Job Info Chart</h2>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        // Data passed from the controller
        var genderData = <?php echo $gender_data; ?>;
        var maritalStatusData = <?php echo $marital_status_data; ?>;
        var ageLevelData = <?php echo $age_level_data; ?>;
        var bankUsageData = <?php echo $bank_usage_data; ?>;
        var jobInfoData = <?php echo $job_info_data; ?>;

        // Helper function to find age level counts
        function getAgeLevelCount(level) {
            var data = ageLevelData.find(a => a.age_level === level);
            return data ? data.count : 0;
        }

        // Profile Info Chart
        var profileChart = echarts.init(document.getElementById('profile_chart'));
        var profileOption = {
            title: {
                text: 'Profile Information Breakdown',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                },
                formatter: function(params) {
                    var result = '';
                    params.forEach(function(item) {
                        result += item.seriesName + ': ' + item.data + '<br>';
                    });
                    return result;
                }
            },
            legend: {
                bottom: '0',
                data: ['Male', 'Female', 'Single', 'Married', '10-20', '20-30', '30+']
            },
            xAxis: {
                type: 'category',
                data: ['Gender', 'Marital Status', 'Age Level'],
                axisLabel: {
                    fontSize: 14,
                    color: '#666'
                }
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    fontSize: 14,
                    color: '#666'
                }
            },
            series: [{
                    name: 'Male',
                    type: 'bar',
                    stack: 'Gender',
                    data: [genderData.find(g => g.gender === 'male')?.count || 0, 0, 0],
                    itemStyle: {
                        color: '#3498db'
                    }
                },
                {
                    name: 'Female',
                    type: 'bar',
                    stack: 'Gender',
                    data: [genderData.find(g => g.gender === 'female')?.count || 0, 0, 0],
                    itemStyle: {
                        color: '#e74c3c'
                    }
                },
                {
                    name: 'Single',
                    type: 'bar',
                    stack: 'Marital Status',
                    data: [0, maritalStatusData.find(m => m.marital_status === 'single')?.count || 0, 0],
                    itemStyle: {
                        color: '#2ecc71'
                    }
                },
                {
                    name: 'Married',
                    type: 'bar',
                    stack: 'Marital Status',
                    data: [0, maritalStatusData.find(m => m.marital_status === 'maried')?.count || 0, 0],
                    itemStyle: {
                        color: '#f39c12'
                    }
                },
                {
                    name: '10-20',
                    type: 'bar',
                    stack: 'Age Level',
                    data: [0, 0, getAgeLevelCount('10-20')],
                    itemStyle: {
                        color: '#9b59b6'
                    }
                },
                {
                    name: '20-30',
                    type: 'bar',
                    stack: 'Age Level',
                    data: [0, 0, getAgeLevelCount('20-30')],
                    itemStyle: {
                        color: '#34495e'
                    }
                },
                {
                    name: '30+',
                    type: 'bar',
                    stack: 'Age Level',
                    data: [0, 0, getAgeLevelCount('30+')],
                    itemStyle: {
                        color: '#d35400'
                    }
                }
            ],
            animationDuration: 2000
        };
        profileChart.setOption(profileOption);

        // Bank Usage Chart
        var bankChart = echarts.init(document.getElementById('bank_chart'));
        var bankOption = {
            title: {
                text: 'Bank Usage Statistics',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            xAxis: {
                type: 'category',
                data: bankUsageData.map(function(item) {
                    return item.bank_name;
                }),
                axisLabel: {
                    fontSize: 12,
                    color: '#666'
                }
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                data: bankUsageData.map(function(item) {
                    return item.count;
                }),
                type: 'bar',
                barWidth: '50%',
                itemStyle: {
                    color: '#2ecc71'
                }
            }]
        };
        bankChart.setOption(bankOption);

        // Job Info Chart
        var jobChart = echarts.init(document.getElementById('job_chart'));
        var jobOption = {
            title: {
                text: 'Job Information (Salary and Experience)',
                left: 'center'
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                bottom: '0',
                data: ['Static Salary', 'Work Experience']
            },
            xAxis: {
                type: 'category',
                data: jobInfoData.map(function(item) {
                    return item.salary;
                }),
                axisLabel: {
                    fontSize: 12,
                    color: '#666'
                }
            },
            yAxis: {
                type: 'value',
                axisLabel: {
                    fontSize: 12,
                    color: '#666'
                }
            },
            series: [{
                    name: 'Static Salary',
                    type: 'bar',
                    data: jobInfoData.map(function(item) {
                        return item.salary;
                    }),
                    itemStyle: {
                        color: '#3498db'
                    }
                },
                {
                    name: 'Work Experience',
                    type: 'bar',
                    data: jobInfoData.map(function(item) {
                        return item.work_experience;
                    }),
                    itemStyle: {
                        color: '#e74c3c'
                    }
                }
            ]
        };
        jobChart.setOption(jobOption);

        // Resize charts on window resize
        window.addEventListener('resize', function() {
            profileChart.resize();
            bankChart.resize();
            jobChart.resize();
        });
    </script>
</body>

</html>
