<?php
session_start();

// Check if the user is logged in and is a donor
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'donor') {
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";  // Default password for XAMPP
$dbname = "donation_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the donor ID from the session
$donor_id = $_SESSION['user_id'];

// Fetch all donations made by this donor, along with any linked requests, ordered by creation date
$sql = "SELECT d.item_type, d.quantity, d.status AS donation_status, d.created_at, 
               r.item_type AS request_item_type, r.status AS request_status
        FROM donations d
        LEFT JOIN requests r ON d.item_type = r.item_type
        WHERE d.donor_id = '$donor_id'
        ORDER BY d.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Donations - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/img/backgrounddon.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #f0f4f7;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #dee2e6;
            padding: 15px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            background-color: #f8f9fa;
        }

        .no-donations {
            text-align: center;
            color: #dc3545;
            font-size: 18px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .highlight {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Track Your Donations</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <tr>
                    <th>Item Type</th>
                    <th>Quantity</th>
                    <th>Donation Status</th>
                    <th>Linked Request</th>
                    <th>Date Donated</th>
                </tr>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['donation_status']); ?></td>
                    <td>
                        <?php if ($row['request_item_type']): ?>
                            Linked to Request (<?php echo htmlspecialchars($row['request_status']); ?>): <?php echo htmlspecialchars($row['request_item_type']); ?>
                        <?php else: ?>
                            Not yet linked
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
                <?php } ?>
            </table>
        <?php else: ?>
            <p class="no-donations">No donations found.</p>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
$conn->close();
?>


