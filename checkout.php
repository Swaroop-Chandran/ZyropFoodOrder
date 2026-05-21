<?php
session_start();
// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout — ZyropFoodOrder</title>
  <meta name="description" content="Complete your payment and place your food order securely."/>
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
  <style>
    .card-input { letter-spacing: 0.15em; }
    .card-preview {
      background: linear-gradient(135deg, #a83300, #c24000);
      border-radius: 16px;
      padding: 28px 24px;
      color: white;
      position: relative;
      overflow: hidden;
      min-height: 160px;
    }
    .card-preview::before {
      content:'';
      position:absolute; top:-40px; right:-40px;
      width:160px; height:160px;
      border-radius:50%;
      background: rgba(255,255,255,0.08);
    }
    .card-preview::after {
      content:'';
      position:absolute; bottom:-60px; right:20px;
      width:200px; height:200px;
      border-radius:50%;
      background: rgba(255,255,255,0.05);
    }
  </style>
</head>
<body class="bg-surface text-on-surface min-h-screen">

<!-- ===== HEADER ===== -->
<header class="bg-surface border-b border-outline-variant/30 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 h-16 flex items-center justify-between">
    <div class="flex items-center gap-4">
      <a href="cart.php" class="flex items-center gap-1 text-secondary hover:text-primary transition-colors font-semibold text-sm">
        <span class="material-symbols-outlined" style="font-size:20px">arrow_back</span>
        Back to Cart
      </a>
      <div class="h-5 w-px bg-outline-variant hidden sm:block"></div>
      <a href="index.php" class="font-extrabold text-xl text-primary hidden sm:block">ZyropFoodOrder</a>
    </div>
    <h1 class="font-extrabold text-lg text-on-surface">Checkout</h1>
    
    <!-- Account Info Header Menu -->
    <div class="flex items-center gap-2 border border-outline-variant/30 rounded-full px-3 py-1 bg-surface-container-low">
      <span class="material-symbols-outlined text-primary" style="font-size:18px">account_circle</span>
      <span class="text-xs font-bold text-on-surface truncate max-w-[100px]"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
      <a href="logout.php" class="material-symbols-outlined text-secondary hover:text-error transition-colors" style="font-size:16px" title="Logout">logout</a>
    </div>
  </div>
</header>

<!-- ===== STEP PROGRESS ===== -->
<div class="bg-surface-container-low border-b border-outline-variant/20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-4">
    <div class="step-bar max-w-sm mx-auto">
      <div class="step-item done">
        <div class="step-circle">
          <span class="material-symbols-outlined" style="font-size:16px">check</span>
        </div>
        <span class="step-label">Cart</span>
      </div>
      <div class="step-item active">
        <div class="step-circle">2</div>
        <span class="step-label">Payment</span>
      </div>
      <div class="step-item">
        <div class="step-circle">3</div>
        <span class="step-label">Confirm</span>
      </div>
    </div>
  </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-8">
  <div class="flex flex-col lg:flex-row gap-8">

    <!-- Left: Payment + Address -->
    <div class="flex-1 flex flex-col gap-6">

      <!-- Delivery address summary -->
      <div class="bg-white rounded-2xl border border-outline-variant/30 p-6">
        <h2 class="font-extrabold text-base text-on-surface mb-4 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary" style="font-size:20px">location_on</span>
          Delivering to
        </h2>
        <div class="flex items-start gap-3">
          <span class="material-symbols-outlined text-tertiary mt-0.5" style="font-size:22px">where_to_vote</span>
          <div>
            <p class="font-bold text-sm text-on-surface" id="checkout-addr1">Loading address…</p>
            <p class="text-xs text-secondary mt-0.5" id="checkout-addr2"></p>
          </div>
          <a href="cart.php" class="ml-auto text-xs font-bold text-primary hover:underline whitespace-nowrap">Change</a>
        </div>
      </div>

      <!-- Payment Methods -->
      <div class="bg-white rounded-2xl border border-outline-variant/30 p-6">
        <h2 class="font-extrabold text-lg text-on-surface mb-5 flex items-center gap-2">
          <span class="material-symbols-outlined text-primary" style="font-size:22px">payments</span>
          Payment Method
        </h2>

        <!-- Method selector -->
        <div class="flex flex-col gap-3 mb-6">
          <div id="pm-card" class="payment-method-card selected" onclick="selectPayment('card')">
            <span class="material-symbols-outlined text-primary" style="font-size:24px">credit_card</span>
            <div class="flex-1">
              <p class="font-bold text-sm">Credit / Debit Card</p>
              <p class="text-xs text-secondary">Visa, Mastercard, RuPay</p>
            </div>
            <span id="pm-card-check" class="material-symbols-outlined text-primary" style="font-size:22px">check_circle</span>
          </div>

          <div id="pm-upi" class="payment-method-card" onclick="selectPayment('upi')">
            <div class="w-6 h-6 flex-shrink-0">
              <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <text y="30" font-size="28" fill="#5f6368">⚡</text>
              </svg>
            </div>
            <div class="flex-1">
              <p class="font-bold text-sm">UPI</p>
              <p class="text-xs text-secondary">GPay, PhonePe, Paytm, BHIM</p>
            </div>
            <span id="pm-upi-check" class="material-symbols-outlined text-secondary" style="font-size:22px">radio_button_unchecked</span>
          </div>

          <div id="pm-cod" class="payment-method-card" onclick="selectPayment('cod')">
            <span class="material-symbols-outlined text-secondary" style="font-size:24px">payments</span>
            <div class="flex-1">
              <p class="font-bold text-sm">Cash on Delivery</p>
              <p class="text-xs text-secondary">Pay when food arrives</p>
            </div>
            <span id="pm-cod-check" class="material-symbols-outlined text-secondary" style="font-size:22px">radio_button_unchecked</span>
          </div>
        </div>

        <!-- ===== CARD FORM ===== -->
        <div id="card-form" class="flex flex-col gap-5">
          <!-- Card preview -->
          <div class="card-preview">
            <div class="flex justify-between items-start mb-8">
              <div>
                <p class="text-white/60 text-xs mb-1">Card Number</p>
                <p class="font-bold text-lg tracking-widest" id="prev-number">•••• •••• •••• ••••</p>
              </div>
              <svg width="48" height="32" viewBox="0 0 48 32" fill="none">
                <circle cx="18" cy="16" r="14" fill="rgba(255,255,255,0.4)"/>
                <circle cx="30" cy="16" r="14" fill="rgba(255,255,255,0.25)"/>
              </svg>
            </div>
            <div class="flex justify-between items-end">
              <div>
                <p class="text-white/60 text-[10px] mb-0.5">CARD HOLDER</p>
                <p class="font-bold text-sm" id="prev-name">YOUR NAME</p>
              </div>
              <div>
                <p class="text-white/60 text-[10px] mb-0.5">EXPIRES</p>
                <p class="font-bold text-sm" id="prev-expiry">MM/YY</p>
              </div>
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-secondary">Card Number</label>
            <div class="input-group">
              <span class="material-symbols-outlined input-icon" style="font-size:20px">credit_card</span>
              <input id="card-number" type="text" class="form-input card-input" placeholder="1234 5678 9012 3456" maxlength="19"
                oninput="formatCardNumber(this); updateCardPreview()"/>
            </div>
          </div>

          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-secondary">Cardholder Name</label>
            <div class="input-group">
              <span class="material-symbols-outlined input-icon" style="font-size:20px">person</span>
              <input id="card-name" type="text" class="form-input" placeholder="As on card"
                oninput="updateCardPreview()"/>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-secondary">Expiry Date</label>
              <input id="card-expiry" type="text" class="form-input" placeholder="MM/YY" maxlength="5"
                oninput="formatExpiry(this); updateCardPreview()"/>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-xs font-semibold text-secondary">CVV</label>
              <div class="input-group">
                <input id="card-cvv" type="password" class="form-input" placeholder="•••" maxlength="4"/>
                <button type="button" class="input-toggle" onclick="togglePwd('card-cvv', this)">
                  <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                </button>
              </div>
            </div>
          </div>

          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" class="w-4 h-4 accent-primary"/>
            <span class="text-sm text-secondary">Save card for future orders</span>
          </label>
        </div>

        <!-- ===== UPI FORM ===== -->
        <div id="upi-form" class="hidden flex flex-col gap-5">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs font-semibold text-secondary">UPI ID</label>
            <div class="input-group">
              <span class="material-symbols-outlined input-icon" style="font-size:20px">bolt</span>
              <input id="upi-id" type="text" class="form-input" placeholder="yourname@upi"/>
            </div>
          </div>
          <div class="grid grid-cols-4 gap-3">
            <button onclick="setUPI('gpay')" class="upi-app-btn flex flex-col items-center gap-1.5 p-3 rounded-xl border border-outline-variant hover:border-primary hover:bg-primary/5 transition-all">
              <span class="text-2xl">G</span>
              <span class="text-xs font-semibold text-secondary">GPay</span>
            </button>
            <button onclick="setUPI('phonepe')" class="upi-app-btn flex flex-col items-center gap-1.5 p-3 rounded-xl border border-outline-variant hover:border-primary hover:bg-primary/10 transition-all">
              <span class="text-2xl">₱</span>
              <span class="text-xs font-semibold text-secondary">PhonePe</span>
            </button>
            <button onclick="setUPI('paytm')" class="upi-app-btn flex flex-col items-center gap-1.5 p-3 rounded-xl border border-outline-variant hover:border-primary hover:bg-primary/10 transition-all">
              <span class="text-2xl">P</span>
              <span class="text-xs font-semibold text-secondary">Paytm</span>
            </button>
            <button onclick="setUPI('bhim')" class="upi-app-btn flex flex-col items-center gap-1.5 p-3 rounded-xl border border-outline-variant hover:border-primary hover:bg-primary/10 transition-all">
              <span class="text-2xl">B</span>
              <span class="text-xs font-semibold text-secondary">BHIM</span>
            </button>
          </div>
          <p class="text-xs text-secondary">Enter your UPI ID or select your preferred app</p>
        </div>

        <!-- ===== COD FORM ===== -->
        <div id="cod-form" class="hidden">
          <div class="bg-surface-container-low rounded-2xl p-5 flex items-start gap-4">
            <span class="material-symbols-outlined text-tertiary" style="font-size:32px">payments</span>
            <div>
              <p class="font-bold text-sm text-on-surface">Cash on Delivery</p>
              <p class="text-xs text-secondary mt-1 leading-relaxed">Please keep exact change ready. Our delivery partner will collect payment when your order arrives.</p>
              <p class="text-xs font-semibold text-primary mt-2 flex items-center gap-1">
                <span class="material-symbols-outlined" style="font-size:14px">info</span>
                COD available for orders up to ₹2,000
              </p>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Right: Order Summary -->
    <div class="lg:w-[360px] xl:w-[400px] flex-shrink-0">
      <div class="bg-white rounded-2xl border border-outline-variant/30 p-6 sticky top-24">
        <h2 class="font-extrabold text-lg text-on-surface mb-5">Order Summary</h2>

        <!-- Item list preview -->
        <div id="checkout-items" class="flex flex-col gap-3 mb-5 max-h-48 overflow-y-auto hide-scrollbar">
          <!-- Populated by JS -->
        </div>

        <div class="flex flex-col gap-2.5 text-sm border-t border-outline-variant/20 pt-4 mb-4">
          <div class="flex justify-between">
            <span class="text-secondary">Item total</span>
            <span class="font-semibold" id="co-subtotal">₹0</span>
          </div>
          <div class="flex justify-between">
            <span class="text-secondary">Delivery fee</span>
            <span class="font-semibold" id="co-delivery">₹49</span>
          </div>
          <div class="flex justify-between">
            <span class="text-secondary">Platform fee</span>
            <span class="font-semibold" id="co-platform">₹5</span>
          </div>
          <div class="flex justify-between text-tertiary" id="co-disc-row" style="display:none!important">
            <span class="font-semibold">Promo discount</span>
            <span class="font-bold" id="co-discount">-₹0</span>
          </div>
          <div class="flex justify-between">
            <span class="text-secondary">GST (5%)</span>
            <span class="font-semibold" id="co-gst">₹0</span>
          </div>
        </div>

        <div class="border-t border-outline-variant/30 pt-4 mb-6">
          <div class="flex justify-between items-center">
            <span class="font-extrabold text-lg">Total</span>
            <span class="font-extrabold text-xl text-primary" id="co-total">₹0</span>
          </div>
        </div>

        <button onclick="placeOrder()" id="place-order-btn" class="btn-primary w-full text-base">
          <span class="material-symbols-outlined" style="font-size:20px">rocket_launch</span>
          Place Order
        </button>

        <div class="mt-4 flex items-center justify-center gap-2 text-xs text-secondary">
          <span class="material-symbols-outlined text-tertiary" style="font-size:16px">lock</span>
          256-bit SSL encrypted payment
        </div>

        <div class="mt-5 pt-4 border-t border-outline-variant/20">
          <p class="text-xs text-secondary text-center leading-relaxed">
            By placing the order you agree to our
            <a href="#" class="text-primary font-semibold">Terms</a> &
            <a href="#" class="text-primary font-semibold">Privacy Policy</a>
          </p>
        </div>
      </div>
    </div>

  </div>
</main>

<div id="toast-container"></div>
<script src="js/cart.js"></script>
<script>
let selectedPayment = 'card';

document.addEventListener('DOMContentLoaded', () => {
  loadCheckoutData();
  loadAddress();
});

function loadCheckoutData() {
  const cart = ZyropCart.getCart();
  const meta = JSON.parse(localStorage.getItem('zyrop_order_meta') || '{}');

  // Item list
  document.getElementById('checkout-items').innerHTML = cart.map(item => `
    <div class="flex items-center gap-3">
      <img src="${item.image}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0"
           onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&q=80'"/>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-on-surface truncate">${item.name}</p>
        <p class="text-xs text-secondary">${item.qty} × ₹${item.price}</p>
      </div>
      <span class="text-sm font-bold text-on-surface flex-shrink-0">₹${item.qty * item.price}</span>
    </div>
  `).join('') || '<p class="text-sm text-secondary">No items</p>';

  // Summary
  document.getElementById('co-subtotal').textContent = `₹${meta.subtotal || 0}`;
  document.getElementById('co-delivery').textContent = `₹${meta.delivery ?? 49}`;
  document.getElementById('co-platform').textContent = `₹${meta.platformFee || 5}`;
  document.getElementById('co-gst').textContent = `₹${meta.gst || 0}`;
  document.getElementById('co-total').textContent = `₹${meta.total || 0}`;
  if (meta.appliedDiscount > 0) {
    document.getElementById('co-disc-row').style.display = 'flex';
    document.getElementById('co-discount').textContent = `-₹${meta.appliedDiscount}`;
  }
}

function loadAddress() {
  const loc = ZyropLocation.get();
  if (loc && loc.label) {
    const parts = loc.label.split(',');
    document.getElementById('checkout-addr1').textContent = parts.slice(0,2).join(',').trim();
    document.getElementById('checkout-addr2').textContent = parts.slice(2).join(',').trim();
  } else {
    document.getElementById('checkout-addr1').textContent = 'Address not set';
    document.getElementById('checkout-addr2').textContent = 'Go back to cart to set delivery address';
  }
}

/* ===== Payment Selection ===== */
function selectPayment(method) {
  selectedPayment = method;
  const methods = ['card','upi','cod'];
  methods.forEach(m => {
    const card = document.getElementById(`pm-${m}`);
    const check = document.getElementById(`pm-${m}-check`);
    const form = document.getElementById(`${m}-form`);
    if (m === method) {
      card.classList.add('selected');
      check.textContent = 'check_circle';
      check.className = 'material-symbols-outlined text-primary';
      check.style.fontSize = '22px';
      form.classList.remove('hidden');
    } else {
      card.classList.remove('selected');
      check.textContent = 'radio_button_unchecked';
      check.className = 'material-symbols-outlined text-secondary';
      check.style.fontSize = '22px';
      form.classList.add('hidden');
    }
  });
}

/* ===== Card Formatting ===== */
function formatCardNumber(input) {
  let val = input.value.replace(/\D/g,'').slice(0,16);
  input.value = val.match(/.{1,4}/g)?.join(' ') || val;
}
function formatExpiry(input) {
  let val = input.value.replace(/\D/g,'');
  if (val.length >= 2) val = val.slice(0,2)+'/'+val.slice(2,4);
  input.value = val;
}
function updateCardPreview() {
  const num = document.getElementById('card-number').value || '•••• •••• •••• ••••';
  const name = document.getElementById('card-name').value.toUpperCase() || 'YOUR NAME';
  const exp = document.getElementById('card-expiry').value || 'MM/YY';
  document.getElementById('prev-number').textContent = num.replace(/\d(?=.{5})/g,'•').trim() || '•••• •••• •••• ••••';
  document.getElementById('prev-name').textContent = name;
  document.getElementById('prev-expiry').textContent = exp;
}
function togglePwd(id, btn) {
  const input = document.getElementById(id);
  const icon = btn.querySelector('.material-symbols-outlined');
  if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
  else { input.type = 'password'; icon.textContent = 'visibility'; }
}

/* ===== UPI App Buttons ===== */
function setUPI(app) {
  const placeholders = { gpay:'name@okaxis', phonepe:'number@ybl', paytm:'number@paytm', bhim:'number@upi' };
  document.getElementById('upi-id').placeholder = placeholders[app] || 'yourname@upi';
  document.getElementById('upi-id').focus();
  document.querySelectorAll('.upi-app-btn').forEach(b => {
    b.classList.remove('border-primary','bg-primary/10');
  });
  event.currentTarget.classList.add('border-primary','bg-primary/10');
}

/* ===== Validate ===== */
function validatePayment() {
  if (selectedPayment === 'card') {
    const num = document.getElementById('card-number').value.replace(/\s/g,'');
    const name = document.getElementById('card-name').value.trim();
    const exp = document.getElementById('card-expiry').value;
    const cvv = document.getElementById('card-cvv').value;
    if (num.length < 16) { showToast('Enter a valid 16-digit card number', 'error'); return false; }
    if (!name) { showToast('Enter cardholder name', 'error'); return false; }
    if (exp.length < 5) { showToast('Enter valid expiry date (MM/YY)', 'error'); return false; }
    if (cvv.length < 3) { showToast('Enter valid CVV', 'error'); return false; }
  }
  if (selectedPayment === 'upi') {
    const uid = document.getElementById('upi-id').value.trim();
    if (!uid.includes('@')) { showToast('Enter a valid UPI ID (e.g. name@upi)', 'error'); return false; }
  }
  return true;
}

/* ===== Place Order ===== */
function placeOrder() {
  if (!validatePayment()) return;
  const btn = document.getElementById('place-order-btn');
  const origHTML = btn.innerHTML;
  btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;animation:spin 0.8s linear infinite">progress_activity</span> Processing payment…';
  btn.disabled = true;

  const cart = ZyropCart.getCart();
  const meta = JSON.parse(localStorage.getItem('zyrop_order_meta') || '{}');
  const address = ZyropLocation.get() || { label: 'Address not detected' };

  fetch('place_order_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      paymentMethod: selectedPayment,
      cart: cart,
      meta: meta,
      address: address
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Clear the local cart
      ZyropCart.clearCart();
      
      // Save order details to localstorage for additional frontend fallback
      localStorage.setItem('zyrop_last_order', JSON.stringify({
        orderId: data.order_id,
        paymentMethod: selectedPayment,
        cart: cart,
        meta: meta,
        address: address,
        placedAt: new Date().toISOString()
      }));

      showToast(data.message, 'success');
      
      // Redirect to confirmation page with server order id
      setTimeout(() => {
        window.location.href = 'order-confirmation.php?id=' + data.order_id;
      }, 1000);
    } else {
      showToast(data.message, 'error');
      btn.innerHTML = origHTML;
      btn.disabled = false;
    }
  })
  .catch(err => {
    showToast('Failed to place order. Please try again.', 'error');
    btn.innerHTML = origHTML;
    btn.disabled = false;
  });
}
</script>
</body>
</html>
