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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_type = mysqli_real_escape_string($conn, $_POST['item_type']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $requester_id = $_SESSION['user_id'];

    $sql = "INSERT INTO requests (requester_id, item_type, quantity, notes, status, created_at) 
            VALUES ('$requester_id', '$item_type', '$quantity', '$notes', 'pending', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Request submitted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='new_request.php';</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Request - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/img/backgrounddon.webp'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: white;
            margin: 0;
        }

        .container {
            max-width: 800px;
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

        .form-group label {
            color: #0056b3;
            font-weight: bold;
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
        <h2>Submit a New Request</h2>
        <form action="new_request.php" method="POST">
            <div class="form-group">
                <label for="item_type">Item Type</label>
                <input type="text" name="item_type" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="notes">Additional Notes</label>
                <textarea name="notes" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
        </form>
    </div>
</body>
</html>
