<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Information Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 25px;
            border-radius: 12px;
        }

        .back-button {
            margin-bottom: 20px;
            padding: 12px 20px;
            background-color: #007bff;
            display: inline-block;
            text-decoration: none;
            color: #ffffff;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        h3 {
            color: #003399;
            border-bottom: 2px solid #e1e1e1;
            padding-bottom: 10px;
            font-size: 1.3em;
        }

        .section {
            margin-bottom: 25px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .info-table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .info-table td:first-child {
            font-weight: bold;
            background-color: #f7f7f7;
            width: 30%;
        }

        .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .materials-table th, .materials-table td {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }

        .materials-table th {
            background-color: #f7f7f7;
            font-weight: bold;
        }

        .icon {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            .info-table td, .materials-table th, .materials-table td {
                padding: 10px;
            }

            h3 {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="#" class="back-button">&larr; Back</a>

        <div class="section">
            <h3>&#128100; Person Information:</h3>
            <table class="info-table">
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#128100;</i> Full Name</td> <td><?php echo isset($passenger_info->fullName) ? $passenger_info->fullName : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#9892;</i> Gender</td> <td><?php echo isset($passenger_info->gender) ? $passenger_info->gender : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#128179;</i> Passport No</td> <td><?php echo isset($passenger_info->passport_no) ? $passenger_info->passport_no : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#127757;</i> City</td> <td><?php echo isset($passenger_info->city) ? $passenger_info->city : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#127968;</i> Local Address</td> <td><?php echo isset($passenger_info->local_address) ? $passenger_info->local_address : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#9993;</i> Email</td> <td><?php echo isset($passenger_info->email) ? $passenger_info->email : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#128222;</i> Phone</td> <td><?php echo isset($passenger_info->phone) ? $passenger_info->phone : "N/A"; ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h3>&#9992; Travel Information:</h3>
            <table class="info-table">
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#128640;</i> Travel Type</td> <td><?php echo isset($travel_info->travel_type) ? $travel_info->travel_type : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#128652;</i> Vehicle Name</td> <td><?php echo isset($travel_info->vehicle_name) ? $travel_info->vehicle_name : "N/A"; ?></td></tr>
                <tr><td><i class="icon" style="font-size: 1.2em; color: #555;">&#128197;</i> Departure Date</td> <td><?php echo isset($travel_info->departure_date) ? $travel_info->departure_date : "N/A"; ?></td></tr>
                <tr><td><i class="icon">&#128197;</i> Transit Date</td> <td><?php echo isset($travel_info->transit_date) ? $travel_info->transit_date : "N/A"; ?></td></tr>
                <tr><td><i class="icon">&#128197;</i> Arrival Date</td> <td><?php echo isset($travel_info->arrival_date) ? $travel_info->arrival_date : "N/A"; ?></td></tr>
            </table>
        </div>

        <div class="section">
            <h3>&#128230; Materials Information:</h3>
            <table class="materials-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Amount</th>
                        <th>Purpose</th>
                        <th>Has Document</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($materials as $m) { ?>
                    <tr>
                        <td><?php echo $m->name ? $m->name : "N/A"; ?></td>
                        <td><?php echo $m->totalValue ? $m->totalValue . ' ' . $m->currency_id : "N/A"; ?></td>
                        <td><?php echo $m->purpose ? $m->purpose : "N/A"; ?></td>
                        <td><?php echo $m->has_document ? $m->has_document : "N/A"; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>