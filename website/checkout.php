
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once 'currency.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session for user data if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validate required fields
    $required_fields = ['first-name', 'last-name', 'email', 'address', 'city', 'country', 'zip-code', 'tel'];
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        $error_message = "Please fill in all required fields: " . implode(', ', $missing_fields);
    } else {
        // Get customer details from form
        $first_name = $conn->real_escape_string($_POST['first-name']);
        $last_name = $conn->real_escape_string($_POST['last-name']);
        $email = $conn->real_escape_string($_POST['email']);
        $address = $conn->real_escape_string($_POST['address']);
        $city = $conn->real_escape_string($_POST['city']);
        $country = $conn->real_escape_string($_POST['country']);
        $zip_code = $conn->real_escape_string($_POST['zip-code']);
        $phone = $conn->real_escape_string($_POST['tel']);
        $notes = isset($_POST['notes']) ? $conn->real_escape_string($_POST['notes']) : '';
        
        $customer_name = "$first_name $last_name";

        // Calculate total from cart
        $cart_items = $conn->query("SELECT * FROM cart");
        $subtotal = 0;
        while ($item = $cart_items->fetch_assoc()) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        // Add shipping cost
        $shipping_cost = 20; // Flat rate shipping
        $total_amount = $subtotal + $shipping_cost;
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // order record
            $stmt = $conn->prepare("INSERT INTO orders (customer_name, total_amount) VALUES (?, ?)");
            $stmt->bind_param("sd", $customer_name, $total_amount);
            $stmt->execute();
            $order_id = $stmt->insert_id; // Get the auto-incremented order ID
            $stmt->close();
            
            // transaction record
            $currency_code = $_SESSION['currency'] ?? 'PHP';
            $stmt = $conn->prepare("INSERT INTO transactions (order_id, total_amount, currency_code) VALUES (?, ?, ?)");
            $stmt->bind_param("ids", $order_id, $total_amount, $currency_code);
            $stmt->execute();
            $stmt->close();
            
            // Iorderitems
            $cart_items = $conn->query("SELECT * FROM cart");
            while ($item = $cart_items->fetch_assoc()) {
                $stmt = $conn->prepare("INSERT INTO orderitems (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $stmt->execute();
                $stmt->close();
            }
            
            // Clear cart
            $conn->query("DELETE FROM cart");
            
            // Commit transaction
            $conn->commit();
            
            // Redirect to thank you page
            header("Location: thankyou.php?order_id=$order_id");
            exit;

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $error_message = "Error processing your order: " . $e->getMessage();
        }
    }
}

// Get cart items for display
$cart_items = $conn->query("SELECT * FROM cart");
$cart_count = $cart_items->num_rows;
$cart_items->data_seek(0); // Reset pointer for reuse

$subtotal = 0;
while ($item = $cart_items->fetch_assoc()) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 20;
$grand_total = $subtotal + $shipping;

