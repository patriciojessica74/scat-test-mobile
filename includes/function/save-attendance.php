<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php'; 
session_start();
date_default_timezone_set('Asia/Manila');

// 1. Session Check
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Session expired.";
    exit();
}

$user_id      = $_SESSION['user_id'];
$user_type    = $_SESSION['usertype']; 
$subject_id   = isset($_POST['subject_id']) ? mysqli_real_escape_string($conn, $_POST['subject_id']) : '';
$student_id   = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';
$status       = isset($_POST['status']) ? strtoupper(mysqli_real_escape_string($conn, $_POST['status'])) : ''; 
$date_posted  = isset($_POST['date']) ? mysqli_real_escape_string($conn, $_POST['date']) : date('Y-m-d'); 

if (empty($subject_id) || empty($student_id)) {
    http_response_code(400);
    echo "Missing data.";
    exit();
}

$current_time = date('H:i:s');
$current_day  = date('l'); 
$today_date   = date('Y-m-d');

// 2. Get OLD Status to handle count reversal
$old_status = "";
$get_old = mysqli_query($conn, "SELECT status FROM attendance_records WHERE student_id = '$student_id' AND subject_id = '$subject_id' AND date = '$date_posted' LIMIT 1");
if ($get_old && $row_old = mysqli_fetch_assoc($get_old)) {
    $old_status = strtoupper($row_old['status']);
}

// 3. Schedule Logic (Bypassed for Professors)
$sched_q = "SELECT day, start_time FROM subject_enrollments WHERE subject_id = '$subject_id' AND student_id = '$student_id' LIMIT 1";
$sched_res = mysqli_query($conn, $sched_q);

if ($sched_res && mysqli_num_rows($sched_res) > 0) {
    $sched = mysqli_fetch_assoc($sched_res);

    if ($user_type === 'student') { 
        if ($date_posted == $today_date && strtolower($current_day) !== strtolower($sched['day'])) {
            http_response_code(400); 
            echo "Wrong Day!"; 
            exit();
        }
    }

    if ($status === 'PRESENT' && $date_posted === $today_date) {
        $diff = (strtotime($current_time) - strtotime($sched['start_time'])) / 60;
        if ($diff > 15) { 
            $status = 'LATE'; 
        }
    }
}

// 4. Update/Insert Attendance Record
$check_q = mysqli_query($conn, "SELECT id FROM attendance_records WHERE student_id = '$student_id' AND subject_id = '$subject_id' AND date = '$date_posted'");

if (mysqli_num_rows($check_q) > 0) {
    $sql = "UPDATE attendance_records SET status = '$status', time_in = '$current_time', professor_id = (CASE WHEN '$user_type' = 'professor' THEN '$user_id' ELSE professor_id END) WHERE student_id = '$student_id' AND subject_id = '$subject_id' AND date = '$date_posted'";
} else {
    $prof_id_val = ($user_type === 'professor') ? $user_id : '0'; 
    $sql = "INSERT INTO attendance_records (student_id, professor_id, subject_id, status, date, time_in) VALUES ('$student_id', '$prof_id_val', '$subject_id', '$status', '$date_posted', '$current_time')";
}

if (mysqli_query($conn, $sql)) {
    // --- STATS SYNC LOGIC ---
    $code_q = mysqli_query($conn, "SELECT subject_code FROM subject_catalog WHERE id = '$subject_id' LIMIT 1");
    $s_code = ($code_q && $row = mysqli_fetch_assoc($code_q)) ? $row['subject_code'] : '';

    if (!empty($s_code)) {
        $stat_exists = mysqli_query($conn, "SELECT id FROM attendance_stats WHERE user_id = '$student_id' AND subject_code = '$s_code'");
        if (mysqli_num_rows($stat_exists) == 0) {
            mysqli_query($conn, "INSERT INTO attendance_stats (user_id, subject_code, absent_count, late_count, status_lock) VALUES ('$student_id', '$s_code', 0, 0, 'OPEN')");
        }

        // Reverse Old
        if ($old_status === 'ABSENT') {
            mysqli_query($conn, "UPDATE attendance_stats SET absent_count = GREATEST(0, absent_count - 1) WHERE user_id = '$student_id' AND subject_code = '$s_code'");
        } elseif ($old_status === 'LATE') {
            mysqli_query($conn, "UPDATE attendance_stats SET late_count = GREATEST(0, late_count - 1) WHERE user_id = '$student_id' AND subject_code = '$s_code'");
        }

        // Apply New
        if ($status === 'ABSENT') {
            mysqli_query($conn, "UPDATE attendance_stats SET absent_count = absent_count + 1 WHERE user_id = '$student_id' AND subject_code = '$s_code'");
        } elseif ($status === 'LATE') {
            mysqli_query($conn, "UPDATE attendance_stats SET late_count = late_count + 1 WHERE user_id = '$student_id' AND subject_code = '$s_code'");
            
            $res = mysqli_query($conn, "SELECT late_count FROM attendance_stats WHERE user_id = '$student_id' AND subject_code = '$s_code'");
            $row = mysqli_fetch_assoc($res);
            if ($row && $row['late_count'] >= 3) {
                mysqli_query($conn, "UPDATE attendance_stats SET late_count = 0, absent_count = absent_count + 1 WHERE user_id = '$student_id' AND subject_code = '$s_code'");
            }
        }

        // --- NEW: UPDATE status_lock COLUMN IN DB ---
        $final_check = mysqli_query($conn, "SELECT absent_count, status_lock FROM attendance_stats WHERE user_id = '$student_id' AND subject_code = '$s_code' LIMIT 1");
        if ($f_row = mysqli_fetch_assoc($final_check)) {
            // Only flip to LOCKED if they aren't already marked as CLEARED
            if ($f_row['status_lock'] !== 'CLEARED') {
                $new_lock = ($f_row['absent_count'] >= 3) ? 'LOCKED' : 'OPEN';
                mysqli_query($conn, "UPDATE attendance_stats SET status_lock = '$new_lock' WHERE user_id = '$student_id' AND subject_code = '$s_code'");
            }
        }
    }
    echo "Success";
} else {
    http_response_code(500); 
    echo "DB Error: " . mysqli_error($conn);
}
?>