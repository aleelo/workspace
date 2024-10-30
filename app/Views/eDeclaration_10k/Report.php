<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern 10K Arrival Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #007bff;
            margin-bottom: 20px;
            font-weight: bold;
        }
        form {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        input[type="date"], input[type="text"] {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        input[type="date"]:focus, input[type="text"]:focus {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
            outline: none;
        }
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        button:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
        }
        button:active {
            transform: translateY(0);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #f0f0f0;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .icon {
            margin-right: 8px;
            color: #007bff;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            form {
                flex-direction: column;
                align-items: stretch;
            }
            input[type="date"], input[type="text"], button {
                width: 100%;
            }
            table {
                font-size: 14px;
            }
            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <h2><i class="fas fa-plane-arrival icon"></i> Destination Details Report</h2>

    <!-- Date Filter Form -->
    <?php echo form_open(get_uri("edeclaration_10k/arriving10k_details1"), array("id" => "arrival-form")); ?>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date ?? '') ?>" placeholder="Start Date">
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date ?? '') ?>" placeholder="End Date">
        <input type="text" id="ref_number" name="ref_number" placeholder="Enter Reference Number" value="<?= htmlspecialchars($ref_number ?? '') ?>">
        <button type="submit"><i class="fas fa-search icon"></i> Filter</button>
    <?php echo form_close(); ?>

    <?php if (!empty($invalid_ref_number) && $invalid_ref_number): ?>
        <p style="color: red;">No matching record found for the given reference number.</p>
    <?php elseif (!empty($is_empty) && $is_empty): ?>
        <p>Please select a date range or enter a reference number to view travel details.</p>
    <?php elseif (!empty($no_travel_data_for_month) && $no_travel_data_for_month): ?>
        <p style="color: red;">No matching record found for the selected date range or reference number.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-id-badge icon"></i>Reference_No</th>
                    <th><i class="fas fa-user icon"></i>Name</th>
                    <th><i class="fas fa-passport icon"></i>PassportNo</th>
                    <th><i class="fas fa-calendar-alt icon"></i>Arrival Date</th>
                    <th><i class="fas fa-calendar-alt icon"></i>Departure Date</th>
                    <th><i class="fas fa-map-marker-alt icon"></i>Destination</th>
                    <th><i class="fas fa-box icon"></i>Material Name</th>
                    <th><i class="fas fa-money-bill icon"></i>Amount</th>
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
                    <tr>
                        <td colspan="9">No data available for the given filter criteria.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>
</html>
