<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'db_conn.php';

$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$userOTP = $_POST['otp'] ?? '';
$correctOTP = $_SESSION['otp'] ?? '';
$otpEmail = $_SESSION['otp_email'] ?? '';
$otpExpire = $_SESSION['otp_expire'] ?? 0;

if (time() > $otpExpire) {
    echo "<script>
        alert('OTP expired. Please try again.');
        window.location.href = '../registration.php';
    </script>";
    exit();
}

if ($userOTP != $correctOTP || $_POST['email'] != $otpEmail) {
    echo "<script>
        alert('Invalid OTP or email mismatch.');
        window.location.href = '../registration.php';
    </script>";
    exit();
}





$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = "Customer";


$check_email = "SELECT * FROM User WHERE email = '$email'";
$check_result = mysqli_query($conn, $check_email);
if (mysqli_num_rows($check_result) > 0) {
    echo "<script>alert('Email already registered. Please use another one.'); history.back();</script>";
    exit();
}

$sql_user = "INSERT INTO User (username, password, email, role) VALUES ('$username', '$hashed_password', '$email', '$role')";
if (mysqli_query($conn, $sql_user)) {
    $userID = mysqli_insert_id($conn);

    $sql_customer = "INSERT INTO Customer (customerID, petName, petAge, petSpecialInfo) VALUES ($userID, 'Not Set', 'Not Set', 'Not Set')";
    if (mysqli_query($conn, $sql_customer)) {
        echo "<script>
                alert('Registration successful! Now going to login page...');
                setTimeout(function() {
                    window.location.href = '../login.html';
                }, 500);
              </script>";
        exit();
    } else {
        error_log("Customer registration insert failed: " . mysqli_error($conn));
        echo "<script>alert('Registration failed. Please try again later.'); history.back();</script>";
    }
} else {
    error_log("User registration insert failed: " . mysqli_error($conn));
    echo "<script>alert('Registration failed. Please try again later.'); history.back();</script>";
}

mysqli_close($conn);
?>
