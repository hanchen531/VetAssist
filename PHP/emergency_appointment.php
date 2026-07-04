<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
include 'db_conn.php';

if (!isset($_SESSION['userID'])) {
    echo "<script>alert('Please log in first.'); window.location.href='../login.html';</script>";
    exit();
}

$userID = $_SESSION['userID'];
$date = date('Y-m-d');


$checkSql = "SELECT COUNT(*) AS count FROM Appointment WHERE customerID = $userID AND date = '$date' AND emergency = 1";
$checkResult = mysqli_query($conn, $checkSql);
$countRow = mysqli_fetch_assoc($checkResult);

if ($countRow['count'] >= 2) {
    echo "<script>alert('You have already made 2 emergency appointments today.'); window.location.href='../index.php';</script>";
    exit();
}


$datetime = date('Y-m-d H:i:s', strtotime('+30 minutes'));
$date = date('Y-m-d', strtotime($datetime));
$time = date('H:i:s', strtotime($datetime));


$doctorSql = "
    SELECT doctorID FROM Doctor
    WHERE doctorID NOT IN (
        SELECT doctorID FROM Appointment
        WHERE date = '$date' AND time = '$time'
    )
";
$doctorResult = mysqli_query($conn, $doctorSql);
$doctorList = [];

while ($row = mysqli_fetch_assoc($doctorResult)) {
    $doctorList[] = $row['doctorID'];
}

if (empty($doctorList)) {
    echo "<script>alert('No available doctor for the selected time.'); window.location.href='../index.php';</script>";
    exit();
}


$doctorID = $doctorList[array_rand($doctorList)];
$insertSql = "INSERT INTO Appointment (customerID, doctorID, date, time, emergency)
              VALUES ($userID, $doctorID, '$date', '$time', 1)";

if (mysqli_query($conn, $insertSql)) {
    echo "<script>alert('Emergency appointment created successfully.'); window.location.href='../index.php';</script>";
} else {
    echo "Failed to create appointment: " . mysqli_error($conn);
}
?>
