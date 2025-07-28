<?php
session_start();

  $servername = "localhost";
  $username = "root";
  $password = "";
  $database = "online_store";
  
  $conn = new mysqli($servername, $username, $password, $database);

  if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set or get selected currency
if (isset($_GET['currency'])) {
    $_SESSION['currency'] = $_GET['currency'];
} elseif (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'PHP';
}

$currency = $_SESSION['currency'];

// Get exchange rates from database
$rates = ['PHP' => 1]; // Default PHP rate
$result = $conn->query("SELECT currency_code, exchange_rate FROM currencies");
while ($row = $result->fetch_assoc()) {
    $rates[$row['currency_code']] = $row['exchange_rate'];
}

// Make globally accessible
$GLOBALS['currency'] = $currency;
$GLOBALS['rates'] = $rates;

function displayPrice($basePrice) {
    $currency = $GLOBALS['currency'];
    $rates = $GLOBALS['rates'];
    
    // Default to PHP if rate not found
    $rate = $rates[$currency] ?? 1;
    
    $symbol = match($currency) {
        'USD' => '$',
        'KRW' => '₩',
        default => '₱'
    };
    
    $converted = $basePrice * $rate;
    
    // Format differently for KRW (no decimals)
    if ($currency === 'KRW') {
        return $symbol . number_format($converted, 0);
    }
    
    return $symbol . number_format($converted, 2);
}
?>

