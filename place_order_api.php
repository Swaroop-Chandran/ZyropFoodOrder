<?php
// place_order_api.php — AJAX Place Order Handler

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login again.']);
    exit();
}

require_once 'db.php';

// Parse POST inputs
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

if (!$data || empty($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit();
}

$paymentMethod = $data['paymentMethod'] ?? 'cod';
$address       = $data['address']['label'] ?? ($data['address']['full'] ?? 'Address Not Specified');
$meta          = $data['meta'] ?? [];
$cart          = $data['cart'] ?? [];

$subtotal    = floatval($meta['subtotal'] ?? 0);
$delivery    = floatval($meta['delivery'] ?? 49);
$platformFee = floatval($meta['platformFee'] ?? 5);
$gst         = floatval($meta['gst'] ?? 0);
$discount    = floatval($meta['appliedDiscount'] ?? 0);
$total       = floatval($meta['total'] ?? 0);

$userId = $_SESSION['user_id'];

// Generate unique Order ID, e.g., ZYR6839201
$orderId = 'ZYR' . mt_rand(1000000, 9999999);

try {
    // Start transaction to guarantee database integrity
    $pdo->beginTransaction();

    // 1. Insert order record
    $stmt = $pdo->prepare("
        INSERT INTO orders (order_id, user_id, subtotal, delivery_fee, platform_fee, gst, discount, total, payment_mode, address, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'placed')
    ");
    
    $stmt->execute([
        $orderId,
        $userId,
        $subtotal,
        $delivery,
        $platformFee,
        $gst,
        $discount,
        $total,
        $paymentMethod,
        $address
    ]);

    $orderDbId = $pdo->lastInsertId();

    // 2. Insert each item from the cart
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, food_id, name, price, quantity)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($cart as $item) {
        $foodId   = $item['id'] ?? '';
        $name     = $item['name'] ?? '';
        $price    = floatval($item['price'] ?? 0);
        $quantity = intval($item['qty'] ?? 1);

        $itemStmt->execute([
            $orderDbId,
            $foodId,
            $name,
            $price,
            $quantity
        ]);
    }

    // Commit all queries
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully! 🚀',
        'order_id' => $orderId
    ]);

} catch (\Exception $e) {
    // Rollback changes on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order: ' . $e->getMessage()
    ]);
}
?>
