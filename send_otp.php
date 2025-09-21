<?php
session_start();
include 'db_connection.php';

$phone = $_POST['phone'];
$role = $_POST['role'];

// Determine which table to check
$table = '';
if ($role == 'user') $table = 'users';
elseif ($role == 'driver') $table = 'drivers';
elseif ($role == 'authority') $table = 'authorities';

// Check if phone exists in table
$result = $conn->query("SELECT * FROM $table WHERE phone='$phone'");

if ($role == 'user' && $result->num_rows == 0) {
    // Auto-register new user
    $conn->query("INSERT INTO users (phone) VALUES ('$phone')");
} elseif ($result->num_rows == 0) {
    die("Phone number not registered for role: $role");
}

// Generate 6-digit OTP
$otp = rand(100000, 999999);
date_default_timezone_set('Asia/Kolkata');
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

// Insert OTP in DB
$conn->query("INSERT INTO otp_table (phone, otp_code, expiry, role) VALUES ('$phone','$otp','$expiry','$role')");

// Display OTP for testing
echo "OTP for $phone is: $otp <br> Use this in verify_otp.php";
?>
<a href="index.php">Back to Login</a>
