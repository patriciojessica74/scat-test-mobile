<?php
// 1. Include the config at the very top using ROOT_PATH logic
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php'; 
session_start();

// Headers for JSON response
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['reset_user'])) {
    echo json_encode(["status" => "error", "message" => "Session missing. Please request a new code."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim to ensure no empty spaces are saved as passwords
    $new_pass = isset($_POST['password']) ? trim($_POST['password']) : '';
    $user_id = $_SESSION['reset_user']['user_id'];

    if (empty($new_pass)) {
        echo json_encode(["status" => "error", "message" => "Password cannot be empty."]);
        exit;
    }

    if (strlen($new_pass) < 8) {
        echo json_encode(["status" => "error", "message" => "Password must be at least 8 characters."]);
        exit;
    }

    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

    // 2. The $conn variable is provided by config.php
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        // Success! Clear the session so the reset flow is finalized
        unset($_SESSION['reset_user']);
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }
    
    $stmt->close();
    $conn->close();
    exit;
}