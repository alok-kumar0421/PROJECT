<?php
$host = "localhost";
$user = "root";
$pass = ""; // default XAMPP
$db = "smartbustracker";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>