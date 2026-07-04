<?php include 'config.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>PayPal Redirect Checkout</title>
  <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $clientID; ?>&currency=USD&intent=authorize"></script>
</head>
<body>
  <h2>Test ：$10 USD</h2>
  <div id="paypal-button-container"></div>

  <script>
    paypal.Buttons({
      style: {
        layout: 'vertical',
        color: 'gold',
        shape: 'rect',
        label: 'paypal'
      },
      createOrder: function(data, actions) {
        return actions.order.create({
          purchase_units: [{
            amount: {
              value: '10.00'
            }
          }],
          application_context: {
            return_url: "http://localhost/V1-Legacy/Paypal/success.php",
            cancel_url: "http://localhost/V1-Legacy/Paypal/cancel.php"
          }
        });
      },
      onApprove: function(data, actions) {
        window.location.href = "success.php?token=" + data.orderID;
      },
      onCancel: function(data) {
        window.location.href = "cancel.php";
      }
    }).render('#paypal-button-container');
  </script>
</body>
</html>
