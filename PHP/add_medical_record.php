<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'db_conn.php';

if ($_SESSION['role'] !== 'Doctor' && $_SESSION['role'] !== 'Admin') {
    echo "<script>alert('Access denied, insufficient authority!!!'); window.location.href='../management.php';</script>";
    exit();
}
$finished = isset($_POST['finished']) ? 1 : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointmentID = $_POST['appointmentID'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $doctorID = $_SESSION['userID'];
    $followupTreatment = isset($_POST['followupTreatment']) ? intval($_POST['followupTreatment']) : 0;

    $query = "
        SELECT u.username AS patientName
        FROM Appointment a
        JOIN Customer c ON a.customerID = c.customerID
        JOIN User u ON u.userID = c.customerID
        WHERE a.appointmentID = $appointmentID
        LIMIT 1
    ";
    $res = mysqli_query($conn, $query);

    if ($res && $row = mysqli_fetch_assoc($res)) {
        $patientName = $row['patientName'];



$sql = "INSERT INTO MedicalRecord (appointmentID, doctorID, diagnosis, treatment, nextTreatment, patientName, Finished)
        VALUES ($appointmentID, $doctorID, '$diagnosis', '$treatment', $followupTreatment, '$patientName', '$finished')";


$result = mysqli_query($conn, $sql);

        if ($result) {
            echo "<script>alert('✅ Medical Record Added'); window.location.href='../management.php';</script>";
        } else {
            echo "<script>alert('❌ Insert Error: " . mysqli_error($conn) . "'); history.back();</script>";
        }
    } else {
        echo "<script>alert('❌ Appointment not found or user missing'); history.back();</script>";
    }

    mysqli_close($conn);
}
?>
