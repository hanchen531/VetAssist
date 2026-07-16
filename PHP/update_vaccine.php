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
    $vaccineID = (int)$_POST['vaccineID'];
    $quantity = (int)$_POST['quantity'];

    $sql = "UPDATE VaccineStock SET quantity = ? WHERE vaccineID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $quantity, $vaccineID);
    $result = $stmt->execute();

    if ($result) {
        echo "<script>alert('Stock Updated Successfully');
        window.location.href='../management.php';</script>";
    } else {
        error_log("Vaccine stock update failed: " . $stmt->error);
        echo "<script>alert('Update failed. Please try again later.');
        history.back();</script>";
    }

    mysqli_close($conn);
}
?>

