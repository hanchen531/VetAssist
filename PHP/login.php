<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
ob_start();

include 'db_conn.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM User WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    if (password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
        exit();
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href = '../login.html';</script>";
    }
} else {
    echo "<script>alert('Invalid email or password.'); window.location.href = '../login.html';</script>";
}

mysqli_close($conn);
