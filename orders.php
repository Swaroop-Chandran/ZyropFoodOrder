<?php
session_start();
// Security Check — Only logged-in users can view order history
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode("orders.php"));
    exit();
}

require_once 'db.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Fetch all user orders
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    // Fetch items for each order
    $orderItems = [];
    if (!empty($orders)) {
        $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        foreach ($orders as $order) {
            $itemStmt->execute([$order['id']]);
            $orderItems[$order['id']] = $itemStmt->fetchAll();
        }
    }

    // Compute stats
    $totalSpent = 0;
    $totalOrders = count($orders);
    $activeOrdersCount = 0;
    foreach ($orders as $order) {
        $totalSpent += floatval($order['total']);
        if ($order['status'] !== 'arrived') {
            $activeOrdersCount++;
        }
    }
} catch (\Exception $e) {
    $orders = [];
    $totalOrders = 0;
    $totalSpent = 0;
    $activeOrdersCount = 0;
    $error = $e->getMessage();
}

// Payment Mode helpers
$pmIcons   = ['card' => 'credit_card', 'upi' => 'bolt', 'cod' => 'payments'];
$pmLabels  = ['card' => 'Card Payment', 'upi' => 'UPI ⚡', 'cod' => 'Cash on Delivery'];

// Status helpers
function getStatusConfig($status) {
    switch ($status) {
        case 'placed':
            return ['label' => 'Order Placed', 'icon' => 'receipt_long', 'color' => 'text-amber-600 bg-amber-50 border-amber-200', 'is_active' => true];
        case 'accepted':
            return ['label' => 'Accepted', 'icon' => 'check_circle', 'color' => 'text-blue-600 bg-blue-50 border-blue-200', 'is_active' => true];
        case 'cooking':
            return ['label' => 'Preparing', 'icon' => 'soup_kitchen', 'color' => 'text-orange-600 bg-orange-50 border-orange-200', 'is_active' => true];
        case 'picked':
            return ['label' => 'On the way', 'icon' => 'delivery_dining', 'color' => 'text-indigo-600 bg-indigo-50 border-indigo-200', 'is_active' => true];
        case 'arrived':
        default:
            return ['label' => 'Delivered', 'icon' => 'where_to_vote', 'color' => 'text-green-600 bg-green-50 border-green-200', 'is_active' => false];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Orders — ZyropFoodOrder</title>
  <meta name="description" content="View and track your previous food orders, prepare a reorder or get support."/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/zyrop.css"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode:"class",
      theme:{extend:{colors:{"surface-container-low":"#f5f3f3","surface-container-lowest":"#ffffff","surface-bright":"#fbf9f8","on-error":"#ffffff","on-primary":"#ffffff","outline":"#907065","surface-container-high":"#e9e8e7","on-tertiary":"#ffffff","surface-variant":"#e4e2e2","tertiary":"#006b29","surface-dim":"#dbdad9","on-secondary":"#ffffff","error":"#ba1a1a","surface":"#fbf9f8","primary-fixed":"#ffdbd0","primary-container":"#d24200","primary":"#a83300","error-container":"#ffdad6","on-surface-variant":"#5c4037","secondary":"#5f5e5e","tertiary-container":"#008735","inverse-surface":"#303031","on-background":"#1b1c1c","background":"#fbf9f8","outline-variant":"#e5beb2","inverse-on-surface":"#f2f0f0","surface-tint":"#ac3500","secondary-container":"#e5e2e1","surface-container":"#efeded","primary-fixed-dim":"#ffb59d","on-surface":"#1b1c1c","surface-container-highest":"#e4e2e2","inverse-primary":"#ffb59d"}}}
    }
  </script>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<!-- ===== HEADER ===== -->
<header class="bg-surface border-b border-outline-variant/30 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 h-16 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="index.php" class="flex items-center gap-1 text-secondary hover:text-primary transition-colors font-semibold text-sm">
        <span class="material-symbols-outlined" style="font-size:20px">arrow_back</span>
        Back to Menu
      </a>
      <div class="h-5 w-px bg-outline-variant hidden sm:block"></div>
      <a href="index.php" class="font-extrabold text-xl text-primary hidden sm:block">ZyropFoodOrder</a>
    </div>
    <h1 class="font-extrabold text-lg text-on-surface">Order History</h1>
    
    <!-- Account Info Header Menu -->
    <div class="flex items-center gap-2 border border-outline-variant/30 rounded-full px-3 py-1 bg-surface-container-low">
      <span class="material-symbols-outlined text-primary" style="font-size:18px">account_circle</span>
      <span class="text-xs font-bold text-on-surface truncate max-w-[100px]"><?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
      <a href="logout.php" class="material-symbols-outlined text-secondary hover:text-error transition-colors" style="font-size:16px" title="Logout">logout</a>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-10">

  <!-- ===== STATS BANNER ===== -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10 animate-fade-in-up">
    <!-- Stat 1: Total Orders -->
    <div class="bg-white rounded-3xl border border-outline-variant/20 p-6 flex items-center gap-5 shadow-sm hover:shadow-md transition-shadow">
      <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
        <span class="material-symbols-outlined text-3xl font-semibold">receipt_long</span>
      </div>
      <div>
        <p class="text-xs font-semibold text-secondary">Total Orders</p>
        <h3 class="text-2xl font-extrabold text-on-surface mt-0.5"><?= $totalOrders ?></h3>
      </div>
    </div>

    <!-- Stat 2: Total Spent -->
    <div class="bg-white rounded-3xl border border-outline-variant/20 p-6 flex items-center gap-5 shadow-sm hover:shadow-md transition-shadow">
      <div class="w-14 h-14 rounded-2xl bg-tertiary/10 flex items-center justify-center text-tertiary flex-shrink-0">
        <span class="material-symbols-outlined text-3xl font-semibold">payments</span>
      </div>
      <div>
        <p class="text-xs font-semibold text-secondary">Total Investment</p>
        <h3 class="text-2xl font-extrabold text-on-surface mt-0.5">&#8377;<?= number_format($totalSpent, 2) ?></h3>
      </div>
    </div>

    <!-- Stat 3: Active Orders -->
    <div class="bg-white rounded-3xl border border-outline-variant/20 p-6 flex items-center gap-5 shadow-sm hover:shadow-md transition-shadow">
      <div class="w-14 h-14 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-600 flex-shrink-0">
        <span class="material-symbols-outlined text-3xl font-semibold">schedule</span>
      </div>
      <div>
        <p class="text-xs font-semibold text-secondary">Active Prepared</p>
        <h3 class="text-2xl font-extrabold text-on-surface mt-0.5"><?= $activeOrdersCount ?></h3>
      </div>
    </div>
  </div>

  <!-- ===== ORDER HISTORY CONTAINER ===== -->
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h2 class="font-extrabold text-xl text-on-surface flex items-center gap-2">
        <span class="material-symbols-outlined text-primary">history</span>
        Your Orders
      </h2>
      <span class="text-sm font-semibold text-secondary bg-surface-container px-4 py-1.5 rounded-full"><?= $totalOrders ?> item<?= $totalOrders !== 1 ? 's':'' ?> placed</span>
    </div>

    <?php if (empty($orders)): ?>
      <!-- ===== EMPTY ORDER HISTORY STATE ===== -->
      <div class="bg-white rounded-3xl border border-outline-variant/30 p-16 text-center flex flex-col items-center justify-center gap-6 animate-scale-in">
        <div class="text-7xl">🍔</div>
        <div>
          <h3 class="text-2xl font-extrabold text-on-surface">No orders placed yet</h3>
          <p class="text-secondary text-sm mt-2 max-w-sm mx-auto">Explore delicious dishes from the home page and place your very first order today!</p>
        </div>
        <a href="index.php" class="btn-primary">
          <span class="material-symbols-outlined" style="font-size:20px">restaurant_menu</span>
          Explore Menu
        </a>
      </div>
    <?php else: ?>

      <!-- ===== ORDERS GRID/LIST ===== -->
      <div class="flex flex-col gap-6">
        <?php foreach ($orders as $index => $order): 
            $items = $orderItems[$order['id']] ?? [];
            $status = getStatusConfig($order['status']);
            $date = new DateTime($order['created_at']);
            $formattedDate = $date->format('M d, Y • h:i A');
        ?>
          <div class="bg-white rounded-3xl border border-outline-variant/30 overflow-hidden shadow-sm hover:shadow-md transition-all animate-fade-in-up" style="animation-delay: <?= $index * 0.05 ?>s;">
            <!-- Header section of Card -->
            <div class="p-6 bg-surface-container-low border-b border-outline-variant/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <p class="text-xs text-secondary font-semibold">Placed on <?= $formattedDate ?></p>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-xs text-secondary">ID:</span>
                  <span class="font-extrabold text-on-surface text-sm font-mono" id="id-<?= $order['order_id'] ?>"><?= htmlspecialchars($order['order_id']) ?></span>
                  <button onclick="copyOrderId('<?= $order['order_id'] ?>')" class="material-symbols-outlined text-secondary hover:text-primary transition-colors" style="font-size:16px;" title="Copy Order ID">content_copy</button>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs font-bold border rounded-full px-3 py-1 inline-flex items-center gap-1.5 <?= $status['color'] ?>">
                  <span class="material-symbols-outlined" style="font-size:16px;"><?= $status['icon'] ?></span>
                  <?= $status['label'] ?>
                </span>
                <?php if ($status['is_active']): ?>
                  <a href="order-confirmation.php?id=<?= $order['order_id'] ?>" class="text-xs font-bold text-primary bg-primary-fixed hover:bg-primary-container hover:text-white px-3 py-1 rounded-full transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined" style="font-size:14px;animation:spin 2s linear infinite">progress_activity</span>
                    Track Live
                  </a>
                <?php endif; ?>
              </div>
            </div>

            <!-- Items & Details body -->
            <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Column 1: Items List -->
                <div class="md:col-span-2 space-y-4">
                  <h4 class="text-xs font-bold uppercase tracking-wider text-secondary">Items Ordered</h4>
                  <div class="divide-y divide-outline-variant/10">
                    <?php foreach ($items as $item): ?>
                      <div class="py-2.5 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3 min-w-0">
                          <span class="text-lg flex-shrink-0">🍽️</span>
                          <div class="min-w-0">
                            <p class="font-bold text-on-surface truncate"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="text-xs text-secondary"><?= $item['quantity'] ?> × &#8377;<?= number_format($item['price'], 2) ?></p>
                          </div>
                        </div>
                        <span class="font-extrabold text-on-surface">&#8377;<?= number_format($item['quantity'] * $item['price'], 2) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Column 2: Cost Summary & Delivery Address -->
                <div class="bg-surface-container-low/50 border border-outline-variant/20 rounded-2xl p-5 flex flex-col justify-between gap-4">
                  <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-secondary mb-3">Order Details</h4>
                    <div class="space-y-1.5 text-xs">
                      <div class="flex justify-between text-secondary">
                        <span>Payment Mode</span>
                        <span class="font-semibold flex items-center gap-1 text-on-surface">
                          <span class="material-symbols-outlined" style="font-size:14px;"><?= $pmIcons[$order['payment_mode']] ?? 'payments' ?></span>
                          <?= $pmLabels[$order['payment_mode']] ?? 'Cash' ?>
                        </span>
                      </div>
                      <div class="flex justify-between text-secondary">
                        <span>GST + Platform</span>
                        <span class="font-semibold text-on-surface">&#8377;<?= number_format(floatval($order['gst']) + floatval($order['platform_fee']), 2) ?></span>
                      </div>
                      <?php if (floatval($order['discount']) > 0): ?>
                        <div class="flex justify-between text-tertiary">
                          <span>Discount Saved</span>
                          <span class="font-bold">-&#8377;<?= number_format($order['discount'], 2) ?></span>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="border-t border-outline-variant/30 pt-3 flex justify-between items-center">
                    <span class="font-extrabold text-sm">Amount Paid</span>
                    <span class="font-extrabold text-base text-primary">&#8377;<?= number_format($order['total'], 2) ?></span>
                  </div>
                </div>
              </div>

              <!-- Address summary at footer -->
              <div class="border-t border-outline-variant/10 mt-6 pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                <div class="flex items-start gap-2 max-w-lg text-secondary">
                  <span class="material-symbols-outlined text-primary" style="font-size:18px;">location_on</span>
                  <span><strong>Delivered to: </strong><?= htmlspecialchars($order['address']) ?></span>
                </div>
                <div class="flex items-center gap-3">
                  <button onclick="reorderItems('<?= htmlspecialchars(json_encode($items)) ?>')" class="flex items-center gap-1.5 text-primary hover:text-white border border-primary bg-transparent hover:bg-primary rounded-full px-4 py-2 font-bold transition-all transform hover:-translate-y-0.5 active:scale-95 text-xs cursor-pointer">
                    <span class="material-symbols-outlined" style="font-size:16px;">restaurant_menu</span>
                    Reorder Items
                  </button>
                  <button onclick="contactSupport('<?= htmlspecialchars($order['order_id']) ?>')" class="flex items-center gap-1.5 text-secondary hover:text-on-surface border border-outline-variant hover:bg-surface-container rounded-full px-4 py-2 font-semibold transition-all text-xs">
                    <span class="material-symbols-outlined" style="font-size:16px;">support_agent</span>
                    Get Help
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>
  </div>

</main>

<div id="toast-container"></div>
<script src="js/cart.js"></script>
<script>
  function copyOrderId(id) {
    navigator.clipboard?.writeText(id).then(() => {
      showToast('Order ID copied to clipboard! 📋', 'success');
    });
  }

  function contactSupport(orderId) {
    showToast(`Support ticket opened for Order ID: ${orderId} 📞`, 'info', 4000);
  }

  // Static food items database mapping for high-fidelity reordering
  const FOOD_METADATA = {
    'f01': { restaurant: "Baker's Basket", veg: true, image: 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80' },
    'f02': { restaurant: 'Dhaba Junction', veg: true, image: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=400&q=80' },
    'f03': { restaurant: 'Dhaba Junction', veg: true, image: 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=400&q=80' },
    'f04': { restaurant: 'Spice Garden', veg: true, image: 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&q=80' },
    'f05': { restaurant: 'Spice Garden', veg: true, image: 'https://images.unsplash.com/photo-1630851840633-f96999247032?w=400&q=80' },
    'f06': { restaurant: 'Biryani House', veg: false, image: 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400&q=80' },
    'f07': { restaurant: 'Wok & Roll', veg: true, image: 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&q=80' },
    'f08': { restaurant: 'Egg Station', veg: false, image: 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=400&q=80' },
    'f09': { restaurant: 'Egg Station', veg: false, image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=400&q=80' },
    'f10': { restaurant: 'Egg Station', veg: false, image: 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=400&q=80' },
    'f11': { restaurant: 'Saravana Bhavan', veg: true, image: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80' },
    'f12': { restaurant: 'Saravana Bhavan', veg: true, image: 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=400&q=80' },
    'f13': { restaurant: 'Shahi Rasoi', veg: true, image: 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=400&q=80' },
    'f14': { restaurant: 'Punjab Grill', veg: false, image: 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=400&q=80' },
    'f15': { restaurant: "Nawab's Kitchen", veg: false, image: 'https://images.unsplash.com/photo-1574653853027-5382a3d23a15?w=400&q=80' },
    'f16': { restaurant: 'Wok & Roll', veg: true, image: 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=400&q=80' },
    'f17': { restaurant: 'Pasta Fresca', veg: true, image: 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400&q=80' },
    'f18': { restaurant: 'Pizza Primo', veg: true, image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80' },
    'f19': { restaurant: 'Green Plates', veg: true, image: 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=400&q=80' },
    'f20': { restaurant: 'Frontier Grill', veg: false, image: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c3?w=400&q=80' },
    'f21': { restaurant: 'Green Plates', veg: true, image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&q=80' }
  };

  // Multi-item reordering script leveraging ZyropCart
  function reorderItems(itemsJSON) {
    try {
      const items = JSON.parse(itemsJSON);
      if (!Array.isArray(items) || items.length === 0) return;
      
      showToast('Readding items to your cart... 🛒', 'info', 1500);
      
      // Inject to local ZyropCart
      items.forEach(item => {
        const meta = FOOD_METADATA[item.food_id] || {
          restaurant: 'N/A',
          veg: true,
          image: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80'
        };

        ZyropCart.addItem({
          id: item.food_id,
          name: item.name,
          price: parseFloat(item.price),
          image: meta.image,
          restaurant: meta.restaurant,
          veg: meta.veg
        });
        
        // Match the quantity
        const neededQty = parseInt(item.quantity) - 1;
        for (let i = 0; i < neededQty; i++) {
          ZyropCart.incrementItem(item.food_id);
        }
      });
      
      showToast('All items added! Redirecting to cart... 🛒', 'success', 1500);
      setTimeout(() => {
        window.location.href = 'cart.php';
      }, 1200);
    } catch (e) {
      showToast('Could not process reorder. Please try again.', 'error');
    }
  }
</script>
</body>
</html>
