<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';
$config = require __DIR__ . '/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "Invalid email address.";
    exit();
}

$otp = rand(100000, 999999);
$_SESSION['otp'] = $otp;
$_SESSION['otp_email'] = $email;
$_SESSION['otp_expire'] = time() + 300;

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($config['smtp_username'], 'VetAssist Group');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'VetAssist OTP Code';
    $mail->Body    = "Your OTP is <strong>$otp</strong>. It expires in 5 minutes. Don't share it with anyone";

    $mail->send();
    echo "OTP sent, please check your email!";
} catch (Exception $e) {
    http_response_code(500);
    error_log("OTP email send failed: " . $mail->ErrorInfo);
    http_response_code(500);
    echo "Failed to send OTP. Please try again later.";
}
?>
