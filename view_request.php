<?php
session_start();

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

// Fetch all requests made by this requester, ordered by creation date
$sql = "SELECT r.*, d.item_type AS donation_item_type, d.quantity AS donation_quantity, d.status AS donation_status 
        FROM requests r 
        LEFT JOIN donations d ON r.item_type = d.item_type AND d.status = 'pending'
        WHERE requester_id = '$requester_id' ORDER BY r.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Requests</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/img/backgrounddon.webp'); /* Replace with your background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            margin-top: 200px;
            background-color: rgba(255, 255, 255, 0.95); /* Semi-transparent white background */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1); /* Soft shadow */
            max-width: 1200px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 12px;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td {
            text-align: center;
            background-color: #f8f9fa;
            font-size: 1em;
            color: #333
        }
        .highlight {
            font-weight: bold;
            color: #28a745;
        }
        .btn-back {
            margin-top: 20px;
            text-align: center;
        }
        .btn-back a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-back a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Requests</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="table">
                <tr>
                    <th>Item Type</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Linked Donation</th>
                    <th>Date Requested</th>
                    <th>Change Status</th>
                </tr>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if ($row['donation_item_type']): ?>
                            Linked with donation: <?php echo htmlspecialchars($row['donation_item_type']); ?>
                        <?php else: ?>
                            No matching donation
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td>
                        <form action="update_status.php" method="GET">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="type" value="request">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="in_progress" <?php if ($row['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                                <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        <?php else: ?>
            <p>No requests found.</p>
        <?php endif; ?>

    </div>
</body>
</html>

<?php
$conn->close();
?>

