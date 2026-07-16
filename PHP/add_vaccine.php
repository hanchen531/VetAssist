<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'db_conn.php';

if ($_SESSION['role'] !== 'Staff' && $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access denied'); window.location.href='../management.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $quantity = (int)$_POST['quantity'];

    $sql = "INSERT INTO VaccineStock (name, quantity) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $name, $quantity);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Vaccine added successfully!!!'); window.location.href='../management.php';</script>";
    } else {
        error_log("Vaccine insert failed: " . $stmt->error);
        echo "<script>alert('Vaccine could not be added. Please try again later.'); history.back();</script>";
    }

    mysqli_close($conn);
}
?>
