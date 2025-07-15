<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Thank You for Your Order</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      background-color: #f4f6f9;
    }
    .thank-you-box {
      background: white;
      max-width: 600px;
      margin: 100px auto;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .thank-you-box i {
      font-size: 60px;
      color: #4eaf2c;
      margin-bottom: 20px;
    }
    .thank-you-box h2 {
      margin-bottom: 10px;
      color: #333;
    }
    .thank-you-box p {
      color: #555;
    }
    .btn-home {
      margin-top: 30px;
      background-color: #4eaf2c;
      border-color: #4eaf2c;
      color: white;
    }
    .btn-home:hover {
      background-color: #3e9822;
      border-color: #3e9822;
    }
  </style>
</head>
<body>

  <div class="thank-you-box">
    <i class="fa fa-check-circle"></i>
    <h2>Thank You for Your Order!</h2>
    <p>Your order has been placed successfully.</p>
    <p><strong>Order Number:</strong> #BYTCH-20250714</p>
    <p>We’ve sent you an email confirmation. You can track your order in the <a href="orderhistory.php">Order History</a>.</p>
    <a href="index.php" class="btn btn-home btn-lg">Back to Home</a>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>

</body>
</html>
