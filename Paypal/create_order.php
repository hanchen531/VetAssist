<?php
$clientId = '';
$secret = '';

$paymentID = $_GET['paymentID'] ?? 'UNKNOWN';
$amount = $_GET['amount'] ?? '0.01';


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
        "return_url" => "http://localhost/V1-Legacy/Paypal/success.php",
        "cancel_url" => "http://localhost/V1-Legacy/Paypal/cancel.php"

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
