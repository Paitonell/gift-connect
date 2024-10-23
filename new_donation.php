<?php
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Database connection settings
$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";  // Default password for XAMPP
$dbname = "donation_system";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture the form data
    $item_type = mysqli_real_escape_string($conn, $_POST['item_type']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    
    // Get donor ID from session (assuming the donor is logged in)
    $donor_id = $_SESSION['user_id'];

    // SQL query to insert donation details into the donations table
    $sql = "INSERT INTO donations (donor_id, item_type, quantity, method, notes, status, created_at) 
            VALUES ('$donor_id', '$item_type', '$quantity', '$method', '$notes', 'pending', NOW())";

    // Execute the query and check for success
    if ($conn->query($sql) === TRUE) {
        // Successful submission
        echo "<script>
                alert('Donation submitted successfully!');
                window.location.href='dashboard.php';
              </script>";
    } else {
        // Error occurred during submission
        echo "<script>
                alert('Error: " . $conn->error . "');
                window.location.href='new_donation.php';
              </script>";
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Donation - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/img/backgrounddon.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 600px;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-custom {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>List a New Donation</h2>

    <form action="new_donation.php" method="POST">
        <div class="form-group">
            <label for="item_type">Item Type</label>
            <input type="text" class="form-control" id="item_type" name="item_type" required>
        </div>

        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>

        <div class="form-group">
            <label for="method">Donation Method</label>
            <select class="form-control" id="method" name="method" required>
                <option value="pickup">Pickup</option>
                <option value="drop-off">Drop-off</option>
            </select>
        </div>

        <div class="form-group">
            <label for="notes">Additional Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-custom">Submit Donation</button>
    </form>
</div>

<!-- Include Bootstrap JS -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>


