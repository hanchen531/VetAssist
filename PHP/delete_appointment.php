<?php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
include 'db_conn.php';

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    exit('Unauthorized!');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed!');
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Invalid CSRF token');
}

if (!isset($_POST['id']) || !ctype_digit($_POST['id'])) {
    http_response_code(400);
    exit('Invalid request.');
}

$appointmentID = (int) $_POST['id'];
$customerID = (int) $_SESSION['userID'];

$stmt = $conn->prepare("
    DELETE FROM Appointment
    WHERE appointmentID = ?
      AND customerID = ?
");

$stmt->bind_param("ii", $appointmentID, $customerID);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    http_response_code(404);
    exit('Appointment not found.');
}

$stmt->close();

header("Location: ../appointment.php");
exit();
?>
