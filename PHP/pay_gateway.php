<?php
session_start();
include 'db_conn.php';

if(!isset($_SESSION['userID'])){
    http_response_code(401);
    exit('Unauthorized!');
}

if (($_SESSION['role'] ?? null) !== 'Customer') {
    http_response_code(403);
    exit('Forbidden!');
}

if (($_POST['method'] ?? '') !== 'paypal') {
    http_response_code(400);
    exit('Invalid payment method!');
}

if (!isset($_POST['paymentID']) || !ctype_digit($_POST['paymentID'])) {
    http_response_code(400);
    exit('Invalid payment ID!');
}

$paymentID = (int) $_POST['paymentID'];
$customerID = (int) $_SESSION['userID'];

$stmt = $conn->prepare("
    SELECT paymentID
    FROM Payment
    WHERE paymentID = ?
      AND customerID = ?
      AND status = 0
    LIMIT 1
    ");


$stmt->bind_param("ii", $paymentID, $customerID);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->fetch_assoc()) {
    http_response_code(404);
    exit('Payment not found.');
} 

$stmt->close();

header("Location: ../Paypal/create_order.php?paymentID=" . $paymentID);
exit();
?>