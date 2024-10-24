v<!DOCTYPE html>
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
            line-height: 1.6;
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
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
            font-size: 1em;
        }
        th {
            background-color: #f8f9fa;
        }
        .icon {
            margin-right: 8px;
            color: #0056b3;
        }
        .section-title {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: left;
        }
    </style>
</head>
<body>

    <!-- Report Title -->
    <h2><i class="fas fa-plane-arrival icon"></i> Edeclaration Details Report</h2>

    <!-- Combined Table -->
    <table>
        <!-- Passenger Information -->
        <tr class="section-title">
            <th colspan="2"><i class="fas fa-user icon"></i> Passenger Information</th>
        </tr>
        <tr>
            <td><i class="fas fa-id-badge icon"></i> Reference Number</td>
            <td><?= htmlspecialchars($passenger_info->ref_number) ?></td>
        </tr>
        <tr>
            <td><i class="fas fa-user icon"></i> Name</td>
            <td><?= htmlspecialchars($passenger_info->fullName) ?></td>
        </tr>
        <tr>
            <td><i class="fas fa-passport icon"></i> Passport Number</td>
            <td><?= htmlspecialchars($passenger_info->passport_number ?? 'N/A') ?></td>
        </tr>

        <!-- Travel Information -->
        <tr class="section-title">
            <th colspan="2"><i class="fas fa-plane icon"></i> Travel Information</th>
        </tr>
        <tr>
            <td><i class="fas fa-plane icon"></i> Flight Number</td>
            <td><?= htmlspecialchars($travel_info->vehicle_number) ?></td>
        </tr>
        <tr>
            <td><i class="fas fa-calendar-alt icon"></i> Arrival Date</td>
            <td><?= htmlspecialchars($travel_info->arrival_date) ?></td>
        </tr>
        <tr>
            <td><i class="fas fa-map-marker-alt icon"></i> Destination</td>
            <td><?= htmlspecialchars($travel_info->destination_country) ?></td>
        </tr>

        <!-- Materials Information -->
        <tr class="section-title">
            <th colspan="2"><i class="fas fa-box icon"></i> Materials Details</th>
        </tr>
        <?php foreach($materials as $material): ?>
        <tr>
            <td><i class="fas fa-box icon"></i> Material Name</td>
            <td><?= htmlspecialchars($material->name) ?></td>
        </tr>
        <tr>
            <td><i class="fas fa-money-bill icon"></i> Value</td>
            <td><?= htmlspecialchars($material->totalValue ? $material->totalValue . ' ' . $material->currency_code : "N/A") ?></td>
        </tr>
        <tr>
            <td><i class="fas fa-tag icon"></i> Purpose</td>
            <td><?= htmlspecialchars($material->purpose ? $material->purpose : "N/A") ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</body>
</html>
