<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'db_conn.php';

if(!isset($_SESSION['userID'])) {   //禁止未登录用户访问
    http_response_code(401);
    echo "Unauthorized Access!";
    exit();
}

if(($_SESSION['role']??null) !== 'Customer') { //禁止非顾客身份访问病历
    http_response_code(403);
    echo "Forbidden, identification not match!";
    exit();
}

if(!isset($_GET['recordID']) || !ctype_digit($_GET['recordID'])){
    http_response_code(400);
    echo "Invalid Medical Record Request!";
    exit();
}


$recordID = (int) $_GET['recordID'];

if ($recordID <= 0) {
    http_response_code(400);
    echo "Invalid Medical Record Request!";
    exit();
}



$customerID = (int) $_SESSION['userID'];

$stmt = $conn->prepare("
    SELECT
        m.diagnosis,
        m.treatment, 
        m.nextTreatment, 
        a.date, 
        a.time, 
        u.username AS doctorName
    FROM MedicalRecord m
        JOIN Appointment a ON m.appointmentID = a.appointmentID
        JOIN Customer c ON a.customerID = c.customerID
        JOIN Doctor d ON m.doctorID = d.doctorID
        JOIN User u ON u.userID = d.doctorID
    WHERE m.recordID = ?  
    AND c.customerID = ? 
    LIMIT 1

");


if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    http_response_code(500);
    echo "Internal server error.";
    exit();
}

$stmt->bind_param("ii", $recordID, $customerID);

if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);

    http_response_code(500);
    echo "Internal server error.";
    exit();
}

$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $status = $row['nextTreatment']
        ? 'Need next treatment'
        : 'Finished';

    echo "<p><strong>Date:</strong> "
        . htmlspecialchars($row['date'])
        . " at "
        . htmlspecialchars($row['time'])
        . "</p>";

    echo "<p><strong>Doctor:</strong> Dr. "
        . htmlspecialchars($row['doctorName'])
        . "</p>";

    echo "<p><strong>Diagnosis:</strong> "
        . htmlspecialchars($row['diagnosis'])
        . "</p>";

    echo "<p><strong>Treatment:</strong> "
        . htmlspecialchars($row['treatment'])
        . "</p>";

    echo "<p><strong>Status:</strong> "
        . htmlspecialchars($status)
        . "</p>";
} else {
    http_response_code(404);
    echo "<p style='color:red;'>No details found.</p>";
}

?>
