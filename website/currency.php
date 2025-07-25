<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "online_store";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Database connection error. Please try again later.");
}

// Set or get selected currency
if (isset($_GET['currency'])) {
    $_SESSION['currency'] = strtoupper(trim($_GET['currency']));
} elseif (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'PHP';
}
$currency = $_SESSION['currency'];

// Get exchange rates from database
$rates = ['PHP' => 1]; // Default PHP rate
$result = $conn->query("SELECT currency_code, exchange_rate FROM currencies");
if (!$result) {
    error_log("Currency query failed: " . $conn->error);
    $rates = ['PHP' => 1, 'USD' => 0.017, 'KRW' => 23.5]; // Fallback
} else {
    while ($row = $result->fetch_assoc()) {
        $rates[$row['currency_code']] = floatval($row['exchange_rate']);
    }
    if (count($rates) == 1) {
        error_log("No currencies found in database, using fallback");
        $rates = ['PHP' => 1, 'USD' => 0.017, 'KRW' => 23.5];
    }
}

// Validate selected currency
if (!isset($rates[$currency])) {
    error_log("Selected currency $currency not in rates, defaulting to PHP");
    $_SESSION['currency'] = 'PHP';
    $currency = 'PHP';
}

// Make globally accessible
$GLOBALS['currency'] = $currency;
$GLOBALS['rates'] = $rates;

function displayPrice($basePrice) {
    $currency = $GLOBALS['currency'];
    $rates = $GLOBALS['rates'];
    
    $rate = $rates[$currency] ?? 1;
    $symbol = match ($currency) {
        'USD' => '$',
        'KRW' => '₩',
        default => '₱'
    };
    
    $converted = floatval($basePrice) * $rate;
    return $currency === 'KRW' ? $symbol . number_format($converted, 0) : $symbol . number_format($converted, 2);
}
?>