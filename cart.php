<?php
$pageTitle = 'Your Cart — Zesto';
$pageDesc = 'Review your cart and proceed to checkout. Fast, fresh food delivery at your doorstep.';
$pageTheme = 'light';
include 'header.php';
?>

<!-- ===== STEP PROGRESS ===== -->
<div class="border-b border-zinc-200/60 bg-white relative z-20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-6">
    <div class="step-bar max-w-sm mx-auto flex items-center justify-between">
      <div class="step-item active flex flex-col items-center">
        <div class="step-circle font-bold">1</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5">Cart</span>
      </div>
      <div class="step-item flex flex-col items-center">
        <div class="step-circle font-bold">2</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5">Payment</span>
      </div>
      <div class="step-item flex flex-col items-center">
        <div class="step-circle font-bold">3</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5">Confirm</span>
      </div>
    </div>
  </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-12 relative z-10">
  <!-- ===== EMPTY STATE ===== -->
  <div id="empty-cart" class="hidden flex-col items-center justify-center py-24 gap-6 text-center animate-fade-in-up">
    <span class="material-symbols-outlined text-zinc-300" style="font-size: 64px">shopping_bag</span>
    <h2 class="font-title text-3xl font-extrabold text-zinc-900">Your cart is empty</h2>
    <p class="text-zinc-500 text-sm max-w-xs leading-relaxed">Explore our curated culinary collection and find something extraordinary for your dining table.</p>
    <a href="index.php" class="btn-primary mt-2 uppercase tracking-widest text-xs font-bold py-3.5 px-8">
      Explore Menu
    </a>
  </div>

  <!-- ===== CART CONTENT ===== -->
  <div id="cart-content" class="flex flex-col lg:flex-row gap-10">

    <!-- Left: Cart Items + Address -->
    <div class="flex-1 flex flex-col gap-8">

      <!-- Cart Items -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 overflow-hidden bg-white">
        <div class="px-8 py-6 border-b border-zinc-200/60 flex items-center justify-between">
          <h2 class="font-title text-xl font-bold text-zinc-900">Order Items</h2>
          <button onclick="clearCart()" class="text-xs font-bold uppercase tracking-wider text-error hover:underline flex items-center gap-1">
            <span class="material-symbols-outlined" style="font-size:16px">delete_sweep</span>
            Clear all
          </button>
        </div>
        <div id="cart-items-list" class="divide-y divide-zinc-200/60">
          <!-- Rendered by JS -->
        </div>
      </div>

      <!-- Delivery Address -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white">
        <h2 class="font-title text-xl font-bold text-zinc-900 mb-6 flex items-center gap-2">
          Delivery Address
        </h2>
        
        <!-- Detected address state -->
        <div id="address-detected" class="hidden flex items-start gap-3 bg-zinc-50 border border-zinc-200/80 rounded p-4 mb-6">
          <span class="material-symbols-outlined text-primary mt-0.5" style="font-size:22px">where_to_vote</span>
          <div class="flex-1 min-w-0">
            <p class="font-bold text-sm text-zinc-800 animate-fade-in" id="address-line1"></p>
            <p class="text-xs text-zinc-500 mt-0.5 animate-fade-in" id="address-line2"></p>
          </div>
          <button onclick="changeAddress()" class="text-xs font-bold uppercase tracking-wider text-primary hover:underline whitespace-nowrap ml-2">Change</button>
        </div>

        <!-- manual address input -->
        <div id="address-manual" class="flex flex-col gap-5">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">Flat / House No.</label>
              <input id="addr-flat" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0" placeholder="e.g. Flat 4B, Block A"/>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">Street / Area</label>
              <input id="addr-street" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0" placeholder="e.g. MG Road"/>
            </div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">City</label>
              <input id="addr-city" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0" placeholder="e.g. Bangalore"/>
            </div>
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">Pincode</label>
              <input id="addr-pin" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0" placeholder="e.g. 560001"/>
            </div>
          </div>
        </div>
      </div>

      <!-- Promo Code -->
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white">
        <h2 class="font-title text-lg font-bold text-zinc-900 mb-4 flex items-center gap-2">
          Offer Code
        </h2>
        <div class="flex gap-3">
          <input id="promo-input" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="Enter promo code (e.g. ZYROP50)"/>
          <button onclick="applyPromo()" class="btn-primary whitespace-nowrap uppercase tracking-widest text-xs font-bold py-3 px-6 rounded">Apply</button>
        </div>
        <p id="promo-msg" class="text-xs mt-3 hidden font-bold"></p>
      </div>

    </div>

    <!-- Right: Order Summary -->
    <div class="lg:w-[360px] xl:w-[400px] flex-shrink-0">
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white sticky top-24 shadow-sm">
        <h2 class="font-title text-xl font-bold text-zinc-900 mb-6">Order Summary</h2>

        <div class="flex flex-col gap-3.5 text-xs font-bold uppercase tracking-wider text-zinc-500 mb-6">
          <div class="flex justify-between">
            <span>Subtotal (<span id="sum-count">0</span> items)</span>
            <span class="font-extrabold text-zinc-900" id="sum-subtotal">₹0</span>
          </div>
          <div class="flex justify-between">
            <span>Delivery fee</span>
            <span class="font-extrabold text-zinc-900" id="sum-delivery">₹49</span>
          </div>
          <div class="flex justify-between">
            <span>Platform fee</span>
            <span class="font-extrabold text-zinc-900">₹5</span>
          </div>
          <div class="flex justify-between text-tertiary" id="discount-row" style="display:none!important">
            <span class="flex items-center gap-1">
              Promo discount
            </span>
            <span class="font-extrabold" id="sum-discount">-₹0</span>
          </div>
          <div class="flex justify-between">
            <span>GST (5%)</span>
            <span class="font-extrabold text-zinc-900" id="sum-gst">₹0</span>
          </div>
        </div>

        <div class="border-t border-zinc-200/60 pt-5 mb-8">
          <div class="flex justify-between items-center">
            <span class="font-title text-lg font-bold text-zinc-900">Total</span>
            <span class="font-title text-2xl font-extrabold text-primary" id="sum-total">₹0</span>
          </div>
          <p class="text-[9px] uppercase tracking-wider font-bold text-zinc-400 mt-1.5">Inclusive of all food taxes</p>
        </div>

        <div class="flex flex-col gap-4">
          <div class="flex items-center gap-3 text-xs text-zinc-500 bg-[#fdfbf9] border border-zinc-200/80 rounded p-4 font-bold uppercase tracking-wider">
            <span class="material-symbols-outlined text-primary" style="font-size:18px">schedule</span>
            <span>Est. Delivery: <strong class="text-zinc-800 font-extrabold">30–45 Mins</strong></span>
          </div>
          <button onclick="proceedToCheckout()" id="checkout-btn" class="btn-primary w-full uppercase tracking-widest text-xs font-bold py-3.5">
            Proceed to Payment
          </button>
          <a href="index.php" class="btn-outline w-full text-center uppercase tracking-widest text-xs font-bold py-3.5 rounded flex items-center justify-center gap-1.5">
            Add More Items
          </a>
        </div>

        <!-- Trust badges -->
        <div class="mt-8 pt-6 border-t border-zinc-200/60 flex items-center justify-around text-center">
          <div class="flex flex-col items-center gap-1">
            <span class="material-symbols-outlined text-[#526043]" style="font-size:22px">verified_user</span>
            <span class="text-[9px] uppercase tracking-wider text-zinc-400 font-bold">100% Secure</span>
          </div>
          <div class="flex flex-col items-center gap-1">
            <span class="material-symbols-outlined text-[#526043]" style="font-size:22px">local_shipping</span>
            <span class="text-[9px] uppercase tracking-wider text-zinc-400 font-bold">Fast Delivery</span>
          </div>
          <div class="flex flex-col items-center gap-1">
            <span class="material-symbols-outlined text-[#526043]" style="font-size:22px">star</span>
            <span class="text-[9px] uppercase tracking-wider text-zinc-400 font-bold">Top Rated</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<div id="toast-container"></div>
