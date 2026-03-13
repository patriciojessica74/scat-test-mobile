<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';
require ROOT_PATH . 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    $checkEmail = $conn->prepare("SELECT id FROM info WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    
    if ($checkEmail->get_result()->num_rows > 0) {
        die("<script>alert('Email already registered!'); window.history.back();</script>");
    }

    $status = $_POST['status'] ?? 'None'; 
    $disability = ($status === 'pwd') ? ($_POST['disability'] ?? 'Not Specified') : 'N/A';

    // FIX: Included 'source' so email-success.php knows where to redirect
    $_SESSION['temp_user'] = [
        'username'       => $_POST['username'],
        'full_name'      => $_POST['full_name'],
        'email'          => $_POST['email'],
        'password'       => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'usertype'       => $_POST['usertype'] ?? 'student',
        'source'         => $_POST['source'] ?? 'user', // <--- PRESERVES THE SOURCE
        'student_id'     => $_POST['student_id'] ?? null,
        'program'        => $_POST['program'] ?? 'N/A',
        'year_level'     => $_POST['year'] ?? 'N/A', 
        'section'        => $_POST['section'] ?? 'N/A',
        'special_status' => $status,                
        'disability'     => $disability,
        'verify_code'    => strval(rand(100000, 999999)) 
    ];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'lloydjeresano14@gmail.com'; 
        $mail->Password   = 'xiwwsmuzdwibnbml'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('lloydjeresano14@gmail.com', 'SCAT System');
        $mail->addAddress($_SESSION['temp_user']['email']);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your SCAT Account';
        $mail->Body    = "Hello " . $_SESSION['temp_user']['full_name'] . ",<br><br>Verification code: <b>" . $_SESSION['temp_user']['verify_code'] . "</b>";
        $mail->send();
        
        header("Location: " . BASE_URL . "email-verify.php");
        exit();
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}
$conn->close();
?>