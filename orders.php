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
$pmLabels  = ['card' => 'Card Payment', 'upi' => 'UPI', 'cod' => 'Cash on Delivery'];

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
<?php
$pageTitle = 'Your Orders — Zesto';
$pageDesc = 'View and track your previous food orders, prepare a reorder or get support.';
$pageTheme = 'light';
include 'header.php';
?>

<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 py-12 relative z-10">

  <!-- ===== STATS BANNER ===== -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10 animate-fade-in-up">
    <!-- Stat 1: Total Orders -->
    <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-6 flex items-center gap-5 bg-white shadow-sm">
      <div class="w-12 h-12 rounded bg-zinc-50 border border-zinc-200 flex items-center justify-center text-primary flex-shrink-0">
        <span class="material-symbols-outlined text-2xl font-bold">receipt_long</span>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wider font-bold text-zinc-500">Total Orders</p>
        <h3 class="text-2xl font-title font-extrabold text-zinc-900 mt-1"><?= $totalOrders ?></h3>
      </div>
    </div>

    <!-- Stat 2: Total Spent -->
    <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-6 flex items-center gap-5 bg-white shadow-sm">
      <div class="w-12 h-12 rounded bg-zinc-50 border border-zinc-200 flex items-center justify-center text-[#526043] flex-shrink-0">
        <span class="material-symbols-outlined text-2xl font-bold">payments</span>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wider font-bold text-zinc-500">Total Spent</p>
        <h3 class="text-2xl font-title font-extrabold text-zinc-900 mt-1">&#8377;<?= number_format($totalSpent, 2) ?></h3>
      </div>
    </div>

    <!-- Stat 3: Active Orders -->
    <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-6 flex items-center gap-5 bg-white shadow-sm">
      <div class="w-12 h-12 rounded bg-zinc-50 border border-zinc-200 flex items-center justify-center text-amber-600 flex-shrink-0">
        <span class="material-symbols-outlined text-2xl font-bold">schedule</span>
      </div>
      <div>
        <p class="text-xs uppercase tracking-wider font-bold text-zinc-500">Active Orders</p>
        <h3 class="text-2xl font-title font-extrabold text-zinc-900 mt-1"><?= $activeOrdersCount ?></h3>
      </div>
    </div>
  </div>

  <!-- ===== ORDER HISTORY CONTAINER ===== -->
  <div class="space-y-8">
    <div class="flex items-center justify-between border-b border-zinc-200/60 pb-4">
      <h2 class="font-title text-2xl font-bold text-zinc-900 flex items-center gap-2">
        Your Order History
      </h2>
      <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 bg-zinc-100 border border-zinc-250/50 px-4 py-1.5 rounded"><?= $totalOrders ?> order<?= $totalOrders !== 1 ? 's':'' ?> placed</span>
    </div>

    <?php if (empty($orders)): ?>
      <!-- ===== EMPTY ORDER HISTORY STATE ===== -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-16 text-center flex flex-col items-center justify-center gap-6 bg-white animate-scale-in">
        <span class="material-symbols-outlined text-zinc-300" style="font-size: 64px">receipt_long</span>
        <div>
          <h3 class="font-title text-2xl font-extrabold text-zinc-900">No orders placed yet</h3>
          <p class="text-zinc-500 text-sm mt-2 max-w-sm mx-auto leading-relaxed">Explore delicious dishes from the home page and place your very first order today!</p>
        </div>
        <a href="index.php#menu" class="btn-primary mt-2 uppercase tracking-widest text-xs font-bold py-3.5 px-8">
          Explore Menu
        </a>
      </div>
    <?php else: ?>

      <!-- ===== ORDERS GRID/LIST ===== -->
      <div class="flex flex-col gap-8">
        <?php foreach ($orders as $index => $order): 
            $items = $orderItems[$order['id']] ?? [];
            $status = getStatusConfig($order['status']);
            $date = new DateTime($order['created_at']);
            $formattedDate = $date->format('M d, Y • h:i A');
        ?>
          <div class="zesto-glass-card rounded-lg border border-zinc-200/60 overflow-hidden bg-white shadow-sm transition-all animate-fade-in-up" style="animation-delay: <?= $index * 0.05 ?>s;">
            <!-- Header section of Card -->
            <div class="p-6 bg-zinc-50 border-b border-zinc-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <p class="text-xs uppercase tracking-wider font-bold text-zinc-400">Placed on <?= $formattedDate ?></p>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-xs uppercase tracking-wider font-bold text-zinc-400">ID:</span>
                  <span class="font-bold text-zinc-800 text-sm font-mono" id="id-<?= $order['order_id'] ?>"><?= htmlspecialchars($order['order_id']) ?></span>
                  <button onclick="copyOrderId('<?= $order['order_id'] ?>')" class="material-symbols-outlined text-zinc-400 hover:text-primary transition-colors" style="font-size:16px;" title="Copy Order ID">content_copy</button>
                </div>
              </div>
              <div class="flex items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider border rounded px-3 py-1 inline-flex items-center gap-1.5 <?= $status['color'] ?>">
                  <span class="material-symbols-outlined" style="font-size:16px;"><?= $status['icon'] ?></span>
                  <?= $status['label'] ?>
                </span>
                <?php if ($status['is_active']): ?>
                  <a href="order-confirmation.php?id=<?= $order['order_id'] ?>" class="text-xs font-bold uppercase tracking-widest text-primary bg-primary/10 hover:bg-primary hover:text-white px-4 py-1.5 rounded transition-colors flex items-center gap-1.5 border border-primary/20">
                    <span class="material-symbols-outlined" style="font-size:14px;animation:spin 2s linear infinite">progress_activity</span>
                    Track Live
                  </a>
                <?php endif; ?>
              </div>
            </div>

            <!-- Items & Details body -->
            <div class="p-8">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Column 1: Items List -->
                <div class="md:col-span-2 space-y-4">
                  <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Items Ordered</h4>
                  <div class="divide-y divide-zinc-200/60">
                    <?php foreach ($items as $item): ?>
                      <div class="py-3 flex items-center justify-between text-sm">
                        <div class="flex items-center gap-3 min-w-0">
                          <span class="w-1.5 h-1.5 rounded-full bg-zinc-400 flex-shrink-0"></span>
                          <div class="min-w-0">
                            <p class="font-bold text-zinc-800 truncate"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="text-xs font-bold text-zinc-400 mt-0.5"><?= $item['quantity'] ?> × &#8377;<?= number_format($item['price'], 2) ?></p>
                          </div>
                        </div>
                        <span class="font-extrabold text-zinc-900">&#8377;<?= number_format($item['quantity'] * $item['price'], 2) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Column 2: Cost Summary & Details -->
                <div class="bg-zinc-50 border border-zinc-200/60 rounded p-6 flex flex-col justify-between gap-5">
                  <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-4">Order Details</h4>
                    <div class="space-y-2.5 text-xs font-bold uppercase tracking-wider text-zinc-500">
                      <div class="flex justify-between">
                        <span>Payment Mode</span>
                        <span class="font-extrabold flex items-center gap-1 text-zinc-800">
                          <span class="material-symbols-outlined" style="font-size:14px;"><?= $pmIcons[$order['payment_mode']] ?? 'payments' ?></span>
                          <?= $pmLabels[$order['payment_mode']] ?? 'Cash' ?>
                        </span>
                      </div>
                      <div class="flex justify-between">
                        <span>GST + Platform</span>
                        <span class="font-extrabold text-zinc-850">&#8377;<?= number_format(floatval($order['gst']) + floatval($order['platform_fee']), 2) ?></span>
                      </div>
                      <?php if (floatval($order['discount']) > 0): ?>
                        <div class="flex justify-between text-tertiary">
                          <span>Discount Saved</span>
                          <span class="font-extrabold text-tertiary">-&#8377;<?= number_format($order['discount'], 2) ?></span>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="border-t border-zinc-200/60 pt-4 flex justify-between items-center">
                    <span class="font-title text-sm font-bold text-zinc-900">Amount Paid</span>
                    <span class="font-title text-lg font-extrabold text-primary">&#8377;<?= number_format($order['total'], 2) ?></span>
                  </div>
                </div>
              </div>

              <!-- Address summary at footer -->
              <div class="border-t border-zinc-200/60 mt-8 pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                <div class="flex items-start gap-2 max-w-lg text-zinc-400 font-bold uppercase tracking-wider leading-relaxed">
                  <span class="material-symbols-outlined text-primary" style="font-size:18px;">location_on</span>
                  <span><strong>Delivered to: </strong><span class="text-zinc-500 font-semibold lowercase first-letter:uppercase"><?= htmlspecialchars($order['address']) ?></span></span>
                </div>
                <div class="flex items-center gap-3">
                  <button onclick="reorderItems('<?= htmlspecialchars(json_encode($items)) ?>')" class="flex items-center gap-1.5 text-primary hover:text-white border border-primary bg-transparent hover:bg-primary rounded px-4 py-2 font-bold uppercase tracking-wider transition-all text-xs cursor-pointer">
                    <span class="material-symbols-outlined" style="font-size:16px;">restaurant_menu</span>
                    Reorder
                  </button>
                  <button onclick="contactSupport('<?= htmlspecialchars($order['order_id']) ?>')" class="flex items-center gap-1.5 text-zinc-400 hover:text-zinc-700 border border-zinc-200 hover:bg-zinc-50 rounded px-4 py-2 font-bold uppercase tracking-wider transition-all text-xs">
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
      showToast('Order ID copied to clipboard!', 'success');
    });
  }

  function contactSupport(orderId) {
    showToast(`Support ticket opened for Order ID: ${orderId}`, 'info', 4000);
  }

  // Static food items database mapping for high-fidelity reordering
  const FOOD_METADATA = {
    'f01': { restaurant: "Baker's Basket Bread", veg: true, image: 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80' },
    'f02': { restaurant: 'Steamed Momo Set', veg: true, image: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=400&q=80' },
    'f03': { restaurant: 'Crispy Samosa Plate', veg: true, image: 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=400&q=80' },
    'f04': { restaurant: 'Steamed Rice', veg: true, image: 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&q=80' },
    'f05': { restaurant: 'Jeera Pilaf Rice', veg: true, image: 'https://images.unsplash.com/photo-1630851840633-f96999247032?w=400&q=80' },
    'f06': { restaurant: 'Hyderabadi Chicken Biryani', veg: false, image: 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400&q=80' },
    'f07': { restaurant: 'Indo-Chinese Noodles', veg: true, image: 'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&q=80' },
    'f08': { restaurant: 'Boiled Eggs Set', veg: false, image: 'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=400&q=80' },
    'f09': { restaurant: 'Sunny Side Up Eggs', veg: false, image: 'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=400&q=80' },
    'f10': { restaurant: 'Scrambled Eggs Special', veg: false, image: 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=400&q=80' },
    'f11': { restaurant: 'Steamed Rice Idli', veg: true, image: 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80' },
    'f12': { restaurant: 'Crispy Masala Dosa', veg: true, image: 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=400&q=80' },
    'f13': { restaurant: 'Kadai Paneer Curry', veg: true, image: 'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=400&q=80' },
    'f14': { restaurant: 'Mughlai Chicken Curry', veg: false, image: 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=400&q=80' },
    'f15': { restaurant: 'Slow-Cooked Mutton Korma', veg: false, image: 'https://images.unsplash.com/photo-1574653853027-5382a3d23a15?w=400&q=80' },
    'f16': { restaurant: 'Indo-Chinese Noodles', veg: true, image: 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=400&q=80' },
    'f17': { restaurant: 'Pasta Arrabiata', veg: true, image: 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400&q=80' },
    'f18': { restaurant: 'Margherita Classic Pizza', veg: true, image: 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80' },
    'f19': { restaurant: 'Butter Mashed Potatoes', veg: true, image: 'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=400&q=80' },
    'f20': { restaurant: 'Gourmet Grilled Chicken', veg: false, image: 'https://images.unsplash.com/photo-1598103442097-8b74394b95c3?w=400&q=80' },
    'f21': { restaurant: 'Roasted Herb Vegetables', veg: true, image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&q=80' }
  };

  // Multi-item reordering script leveraging ZyropCart
  function reorderItems(itemsJSON) {
    try {
      const items = JSON.parse(itemsJSON);
      if (!Array.isArray(items) || items.length === 0) return;
      
      showToast('Adding items to your cart...', 'info', 1500);
      
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
      
      showToast('All items added! Redirecting to cart...', 'success', 1500);
      setTimeout(() => {
        window.location.href = 'cart.php';
      }, 1200);
    } catch (e) {
      showToast('Could not process reorder. Please try again.', 'error');
    }
  }
</script>
<?php include 'footer.php'; ?>
