<?php
// place_order_api.php — AJAX Place Order Handler (Supports Guest Checkout)

session_start();
header('Content-Type: application/json');

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

// Determine User ID (Support logged-in or dynamic guest user creation)
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
} else {
    // Guest Checkout flow
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    
    if (!$email || !$phone) {
        echo json_encode(['success' => false, 'message' => 'Email and Phone Number are required for guest checkout.']);
        exit();
    }
    
    try {
        // Check if a user with this email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $existingUser = $stmt->fetch();
        
        if ($existingUser) {
            $userId = $existingUser['id'];
        } else {
            // Auto-create a guest user account
            $usernameParts = explode('@', $email);
            $name = trim(strtoupper($usernameParts[0])) . ' (Guest)';
            $randomPassword = bin2hex(random_bytes(16));
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            
            $insertUser = $pdo->prepare("INSERT INTO users (name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
            $insertUser->execute([$name, $email, $phone, $hashedPassword, $address]);
            $userId = $pdo->lastInsertId();
        }
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to process guest user: ' . $e->getMessage()]);
        exit();
    }
}

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
        'message' => 'Order placed successfully!',
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
