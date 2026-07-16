<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
include 'db_conn.php';

if (!isset($_SESSION['userID'])) {
    echo "<script>alert('Please log in first.'); window.location.href='../login.html';</script>";
    exit();
}

$userID = $_SESSION['userID'];
$date = date('Y-m-d');


$checkSql = "SELECT COUNT(*) AS count FROM Appointment WHERE customerID = ? AND date = ? AND emergency = 1";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("is", $userID, $date);
$stmt->execute();
$checkResult = $stmt->get_result();
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
        WHERE date = ? AND time = ?
    )
";
$stmt = $conn->prepare($doctorSql);
$stmt->bind_param("ss", $date, $time);
$stmt->execute();
$doctorResult = $stmt->get_result();
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
              VALUES (?, ?, ?, ?, 1)";

$stmt = $conn->prepare($insertSql);
$stmt->bind_param("iiss", $userID, $doctorID, $date, $time);

if ($stmt->execute()) {
    echo "<script>alert('Emergency appointment created successfully.'); window.location.href='../index.php';</script>";
} else {
    error_log("Emergency appointment creation failed: " . $stmt->error);
    http_response_code(500);
    echo "Internal server error.";
}
?>
