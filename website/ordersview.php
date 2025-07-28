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

// Get order ID and currency
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$currency = isset($_GET['currency']) ? $_GET['currency'] : 'PHP';

// Get exchange rate
$rate = 1.00;
if ($currency !== 'PHP') {
    $stmt = $conn->prepare("SELECT exchange_rate FROM currencies WHERE currency_code = ?");
    $stmt->bind_param("s", $currency);
    $stmt->execute();
    $stmt->bind_result($rate);
    $stmt->fetch();
    $stmt->close();
}

// Currency symbols
$symbols = [
    'PHP' => '₱',
    'USD' => '$',
    'KRW' => '₩'
];
$symbol = $symbols[$currency] ?? '₱';

// Get order details
$order = [];
$orderitems = [];

if ($order_id > 0) {
    // Get basic order info
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        // Get order items (you'll need an orderitems table)
        // This assumes you have an orderitems table that links orders to products
        $stmt = $conn->prepare("
            SELECT oi.*, p.name, p.price, p.image 
            FROM orderitems oi
            JOIN products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orderitems[] = $row;
        }
        $stmt->close();
    }
}

// If no order found, redirect back
if (empty($order)) {
    header("Location: orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?= $order_id ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Order Details #<?= $order_id ?></h2>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Customer Information</h4>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
            <p><strong>Status:</strong> 
                <span class="badge 
                    <?= $order['status'] == 'Pending' ? 'badge-warning' : 
                       ($order['status'] == 'Processing' ? 'badge-info' : 
                       ($order['status'] == 'Completed' ? 'badge-success' : 'badge-danger')) ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </span>
            </p>
        </div>
        <div class="col-md-6">
            <h4>Order Summary</h4>
            <p><strong>Total Amount:</strong> <?= $symbol . number_format($order['total_amount'] * $rate, 2) ?></p>
        </div>
    </div>

    <h4>Order Items</h4>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orderitems)): ?>
                <tr>
                    <td colspan="4" class="text-center">No items found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orderitems as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['image']): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" width="50" class="mr-2">
                            <?php endif; ?>
                            <?= htmlspecialchars($item['name']) ?>
                        </td>
                        <td><?= $symbol . number_format($item['price'] * $rate, 2) ?></td>
                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                        <td><?= $symbol . number_format($item['price'] * $item['quantity'] * $rate, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="orders.php?currency=<?= urlencode($currency) ?>" class="btn btn-secondary">Back to Orders</a>
</div>
</body>
</html>