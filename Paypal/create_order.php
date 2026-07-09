<?php
session_start();
include '../PHP/db_conn.php';

$clientId = 'Af-IyoqYQErQw1uc-hu7CivQhwQ_dwtKNfyGhGAE6hWH1K7ltKBnl2CV65ETWEFYkAmbmhuKFmS9lxwK';
$secret = 'ELwj6QJ31RZ7Zu-07m-VN5DUwrMY-wiy-PMpX9tjiogMzknQmreyudDD9FNe85nB_bCcgKWkY3xIN-yg';

if (!isset($_SESSION['userID'])) {
    http_response_code(401);
    exit('Unauthorized');
}

if (($_SESSION['role'] ?? null) !== 'Customer') {
    http_response_code(403);
    exit('Forbidden');
}

if (!isset($_GET['paymentID']) || !ctype_digit($_GET['paymentID'])) {
    http_response_code(400);
    exit('Invalid payment ID.');
}

$paymentID = (int) $_GET['paymentID'];
$customerID = (int) $_SESSION['userID'];

$stmt = $conn->prepare("
    SELECT amount
    FROM Payment
    WHERE paymentID = ?
      AND customerID = ?
      AND status = 0
    LIMIT 1
");

$stmt->bind_param("ii", $paymentID, $customerID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    http_response_code(404);
    exit('Payment not found.');
}

$amount = number_format((float)$row['amount'], 2, '.', '');
$stmt->close();


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Accept-Language: en_US"
]);

$response = curl_exec($ch);
$result = json_decode($response, true);
$accessToken = $result['access_token'];
curl_close($ch);


$data = [
    "intent" => "CAPTURE",
    "purchase_units" => [[
        "reference_id" => $paymentID,
        "amount" => [
            "currency_code" => "USD",
            "value" => number_format($amount, 2, '.', '') // 保证格式正确
        ]
    ]],
    "application_context" => [
        "locale" => "en-US",
        "user_action" => "PAY_NOW",     
        "shipping_preference" => "NO_SHIPPING",
        "return_url" => "http://localhost/V2-Legacy-Secured-Update/Paypal/success.php",
        "cancel_url" => "http://localhost/V2-Legacy-Secured-Update/Paypal/cancel.php"

    ]
];


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);

$response = curl_exec($ch);
$order = json_decode($response, true);
curl_close($ch);


foreach ($order['links'] as $link) {
    if ($link['rel'] === 'approve') {
        header("Location: " . $link['href']);
        exit;
    }
}

echo "Create order failure, please try again!";