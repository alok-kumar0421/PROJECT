<?php
// signup.php
session_start();
include 'db_connection.php'; // provides $conn (mysqli)

// Get POST data
//$name   = trim($_POST['name'] ?? '');
//$phone  = trim($_POST['mobile'] ?? '');  // from your signup.html input
//$email  = trim($_POST['email'] ?? '');
//$pass   = $_POST['password'] ?? '';
//$conf   = $_POST['confirm'] ?? '';
//$role   = $_POST['role'] ?? 'user';
//$driver_reg_no = trim($_POST['driverId'] ?? '');   // from signup.html field
//$bus_number    = trim($_POST['bus_number'] ?? '');

//new code mei upar wala hai but wo error de rha tha ki form khaali hai to maine niche wala code likha hai

// Get POST data (assumes form names: name, phone, email, password, confirm, role, driver_reg_no, bus_number)
 $name = isset($_POST['name']) ? trim($_POST['name']) : ''; 
 $phone = isset($_POST['phone']) ? trim($_POST['phone']) : ''; 
 $email = isset($_POST['email']) ? trim($_POST['email']) : '';
 $pass = isset($_POST['password']) ? $_POST['password'] : ''; 
 $conf = isset($_POST['confirm']) ? $_POST['confirm'] : '';
 $role = isset($_POST['role']) ? $_POST['role'] : 'user'; 
 $driver_reg_no = isset($_POST['driver_reg_no']) ? trim($_POST['driver_reg_no']) : ''; 
 $bus_number = isset($_POST['bus_number']) ? trim($_POST['bus_number']) : '';
// Basic validation
if ($name === '' || $phone === '' || $email === '' || $pass === '' || $conf === '') {
    die("All required fields must be filled.");
}
if ($pass !== $conf) {
    die("Passwords do not match.");
}
if ($role === 'driver' && $driver_reg_no === '') {
    die("Driver registration number is required for driver signup.");
}

// Escape for safety
$name = $conn->real_escape_string($name);
$phone = $conn->real_escape_string($phone);
$email = $conn->real_escape_string($email);
$driver_reg_no = $driver_reg_no ? $conn->real_escape_string($driver_reg_no) : '';
$bus_number = $bus_number ? $conn->real_escape_string($bus_number) : '';

// Hash the password
$hashed = password_hash($pass, PASSWORD_BCRYPT);

// Start transaction
$conn->begin_transaction();

try {
    // Check if a user with same phone or email already exists
    $res = $conn->query("SELECT * FROM users WHERE phone='$phone' OR email='$email'");
    if ($res === false) throw new Exception("DB error: " . $conn->error);

    if ($res->num_rows > 0) {
        // existing user
        $user = $res->fetch_assoc();
        $user_id = (int)$user['id'];

        if ($role === 'user') {
            // user already exists - ask them to login instead
            $conn->rollback();
            die("User already exists. Please login.");
        }

        // role == driver
        $drv = $conn->query("SELECT * FROM drivers WHERE user_id=$user_id");
        if ($drv === false) throw new Exception("DB error: " . $conn->error);

        if ($drv->num_rows > 0) {
            $row = $drv->fetch_assoc();

            // Case 1: reg number mismatch
            if ($row['registration_number'] !== $driver_reg_no) {
                $conn->rollback();
                die("This phone/email is already linked to another registration number (" . $row['registration_number'] . ").");
            }

            // Case 2: reg number matches → just tell them application already exists
            $status = $row['approved'] ? 'approved' : 'pending';
            $conn->rollback();
            die("Driver application already exists (status: $status).");
        }

        // also check if registration_number already used by another driver
        $chkReg = $conn->query("SELECT * FROM drivers WHERE registration_number='$driver_reg_no'");
        if ($chkReg === false) throw new Exception("DB error: " . $conn->error);
        if ($chkReg->num_rows > 0) {
            $conn->rollback();
            die("This driver registration number is already used. Contact support.");
        }

        // Insert into drivers
        $sql = "INSERT INTO drivers (user_id, registration_number, bus_number, approved) 
                VALUES ($user_id, '$driver_reg_no', '$bus_number', 0)";
        if (!$conn->query($sql)) {
            throw new Exception("DB error inserting driver: " . $conn->error);
        }

        $conn->commit();
        echo "Driver application submitted for this account. Await authority approval.";
        exit;
    } else {
        // No existing user -> create user (and possibly driver record)
        $sqlUser = "INSERT INTO users (name, phone, email, password) VALUES ('$name', '$phone', '$email', '$hashed')";
        if (!$conn->query($sqlUser)) throw new Exception("DB error inserting user: " . $conn->error);
        $user_id = (int)$conn->insert_id;

        if ($role === 'driver') {
            // check reg no unique
            $chkReg = $conn->query("SELECT * FROM drivers WHERE registration_number='$driver_reg_no'");
            if ($chkReg === false) throw new Exception("DB error: " . $conn->error);
            if ($chkReg->num_rows > 0) {
                // rollback user insert to avoid orphan user if regNo conflicts
                $conn->rollback();
                die("Driver registration number already taken. Choose a different number or contact support.");
            }

            $sqlDriver = "INSERT INTO drivers (user_id, registration_number, bus_number, approved) 
                          VALUES ($user_id, '$driver_reg_no', '$bus_number', 0)";
            if (!$conn->query($sqlDriver)) throw new Exception("DB error inserting driver: " . $conn->error);
        }

        $conn->commit();
        if ($role === 'driver') {
            echo "Signup successful. Driver application submitted and pending authority approval.";
        } else {
            echo "Signup successful. You can now login.";
        }
        exit;
    }
} catch (Exception $e) {
    $conn->rollback();
    die("Signup failed: " . $e->getMessage());
}
?>