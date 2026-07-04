<?php
$method = $_POST['method'] ?? '';
$paymentID = $_POST['paymentID'] ?? '';
$amount = $_POST['amount'] ?? '';

if ($method === 'paypal') {
    $query = http_build_query([
        'paymentID' => $paymentID,
        'amount' => $amount
    ]);
    header("Location: ../Paypal/create_order.php?$query");
    exit;

} else {
    echo "Invaild, please try again!";
}
