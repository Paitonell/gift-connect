<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    echo "<script>alert('You need to be logged in to update the status.'); window.location.href='login.php';</script>";
    exit();
}

$role = $_SESSION['role'];

// Validate user roles
if ($role !== 'admin' && $role !== 'requester' && $role !== 'donor') {
    echo "<script>alert('You do not have permission to update the status.'); window.location.href='dashboard.php';</script>";
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

// Check if ID, type (request or donation), and status are provided
if (isset($_GET['id']) && isset($_GET['type']) && isset($_GET['status'])) {
    $item_id = intval($_GET['id']);
    $item_type = $_GET['type'];
    $new_status = $_GET['status'];

    // Validate the item type and the new status
    $allowed_item_types = ['request', 'donation'];
    $allowed_statuses = ['pending', 'in_progress', 'completed'];

    if (!in_array($item_type, $allowed_item_types)) {
        echo "<script>alert('Invalid item type provided.'); window.location.href='dashboard.php';</script>";
        exit();
    }

    if (!in_array($new_status, $allowed_statuses)) {
        echo "<script>alert('Invalid status provided.'); window.location.href='dashboard.php';</script>";
        exit();
    }

    // Prepare SQL statement based on whether it's a request or donation
    if ($item_type === 'request') {
        // Only requester or admin can update a request status
        if ($role !== 'requester' && $role !== 'admin') {
            echo "<script>alert('You do not have permission to update request status.'); window.location.href='view_requests.php';</script>";
            exit();
        }

        // Update request status
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        if ($stmt === false) {
            echo "<script>alert('Error preparing statement: " . $conn->error . "'); window.location.href='view_requests.php';</script>";
            exit();
        }
        $stmt->bind_param("si", $new_status, $item_id);

        if ($stmt->execute()) {
            echo "<script>alert('Request status updated successfully!'); window.location.href='view_request.php';</script>";
        } else {
            echo "<script>alert('Error updating request status: " . $stmt->error . "'); window.location.href='view_requests.php';</script>";
        }
    } elseif ($item_type === 'donation') {
        // Only donor or admin can update a donation status
        if ($role !== 'donor' && $role !== 'admin') {
            echo "<script>alert('You do not have permission to update donation status.'); window.location.href='view_donations.php';</script>";
            exit();
        }

        // Update donation status
        $stmt = $conn->prepare("UPDATE donations SET status = ? WHERE id = ?");
        if ($stmt === false) {
            echo "<script>alert('Error preparing statement: " . $conn->error . "'); window.location.href='view_donations.php';</script>";
            exit();
        }
        $stmt->bind_param("si", $new_status, $item_id);

        if ($stmt->execute()) {
            echo "<script>alert('Donation status updated successfully!'); window.location.href='view_donations.php';</script>";
        } else {
            echo "<script>alert('Error updating donation status: " . $stmt->error . "'); window.location.href='view_donations.php';</script>";
        }
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request. ID, type, or status not provided.'); window.location.href='dashboard.php';</script>";
}

$conn->close();
?>


