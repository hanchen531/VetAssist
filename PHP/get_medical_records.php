<?php
include 'db_conn.php';
$recordID = $_GET['recordID'] ?? 0;

$sql = "SELECT m.diagnosis, m.treatment, m.nextTreatment, a.date, a.time, u.username AS doctorName
        FROM MedicalRecord m
        JOIN Appointment a ON m.appointmentID = a.appointmentID
        JOIN Doctor d ON m.doctorID = d.doctorID
        JOIN User u ON u.userID = d.doctorID
        WHERE m.recordID = $recordID LIMIT 1";

$result = mysqli_query($conn, $sql);
if ($row = mysqli_fetch_assoc($result)) {
    $status = $row['nextTreatment'] ? 'Need next treatment' : 'Finished';

    echo "<p><strong>Date:</strong> " . $row['date'] . " at " . $row['time'] . "</p>";
    echo "<p><strong>Doctor:</strong> Dr. " . htmlspecialchars($row['doctorName']) . "</p>";
    echo "<p><strong>Diagnosis:</strong> " . htmlspecialchars($row['diagnosis']) . "</p>";
    echo "<p><strong>Treatment:</strong> " . htmlspecialchars($row['treatment']) . "</p>";
    echo "<p><strong>Status:</strong> $status</p>";
} else {
    echo "<p style='color:red;'>No details found.</p>";
}


?>
