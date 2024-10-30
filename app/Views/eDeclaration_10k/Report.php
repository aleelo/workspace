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
        form {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        label {
            margin-right: 10px;
        }
        input[type="date"], input[type="text"] {
            flex: 1 1 30%;
            margin-right: 10px;
            padding: 8px;
        }
        button {
            flex: 1 1 15%;
            padding: 10px;
            background-color: #0056b3;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #004494;
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
        /* Ensure Passenger Name stays on one line */
        td.passenger-name {
            white-space: nowrap;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            form {
                flex-direction: column;
            }
            input[type="date"], input[type="text"], button {
                margin-bottom: 10px;
            }
            table {
                font-size: 12px;
            }
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>

    <h2><i class="fas fa-plane-arrival icon"></i> Destination Details Report</h2>

    <!-- Date Filter Form -->
    <?php echo form_open(get_uri("edeclaration_10k/arriving10k_details1"), array("id" => "arrival-form")); ?>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>">

        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date ?? '') ?>">

        <label for="ref_number">Reference Number:</label>
        <input type="text" id="ref_number" name="ref_number" placeholder="Enter Reference Number" value="<?= htmlspecialchars($ref_number ?? '') ?>">
        
        <button type="submit"><i class="fas fa-search icon"></i> Filter</button>
    <?php echo form_close(); ?>

    <?php if (!empty($invalid_ref_number) && $invalid_ref_number): ?>
        <!-- Display error message for incorrect reference number -->
        <p style="color: red;">No matching record found for the given reference number.</p>
    <?php elseif (!empty($is_empty) && $is_empty): ?>
        <!-- Display message when no filter is applied -->
        <p>Please select a date range or enter a reference number to view travel details.</p>
    <?php elseif (!empty($no_travel_data_for_month) && $no_travel_data_for_month): ?>
        <!-- Display error message for no matching record found for the given date range -->
        <p style="color: red;">No matching record found for the selected date range or reference number.</p>
    <?php else: ?>
        <!-- Show table if data is available -->
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-id-badge icon"></i> Reference No</th>
                    <th><i class="fas fa-user icon"></i> Name</th>
                    <th><i class="fas fa-passport icon"></i> Passport No</th>
                    <th><i class="fas fa-calendar-alt icon"></i> Arrival Date</th>
                    <th><i class="fas fa-calendar-alt icon"></i> Departure Date</th>
                    <th><i class="fas fa-map-marker-alt icon"></i> Destination</th>
                    <th><i class="fas fa-box icon"></i> Material Name</th>
                    <th><i class="fas fa-money-bill icon"></i> Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($passenger_info && $travel_info): ?>
                    <tr>
                        <td><?= htmlspecialchars($passenger_info->ref_number) ?></td>
                        <td class="passenger-name"><?= htmlspecialchars($passenger_info->fullName) ?></td>
                        <td><?= htmlspecialchars($passenger_info->passport_no ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($travel_info->arrival_date ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($travel_info->departure_date ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($travel_info->destination_country ?? 'N/A') ?></td>

                        <?php if (!empty($materials)): ?>
                            <?php foreach ($materials as $material): ?>
                                <td><?= htmlspecialchars($material->name ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars(($material->totalValue ? $material->totalValue . ' ' . $material->currency_code : "N/A")) ?></td>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <td colspan="2">No materials available</td>
                        <?php endif; ?>
                    </tr>
                <?php else: ?>
                    <!-- If no data available for the given filter criteria -->
                    <tr>
                        <td colspan="9">No data available for the given filter criteria.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
