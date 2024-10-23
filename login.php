<?php
session_start();

// Display all PHP errors (for development only, disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";  // Default username for XAMPP
$password = "";  // Default password for XAMPP
$dbname = "donation_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, full_name, email, password, mobile_number, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password with the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // Store user details in session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['mobile'] = $user['mobile_number'];  
            $_SESSION['role'] = $user['role'];

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();
        } else {
            $error_msg = "Invalid password. Please try again.";
        }
    } else {
        $error_msg = "Email not found. Please try again.";
    }

    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f1f2f6;
            margin: 0;
        }

        .login-container {
            display: flex;
            width: 900px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-left {
            background-color: #4A90E2;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 50%;
        }

        .login-left h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .login-left p {
            font-size: 1.2rem;
            margin-bottom: 40px;
        }

        .login-left .btn-signup {
            background-color: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 25px;
            text-align: center;
            text-decoration: none;
            font-size: 1.1rem;
            width: fit-content;
            align-self: center;
        }

        .login-left .btn-signup:hover {
            background-color: #fff;
            color: #3DB39E;
            transition: 0.3s;
        }

        .login-right {
            padding: 40px;
            width: 50%;
        }

        .login-right h3 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .btn-login {
            width: 100%;
            background-color: #4A90E2;
            color: white;
            padding: 10px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
        }

        .btn-login:hover {
            background-color: #34A48A;
            transition: 0.3s;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Left Section (Create Account) -->
    <div class="login-left">
        <h2>Welcome Back!</h2>
        <p>To keep connected with us, please log in with your personal info.</p>
        <a href="register.php" class="btn-signup">SIGN UP</a>
    </div>

    <!-- Right Section (Login Form) -->
    <div class="login-right">
        <h3>Log In</h3>

        <!-- Display error message if any -->
        <?php if (!empty($error_msg)): ?>
            <div class="error">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-login">LOGIN</button>

            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        </form>
    </div>
</div>

</body>
</html>


