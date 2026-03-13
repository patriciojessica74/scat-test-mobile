<?php
// 1. Include config (this also includes connection.php)
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $year = mysqli_real_escape_string($conn, $_POST['year']);
    $section = mysqli_real_escape_string($conn, $_POST['section']);
    
    $image_update = "";

    // File Upload Logic
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $filename = "user_" . $user_id . "_" . time() . "." . $ext;
        
        // 2. Use UPLOAD_DIR (The physical path on the server)
        $destination = UPLOAD_DIR . $filename;

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
            $image_update = ", profile_pix = '$filename'";
        }
    }

    // UPDATE query using your specific column names
    $sql = "UPDATE info SET year_level = '$year', section = '$section' $image_update WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo mysqli_error($conn);
    }
}
?>