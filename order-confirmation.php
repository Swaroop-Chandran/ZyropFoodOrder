<?php
session_start();
// Gated behind active session check, preserving redirect location with Order ID
if (!isset($_SESSION['user_id'])) {
    $orderId = $_GET['id'] ?? '';
    header("Location: login.php?redirect=" . urlencode("order-confirmation.php?id=" . $orderId));
    exit();
}

$orderId = $_GET['id'] ?? '';
if (!$orderId) {
    header("Location: index.php");
    exit();
}

require_once 'db.php';

try {
    // Fetch order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if (!$order) {
        header("Location: index.php");
        exit();
    }

    // Fetch items in the order
    $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->execute([$order['id']]);
    $orderItems = $itemStmt->fetchAll();

} catch (\Exception $e) {
    die("Error loading order: " . $e->getMessage());
}

$pmIcons   = ['card' => 'credit_card', 'upi' => 'bolt', 'cod' => 'payments'];
$pmLabels  = ['card' => 'Paid via Card', 'upi' => 'Paid via UPI', 'cod' => 'Cash on Delivery'];
$paymentMode = $order['payment_mode'] ?? 'cod';
?>
<?php
$pageTitle = 'Order Confirmed — Zesto';
$pageDesc = 'Your food order has been placed successfully! Track your delivery in real time.';
$pageTheme = 'light';
include 'header.php';
?>

<style>
  /* Pulsing delivery dot */
  @keyframes delivery-pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(138,79,53,0.3); }
    50%       { transform: scale(1.15); box-shadow: 0 0 0 6px rgba(138,79,53,0); }
  }
  .delivery-dot { animation: delivery-pulse 1.8s ease-in-out infinite; }

  /* Track progress fill animation */
  .track-fill {
    height: 2px;
    background: #526043;
    transition: width 1s ease;
  }

  /* ETA ring visual */
  .eta-ring-premium {
    width: 72px; height: 72px;
    border-radius: 50%;
    border: 2px solid #e8e5e0;
    display: flex; align-items: center; justify-content: center;
  }
</style>

