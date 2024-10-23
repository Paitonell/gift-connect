<?php
session_start();

// Check if the user is logged in
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

$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form inputs
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];

    // Fetch the current password from the database
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if new password matches confirm password
        if ($new_password === $confirm_password) {
            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $sql_update = "UPDATE users SET password='$hashed_new_password' WHERE id='$user_id'";
            if ($conn->query($sql_update) === TRUE) {
                echo "<script>alert('Password updated successfully!'); window.location.href='dashboard.php';</script>";
            } else {
                $error_msg = "Error updating password: " . $conn->error;
            }
        } else {
            $error_msg = "New password and confirmation do not match.";
        }
    } else {
        $error_msg = "Current password is incorrect.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(0, 86, 179, 0.9); /* Slightly transparent blue */
            padding: 15px;
            border-radius: 10px 10px 0 0;
            color: white;
        }
        .form-group input, .form-group button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Reset Password</h2>

        <!-- Display error message if any -->
        <?php if (!empty($error_msg)): ?>
            <div class="error">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Reset Password Form -->
        <form action="reset_password.php" method="POST">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>

</body>
</html>


