<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders & Returns</title>
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/font-awesome.min.css" />
  <link rel="stylesheet" href="css/style.css" />
  <style>
    .orders-returns-section h4 {
      margin-top: 30px;
    }
    .orders-returns-section ul {
      padding-left: 20px;
    }
    .orders-returns-section ul li {
      margin-bottom: 8px;
    }
    .orders-returns-section p {
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <?php include 'currency.php'; ?> 

  <!-- HEADER -->
  <?php include 'header.php'; ?>
  <!-- /HEADER -->

  <!-- NAVIGATION -->
  <?php include 'navigation.php'; ?>
  <!-- /NAVIGATION -->

  <!-- BREADCRUMB -->
  <div id="breadcrumb" class="section">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h3 class="breadcrumb-header">Orders & Returns</h3>
          <ul class="breadcrumb-tree">
            <li>Support</li>
            <li class="active">Orders & Returns</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!-- /BREADCRUMB -->

  <!-- SECTION -->
  <section class="section orders-returns-section">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <h2 class="text-center mb-4">Orders & Returns</h2>

          <h4>Placing an Order</h4>
          <p>To place an order, browse our catalog, add your desired items to the cart, and complete the checkout process. A confirmation email will be sent to you shortly after the order is placed.</p>

          <h4>Order Tracking</h4>
          <p>Once your order is dispatched, you will receive an email with a tracking number. Use it to monitor your shipment either on our website or directly via the courier’s tracking page.</p>

          <h4>Returns & Exchanges</h4>
          <ul>
            <li>Returns are accepted within 7 days from the delivery date.</li>
            <li>Products must be unused, undamaged, and in their original packaging.</li>
            <li>Return shipping costs are covered by the customer unless the item is defective or incorrect.</li>
            <li>We offer a full refund or replacement for faulty or wrong items.</li>
          </ul>

          <h4>How to Initiate a Return</h4>
          <p>Send us your order number, registered email address, and reason for return to our support email. Our team will respond within 1–2 business days with return instructions.</p>

          <h4>Contact for Support</h4>
          <p>If you need help with orders or returns, don’t hesitate to reach out at <a href="mailto:support@bytech.com"><strong>support@bytech.com</strong></a> or call us at <strong>(632) 8634-1111</strong>.</p>
        </div>
      </div>
    </div>
  </section>
  <!-- /SECTION -->

  <!-- FOOTER -->
  <?php include 'footer.php'; ?>
  <!-- /FOOTER -->

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</body>
</html>
