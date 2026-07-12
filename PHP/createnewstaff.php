<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'db_conn.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access denied: Admin only');
    window.location.href='dashboard.html';</script>";
    exit();
}   

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    $position = $_POST['position'];

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match'); history.back();</script>";
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO User (username, password, email, role) VALUES ('$username', '$hashed', '$email', '$position')";
    if (mysqli_query($conn, $sql)) {
        $userID = mysqli_insert_id($conn);
        $today = date('Y-m-d');

        if ($position === 'Doctor') {
            mysqli_query($conn, "INSERT INTO Doctor (doctorID, docWorkStart) VALUES ('$userID', '$today')");
        } elseif ($position === 'Staff') {
            mysqli_query($conn, "INSERT INTO Staff (staffID, staffWorkStart) VALUES ('$userID', '$today')");
        }

        echo "<script>alert('Staff account added successfully'); window.location.href='../newstaff.php';</script>";
    } else {
        error_log("Staff account creation failed: " . mysqli_error($conn));
        echo "<script>alert('Staff account creation failed. Please try again later.'); history.back();</script>";
    }

    mysqli_close($conn);
}
?>

