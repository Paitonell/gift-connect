<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details from the session
$user_name = htmlspecialchars($_SESSION['name']);
$email = htmlspecialchars($_SESSION['email']);
$mobile = htmlspecialchars($_SESSION['mobile'] ?? 'Not provided'); // Assuming the mobile is stored in session
$role = htmlspecialchars($_SESSION['role']);

// CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
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
            z-index: 2;
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
            margin-left: 260px; /* To make space for the sidebar */
            padding: 20px;
            width: calc(100% - 260px);
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #4A90E2;
            color: white;
            border-radius: 10px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            height: 40px; /* Adjust the height of your logo */
            margin-right: 10px; /* Space between logo and text */
        }

        .logo-section h3 {
            font-size: 1.8em;
            color: white;
            margin: 0;
        }

        .user-info {
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .profile-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .profile-container h3 {
            margin-bottom: 20px;
        }

        .profile-details {
            margin-bottom: 10px;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <div class="logo-section">
            <img src="assets/img/logo.webp" alt="Gift Connect Logo" class="logo"> <!-- Add the logo here -->
            <h3>Gift Connect</h3>
        </div>
        <a href="#" onclick="loadContent('dashboard')"><i class="fas fa-home"></i> Dashboard</a>

        <?php if ($role === 'donor'): ?>
            <a href="#" onclick="loadContent('new_donation')"><i class="fas fa-plus-circle"></i> New Donation</a>
            <a href="#" onclick="loadContent('view_donations')"><i class="fas fa-eye"></i> View Donations</a>
            <a href="#" onclick="loadContent('track_donations')"><i class="fas fa-shipping-fast"></i> Track Donations</a>
        <?php elseif ($role === 'requester'): ?>
            <a href="#" onclick="loadContent('new_request')"><i class="fas fa-plus-circle"></i> New Request</a>
            <a href="#" onclick="loadContent('view_request')"><i class="fas fa-eye"></i> View Requests</a>
            <a href="#" onclick="loadContent('track_request')"><i class="fas fa-shipping-fast"></i> Track Requests</a>
        <?php endif; ?>

        <a href="#" onclick="loadContent('profile')"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php?csrf_token=<?php echo $csrf_token; ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content Section -->
    <div class="main-content" id="main-content">
        <div class="profile-container">
            <h3>Your Profile</h3>
            <p class="profile-details"><strong>Name:</strong> <?php echo htmlspecialchars($user_name); ?></p>
            <p class="profile-details"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p class="profile-details"><strong>Mobile:</strong> <?php echo htmlspecialchars($mobile); ?></p>
            <p class="profile-details"><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>

            <!-- Edit Profile Button -->
            <a href="edit_profile.php?csrf_token=<?php echo $csrf_token; ?>" class="btn-custom">Edit Profile</a>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- AJAX-based content loading -->
    <script>
        function loadContent(page) {
            const allowedPages = ['dashboard', 'new_donation', 'view_donations', 'track_donations', 'new_request', 'view_request', 'track_request', 'profile'];
            if (!allowedPages.includes(page)) {
                document.getElementById('main-content').innerHTML = '<p>Error loading content.</p>';
                return;
            }

            let xhr = new XMLHttpRequest();
            xhr.open("GET", page + ".php", true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    document.getElementById('main-content').innerHTML = xhr.responseText;
                } else {
                    document.getElementById('main-content').innerHTML = '<p>Error loading content.</p>';
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
