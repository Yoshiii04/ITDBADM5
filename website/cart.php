<?php
include 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle add to cart
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $session_id = session_id();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Fetch product info
    $sql = "SELECT name, price, stock FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product && $product['stock'] > 0) {
        // Check if product already in cart
        if ($user_id) {
            $sql = "SELECT item_id, quantity FROM cart WHERE product_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $product_id, $user_id);
        } else {
            $sql = "SELECT item_id, quantity FROM cart WHERE product_id = ? AND session_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $product_id, $session_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + 1;
            $sql = "UPDATE cart SET quantity = ? WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_quantity, $row['item_id']);
        } else {
            // Insert new item
            $sql = "INSERT INTO cart (product_id, name, price, quantity, user_id, session_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isdiiis", $product_id, $product['name'], $product['price'], 1, $user_id, $session_id);
        }
        $stmt->execute();
        $stmt->close();
    }
    header("Location: cart.php");
    exit;
}

// Handle quantity update
if (isset($_POST['update_quantity'])) {
    $item_id = (int)$_POST['item_id'];
    $quantity = (int)$_POST['quantity'];
    $session_id = session_id();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($quantity > 0) {
        if ($user_id) {
            $sql = "UPDATE cart SET quantity = ? WHERE item_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $quantity, $item_id, $user_id);
        } else {
            $sql = "UPDATE cart SET quantity = ? WHERE item_id = ? AND session_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $quantity, $item_id, $session_id);
        }
    } else {
        if ($user_id) {
            $sql = "DELETE FROM cart WHERE item_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $item_id, $user_id);
        } else {
            $sql = "DELETE FROM cart WHERE item_id = ? AND session_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $item_id, $session_id);
        }
    }
    $stmt->execute();
    $stmt->close();
    header("Location: cart.php");
    exit;
}

// Handle remove item from cart
if (isset($_POST['remove'])) {
    $item_id = (int)$_POST['remove'];
    $session_id = session_id();
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if ($user_id) {
        $sql = "DELETE FROM cart WHERE item_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $item_id, $user_id);
    } else {
        $sql = "DELETE FROM cart WHERE item_id = ? AND session_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $item_id, $session_id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: cart.php");
    exit;
}

// Get cart items
$session_id = session_id();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id) {
    $sql = "SELECT c.item_id, c.name, c.price, c.quantity, c.product_id, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.user_id = ? AND p.stock > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else {
    $sql = "SELECT c.item_id, c.name, c.price, c.quantity, c.product_id, p.image 
            FROM cart c 
            JOIN products p ON c.product_id = p.product_id 
            WHERE c.session_id = ? AND p.stock > 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
}
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $total += $row['price'] * $row['quantity'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
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
                        <li><a href="index.php">Home</a></li>
                        <li class="active">My Cart</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Your Shopping Cart</h2>
        <?php if (!empty($cart_items)): ?>
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
                        <?php foreach ($cart_items as $row): 
                            $item_total = $row['price'] * $row['quantity'];
                        ?>
                            <tr>
                                <td>
                                    <?php if (isset($row['image']) && $row['image'] && file_exists($row['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" width="50">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo displayPrice($row['price']); ?></td>
                                <td>
                                    <form method="POST" class="d-flex">
                                        <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" 
                                               class="form-control" style="width: 70px;" min="0">
                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-primary ml-2">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </form>
                                </td>
                                <td><?php echo displayPrice($item_total); ?></td>
                                <td>
                                    <form method="POST">
                                        <button name="remove" value="<?php echo $row['item_id']; ?>" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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