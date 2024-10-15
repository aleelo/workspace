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
                                    <th>Amount</th>
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
                                    <?php
                                    $now = new DateTime(); // Get current date and time

                                    // Filter materials to only include upcoming departures and arrivals
                                    $filtered_materials = array_filter($materials, function ($data) use ($now) {
                                        $departure_date = !empty($data->departure_date) ? new DateTime($data->departure_date) : null;
                                        $arrival_date = !empty($data->arrival_date) ? new DateTime($data->arrival_date) : null;

                                        // Keep records where either departure_date or arrival_date is still upcoming
                                        return ($departure_date && $departure_date >= $now) || ($arrival_date && $arrival_date >= $now);
                                    });
                                    ?>

                                    <?php if (count($filtered_materials) > 0): ?>
                                        <?php foreach ($filtered_materials as $data): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($data->fullName ?? 'N/A'); ?></td>
                                                <td><?php echo $data->totalValue? $data->totalValue . ' ' . $data->currency_id : "N/A"; ?></td>

                                                <td><?= htmlspecialchars($data->departure_country ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($data->destination_country ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($data->transit_country ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($data->departure_date ?? 'N/A'); ?></td>
                                                <td><?= htmlspecialchars($data->arrival_date ?? 'N/A'); ?></td>
                                                <td class="text-center">
                                                    <div style="display: flex; justify-content: center; gap: 5px;">
                                                        <?php if ($data->trip_type == 'Arrival' && $data->q_type == 1) { ?>
                                                            <span style="background-color: #4caf50; color: white; font-weight: bold; padding: 4px 8px; border-radius: 5px; font-size: 12px;">
                                                                Arrival - 10K Above
                                                                <i class="mdi mdi-airplane-landing"></i>
                                                            </span>
                                                        <?php } elseif ($data->trip_type == 'Arrival' && $data->q_type == 2) { ?>
                                                            <span style="background-color: #f44336; color: white; font-weight: bold; padding: 4px 8px; border-radius: 5px; font-size: 12px;">
                                                                Arrival - 100K Above
                                                                <i class="mdi mdi-airplane-landing"></i>
                                                            </span>
                                                        <?php } elseif ($data->trip_type == 'Departure' && $data->q_type == 1) { ?>
                                                            <span style="background-color: #ff9800; color: white; font-weight: bold; padding: 4px 8px; border-radius: 5px; font-size: 12px;">
                                                                Departure - 10K Above
                                                                <i class="mdi mdi-airplane-takeoff"></i>
                                                            </span>
                                                        <?php } elseif ($data->trip_type == 'Departure' && $data->q_type == 2) { ?>
                                                            <span style="background-color: #9c27b0; color: white; font-weight: bold; padding: 4px 8px; border-radius: 5px; font-size: 12px;">
                                                                Departure - 100K Above
                                                                <i class="mdi mdi-airplane-takeoff"></i>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span style="font-weight: bold; font-size: 12px;">
                                                                <?= htmlspecialchars($data->trip_type) ?>
                                                            </span>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No data available</td>
                                        </tr>
                                    <?php endif; ?>
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
        // Initialize bar chart using Chart.js
        const ctx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Departures', 'Arrivals'],
                datasets: [{
                    label: 'Total Flights',
                    data: [100000, 100000],
                    backgroundColor: ['#007bff', '#28a745'],
                    borderColor: ['#007bff', '#28a745'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>
