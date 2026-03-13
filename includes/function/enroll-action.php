<?php
// 1. Include the config at the very top
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php'; 
session_start();

if (isset($_POST['subject_id']) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $sid = $_POST['subject_id'];
    
    // Capture the extra fields from your form
    $prof_id  = $_POST['professor_id'] ?? null;
    $day      = $_POST['day'] ?? null;
    $start    = $_POST['time_in'] ?? null;
    $end      = $_POST['time_out'] ?? null;
    $room     = $_POST['room'] ?? null;
    $semester = $_POST['semester'] ?? null;

    // 1. Check if specific enrollment exists to prevent the "Already Enrolled" error
    $check = $conn->prepare("SELECT id FROM subject_enrollments WHERE student_id = ? AND subject_id = ?");
    $check->bind_param("ii", $uid, $sid);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['status' => 'exists', 'message' => 'You are already enrolled in this subject.']);
        exit;
    }

    // 2. Full Insert based on your database structure
    $sql = "INSERT INTO subject_enrollments 
            (student_id, subject_id, professor_id, day, start_time, end_time, room, semester) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisssss", $uid, $sid, $prof_id, $day, $start, $end, $room, $semester);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    
    $stmt->close();
}
?>