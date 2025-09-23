<?php
session_start();
include 'db_connection.php';

$phone = $_POST['phone'];
$role = $_POST['role'];

// Role-based login
if ($role == 'user') {
    // Check in users table
    $result = $conn->query("SELECT * FROM users WHERE phone='$phone'");
    if ($result->num_rows == 0) {
        die("User not Registered. Sign up first.");
    }
}
elseif ($role == 'driver') {
    // Check user exists
    $userResult = $conn->query("SELECT * FROM users WHERE phone='$phone'");
    if ($userResult->num_rows == 0) {
        die("Phone number not registered. Please sign up first.");
    }
    $user = $userResult->fetch_assoc();
    $user_id = $user['id'];

    // Check if this user_id is also a driver
    $driverResult = $conn->query("SELECT * FROM drivers WHERE user_id='$user_id'");
    if ($driverResult->num_rows == 0) {
        die("This account is not registered as a driver. Contact authority.");
    }
}
elseif ($role == 'authority') {
    // Similar check for authority table (assuming phone column exists there)
    $result = $conn->query("SELECT * FROM authorities WHERE phone='$phone'");
    if ($result->num_rows == 0) {
        die("Phone number not registered for role: authority. Contact admin.");
    }
}
else {
    die("Invalid role selected.");
}

// If passed checks → generate OTP
$otp = rand(100000, 999999);
date_default_timezone_set('Asia/Kolkata');
$expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

$conn->query("INSERT INTO otp_table (phone, otp_code, expiry, role) VALUES ('$phone','$otp','$expiry','$role')");

// For now show OTP directly
//echo "OTP for $phone is: $otp <br> Use this in verify_otp.php";


// Fetch user details (name + email) from users table
$userDetails = $conn->query("SELECT name, email FROM users WHERE phone='$phone'")->fetch_assoc();
$userName  = $userDetails['name'];
$userEmail = $userDetails['email'];

// Your Brevo API Key
$apiKey = getenv('BREVO_API_KEY');

// Your template ID from Brevo
$templateId = 1; 

// Prepare payload
$data = [
    "to" => [
        [
            "email" => $userEmail,
            "name"  => $userName
        ]
    ],
    "templateId" => $templateId,
    "params" => [
        "name" => $userName,
        "otp"  => $otp
    ]
];

// Send email via Brevo API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.brevo.com/v3/smtp/email");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "accept: application/json",
    "api-key: $apiKey",
    "content-type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 201) {
    echo "OTP sent to $userEmail!";
} else {
    echo "Failed to send OTP. Response: $response";
}
