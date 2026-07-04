<?php
session_start();
include 'db_conn.php';

if ($_SESSION['role'] !== 'Staff' && $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access denied'); window.location.href='../management.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO VaccineStock (name, quantity) VALUES ('$name', $quantity)";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Vaccine added successfully!!!'); window.location.href='../management.php';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "'); history.back();</script>";
    }

    mysqli_close($conn);
}
?>