<script src="js/cart.js"></script>
<script>
const DELIVERY_FEE = 49;
const PLATFORM_FEE = 5;
const GST_RATE = 0.05;
const PROMO_CODES = { 'ZYROP50': 50, 'FIRST100': 100, 'SAVE30': 30 };
let appliedDiscount = 0;

document.addEventListener('DOMContentLoaded', () => {
  renderCart();
  loadAddress();
});

window.addEventListener('zyrop:cart-updated', renderCart);

/* ===== Render Cart Items ===== */
function renderCart() {
  const cart = ZyropCart.getCart();
  const empty = document.getElementById('empty-cart');
  const content = document.getElementById('cart-content');

  if (cart.length === 0) {
    empty.classList.remove('hidden');
    empty.classList.add('flex');
    content.classList.add('hidden');
    return;
  }
  empty.classList.add('hidden');
  empty.classList.remove('flex');
  content.classList.remove('hidden');

  document.getElementById('cart-items-list').innerHTML = cart.map(item => `
    <div class="flex gap-5 p-6 hover:bg-zinc-50/50 transition-colors animate-fade-in" id="cart-row-${item.id}">
      <img src="${item.image}" alt="${item.name}" class="w-20 h-20 rounded object-cover flex-shrink-0 border border-zinc-200/60"
           onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&q=80'"/>
      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-4 h-4 rounded border flex items-center justify-center text-[9px] font-extrabold ${item.veg ? 'border-green-600 text-green-600' : 'border-red-600 text-red-600'}">●</span>
              <h3 class="font-title font-bold text-base text-zinc-900 leading-snug truncate max-w-[200px]">${item.name}</h3>
            </div>
            <p class="text-[10px] uppercase tracking-wider font-bold text-zinc-400 mt-1">${item.restaurant}</p>
          </div>
          <button onclick="removeItem('${item.id}')" class="text-zinc-400 hover:text-error transition-colors flex-shrink-0">
            <span class="material-symbols-outlined" style="font-size:18px">close</span>
          </button>
        </div>
        <div class="flex items-center justify-between mt-4">
          <div class="qty-stepper">
            <button class="qty-btn" onclick="cartQty('${item.id}',-1)">−</button>
            <span class="qty-count" id="cart-qty-${item.id}">${item.qty}</span>
            <button class="qty-btn" onclick="cartQty('${item.id}',1)">+</button>
          </div>
          <span class="font-extrabold text-sm text-zinc-900">₹${item.price * item.qty}</span>
        </div>
      </div>
    </div>
  `).join('');

  updateSummary();
}

