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
    $vaccineID = $_POST['vaccineID'];
    $quantity = $_POST['quantity'];

    $sql = "UPDATE VaccineStock SET quantity = $quantity WHERE vaccineID = $vaccineID";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo "<script>alert('Stock Updated Successfully');
        window.location.href='../management.php';</script>";
    } else {
        echo "<script>alert('Update Error: " . mysqli_error($conn) . "');
        history.back();</script>";
    }

    mysqli_close($conn);
}
?>

