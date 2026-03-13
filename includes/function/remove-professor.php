<?php
// 1. Include the config at the very top (includes connection.php)
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php'; 
session_start();

// Auth Check: Only admins can perform this action
if (isset($_SESSION['usertype']) && $_SESSION['usertype'] === 'admin' && isset($_GET['id'])) {
    // Sanitize the ID to prevent SQL Injection
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // This will delete from 'users' (and 'info' if FK CASCADE is set up)
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id' AND usertype = 'professor'");
}

// 2. FIXED REDIRECT: Use ADMIN_URL to point to the correct folder
header("Location: " . ADMIN_URL . "admin-professor-list.php");
exit();
?>