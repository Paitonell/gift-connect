<?php 
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Function to send reset email (dummy function)
function send_reset_email($email, $token) {
    // Placeholder for real email functionality (e.g., PHPMailer)
    return true;  // Simulating email sent successfully
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.location.href='forgot_password.html';</script>";
        exit();
    }

    // Prepare SQL statement to select user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Error preparing the statement: " . $conn->error);  // Improved error reporting
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, generate token
        $token = bin2hex(random_bytes(50));  // Generate a secure random token
        $expiry_time = date("Y-m-d H:i:s", strtotime('+1 hour'));  // Token expires in 1 hour

        // Store token and expiration time in the database
        $stmt_token = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        if ($stmt_token === false) {
            die("Error preparing token statement: " . $conn->error);  // Improved error reporting
        }

        $stmt_token->bind_param("sss", $token, $expiry_time, $email);

        if ($stmt_token->execute()) {
            if (send_reset_email($email, $token)) {
                echo "<script>alert('A password reset link has been sent to your email.'); window.location.href='login.php';</script>";
            } else {
                echo "<script>alert('Failed to send reset email. Please try again later.'); window.location.href='forgot_password.html';</script>";
            }
        } else {
            echo "<script>alert('Error saving token. Please try again.'); window.location.href='forgot_password.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found.'); window.location.href='forgot_password.html';</script>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            width: 100%;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Forgot Password</h2>
        <p class="text-center">Enter your email to reset your password</p>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn btn-custom">Submit</button>
        </form>
    </div>
</body>
</html>
