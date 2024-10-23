<?php
session_start();

// Check if the user is logged in and is a requester
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'requester') {
    header('Location: login.html');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the requester ID from the session
$requester_id = $_SESSION['user_id'];

// Fetch all requests made by this requester
$sql = "SELECT r.item_type, r.quantity, r.status, r.created_at, d.item_type AS donation_item_type, d.quantity AS donation_quantity, d.status AS donation_status
        FROM requests r
        LEFT JOIN donations d ON r.item_type = d.item_type AND d.status = 'in_progress'
        WHERE r.requester_id = '$requester_id' ORDER BY r.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Requests</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/img/backgrounddon.webp'); /* Background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: white;
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            border-radius: 10px;
        }

        h2 {
            color: #0056b3;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #0056b3;
            color: white;
        }

        td {
            color: #333;
        }

        .highlight {
            font-weight: bold;
            color: #28a745;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-primary:hover {
            background-color: #003d80;
            border-color: #003d80;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Track Your Requests</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table">
                <tr>
                    <th>Item Type</th>
                    <th>Requested Quantity</th>
                    <th>Status</th>
                    <th>Linked Donation</th>
                    <th>Date Requested</th>
                </tr>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td class="highlight"><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['donation_item_type']): ?>
                            <span class="highlight">Linked Donation: <?php echo htmlspecialchars($row['donation_item_type']); ?></span>
                        <?php else: ?>
                            No linked donation
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php else: ?>
            <p class="text-center">No requests found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>


