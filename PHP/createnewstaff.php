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

    if ($position !== 'Doctor' && $position !== 'Staff') {
        echo "<script>alert('Invalid position'); history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO User (username, password, email, role) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $hashed, $email, $position);

    if ($stmt->execute()) {
        $userID = $conn->insert_id;
        $today = date('Y-m-d');

        if ($position === 'Doctor') {
            $stmt = $conn->prepare("INSERT INTO Doctor (doctorID, docWorkStart) VALUES (?, ?)");
            $stmt->bind_param("is", $userID, $today);
            $stmt->execute();
        } elseif ($position === 'Staff') {
            $stmt = $conn->prepare("INSERT INTO Staff (staffID, staffWorkStart) VALUES (?, ?)");
            $stmt->bind_param("is", $userID, $today);
            $stmt->execute();
        }

        echo "<script>alert('Staff account added successfully'); window.location.href='../newstaff.php';</script>";
    } else {
        error_log("Staff account creation failed: " . $stmt->error);
        echo "<script>alert('Staff account creation failed. Please try again later.'); history.back();</script>";
    }

    mysqli_close($conn);
}
?>

