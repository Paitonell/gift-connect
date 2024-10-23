<?php
session_start();

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_system";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Use error_log() for logging if needed
    error_log("Connection failed: " . $conn->connect_error);
    header('Location: error.php');
    exit();
}

// Initialize error messages
$errors = [];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture and sanitize form inputs
    $full_name = htmlspecialchars($_POST['full_name'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobile_number = htmlspecialchars($_POST['mobile_number'], ENT_QUOTES, 'UTF-8');
    $role = htmlspecialchars($_POST['role'], ENT_QUOTES, 'UTF-8');
    $agreed_terms = isset($_POST['agreed_terms']) ? 1 : 0;

    // Validate form inputs
    if (empty($full_name)) {
        $errors[] = "Full name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!$email) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($mobile_number)) {
        $errors[] = "Mobile number is required.";
    }
    if (empty($role)) {
        $errors[] = "Role is required.";
    }
    if (!$agreed_terms) {
        $errors[] = "You must agree to the terms and conditions.";
    }

    // Check for any existing email
    $email_check_query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $errors[] = "Email is already registered.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL query to insert data into the users table
        $sql = "INSERT INTO users (full_name, email, password, mobile_number, role, agreed_terms) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $full_name, $email, $hashed_password, $mobile_number, $role, $agreed_terms);
        $stmt->execute();

        // Redirect to login page after successful registration
        $_SESSION['message'] = "Registration successful! Please log in.";
        header('Location: login.php');
        exit();
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
    <title>Register - Gift Connect</title>
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

        .register-container {
            display: flex;
            width: 900px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .register-left {
            background-color: #4A90E2;
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 50%;
        }

        .register-left h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .register-left p {
            font-size: 1.2rem;
            margin-bottom: 40px;
        }

        .register-left .btn-signin {
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

        .register-left .btn-signin:hover {
            background-color: #fff;
            color: #3DB39E;
            transition: 0.3s;
        }

        .register-right {
            padding: 40px;
            width: 50%;
        }

        .register-right h3 {
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

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-check input {
            margin-right: 10px;
        }

        .btn-signup {
            width: 100%;
            background-color: #4A90E2;
            color: white;
            padding: 10px;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
        }

        .btn-signup:hover {
            background-color: #34A48A;
            transition: 0.3s;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="register-container">
    <!-- Left Section (Sign In) -->
    <div class="register-left">
        <h2>Welcome Back!</h2>
        <p>To keep connected with us, please log in with your personal info.</p>
        <a href="login.php" class="btn-signin">SIGN IN</a>
    </div>
    <script>
const para = document.createElement("p");
const node = document.createTextNode("This is new.");
para.appendChild(node);
const element = document.getElementById("div class=register-left");
element.appendChild(para);
</script>

    <!-- Right Section (Create Account) -->
    <div class="register-right">
        <h3>Create Account</h3>

        <!-- Display errors if any -->
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="form-group">
                <input type="text" id="full_name"  name="full_name" placeholder="Full Name" value="<?php echo htmlspecialchars($full_name ?? ''); ?>" Required>
            </div>

            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email Address" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            </div>

            <div class="form-group">
                <input type="text" id="mobile_number" name="mobile_number" placeholder="Mobile Number" required>
            </div>

            <div class="form-group">
                <select id="role" name="role" required>
                    <option value="">Select a role</option>
                    <option value="donor" <?php echo ($role ?? '') == 'donor' ? 'selected' : ''; ?>>Donor</option>
                    <option value="requester" <?php echo ($role ?? '') == 'requester' ? 'selected' : ''; ?>>Requester</option>
                </select>
            </div>

            <div class="form-check">
                <input type="checkbox" id="agreed_terms" name="agreed_terms" <?php echo isset($agreed_terms) && $agreed_terms ? 'checked' : ''; ?>>
                <label for="agreed_terms">I agree to the <a href="#">terms and conditions</a></label>
            </div>

            <button type="submit" class="btn-signup">SIGN UP</button>
        </form>
    </div>
</div>

</body>
</html>


