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

// Handle add to cart
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $session_id = session_id();
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    // Check if product already in cart for this session/user
    $check = $conn->query("SELECT * FROM cart WHERE product_id = $product_id AND 
                          (session_id = '$session_id'".($user_id ? " OR user_id = $user_id" : "").")");
    
    if ($check->num_rows > 0) {
        $conn->query("UPDATE cart SET quantity = quantity + 1 
                     WHERE product_id = $product_id AND 
                     (session_id = '$session_id'".($user_id ? " OR user_id = $user_id" : "").")");
    } else {
        $product = $conn->query("SELECT * FROM products WHERE product_id = $product_id")->fetch_assoc();
        if ($product) {
            $name = $conn->real_escape_string($product['name']);
            $price = $product['price'];
            $conn->query("INSERT INTO cart (product_id, name, price, quantity, session_id, user_id) 
                         VALUES ($product_id, '$name', $price, 1, '$session_id', ".($user_id ?: 'NULL').")");
        }
    }
    header("Location: cart.php");
    exit;
}

// Handle quantity update
if (isset($_POST['update_quantity'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    
    if ($quantity > 0) {
        $conn->query("UPDATE cart SET quantity = $quantity WHERE item_id = $item_id");
    } else {
        $conn->query("DELETE FROM cart WHERE item_id = $item_id");
    }
    header("Location: cart.php");
    exit;
}

// Handle remove item from cart
if (isset($_POST['remove'])) {
    $item_id = (int)$_POST['remove'];
    $conn->query("DELETE FROM cart WHERE item_id = $item_id");
    header("Location: cart.php");
    exit;
}

// Get cart items for current session/user
$session_id = session_id();
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$cart_items = $conn->query("
    SELECT c.*, p.image as product_image 
    FROM cart c
    LEFT JOIN products p ON c.product_id = p.product_id
    WHERE c.session_id = '$session_id'".($user_id ? " OR c.user_id = $user_id" : "")."
");
$total = 0;

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
    <?php include 'currency.php'; ?>
    <?php include 'header.php'; ?>
    <?php include 'navigation.php'; ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Your Shopping Cart</h2>
        <?php if ($cart_items->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $cart_items->fetch_assoc()): 
                            $item_total = $row['price'] * $row['quantity'];
                            $total += $item_total;
                            $image_src = $row['product_image'];
                        ?>
                            <tr>
                                <td><img src="<?= $image_src ?>" alt="<?= htmlspecialchars($row['name']) ?>" width="50" /></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= displayPrice($row['price']) ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
                                        <input type="number" name="quantity" value="<?= $row['quantity'] ?>" 
                                               class="form-control" style="width: 70px;" min="1">
                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-primary ml-2">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </form>
                                </td>
                                <td><?= displayPrice($item_total) ?></td>
                                <td>
                                    <form method="POST">
                                        <button name="remove" value="<?= $row['item_id'] ?>" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <h4>Cart Summary</h4>
                    <ul class="list-group">
                        <li class="list-group-item">Subtotal: <strong><?= displayPrice($total) ?></strong></li>
                        <li class="list-group-item">Shipping: <strong><?= displayPrice(20) ?></strong></li>
                        <li class="list-group-item">Total: <strong><?= displayPrice($total + 20) ?></strong></li>
                    </ul>
                    <form method="POST" action="checkout.php">
                        <button type="submit" class="btn btn-success btn-block mt-3">Proceed to Checkout</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Your cart is empty. <a href="store.php" class="alert-link">Continue shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
<?php $conn->close(); ?>