function cartQty(id, delta) {
  if (delta > 0) ZyropCart.incrementItem(id);
  else ZyropCart.decrementItem(id);
  renderCart();
}

function updateCartBadge() {
  const badges = document.querySelectorAll('.cart-count-badge');
  const count = ZyropCart.getTotalCount();
  badges.forEach(badge => {
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
  });
}

function removeItem(id) {
  ZyropCart.removeItem(id);
  renderCart();
  updateCartBadge();
  showToast('Item removed from cart', 'info');
}

function clearCart() {
  ZyropCart.clearCart();
  renderCart();
  updateCartBadge();
  showToast('Cart cleared', 'info');
}

/* ===== Summary ===== */
function updateSummary() {
  const cart = ZyropCart.getCart();
  const subtotal = ZyropCart.getSubtotal();
  const count = ZyropCart.getTotalCount();
  const freeDelivery = subtotal >= 499;
  const delivery = freeDelivery ? 0 : DELIVERY_FEE;
  const gst = Math.round((subtotal + delivery + PLATFORM_FEE) * GST_RATE);
  const total = subtotal + delivery + PLATFORM_FEE + gst - appliedDiscount;

  document.getElementById('sum-count').textContent = count;
  document.getElementById('sum-subtotal').textContent = `₹${subtotal}`;
  document.getElementById('sum-delivery').textContent = freeDelivery ? `₹0` : `₹${delivery}`;
  document.getElementById('sum-gst').textContent = `₹${gst}`;
  document.getElementById('sum-total').textContent = `₹${Math.max(0,total)}`;

  const discRow = document.getElementById('discount-row');
  if (appliedDiscount > 0) {
    discRow.style.display = 'flex';
    document.getElementById('sum-discount').textContent = `-₹${appliedDiscount}`;
  } else {
    discRow.style.display = 'none';
  }
}

