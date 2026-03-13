<?php
// 1. Include config (this handles connection.php and ROOT_PATH)
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = $_POST['username']; 
    $pass = $_POST['password'];

    // 2. Fetch user data (using $conn from config.php)
    $sql = "SELECT users.id, users.username, users.password, users.usertype, users.student_id 
            FROM users 
            LEFT JOIN info ON users.id = info.id 
            WHERE users.username = ? OR info.email = ? LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // 3. Verify hashed password
        if (password_verify($pass, $row['password'])) {
            // Set Session Variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['usertype'] = $row['usertype'];
            $_SESSION['student_id'] = $row['student_id'] ?? 'N/A'; 
            
            // 4. Fetch additional info
            $info_sql = "SELECT full_name, year_level, section, program FROM info WHERE id = ?";
            $info_stmt = $conn->prepare($info_sql);
            $info_stmt->bind_param("i", $row['id']);
            $info_stmt->execute();
            $info_result = $info_stmt->get_result();
            
            if ($info_res = $info_result->fetch_assoc()) {
                $_SESSION['full_name'] = $info_res['full_name'];
                $_SESSION['year_level'] = $info_res['year_level'];
                $_SESSION['section'] = $info_res['section'];
                $_SESSION['program'] = $info_res['program'];
            }

            // 5. Redirect based on usertype using BASE_URL
           if ($row['usertype'] === 'admin') {
                header("Location: " . ADMIN_URL . "admin-professor-list.php"); 
                 exit();
            } elseif ($row['usertype'] === 'professor') {
    // FIXED: Added 'faculty/' folder to the path
                echo "<script>
                    localStorage.setItem('scat_prof_current', '" . $row['username'] . "'); 
                      window.location.href='" . BASE_URL . "faculty/prof-dashboard.php';
                 </script>";
                exit();
            } else {
    // Students stay in root
             echo "<script>
                localStorage.setItem('scat_student_current', '" . $row['username'] . "'); 
                window.location.href='" . BASE_URL . "dashboard.php';
            </script>";
                exit();
            }

        } else {
            echo "<script>alert('Incorrect password!'); window.location='" . BASE_URL . "login.php';</script>";
        }
    } else {
        echo "<script>alert('Account not found!'); window.location='" . BASE_URL . "login.php';</script>";
    }
    $stmt->close();
}
$conn->close();
?>