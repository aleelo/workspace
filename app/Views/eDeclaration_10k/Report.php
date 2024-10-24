<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>10K Arrival Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #0056b3;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #6c757d;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .icon {
            margin-right: 8px;
            color: #0056b3;
        }
    </style>
</head>
<body>

    <h2><i class="fas fa-plane-arrival icon"></i> Arrival Details Report</h2>

    <table>
        <thead>
            <tr>
                <th><i class="fas fa-id-badge icon"></i> Reference Number</th>
                <th><i class="fas fa-user icon"></i> Passenger Name</th>
                <th><i class="fas fa-passport icon"></i> Passport Number</th>
                <th><i class="fas fa-plane icon"></i> Flight Number</th>
                <th><i class="fas fa-calendar-alt icon"></i> Arrival Date</th>
                <th><i class="fas fa-map-marker-alt icon"></i> Destination</th>
                <th><i class="fas fa-box icon"></i> Material Name</th>
                <th><i class="fas fa-money-bill icon"></i> Value</th>
                <th><i class="fas fa-tag icon"></i> Purpose</th>
            </tr>
        </thead>
        <tbody>
            <!-- Passenger and travel details -->
            <tr>
                <td><?= htmlspecialchars($passenger_info->ref_number) ?></td>
                <td><?= htmlspecialchars($passenger_info->fullName) ?></td>
                <td><?= htmlspecialchars($passenger_info->passport_no ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($travel_info->vehicle_number) ?></td>
                <td><?= htmlspecialchars($travel_info->arrival_date) ?></td>
                <td><?= htmlspecialchars($travel_info->destination_country) ?></td>
                
                <!-- Material details -->
                <?php foreach ($materials as $material): ?>
                    <td><?= htmlspecialchars($material->name) ?></td>
                    <td><?= htmlspecialchars($material->totalValue ? $material->totalValue . ' ' . $material->currency_code : "N/A") ?></td>
                    <td><?= htmlspecialchars($material->purpose ? $material->purpose : "N/A") ?></td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>

</body>
</html>
