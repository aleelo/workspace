<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Information Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            font-size: 1.5em;
            display: flex;
            align-items: center;
        }

        h3 i {
            margin-right: 10px;
        }

        .section {
            margin-bottom: 25px;
        }

        .info-table, .materials-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .info-table tr, .materials-table tr {
            border-bottom: 1px solid #ddd;
        }

        .info-table td, .materials-table th, .materials-table td {
            padding: 15px;
            text-align: left;
        }

        .info-table td {
            padding: 15px;
            text-align: left;
            font-size: 1.1em;
        }

        .info-table th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            font-size: 1.2em;
            background-color: #f7f7f7;
        }

        .materials-table th {
            font-weight: bold;
            font-size: 1.2em;
            background-color: #f7f7f7;
        }

        .info-table td {
            display: flex;
            align-items: center;
        }

        .info-table td i {
            margin-right: 10px;
            color: #007bff;
        }

        .action-icon {
            cursor: pointer;
            font-size: 1.5em;
            margin: 0 8px;
        }

        .approve-icon {
            color: #28a745;
        }
        .action-buttons {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
        }
        .action-buttons i {
            cursor: pointer;
            font-size: 1.8em;
            padding: 8px 12px;
            border-radius: 5px;
            transition: transform 0.2s ease-in-out;
        }
        .approve-icon {
            color: #28a745;
            background-color: #e6f4ea;
        }
        .approve-icon:hover {
           
            color: #fff;
            transform: scale(1.1);
        }
        .reject-icon {
            color: #dc3545;
            background-color: #fce4e4;
        }
        .reject-icon:hover {
           
            color: #fff;
            transform: scale(1.1);
        }

        .approve-icon:hover {
            color: #218838;
        }

        .reject-icon {
            color: #dc3545;
        }

        .reject-icon:hover {
            color: #c82333;
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
        <a href="#" class="back-button"><i class="fas fa-arrow-left"></i> Back</a>

        <div class="section">
            <h3><i class="fas fa-user"></i> Person Information:</h3>
            <table class="info-table">
                <tr>
                    <th><i class="fas fa-id-badge"></i> Full Name</th>
                    <td><?php echo isset($passenger_info->fullName) ? $passenger_info->fullName : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-venus-mars"></i> Gender</th>
                    <td><?php echo isset($passenger_info->gender) ? $passenger_info->gender : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-passport"></i> Passport No</th>
                    <td><?php echo isset($passenger_info->passport_no) ? $passenger_info->passport_no : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-city"></i> City</th>
                    <td><?php echo isset($passenger_info->city) ? $passenger_info->city : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-map-marker-alt"></i> Local Address</th>
                    <td><?php echo isset($passenger_info->local_address) ? $passenger_info->local_address : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-envelope"></i> Email</th>
                    <td><?php echo isset($passenger_info->email) ? $passenger_info->email : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-phone"></i> Phone</th>
                    <td><?php echo isset($passenger_info->phone) ? $passenger_info->phone : "N/A"; ?></td>
                </tr>
            </table>
        </div>
        <div class="section">
            <h3><i class="fas fa-plane"></i> Travel Information:</h3>
            <table class="info-table">
                <tr>
                    <th><i class="fas fa-suitcase-rolling"></i> Travel Type</th>
                    <td><?php echo isset($travel_info->travel_type) ? $travel_info->travel_type : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-bus"></i> Vehicle Name</th>
                    <td><?php echo isset($travel_info->vehicle_name) ? $travel_info->vehicle_name : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-alt"></i> Departure Date</th>
                    <td><?php echo isset($travel_info->departure_date) ? $travel_info->departure_date : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-alt"></i> Transit Date</th>
                    <td><?php echo isset($travel_info->transit_date) ? $travel_info->transit_date : "N/A"; ?></td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar-alt"></i> Arrival Date</th>
                    <td><?php echo isset($travel_info->arrival_date) ? $travel_info->arrival_date : "N/A"; ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h3><i class="fas fa-box"></i> Materials Information:</h3>
            <table class="materials-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-box"></i> Name</th>
                        <th><i class="fas fa-money-bill"></i> Amount</th>
                        <th><i class="fas fa-bullseye"></i> Purpose</th>
                        <th><i class="fas fa-file-alt"></i>Document</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                         
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($materials as $m) { ?>
                    <tr data-id="<?php echo $m->passenger_id; ?>">
                        <td><?php echo $m->name ? $m->name : "N/A"; ?></td>
                        <td><?php echo $m->totalValue ? $m->totalValue . ' ' . $m->currency_code : "N/A"; ?></td>
                        <td><?php echo $m->purpose ? $m->purpose : "N/A"; ?></td>
                        <td><?php echo $m->has_document ? $m->has_document : "N/A"; ?></td>
                        <td><?php echo $m->NewStatus ?></td>
                        <!-- <td class="action-buttons">
                      
                        <i class="fas fa-check-circle approve-icon" title="Approve"></i>
                        <i class="fas fa-times-circle reject-icon" title="Reject"></i>
                        </td> -->
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h3><i class="fas fa-box"></i> Status Action:</h3>
            
            <i class="fas fa-check-circle approve-icon" title="Approve"></i>
            <i class="fas fa-times-circle reject-icon" title="Reject"></i>
            
        </div>
    </div>

    <script>
        var csrfHash = '<?php echo csrf_hash(); ?>';
        
        $(document).ready(function () {
            // Event listeners for approve buttons
            $('.approve-icon').on('click', function (event) {
                event.preventDefault();
                const passengerId = "<?php echo $passenger_info?->id?>"
                alert('clicked');
                console.log("Approve clicked for ID:", passengerId); // Debugging
                updateStatus(passengerId, 'approved');
            });

            // Event listeners for reject buttons
            $('.reject-icon').on('click', function (event) {
                event.preventDefault();
                const passengerId = "<?php echo $passenger_info?->id?>";
                console.log("Reject clicked for ID:", passengerId); // Debugging
                updateStatus(passengerId, 'rejected');
            });

            function updateStatus(id, status) {
                console.log("Sending request for ID:", id, "with status:", status); // Debugging
                
                $.ajax({
                    url: '<?php echo site_url('edeclaration_100k/update_status'); ?>',
                    method: 'POST',                    
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({ id: id, status: status,'rise_csrf_token': csrfHash }),
                    success: function (data) {
                        if (data.success) {
                            alert('Status updated successfully!');
                            location.reload(); // Optionally reload page to reflect changes
                        } else {
                            alert('Error updating status: ' + data.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred while updating the status.');
                    }
                });
            }
        });
    </script>
</body>
</html>
