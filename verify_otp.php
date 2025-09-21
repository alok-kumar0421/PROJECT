<?php
session_start();
include 'db_connection.php';

$phone = $_POST['phone'];
$role = $_POST['role'];
$otp = $_POST['otp'];

// Check OTP
$result = $conn->query("SELECT * FROM otp_table 
WHERE phone='$phone' AND role='$role' AND otp_code='$otp' 
AND verified=0 AND expiry > NOW()");

if ($result->num_rows == 0) {
    die("Invalid or expired OTP");
}

// Mark OTP as used
$conn->query("UPDATE otp_table SET verified=1 WHERE phone='$phone' AND role='$role'");

// Get user ID
$table = ($role=='user') ? 'users' : (($role=='driver') ? 'drivers' : 'authorities');
$user = $conn->query("SELECT * FROM $table WHERE phone='$phone'")->fetch_assoc();

// Create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $role;


// Respond with success and redirect URL
$redirect = ($role=='user') ? 'passenger.php' : (($role=='driver') ? 'driver_panel.php' : 'authority.php');
echo json_encode(["success"=>true, "redirect"=>$redirect]);
exit;
?>
