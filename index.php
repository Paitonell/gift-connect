<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Gift Connect</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('assets/img/background.jpg'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: Arial, sans-serif;
            color: white;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9); 
            border-radius: 10px;
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
        }

        .logo-section img {
            height: 50px;
            margin-right: 15px;
        }

        .logo-section h1 {
            font-size: 1.8em;
            margin: 0;
            color: white;
            font-weight: bold;
        }

        .header nav a {
            color: white;
            margin-left: 20px;
            font-weight: bold;
            text-decoration: none;
        }

        .hero-section {
            background-color: #4A90E2; 
            color: white;
            padding: 60px 20px;
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
            margin-bottom: 30px;
        }

        .btn-custom {
            background-color: #28a745;
            color: white;
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 5px;
            text-decoration: none;
            margin: 5px;
        }

        .btn-custom:hover {
            background-color: #218838;
        }

        .feature-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .feature {
            background-color: white;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            width: 30%;
        }

        .feature h4 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #0056b3;
        }

        .feature p {
            color: #666;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <header class="header">
        <div class="logo-section">
            <img src="assets/img/logo.webp" alt="Gift Connect Logo">
            <h1>Gift Connect</h1>
        </div>
        <nav>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h2>Welcome to Gift Connect</h2>
            <p>Connect surplus resources with communities in need and make a difference today.</p>
            <div>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <a href="dashboard.php" class="btn-custom">Go to Dashboard</a>
                <?php else: ?>
                    <a href="register.php" class="btn-custom">Join Us</a>
                    <a href="login.php" class="btn-custom">Login</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Features Section -->
        <div class="feature-section">
            <div class="feature">
                <h4>About</h4>
                <p>Gift Connect is a transformative donation platform that not only connects donors with those in need but also plays a crucial role in reducing waste, especially food waste. Our platform is designed to make giving easier, transparent, and more impactful, while also addressing the growing issue of waste management by redirecting surplus resources, including food, to those who can benefit from them.</p>
            </div>
            <div class="feature">
                <h4>Mission</h4>
                <p>Our mission is to create a world where giving is seamless, transparent, and impactful, while reducing waste, particularly food waste. We aim to empower individuals and organizations to contribute excess goods and food to those in need, turning potential waste into valuable resources for communities.</p>
            </div>
            <div class="feature">
                <h4>Vision</h4>
                <p>To be the global leader in connecting surplus resources, including food, to communities that need them. We envision a future where waste, especially food waste, is minimized, and every donor—whether an individual or an organization—can make meaningful contributions that reduce waste and transform lives.</p>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>&copy; 2024 Gift Connect</p>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