/* ===== Promo ===== */
function applyPromo() {
  const code = document.getElementById('promo-input').value.trim().toUpperCase();
  const msg = document.getElementById('promo-msg');
  if (PROMO_CODES[code]) {
    appliedDiscount = PROMO_CODES[code];
    msg.className = 'text-xs mt-3 text-tertiary font-bold';
    msg.textContent = `Promo applied! You save ₹${appliedDiscount}`;
    msg.classList.remove('hidden');
    updateSummary();
    showToast(`Promo code applied! ₹${appliedDiscount} off`, 'success');
  } else {
    appliedDiscount = 0;
    msg.className = 'text-xs mt-3 text-error font-bold';
    msg.textContent = 'Invalid promo code. Try ZYROP50';
    msg.classList.remove('hidden');
    updateSummary();
  }
}

/* ===== Address ===== */
function loadAddress() {
  const loc = JSON.parse(localStorage.getItem('zyrop_delivery_address') || 'null');
  const detected = document.getElementById('address-detected');
  const manual = document.getElementById('address-manual');
  if (loc && loc.label && detected && manual) {
    const parts = loc.label.split(',');
    document.getElementById('address-line1').textContent = parts.slice(0,2).join(',').trim();
    document.getElementById('address-line2').textContent = parts.slice(2).join(',').trim();
    detected.classList.remove('hidden');
    manual.classList.add('hidden');
  }
}

function changeAddress() {
  const detected = document.getElementById('address-detected');
  const manual = document.getElementById('address-manual');
  if (detected && manual) {
    detected.classList.add('hidden');
    manual.classList.remove('hidden');
  }
}

/* ===== Checkout ===== */
function proceedToCheckout() {
  const cart = ZyropCart.getCart();
  if (cart.length === 0) { showToast('Your cart is empty!', 'error'); return; }

  // Save address from manual inputs if shown
  const manual = document.getElementById('address-manual');
  const manualVisible = manual && !manual.classList.contains('hidden');
  if (manualVisible) {
    const flat = document.getElementById('addr-flat').value;
    const street = document.getElementById('addr-street').value;
    const city = document.getElementById('addr-city').value;
    const pin = document.getElementById('addr-pin').value;
    if (!flat || !city) {
      showToast('Please enter your delivery address', 'error');
      return;
    }
    const label = [flat, street, city, pin].filter(Boolean).join(', ');
    localStorage.setItem('zyrop_delivery_address', JSON.stringify({ label, full: label }));
  }

  // Save order metadata
  const subtotal = ZyropCart.getSubtotal();
  const freeDelivery = subtotal >= 499;
  const delivery = freeDelivery ? 0 : DELIVERY_FEE;
  const gst = Math.round((subtotal + delivery + PLATFORM_FEE) * GST_RATE);
  const total = Math.max(0, subtotal + delivery + PLATFORM_FEE + gst - appliedDiscount);
  localStorage.setItem('zyrop_order_meta', JSON.stringify({ subtotal, delivery, gst, appliedDiscount, total, platformFee: PLATFORM_FEE }));

  const btn = document.getElementById('checkout-btn');
  btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;animation:spin 0.8s linear infinite">progress_activity</span> Redirecting…';
  btn.disabled = true;
  setTimeout(() => { window.location.href = 'checkout.php'; }, 600);
}
</script>
<?php include 'footer.php'; ?>
