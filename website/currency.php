<?php
session_start();

// Set or get selected currency
if (isset($_GET['currency'])) {
    $_SESSION['currency'] = $_GET['currency'];
} elseif (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'PHP';
}

$currency = $_SESSION['currency'];
$rates = [
    'PHP' => 1,
    'USD' => 0.018,
    'KRW' => 23.45
];

// Make globally accessible
$GLOBALS['currency'] = $currency;
$GLOBALS['rates'] = $rates;

function displayPrice($basePrice) {
    $currency = $GLOBALS['currency'];
    $rates = $GLOBALS['rates'];
    $symbol = $currency === 'USD' ? '$' : ($currency === 'KRW' ? '₩' : '₱');
    $converted = $basePrice * $rates[$currency];
    return $symbol . number_format($converted, 2);
}

// <span class="price"><?php echo displayPrice(1000); /?/> then close span 

?>



