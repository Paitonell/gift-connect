<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Fetch user info from session
$user_name = $_SESSION['name'];
$role = $_SESSION['role']; // Assuming this stores whether the user is a 'donor' or 'requester'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gift Connect</title>
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
            width: 250px; /* Fixed width for sidebar */
            height: 100%;
            background-color: #4A90E2;
            padding-top: 20px;
            color: white;
            display: flex;
            flex-direction: column;
            z-index: 2; /* Ensures sidebar is always on top */
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
            margin-left: 250px; /* Match the sidebar width to prevent shifting */
            padding: 20px;
            width: calc(100% - 250px); /* Ensure it takes the rest of the space */
            box-sizing: border-box;
            transition: margin-left 0.3s ease; /* Smooth transition */
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

        /* Sidebar styling */
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
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .user-info i {
            margin-right: 10px;
            font-size: 5em;
        }

        .hero-section {
            background-color: #4A90E2;
            color: white;
            padding: 40px 20px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .hero-section h2 {
            font-size: 3em;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2em;
        }

        /* Quick Action and Impact Stats */
        .quick-actions {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .action-card, .stat-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 30%;
            transition: transform 0.3s ease;
        }

        .action-card:hover, .stat-card:hover {
            transform: scale(1.05);
        }

        .action-card i, .stat-card i {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #4A90E2;
        }

        /* Impact Stats */
        .impact-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .impact-card {
            position: relative;
            width: 30%;
            height: 200px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .impact-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }

        .impact-card .content {
            z-index: 1;
        }

        .impact-card.donations {
            background-image: url('assets/img/donations.jpg');
        }

        .impact-card.requests {
            background-image: url('assets/img/request.jpg');
        }

        .impact-card.help {
            background-image: url('assets/img/help.jpg'); /* Corrected the image extension */
        }

        .btn-custom {
            background-color: #4A90E2;
            color: white;
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
        }

        .btn-custom:hover {
            background-color: transparent;
        }

        .logout {
            margin-top: auto;
            padding: 15px;
        }

        .logout a {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            display: block;
            text-align: center;
        }

        .logout a:hover {
            background-color: #c82333;
        }

        /* Table Styling */
        .table-container {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th, .table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .table th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <div class="logo-section">
            <img src="assets/img/logos.png" alt="Gift Connect Logo" class="logo"> <!-- Add the logo here -->
            <h3>Gift Connect</h3>
        </div>
        <a href="#" onclick="loadContent('dashboard')"><i class="fas fa-home"></i> Dashboard</a>

        <!-- Check user role to display relevant options -->
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

        <!-- Logout Section -->
        <div class="logout">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <div id="main-content" class="content-section">
            <h2>Welcome, <?php echo htmlspecialchars($user_name); ?>!</h2>

            <!-- Quick Action Cards -->
            <div class="quick-actions">
                <?php if ($role === 'donor'): ?>
                    <div class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <a href="new_donation.php" class="btn-custom">Create New Donation</a>
                    </div>
                    <div class="action-card">
                        <i class="fas fa-eye"></i>
                        <a href="view_donations.php" class="btn-custom">View Donations</a>
                    </div>
                    <div class="action-card">
                        <i class="fas fa-shipping-fast"></i>
                        <a href="track_donations.php" class="btn-custom">Track Donations</a>
                    </div>
                <?php elseif ($role === 'requester'): ?>
                    <div class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <a href="new_request.php" class="btn-custom">Create New Request</a>
                    </div>
                    <div class="action-card">
                        <i class="fas fa-eye"></i>
                        <a href="view_request.php" class="btn-custom">View Requests</a>
                    </div>
                    <div class="action-card">
                        <i class="fas fa-shipping-fast"></i>
                        <a href="track_request.php" class="btn-custom">Track Requests</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Impact Counters -->
            <div class="impact-stats">
                <div class="impact-card donations">
                    <div class="impact-overlay"></div>
                    <div class="content">100+ Donations</div>
                </div>
                <div class="impact-card requests">
                    <div class="impact-overlay"></div>
                    <div class="content">75+ Requests</div>
                </div>
                <div class="impact-card help">
                    <div class="impact-overlay"></div>
                    <div class="content">Global Impact</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>

    <!-- AJAX-based content loading -->
    <script>
        function loadContent(page) {
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
