<?php
session_start();
include 'db_conn.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $appointmentID = intval($_GET['id']);
    $customerID = $_SESSION['userID'];

    $sql = "DELETE FROM Appointment WHERE appointmentID = $appointmentID AND customerID = $customerID";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../appointment.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
