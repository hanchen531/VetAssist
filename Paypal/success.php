<?php
include '../PHP/db_conn.php'; 


ini_set('display_errors', 1);
error_reporting(E_ALL);


$clientId = '';
$secret   = '';

$orderID = $_GET['token'] ?? '';
if (!$orderID) exit("Didn't find order number!");


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERPWD, "$clientId:$secret");
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Accept: application/json",
    "Accept-Language: en_US"
]);
$tokenResponse = curl_exec($ch);
$accessToken = json_decode($tokenResponse, true)['access_token'] ?? null;
curl_close($ch);

if (!$accessToken) exit("Can not access token");


$ch = curl_init("https://api.sandbox.paypal.com/v2/checkout/orders/$orderID/capture");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Content-Type: application/json"
]);
$captureResponse = curl_exec($ch);
$captureData = json_decode($captureResponse, true);
curl_close($ch);


$status         = $captureData['status'] ?? 'UNKNOWN';
$referenceID    = $captureData['purchase_units'][0]['reference_id'] ?? '';
$amount         = $captureData['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? '0.00';
$payerAccount   = $captureData['payer']['email_address'] ?? 'unknown@example.com';
$method         = 'PayPal';
$transactionDate = date('Y-m-d H:i:s');

if ($status !== 'COMPLETED') exit("❌ Payment Failure（Status：$status）");

$stmt1 = $conn->prepare("UPDATE Payment SET status = 1 WHERE paymentID = ?");
$stmt1->bind_param("s", $referenceID);
$stmt1->execute();
$stmt1->close();


$stmt2 = $conn->prepare("
    INSERT INTO OnlinePaymentGateway 
    (paymentID, transactionStatus, transactionDate, paymentMethod, Amount, payerAccount)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt2->bind_param("isssds", $referenceID, $status, $transactionDate, $method, $amount, $payerAccount);
$stmt2->execute();
$stmt2->close();

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Success</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      padding: 40px;
      text-align: center;
    }
    .success-icon {
      font-size: 60px;
      color: #28a745;
    }
    .order-text {
      font-size: 18px;
      color: #6c757d;
    }
    .btn-home {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="success-icon mb-3">✅</div>
    <h2 class="mb-3 text-success">Payment Successful!</h2>
    <p class="order-text">Thank you for your payment.</p>
    <p class="order-text">Your Order ID:</p>
    <h4 class="text-primary mb-4"><?php echo htmlspecialchars($referenceID); ?></h4>
    <p class="order-text">Amount Paid: $<?php echo htmlspecialchars($amount); ?> USD</p>
    <p class="order-text">Payer Account: <?php echo htmlspecialchars($payerAccount); ?></p>
    <a href="../payments.php" class="btn btn-outline-primary btn-home">Return to Home</a>
  </div>
</body>
</html>
