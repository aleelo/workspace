<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Flight Dashboard</title>
    <style>
        body {
            background-color: #f4f6f9;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .summary-card {
            border-radius: 15px;
            transition: transform 0.2s;
        }

        .summary-card:hover {
            transform: scale(1.05);
        }

        .plane-icon {
            font-size: 3rem;
            color: #007bff;
        }

        .card-value {
            font-size: 2.5rem;
            color: #333;
        }

        .circular-progress {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(#007bff 0% 80%, #ddd 80% 100%);
            margin: 0 auto;
        }

        .progress-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container-fluid p-4">
        <div class="row text-center">
            <!-- Summary Cards -->
            <div class="col-md-3 mb-3">
                <div class="card shadow summary-card">
                    <div class="card-body">
                        <i class="fas fa-plane-departure plane-icon"></i>
                        <h5 class="card-title mt-3">Departures</h5>
                        <h2 class="card-value">100K</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow summary-card">
                    <div class="card-body">
                        <i class="fas fa-plane-arrival plane-icon"></i>
                        <h5 class="card-title mt-3">Arrivals</h5>
                        <h2 class="card-value">100K</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow summary-card">
                    <div class="card-body">
                        <i class="fas fa-plane-departure plane-icon"></i>
                        <h5 class="card-title mt-3">Departures</h5>
                        <h2 class="card-value">10K</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card shadow summary-card">
                    <div class="card-body">
                        <i class="fas fa-plane-arrival plane-icon"></i>
                        <h5 class="card-title mt-3">Arrivals</h5>
                        <h2 class="card-value">10K</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Circular Progress Indicators -->
        <!-- <div class="row my-5 text-center">
            <div class="col-md-3">
                <div class="circular-progress" data-percent="80">
                    <span class="progress-value">80%</span>
                </div>
                <p class="mt-3">Departures On Time</p>
            </div>
            <div class="col-md-3">
                <div class="circular-progress" data-percent="60">
                    <span class="progress-value">60%</span>
                </div>
                <p class="mt-3">Arrivals On Time</p>
            </div>
            <div class="col-md-3">
                <div class="circular-progress" data-percent="90">
                    <span class="progress-value">90%</span>
                </div>
                <p class="mt-3">Transit Efficiency</p>
            </div>
            <div class="col-md-3">
                <div class="circular-progress" data-percent="75">
                    <span class="progress-value">75%</span>
                </div>
                <p class="mt-3">Customer Satisfaction</p>
            </div>
        </div> -->

        <!-- Data Table -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>Flight Summary Table</h4>
                    </div>
                    <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-header">
                    <tr>
                        <th>Passenger Name</th>
                        <th>Departure Country</th>
                        <th>Destination Country</th>
                        <th>Transit Country</th>
                        <th>Departure Date</th>
                        <th>Arrival Date</th>
                        <th>Trip Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($materials) && count($materials) > 0): ?>
                        <?php foreach ($materials as $data): ?>
                        <tr>
                            <td><?= htmlspecialchars($data->name ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($data->departure_country_id ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($data->destination_country ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($data->transit_country ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($data->departure_date ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($data->arrival_date ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($data->trip_type ?? 'N/A'); ?></td>
        
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="row mt-5">
            <div class="col-md-12">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        
    </script>
</body>

</html>