<?php
// 1. Use absolute path for config to prevent connection errors
$config_path = $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';
if (file_exists($config_path)) {
    include_once $config_path;
} else {
    // Fallback if config isn't found
    include 'connection.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check if this is a student enrollment request (sent via AJAX from subject-view.js)
    if (isset($_POST['subject_code']) && !isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        $user_id    = $_SESSION['user_id'];
        $subject_id = mysqli_real_escape_string($conn, $_POST['subject_code']); // This is the ID from the dropdown
        $prof_id    = mysqli_real_escape_string($conn, $_POST['professor_id']);
        $day        = mysqli_real_escape_string($conn, $_POST['day']);
        $start_t    = mysqli_real_escape_string($conn, $_POST['start_time']);
        $end_t      = mysqli_real_escape_string($conn, $_POST['end_time']);
        $room       = mysqli_real_escape_string($conn, $_POST['room']);
        $semester   = mysqli_real_escape_string($conn, $_POST['semester']);

        // Check for existing enrollment to prevent duplicates
        $check = mysqli_query($conn, "SELECT id FROM subject_enrollments WHERE student_id = '$user_id' AND subject_id = '$subject_id'");
        
        if (mysqli_num_rows($check) == 0) {
            $sql = "INSERT INTO subject_enrollments (student_id, subject_id, professor_id, day, start_time, end_time, room, semester) 
                    VALUES ('$user_id', '$subject_id', '$prof_id', '$day', '$start_t', '$end_t', '$room', '$semester')";
            
            if (mysqli_query($conn, $sql)) {
                echo json_encode(["status" => "success"]);
            } else {
                echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "You are already enrolled in this subject."]);
        }
        exit;
    }

    // --- ORIGINAL LOGIC FOR SUBJECT CATALOG MANAGEMENT ---
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add') {
            $code = mysqli_real_escape_string($conn, strtoupper($_POST['code']));
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $room = mysqli_real_escape_string($conn, $_POST['room']);

            $sql = "INSERT INTO subject_catalog (subject_code, subject_name, room) VALUES ('$code', '$name', '$room')";
            if (mysqli_query($conn, $sql)) { 
                echo "success"; 
            } else { 
                echo "Error: " . mysqli_error($conn); 
            }
        }

        if ($action === 'delete') {
            $id = (int)$_POST['id'];
            if (mysqli_query($conn, "DELETE FROM subject_catalog WHERE id = $id")) {
                echo "success";
            } else {
                echo "Error deleting record.";
            }
        }
    }
    exit;
}
?>