<?php
session_start();

require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';

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
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'zhanghanchen@graduate.utm.my';
    $mail->Password = 'fyta xxpf hjib zazy';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('zhanghanchen@graduate.utm.my', 'VetAssist Group');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'VetAssist OTP Code';
    $mail->Body    = "Your OTP is <strong>$otp</strong>. It expires in 5 minutes. Don't share it with anyone";

    $mail->send();
    echo "OTP sent, please check your email!";
} catch (Exception $e) {
    http_response_code(500);
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
