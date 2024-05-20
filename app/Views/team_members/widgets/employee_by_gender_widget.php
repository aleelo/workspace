<div class="card bg-white">
    <div class="card-header clearfix">
        <i data-feather="list" class="icon-16"></i> &nbsp;<?php echo 'Employees By Gender'; ?>
    </div>
    <div class="card-body rounded-bottom" id="employee-gender-widget">
        <div class="row">
            <div class="col-md-6">
                <canvas id="employees-by-gender-chart" style="width: 100%; height: 160px;"></canvas>
            </div>
            <div class="col-md-6 pl20 <?php echo count($task_statuses) > 8 ? "" : "pt-4"; ?>">
                <?php
                foreach ($task_statuses as $task_status) {
                    ?>
                    <a href="<?php echo get_uri('team_members'); ?>" class="text-default">
                        <div class="pb-2">
                            <div class="color-tag border-circle me-3 wh10" style="background-color: #2daef9"></div>Male
                            <span class="strong float-end" style="color: #2daef9"><?php echo $widget_data->maleCount; ?></span>
                        </div>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$titles = array();
$data = array();
$status_colors = array();
$titles[] = ['Male', 'Female'];
$data[] = $widget_data->maleCount;
$data[] = $widget_data->femaleCount;

$status_colors[] = '#2daef9';
$status_colors[] = '#d02df9';
?>
<script type="text/javascript">
    //for task status chart
    var labels = <?php echo json_encode($titles) ?>;
    var data = <?php echo json_encode($data) ?>;
    var status_colors = <?php echo json_encode($status_colors) ?>;
    var emplyeeGenderchart = document.getElementById("employees-by-gender-chart");
    new Chart(emplyeeGenderchart, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [
                {
                    data: data,
                    backgroundColor: status_colors,
                    borderWidth: 0
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 87,
            tooltips: {
                callbacks: {
                    title: function (tooltipItem, data) {
                        return data['labels'][tooltipItem[0]['index']];
                    },
                    label: function (tooltipItem, data) {
                        return "";
                    },
                    afterLabel: function (tooltipItem, data) {
                        var dataset = data['datasets'][0];
                        var percent = Math.round((dataset['data'][tooltipItem['index']] / dataset["_meta"][Object.keys(dataset["_meta"])[0]]['total']) * 100);
                        return '(' + percent + '%)';
                    }
                }
            },
            legend: {
                display: false
            },
            animation: {
                animateScale: true
            }
        }
    });

    $(document).ready(function () {
        initScrollbar('#employee-gender-widget', {
            setHeight: 327
        });
    });

</script>