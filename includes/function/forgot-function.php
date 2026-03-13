<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/SCAT1/includes/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();
require ROOT_PATH . 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure JSON output
    header('Content-Type: application/json');
    
    $email = $_POST['email'] ?? '';

    $stmt = $conn->prepare("SELECT id, full_name FROM info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $code = strval(rand(100000, 999999));

        $_SESSION['reset_user'] = [
            'email' => $email,
            'verify_code' => $code,
            'user_id' => $user['id']
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

            $mail->setFrom('lloydjeresano14@gmail.com', 'SCAT CMS Support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "Hello " . htmlspecialchars($user['full_name']) . ",<br><br>Your password reset code is: <b>$code</b>";

            $mail->send();
            echo json_encode([
                "status" => "success", 
                "redirect" => BASE_URL . "email-verify.php"
            ]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Mailer Error: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Email address not found."]);
    }
    exit();
}