// Redirect if cart is empty
if ($cart_count == 0) {
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - TechShop</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/slick.css"/>
    <link type="text/css" rel="stylesheet" href="css/slick-theme.css"/>
    <link type="text/css" rel="stylesheet" href="css/nouislider.min.css"/>
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link type="text/css" rel="stylesheet" href="css/style.css"/>
</head>
<body>

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
                    <h3 class="breadcrumb-header">Checkout</h3>
                    <ul class="breadcrumb-tree">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="cart.php">Cart</a></li>
                        <li class="active">Checkout</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- /BREADCRUMB -->

    <!-- SECTION -->
    <div class="section">
        <div class="container">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <form method="POST" action="checkout.php" class="clearfix">
                    <div class="col-md-7">
                        <!-- Billing Details -->
                        <div class="billing-details">
                            <div class="section-title">
                                <h3 class="title">Billing address</h3>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="first-name" placeholder="First Name" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="last-name" placeholder="Last Name" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="address" placeholder="Address" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="city" placeholder="City" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="country" placeholder="Country" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="text" name="zip-code" placeholder="ZIP Code" required>
                            </div>
                            <div class="form-group">
                                <input class="input" type="tel" name="tel" placeholder="Telephone" required>
                            </div>
                        </div>
                        <!-- /Billing Details -->

                        <!-- Shipping Details -->
                        <div class="shiping-details">
                            <div class="section-title">
                                <h3 class="title">Shipping address</h3>
                            </div>
                            <div class="input-checkbox">
                                <input type="checkbox" id="shiping-address">
                                <label for="shiping-address">
                                    <span></span>
                                    Ship to a different address?
                                </label>
                                <div class="caption">
                                    <div class="form-group">
                                        <input class="input" type="text" name="ship-first-name" placeholder="First Name">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="text" name="ship-last-name" placeholder="Last Name">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="email" name="ship-email" placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="text" name="ship-address" placeholder="Address">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="text" name="ship-city" placeholder="City">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="text" name="ship-country" placeholder="Country">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="text" name="ship-zip-code" placeholder="ZIP Code">
                                    </div>
                                    <div class="form-group">
                                        <input class="input" type="tel" name="ship-tel" placeholder="Telephone">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Shipping Details -->

                        <!-- Order notes -->
                        <div class="order-notes">
                            <textarea class="input" name="notes" placeholder="Order Notes"></textarea>
                        </div>
                        <!-- /Order notes -->
                    </div>

                    <!-- Order Details -->
                    <div class="col-md-5 order-details">
                        <div class="section-title text-center">
                            <h3 class="title">Your Order</h3>
                        </div>
                        <div class="order-summary">
                            <div class="order-col">
                                <div><strong>PRODUCT</strong></div>
                                <div><strong>TOTAL</strong></div>
                            </div>
                            <div class="order-products">
                                <?php 
                                $cart_items = $conn->query("SELECT * FROM cart");
                                while ($item = $cart_items->fetch_assoc()): 
                                    $item_total = $item['price'] * $item['quantity'];
                                ?>
                                    <div class="order-col">
                                        <div><?= $item['quantity'] ?>x <?= htmlspecialchars($item['name']) ?></div>
                                        <div><?= displayPrice($item_total) ?></div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="order-col">
                                <div>Shipping</div>
                                <div><strong><?= displayPrice($shipping) ?></strong></div>
                            </div>
                            <div class="order-col">
                                <div><strong>TOTAL</strong></div>
                                <div><strong class="order-total"><?= displayPrice($grand_total) ?></strong></div>
                            </div>
                        </div>
                        <div class="payment-method">
                            <div class="input-radio">
                                <input type="radio" name="payment" id="payment-1" value="bank" checked>
                                <label for="payment-1">
                                    <span></span>
                                    Direct Bank Transfer
                                </label>
                                <div class="caption">
                                    <p>Make your payment directly into our bank account. Please use your Order ID as the payment reference.</p>
                                </div>
                            </div>
                            <div class="input-radio">
                                <input type="radio" name="payment" id="payment-2" value="cheque">
                                <label for="payment-2">
                                    <span></span>
                                    Cheque Payment
                                </label>
                                <div class="caption">
                                    <p>Please send a check to Store Name, Store Street, Store Town, Store State/County, Store Postcode.</p>
                                </div>
                            </div>
                            <div class="input-radio">
                                <input type="radio" name="payment" id="payment-3" value="paypal">
                                <label for="payment-3">
                                    <span></span>
                                    Paypal System
                                </label>
                                <div class="caption">
                                    <p>You will be redirected to PayPal to complete your payment securely.</p>
                                </div>
                            </div>
                        </div>
                        <div class="input-checkbox">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">
                                <span></span>
                                I've read and accept the <a href="#">terms & conditions</a>
                            </label>
                        </div>
                        <button type="submit" name="place_order" class="primary-btn order-submit">Place order</button>
                    </div>
                    <!-- /Order Details -->
                </form>
            </div>
            <!-- /row -->
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->

    <!-- FOOTER -->
    <?php include 'footer.php'; ?>
    <!-- /FOOTER -->

    <!-- jQuery Plugins -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/nouislider.min.js"></script>
    <script src="js/jquery.zoom.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php $conn->close(); ?>