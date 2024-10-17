<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Fetch user info from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; // Ensure role is fetched from the session

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($_POST['full_name'], ENT_QUOTES);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $mobile_number = htmlspecialchars($_POST['mobile_number'], ENT_QUOTES);

    if (!$full_name) {
        $errors[] = "Full name is required.";
    }
    if (!$email) {
        $errors[] = "Valid email is required.";
    }
    if (!$mobile_number) {
        $errors[] = "Mobile number is required.";
    }

    if (empty($errors)) {
        // Update user profile
        $sql = "UPDATE users SET full_name = ?, email = ?, mobile_number = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $full_name, $email, $mobile_number, $user_id);

        if ($stmt->execute()) {
            // Update session variables
            $_SESSION['name'] = $full_name;
            $_SESSION['email'] = $email;
            $_SESSION['mobile'] = $mobile_number;

            // Redirect back to profile page
            header('Location: profile.php');
            exit();
        } else {
            $errors[] = "Error updating profile.";
        }
    }
}

// Fetch current user details
$sql = "SELECT full_name, email, mobile_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $full_name = $user['full_name'];
    $email = $user['email'];
    $mobile_number = $user['mobile_number'];
} else {
    echo "Error fetching user information.";
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-image: url('assets/img/backgrounddon.webp');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
        }

        /* Sidebar styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100%;
            background-color: #4A90E2;
            padding-top: 20px;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar h3 {
            text-align: center;
            margin-bottom: 30px;
        }

        .sidebar a {
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
            display: block;
        }

        .sidebar a:hover {
            background-color: #357ABD;
        }

        /* Main content styles */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <h3>Gift Connect</h3>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>

        <?php if ($role === 'donor'): ?>
            <a href="new_donation.php"><i class="fas fa-plus-circle"></i> New Donation</a>
            <a href="view_donations.php"><i class="fas fa-eye"></i> View Donations</a>
        <?php elseif ($role === 'requester'): ?>
            <a href="new_request.php"><i class="fas fa-plus-circle"></i> New Request</a>
            <a href="view_request.php"><i class="fas fa-eye"></i> View Requests</a>
        <?php endif; ?>
        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <div class="container">
            <h3>Edit Your Profile</h3>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" value="<?php echo htmlspecialchars($mobile_number); ?>" required>
                </div>

                <button type="submit" class="btn-custom">Save Changes</button>
            </form>
        </div>
    </div>

</body>
</html>
