<?php
session_start();
ob_start();

include 'db_conn.php';

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM User WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc(result: $result);
    session_regenerate_id(true);
    if (password_verify($password, $user['password'])) {
        $_SESSION['userID'] = $user['userID'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
        exit();
    } else {
        echo "<script>alert('Wrong Password, Please Enter Again!'); window.location.href = '../login.html';</script>";
    }
} else {
    echo "<script>alert('Invaild or Wrong Email Address, Please Double Check!'); window.location.href = '../login.html';</script>";
}

mysqli_close($conn);
