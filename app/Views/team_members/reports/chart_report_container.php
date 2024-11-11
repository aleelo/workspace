<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Reports</title>
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
             <div class="chart-container" id="leave_application_chart">
                <h2>Leave Applications by Type</h2>
            </div>
            <div class="chart-container" id="monthly_leave_chart">
                <h2>Monthly Leave Applications</h2>
            </div>

            <div class="chart-container" id="department_leave_chart">
                <h2>Leave Applications by Department</h2>
            </div>

            <!-- <div class="chart-container" id="employee_leave_chart">
                <h2>Total Leave Days by Employee</h2>
            </div> -->

            

            

            <div class="chart-container" id="leave_status_chart">
                <h2>Leave Applications by Status</h2>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var monthlyLeaveData = <?php echo $monthly_leave_data; ?>;
        var departmentLeaveData = <?php echo $department_leave_data; ?>;
        var employeeLeaveData = <?php echo $employee_leave_data; ?>;
        var leaveTypeStatusData = <?php echo $leave_type_status_data; ?>;
        var leaveApplicationData = <?php echo $leave_application_data; ?>;
        var leaveStatusData = <?php echo $leave_status_data; ?>;

        // Monthly Leave Applications Chart
        var monthlyChart = echarts.init(document.getElementById('monthly_leave_chart'));
monthlyChart.setOption({
    title: { text: 'Monthly Leave Applications', left: 'center' },
    tooltip: { trigger: 'axis' },
    legend: { data: ['Applications', 'Trend'], bottom: 0 },
    xAxis: {
        type: 'category',
        data: monthlyLeaveData.map(item => item.month),
        axisLabel: { rotate: 45 }
    },
    yAxis: { type: 'value' },
    series: [
        {
            name: 'Applications',
            data: monthlyLeaveData.map(item => item.count),
            type: 'bar',
            itemStyle: { color: '#3498db' },
            label: { show: true, position: 'top', color: '#333' }
        },
        {
            name: 'Trend',
            data: monthlyLeaveData.map(item => item.count),
            type: 'line',
            smooth: true,
            itemStyle: { color: '#e74c3c' },
            lineStyle: { width: 2 }
        }
    ]
});


        // Department Leave Applications Chart
        var departmentChart = echarts.init(document.getElementById('department_leave_chart'));
        departmentChart.setOption({
            title: { text: 'Leave Applications by Department', left: 'center' },
            xAxis: { type: 'category', data: departmentLeaveData.map(item => item.department) },
            yAxis: { type: 'value' },
            series: [{ data: departmentLeaveData.map(item => item.count), type: 'bar', itemStyle: { color: '#e74c3c' } }]
        });

        // Employee Leave Days Chart
        var employeeChart = echarts.init(document.getElementById('employee_leave_chart'));
        employeeChart.setOption({
            title: { text: 'Total Leave Days by Employee', left: 'center' },
            xAxis: { type: 'category', data: employeeLeaveData.map(item => item.employee) },
            yAxis: { type: 'value' },
            series: [{ data: employeeLeaveData.map(item => item.total_leave_days), type: 'bar', itemStyle: { color: '#2ecc71' } }]
        });

        // Leave Status by Type Chart
        // var leaveTypeStatusChart = echarts.init(document.getElementById('leave_type_status_chart'));
        // leaveTypeStatusChart.setOption({
        //     title: { text: 'Leave Status by Type', left: 'center' },
        //     tooltip: { trigger: 'item' },
        //     series: [{
        //         type: 'sunburst',
        //         data: leaveTypeStatusData.reduce((acc, item) => {
        //             let type = acc.find(i => i.name === item.leave_type);
        //             if (!type) {
        //                 type = { name: item.leave_type, children: [] };
        //                 acc.push(type);
        //             }
        //             type.children.push({ name: item.status, value: item.count });
        //             return acc;
        //         }, []),
        //         radius: [0, '80%'],
        //         label: { rotate: 'tangential' }
        //     }]
        // });

        // Leave Applications by Type Chart
        var leaveApplicationChart = echarts.init(document.getElementById('leave_application_chart'));
        leaveApplicationChart.setOption({
            title: { text: 'Leave Applications by Type', left: 'center' },
            tooltip: { trigger: 'item' },
            series: [{
                type: 'pie',
                radius: '50%',
                data: leaveApplicationData.map(function (item) {
                    return { value: item.count, name: item.leave_type };
                })
            }]
        });

        // Leave Applications by Status Chart
        var leaveStatusChart = echarts.init(document.getElementById('leave_status_chart'));
        leaveStatusChart.setOption({
            title: { text: 'Leave Applications by Status', left: 'center' },
            xAxis: { type: 'category', data: leaveStatusData.map(item => item.status) },
            yAxis: { type: 'value' },
            series: [{ data: leaveStatusData.map(item => item.count), type: 'bar', itemStyle: { color: '#8e44ad' } }]
        });

        // Resize charts on window resize
        window.addEventListener('resize', function() {
            monthlyChart.resize();
            departmentChart.resize();
            employeeChart.resize();
            leaveTypeStatusChart.resize();
            leaveApplicationChart.resize();
            leaveStatusChart.resize();
        });
    </script>
</body>
</html>
