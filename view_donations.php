<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
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

// Get the donor ID from the session
$donor_id = $_SESSION['user_id'];

// Fetch all donations made by this donor, ordered by creation date
$sql = "SELECT d.*, r.item_type AS request_item_type, r.quantity AS request_quantity, r.status AS request_status 
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
    <title>View Donations - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        /* Background Image Setup */
        body {
            background-image: url('assets/img/backgrounddon.webp'); /* Replace with your background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Container for layout */
        .container {
            margin-top: 200px;
            background-color: rgba(255, 255, 255, 0.95); /* Semi-transparent white background */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1); /* Soft shadow */
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Table styling */
        table {
            width: 100%;
            margin-top: 50px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            background-color: #f8f9fa;
            font-size: 1em;
            color: #333;
        }

        /* Search and filter section */
        .search-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 60%;
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .filter-sort {
            display: flex;
            gap: 15px;
        }

        .filter-sort select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Button styling */
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
        <h2 class="text-center">Your Donations</h2>


        <?php if ($result->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Type</th>
                        <th>Quantity</th>
                        <th>Method</th>
                        <th>Linked Request</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['method']); ?></td>
                        <td>
                            <?php if ($row['request_item_type']): ?>
                                Matched with request: <?php echo htmlspecialchars($row['request_item_type']); ?>
                            <?php else: ?>
                                No matching request
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <form action="update_status.php" method="GET">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="type" value="donation">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php if ($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                    <option value="in_progress" <?php if ($row['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="completed" <?php if ($row['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No donations found.</p>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>


