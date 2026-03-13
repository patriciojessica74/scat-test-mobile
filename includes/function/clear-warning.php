<?php
// 1. Include config for database connection
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';
session_start();

// 2. Security: Ensure only logged-in Admins can perform this action
if (!isset($_SESSION['user_id']) || $_SESSION['usertype'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

if (isset($_POST['user_id'])) {
    $uid = mysqli_real_escape_string($conn, $_POST['user_id']);

    // 3. THE FRESH START: Reset counts to 0 and set status to OPEN
    $sql = "UPDATE attendance_stats 
            SET status_lock = 'OPEN', 
                absent_count = 0, 
                late_count = 0 
            WHERE user_id = '$uid' AND status_lock = 'LOCKED'";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No User ID provided.']);
}
?>