<!-- ===== STEP PROGRESS ===== -->
<div class="border-b border-zinc-200/60 bg-white relative z-20">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-6">
    <div class="step-bar max-w-sm mx-auto flex items-center justify-between">
      <div class="step-item done flex flex-col items-center">
        <div class="step-circle font-bold">✓</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5 text-[#526043]">Cart</span>
      </div>
      <div class="step-item done flex flex-col items-center">
        <div class="step-circle font-bold">✓</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5 text-[#526043]">Payment</span>
      </div>
      <div class="step-item active flex flex-col items-center">
        <div class="step-circle font-bold">3</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5">Confirm</span>
      </div>
    </div>
  </div>
</div>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-12 pb-24 relative z-10">
  <div class="flex flex-col lg:flex-row gap-10">

    <!-- Left: Success + Tracking -->
    <div class="flex-1 flex flex-col gap-8">

      <!-- Success Card -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 text-center bg-white animate-scale-in">
        <!-- Minimal Checkmark Icon -->
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16 rounded-full bg-[#526043]/10 border border-[#526043]/20 flex items-center justify-center text-[#526043]">
            <span class="material-symbols-outlined" style="font-size:32px">verified</span>
          </div>
        </div>

        <h1 class="font-title text-3xl font-extrabold text-zinc-900 mb-2">Order Confirmed</h1>
        <p class="text-zinc-500 text-sm mb-6">Your order is being freshly prepared by the kitchen.</p>

        <div class="inline-flex items-center gap-3 bg-zinc-50 border border-zinc-200 rounded px-5 py-3 mb-8">
          <span class="material-symbols-outlined text-zinc-400" style="font-size:18px">receipt_long</span>
          <span class="text-xs uppercase tracking-wider font-bold text-zinc-400">Order ID:</span>
          <span class="font-bold text-zinc-800 text-sm font-mono" id="order-id"><?= htmlspecialchars($order['order_id']) ?></span>
          <button onclick="copyOrderId()" class="material-symbols-outlined text-zinc-450 hover:text-primary transition-colors" style="font-size:16px">content_copy</button>
        </div>

        <!-- ETA -->
        <div class="flex items-center justify-center gap-6 border-t border-zinc-200/60 pt-6">
          <div class="eta-ring-premium">
            <div class="text-center">
              <p class="text-xl font-extrabold text-primary font-title" id="eta-minutes">35</p>
              <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-wider">MINS</p>
            </div>
          </div>
          <div class="text-left">
            <p class="text-xs uppercase tracking-wider font-bold text-zinc-400">Estimated Delivery</p>
            <p class="text-zinc-850 font-bold text-sm mt-0.5" id="eta-time">by 00:00 AM</p>
            <div class="flex items-center gap-1.5 mt-1.5">
              <span class="w-2 h-2 bg-[#526043] rounded-full delivery-dot"></span>
              <span class="text-xs text-[#526043] font-bold uppercase tracking-wide">Live Tracking Active</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Tracking Timeline -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white">
        <h2 class="font-title text-xl font-bold text-zinc-900 mb-8">
          Delivery Tracking
        </h2>

        <div class="flex flex-col gap-6" id="tracking-steps">
          <!-- Rendered by JS -->
        </div>
      </div>

      <!-- Delivery Address Reminder -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-6 bg-white">
        <div class="flex items-start gap-4">
          <span class="material-symbols-outlined text-primary mt-0.5" style="font-size:22px">location_on</span>
          <div class="flex-1">
            <p class="text-xs uppercase tracking-wider font-bold text-zinc-400">Delivering to</p>
            <p class="text-sm font-semibold text-zinc-800 mt-1" id="confirm-addr"><?= htmlspecialchars($order['address']) ?></p>
          </div>
          <div class="flex items-center gap-1 text-[10px] font-bold uppercase tracking-wider text-zinc-500 bg-zinc-50 border border-zinc-200 rounded px-3 py-1">
            <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
            <span id="confirm-time">~35 min</span>
          </div>
        </div>
      </div>

    </div>

    <!-- Right: Order Details -->
    <div class="lg:w-[340px] xl:w-[380px] flex-shrink-0 flex flex-col gap-6">

      <!-- Order Items -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white">
        <h2 class="font-title text-lg font-bold text-zinc-900 mb-5">Your Order</h2>
        <div id="confirm-items" class="flex flex-col gap-4">
          <?php foreach ($orderItems as $item): ?>
            <div class="flex items-center gap-3">
              <div class="w-1.5 h-1.5 rounded-full bg-zinc-400 flex-shrink-0"></div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-zinc-800 truncate"><?= htmlspecialchars($item['name']) ?></p>
                <p class="text-xs font-bold text-zinc-450 mt-0.5"><?= $item['quantity'] ?> × ₹<?= number_format($item['price'], 2) ?></p>
              </div>
              <span class="text-sm font-extrabold text-zinc-900">₹<?= number_format($item['quantity'] * $item['price'], 2) ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Bill Summary -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white">
        <h2 class="font-title text-lg font-bold text-zinc-900 mb-5">Bill Summary</h2>
        <div class="flex flex-col gap-3 text-xs font-bold uppercase tracking-wider text-zinc-500" id="confirm-summary">
          <div class="flex justify-between"><span>Subtotal</span><span class="font-extrabold text-zinc-900">₹<?= number_format($order['subtotal'], 2) ?></span></div>
          <div class="flex justify-between"><span>Delivery</span><span class="font-extrabold text-zinc-900">₹<?= number_format($order['delivery_fee'], 2) ?></span></div>
          <div class="flex justify-between"><span>Platform fee</span><span class="font-extrabold text-zinc-900">₹<?= number_format($order['platform_fee'], 2) ?></span></div>
          <div class="flex justify-between"><span>GST</span><span class="font-extrabold text-zinc-900">₹<?= number_format($order['gst'], 2) ?></span></div>
          <?php if ($order['discount'] > 0): ?>
            <div class="flex justify-between text-tertiary"><span>Discount</span><span class="font-extrabold text-tertiary">-₹<?= number_format($order['discount'], 2) ?></span></div>
          <?php endif; ?>
        </div>
        <div class="border-t border-zinc-200/60 mt-5 pt-4 flex justify-between items-center">
          <span class="font-title text-base font-bold text-zinc-900">Total Paid</span>
          <span class="font-title text-xl font-extrabold text-primary" id="confirm-total">₹<?= number_format($order['total'], 2) ?></span>
        </div>
        <div class="mt-4 flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-400">
          <span class="material-symbols-outlined" style="font-size:16px" id="confirm-pm-icon"><?= $pmIcons[$paymentMode] ?? 'payments' ?></span>
          <span id="confirm-pm-label"><?= $pmLabels[$paymentMode] ?? 'Paid Securely' ?></span>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col gap-4">
        <a href="index.php" class="btn-primary w-full uppercase tracking-widest text-xs font-bold py-3.5">
          Back to Home
        </a>
        <a href="index.php#menu" class="btn-outline w-full text-center uppercase tracking-widest text-xs font-bold py-3.5 rounded flex items-center justify-center gap-1.5">
          Order Again
        </a>
        <button onclick="shareOrder()" class="flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider text-zinc-400 hover:text-zinc-800 transition-colors py-2">
          <span class="material-symbols-outlined" style="font-size:18px">share</span>
          Share Confirmation
        </button>
      </div>

      <!-- Rating Prompt -->
      <div class="bg-zinc-50 border border-zinc-200/60 rounded p-6 text-center">
        <p class="font-bold text-sm text-zinc-800 uppercase tracking-wide">Enjoying Zesto?</p>
        <p class="text-xs text-zinc-500 mt-1.5 leading-relaxed font-semibold">Rate your experience after delivery</p>
        <div class="flex justify-center gap-3 mt-4" id="star-rating">
          <button onclick="rate(1)" class="star-btn text-2xl text-zinc-400 hover:text-yellow-500 transition-colors">★</button>
          <button onclick="rate(2)" class="star-btn text-2xl text-zinc-400 hover:text-yellow-500 transition-colors">★</button>
          <button onclick="rate(3)" class="star-btn text-2xl text-zinc-400 hover:text-yellow-500 transition-colors">★</button>
          <button onclick="rate(4)" class="star-btn text-2xl text-zinc-400 hover:text-yellow-500 transition-colors">★</button>
          <button onclick="rate(5)" class="star-btn text-2xl text-zinc-400 hover:text-yellow-500 transition-colors">★</button>
        </div>
      </div>

    </div>
  </div>
</main>

<div id="toast-container"></div>
<script src="js/cart.js"></script>
<script>
const TRACKING_STEPS = [
  { id:'placed',   icon:'receipt_long',     label:'Order Placed',         desc:'Restaurant has received your order', time: 0 },
  { id:'accepted', icon:'check_circle',     label:'Order Accepted',       desc:'The restaurant is preparing your food', time: 1500 },
  { id:'cooking',  icon:'soup_kitchen',     label:'Being Prepared',       desc:'Your food is being freshly cooked', time: 4000 },
  { id:'picked',   icon:'delivery_dining',  label:'Out for Delivery',     desc:'Rider is on the way to your location', time: 8000 },
  { id:'arrived',  icon:'where_to_vote',    label:'Delivered',            desc:'Enjoy your meal', time: 14000 },
];

let completedSteps = new Set(['placed']);
let activeStep = 'accepted';

document.addEventListener('DOMContentLoaded', () => {
  renderTracking();
  startTracking();
  setETA();
});

/* ===== Tracking ===== */
function renderTracking() {
  const container = document.getElementById('tracking-steps');
  container.innerHTML = TRACKING_STEPS.map((step, i) => {
    const done   = completedSteps.has(step.id);
    const active = step.id === activeStep;
    const isLast = i === TRACKING_STEPS.length - 1;
    return `
    <div class="track-step ${done?'done':''} ${active?'active':''}" id="track-${step.id}">
      <div class="track-dot" id="dot-${step.id}">
        <span class="material-symbols-outlined ${done||active?'text-white':'text-secondary'}" style="font-size:16px">${step.icon}</span>
      </div>
      <div class="flex-1 pb-${isLast?'0':'2'}">
        <p class="font-bold text-sm ${done?'text-tertiary':active?'text-primary':'text-secondary'}">${step.label}</p>
        <p class="text-xs ${done||active?'text-secondary':'text-outline-variant'} mt-0.5">${step.desc}</p>
        <p class="text-xs text-outline-variant mt-0.5 font-bold uppercase tracking-wider" id="time-${step.id}">${done?'Completed':active?'In progress…':''}</p>
      </div>
      ${active ? `<div class="flex-shrink-0 w-5 h-5 border-2 border-primary border-t-transparent rounded-full" style="animation:spin 0.8s linear infinite"></div>` : ''}
    </div>`;
  }).join('');
}

function startTracking() {
  TRACKING_STEPS.forEach((step, i) => {
    if (step.time > 0) {
      setTimeout(() => {
        completedSteps.add(TRACKING_STEPS[i-1]?.id || step.id);
        activeStep = step.id;
        if (i === TRACKING_STEPS.length - 1) {
          completedSteps.add(step.id);
          activeStep = '';
        }
        renderTracking();
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' });
        const timeEl = document.getElementById(`time-${step.id}`);
        if (timeEl) timeEl.textContent = timeStr;
        if (step.id === 'arrived') {
          showToast('Your order has been delivered!', 'success', 5000);
        } else {
          showToast(step.label, 'info', 2000);
        }
      }, step.time);
    }
  });
}

/* ===== ETA ===== */
function setETA() {
  const now = new Date();
  const eta = new Date(now.getTime() + 35 * 60000);
  document.getElementById('eta-time').textContent = `by ${eta.toLocaleTimeString('en-IN', { hour:'2-digit', minute:'2-digit' })}`;
}

/* ===== Copy Order ID ===== */
function copyOrderId() {
  const id = document.getElementById('order-id').textContent;
  navigator.clipboard?.writeText(id).then(() => showToast('Order ID copied!', 'success'));
}

/* ===== Star Rating ===== */
function rate(n) {
  document.querySelectorAll('.star-btn').forEach((btn, i) => {
    btn.style.color = i < n ? '#f59e0b' : '';
  });
  showToast(`Thanks for rating us ${n} star${n>1?'s':''}!`, 'success');
}

/* ===== Share ===== */
function shareOrder() {
  const id = document.getElementById('order-id').textContent;
  if (navigator.share) {
    navigator.share({ title:'My Zesto', text:`I just ordered via Zesto! Order ID: ${id}`, url: location.href });
  } else {
    showToast('Sharing not supported on this device', 'error');
  }
}
</script>
<?php include 'footer.php'; ?>
