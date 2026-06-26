<?php
// ============================================
// config.php - Database Configuration
// ============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session only if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'wolf_turtle_db';

// Create connection with error handling
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("<h2 style='color:red; text-align:center; margin-top:50px;'>⚠️ Database Connection Failed!</h2>
         <p style='text-align:center;'>Please make sure XAMPP is running and MySQL is started.<br>
         Error: " . $conn->connect_error . "</p>");
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Kuala_Lumpur');

// ============================================
// FUNCTION: Generate 6-digit Staff ID (Unique)
// ============================================
function generateStaffID($conn) {
    do {
        $random = rand(100000, 999999);
        $check = $conn->query("SELECT id FROM users WHERE staff_id = '$random'");
        $exists = $check && $check->num_rows > 0;
    } while ($exists);
    return $random;
}

// ============================================
// FUNCTION: Generate Order Number
// ============================================
function generateOrderNumber() {
    return 'WT' . date('Ymd') . rand(100, 999);
}

// ============================================
// FUNCTION: Redirect
// ============================================
function redirect($url) {
    header("Location: $url");
    exit();
}

// ============================================
// FUNCTION: Check Login
// ============================================
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'staff';
}
?>