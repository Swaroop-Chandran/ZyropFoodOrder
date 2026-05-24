<?php
// Mock user session
session_start();
$_SESSION['user_id'] = 9; // Test User id from db
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_email'] = 'testuser@example.com';

echo "=== MOCKING ORDER SUBMISSION API TRANSACTION ===\n";

$cart = [
    [
        'id' => 'f01',
        'name' => "Baker's Basket Bread",
        'price' => 120,
        'qty' => 2,
        'restaurant' => 'Le Artisan',
        'veg' => true
    ],
    [
        'id' => 'f18',
        'name' => 'Margherita Classic Pizza',
        'price' => 299,
        'qty' => 1,
        'restaurant' => 'Pizzeria Uno',
        'veg' => true
    ]
];

$meta = [
    'subtotal' => 539,
    'delivery' => 0,
    'platformFee' => 5,
    'gst' => 27,
    'appliedDiscount' => 50,
    'total' => 521
];

$address = [
    'label' => 'Flat 4B, MG Road, Bangalore, 560001'
];

$payload = [
    'paymentMethod' => 'cod',
    'cart' => $cart,
    'meta' => $meta,
    'address' => $address
];

// Send request to place_order_api.php using curl/local request
// Since we want to test place_order_api.php directly in the same thread, we can require it!
// But place_order_api.php uses file_get_contents('php://input'), so we can mock that by writing to a temporary stream or mocking the variables if possible, or using curl with the session cookie!

// Let's do a curl request! We will pass the cookie PHPSESSID that we got.
$session_id = session_id();
echo "Session ID: $session_id\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/ZyropFoodOrder/place_order_api.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    "Cookie: PHPSESSID=$session_id"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

echo "HTTP Code: " . $info['http_code'] . "\n";
echo "Response: $response\n";

// Let's clean up and print out the orders count in database
require_once 'db.php';
$ordersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$itemsCount = $pdo->query("SELECT COUNT(*) FROM order_items")->fetchColumn();
echo "Total orders in database: $ordersCount\n";
echo "Total order items in database: $itemsCount\n";

unlink(__FILE__);
