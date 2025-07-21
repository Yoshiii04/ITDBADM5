<?php
// MySQL connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle remove item from cart
if (isset($_POST['remove'])) {
    $item_id = (int)$_POST['remove'];
    $conn->query("DELETE FROM cart WHERE item_id = $item_id");
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css" />
    <link rel="stylesheet" href="css/style.css" />
  </head>
  <body>
    <!-- currency, header, navigation, footer are important in each page if they require a header n a footer -->
	<?php include 'currency.php'; ?>
	<?php include 'header.php'; ?>
	<?php include 'navigation.php'; ?>

	<!-- BREADCRUMB -->
	<div id="breadcrumb" class="section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h3 class="breadcrumb-header">Cart</h3>
					<ul class="breadcrumb-tree">
						<li>Cart</li>
						<li class="active">My Cart</li>
					</ul>
				</div>
			</div>
		</div>
	</div>

    <div class="container mt-5">
      <h2 class="text-center mb-4">Your Shopping Cart</h2>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Product</th>
              <th>Name</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $result = $conn->query("SELECT * FROM cart");
            $total = 0;

            while ($row = $result->fetch_assoc()) {
                $name = htmlspecialchars($row['name']);
                $price = $row['price'];
                $qty = $row['quantity'];
                $item_total = $price * $qty;
                $total += $item_total;

                echo "<tr>
                    <td><img src='img/product01.png' alt='Product' width='50' /></td>
                    <td>{$name}</td>
                    <td>" . displayPrice($price) . "</td>
                    <td><input type='number' value='{$qty}' class='form-control' style='width: 70px;' disabled></td>
                    <td>" . displayPrice($item_total) . "</td>
                    <td>
                        <form method='POST'>
                            <button name='remove' value='{$row['item_id']}' class='btn btn-danger btn-sm'>
                                <i class='fa fa-trash'></i>
                            </button>
                        </form>
                    </td>
                  </tr>";
            }
            ?>
          </tbody>
        </table>
      </div>

      <div class="row justify-content-end">
        <div class="col-md-4">
          <h4>Cart Summary</h4>
          <ul class="list-group">
            <li class="list-group-item">Subtotal: <strong><?php echo displayPrice($total); ?></strong></li>
            <li class="list-group-item">Shipping: <strong><?php echo displayPrice(20); ?></strong></li>
            <li class="list-group-item">Total: <strong><?php echo displayPrice($total + 20); ?></strong></li>
          </ul>
          <a href="checkout.php" class="btn btn-success btn-block mt-3">Proceed to Checkout</a>
        </div>
      </div>
    </div>

	<?php include 'footer.php'; ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>
