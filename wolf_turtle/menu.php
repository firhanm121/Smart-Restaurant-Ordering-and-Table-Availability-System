<?php
// ============================================
// logout.php - Logout
// ============================================
include 'config.php';

// Destroy all session data
session_destroy();

// Redirect to login
redirect('login.php');
?>