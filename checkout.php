<?php
$pageTitle = 'Confirm Your Order — Zesto';
$pageDesc = 'Confirm your items and complete your guest checkout details.';
$pageTheme = 'light';
include 'header.php';
?>

<!-- ===== STEP PROGRESS ===== -->
<div class="border-b border-zinc-200/60 bg-white relative z-20">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-6">
    <div class="step-bar max-w-sm mx-auto flex items-center justify-between">
      <div class="step-item done flex flex-col items-center">
        <div class="step-circle font-bold">✓</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5 text-[#526043]">Cart</span>
      </div>
      <div class="step-item active flex flex-col items-center">
        <div class="step-circle font-bold">2</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5">Details</span>
      </div>
      <div class="step-item flex flex-col items-center">
        <div class="step-circle font-bold">3</div>
        <span class="step-label text-xs uppercase tracking-wider font-bold mt-1.5">Confirm</span>
      </div>
    </div>
  </div>
</div>

<main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-12 relative z-10">
  <div class="flex flex-col lg:flex-row gap-10">

    <!-- Left side: Forms / OTP (Dynamic Toggle) -->
    <div class="flex-1 flex flex-col gap-8">
      
      <!-- ===== STAGE 1: ORDER DETAILS FORM ===== -->
      <div id="details-container" class="flex flex-col gap-8 animate-fade-in">
        
        <!-- Contact & Delivery Information -->
        <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white shadow-sm">
          <h2 class="font-title text-xl font-bold text-zinc-900 mb-2">Confirm Your Details</h2>
          <p class="text-zinc-500 text-xs font-semibold uppercase tracking-wider mb-6">Confirm your items and enter your details below. We’ll send a verification code to your email to confirm this order.</p>

          <form id="details-form" onsubmit="triggerVerification(event)" class="flex flex-col gap-5">
            <!-- Email Input -->
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="checkout-email">Email Address</label>
              <input id="checkout-email" type="email" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="you@example.com" required/>
            </div>

            <!-- Phone Input -->
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="checkout-phone">Phone Number</label>
              <input id="checkout-phone" type="tel" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="+91 98765 43210" required/>
            </div>

            <!-- Delivery Address -->
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="checkout-address">Delivery Address</label>
              <textarea id="checkout-address" rows="3" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full leading-relaxed resize-none" placeholder="Enter complete flat number, street name, and city details..." required></textarea>
            </div>
            
            <button type="submit" style="display:none;"></button> <!-- Native Enter support -->
          </form>
        </div>

        <!-- Payment Method Selection -->
        <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white shadow-sm">
          <h2 class="font-title text-xl font-bold text-zinc-900 mb-6">Payment Selection</h2>
          
          <div class="flex flex-col gap-4 mb-8">
            <div id="pm-card" class="payment-method-card selected rounded border border-zinc-200 p-4 cursor-pointer flex items-center gap-4 transition-all" onclick="selectPayment('card')">
              <span class="material-symbols-outlined text-zinc-500" style="font-size:24px">credit_card</span>
              <div class="flex-1">
                <p class="font-bold text-sm text-zinc-800">Credit / Debit Card</p>
                <p class="text-xs text-zinc-400 font-medium">Visa, Mastercard, RuPay</p>
              </div>
              <span id="pm-card-check" class="material-symbols-outlined text-primary" style="font-size:22px">check_circle</span>
            </div>

            <div id="pm-upi" class="payment-method-card rounded border border-zinc-200 p-4 cursor-pointer flex items-center gap-4 transition-all" onclick="selectPayment('upi')">
              <span class="material-symbols-outlined text-zinc-450" style="font-size:24px">bolt</span>
              <div class="flex-1">
                <p class="font-bold text-sm text-zinc-800">UPI / NetBanking</p>
                <p class="text-xs text-zinc-400 font-medium">Google Pay, PhonePe, Paytm, BHIM</p>
              </div>
              <span id="pm-upi-check" class="material-symbols-outlined text-zinc-400" style="font-size:22px">radio_button_unchecked</span>
            </div>

            <div id="pm-cod" class="payment-method-card rounded border border-zinc-200 p-4 cursor-pointer flex items-center gap-4 transition-all" onclick="selectPayment('cod')">
              <span class="material-symbols-outlined text-zinc-450" style="font-size:24px">payments</span>
              <div class="flex-1">
                <p class="font-bold text-sm text-zinc-800">Cash on Delivery</p>
                <p class="text-xs text-zinc-400 font-medium">Pay when food arrives at your door</p>
              </div>
              <span id="pm-cod-check" class="material-symbols-outlined text-zinc-400" style="font-size:22px">radio_button_unchecked</span>
            </div>
          </div>

          <!-- ===== CARD FORM ===== -->
          <div id="card-form" class="flex flex-col gap-6 animate-fade-in">
            <!-- Card preview -->
            <div class="card-preview bg-[#221f1d] rounded-lg p-6 text-white shadow relative overflow-hidden">
              <div class="flex justify-between items-start mb-8">
                <div>
                  <p class="text-white/50 text-[10px] uppercase tracking-wider font-bold mb-1">Card Number</p>
                  <p class="font-bold text-base tracking-widest font-mono" id="prev-number">•••• •••• •••• ••••</p>
                </div>
                <span class="material-symbols-outlined text-white/30" style="font-size:32px">credit_card</span>
              </div>
              <div class="flex justify-between items-end">
                <div>
                  <p class="text-white/50 text-[9px] uppercase tracking-wider font-bold mb-0.5">CARD HOLDER</p>
                  <p class="font-bold text-xs font-title tracking-wider" id="prev-name">YOUR NAME</p>
                </div>
                <div>
                  <p class="text-white/50 text-[9px] uppercase tracking-wider font-bold mb-0.5">EXPIRES</p>
                  <p class="font-bold text-xs font-mono" id="prev-expiry">MM/YY</p>
                </div>
              </div>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">Card Number</label>
              <input id="card-number" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="1234 5678 9012 3456" maxlength="19"
                oninput="formatCardNumber(this); updateCardPreview()"/>
            </div>

            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">Cardholder Name</label>
              <input id="card-name" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="As on card"
                oninput="updateCardPreview()"/>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="flex flex-col gap-1.5">
                <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">Expiry Date</label>
                <input id="card-expiry" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="MM/YY" maxlength="5"
                  oninput="formatExpiry(this); updateCardPreview()"/>
              </div>
              <div class="flex flex-col gap-1.5">
                <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">CVV</label>
                <div class="relative">
                  <input id="card-cvv" type="password" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 pr-12 focus:border-primary focus:ring-0 w-full" placeholder="•••" maxlength="4"/>
                  <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-primary" onclick="togglePwd('card-cvv', this)">
                    <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ===== UPI FORM ===== -->
          <div id="upi-form" class="hidden flex flex-col gap-6 animate-fade-in">
            <div class="flex flex-col gap-1.5">
              <label class="text-xs uppercase tracking-wider font-bold text-zinc-500">UPI ID</label>
              <input id="upi-id" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 focus:border-primary focus:ring-0 w-full" placeholder="yourname@upi"/>
            </div>
            <div class="grid grid-cols-4 gap-3">
              <button onclick="setUPI('gpay')" class="upi-app-btn flex flex-col items-center gap-1.5 py-3 rounded border border-zinc-200 hover:border-primary hover:bg-zinc-50 transition-all">
                <span class="text-sm font-extrabold text-zinc-850">GPay</span>
              </button>
              <button onclick="setUPI('phonepe')" class="upi-app-btn flex flex-col items-center gap-1.5 py-3 rounded border border-zinc-200 hover:border-primary hover:bg-zinc-50 transition-all">
                <span class="text-sm font-extrabold text-zinc-850">PhonePe</span>
              </button>
              <button onclick="setUPI('paytm')" class="upi-app-btn flex flex-col items-center gap-1.5 py-3 rounded border border-zinc-200 hover:border-primary hover:bg-zinc-50 transition-all">
                <span class="text-sm font-extrabold text-zinc-850">Paytm</span>
              </button>
              <button onclick="setUPI('bhim')" class="upi-app-btn flex flex-col items-center gap-1.5 py-3 rounded border border-zinc-200 hover:border-primary hover:bg-zinc-50 transition-all">
                <span class="text-sm font-extrabold text-zinc-850">BHIM</span>
              </button>
            </div>
          </div>

          <!-- ===== COD FORM ===== -->
          <div id="cod-form" class="hidden animate-fade-in">
            <div class="bg-zinc-50 border border-zinc-200/80 rounded p-5 flex items-start gap-4">
              <span class="material-symbols-outlined text-primary mt-0.5" style="font-size:24px">payments</span>
              <div>
                <p class="font-bold text-sm text-zinc-800 uppercase tracking-wide">Cash on Delivery</p>
                <p class="text-xs text-zinc-500 mt-2 leading-relaxed font-medium">Please keep exact change ready. Our delivery partner will collect payment when your order is handed over.</p>
                <p class="text-xs font-bold text-primary mt-3 flex items-center gap-1.5 uppercase tracking-wider">
                  <span class="material-symbols-outlined" style="font-size:16px">info</span>
                  COD available for orders up to ₹2,000
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>
      
      <!-- ===== STAGE 2: OTP VERIFICATION VIEW ===== -->
      <div id="otp-container" class="hidden zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white shadow-sm animate-scale-in">
        <div class="flex justify-center mb-6">
          <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined" style="font-size:32px">mark_email_read</span>
          </div>
        </div>

        <h2 class="font-title text-2xl font-bold text-zinc-900 text-center mb-2">Enter verification code</h2>
        <p class="text-zinc-500 text-xs text-center font-bold uppercase tracking-wider mb-8">We sent a 4-digit code to your email.</p>

        <div class="flex flex-col gap-6 max-w-xs mx-auto">
          <!-- OTP input box -->
          <div class="flex flex-col gap-1.5 text-center">
            <label class="text-xs uppercase tracking-wider font-bold text-zinc-500 mb-2">Verification Code</label>
            <input id="otp-input" type="text" class="form-input bg-white border border-zinc-200 text-zinc-900 rounded-lg px-4 py-3.5 text-center font-mono font-bold text-xl tracking-[0.4em] focus:border-primary focus:ring-0" placeholder="••••" maxlength="4" autocomplete="one-time-code" required/>
          </div>

          <button onclick="placeOrder()" id="place-order-btn" class="btn-primary w-full uppercase tracking-widest text-xs font-bold py-3.5 mt-2">
            Confirm Order
          </button>
          
          <div class="text-center">
            <button onclick="resendCode()" class="text-xs font-bold uppercase tracking-wider text-primary hover:underline">Resend code</button>
          </div>
        </div>
      </div>
      
    </div>

    <!-- Right: Order Summary -->
    <div class="lg:w-[360px] xl:w-[400px] flex-shrink-0">
      <div class="zesto-glass-card rounded-lg border border-zinc-200/60 p-8 bg-white sticky top-24 shadow-sm">
        <h2 class="font-title text-xl font-bold text-zinc-900 mb-6">Order Summary</h2>

        <!-- Item list preview -->
        <div id="checkout-items" class="flex flex-col gap-4 mb-6 max-h-48 overflow-y-auto hide-scrollbar">
          <!-- Populated by JS -->
        </div>

        <div class="flex flex-col gap-3.5 text-xs font-bold uppercase tracking-wider text-zinc-500 border-t border-zinc-200/60 pt-5 mb-5">
          <div class="flex justify-between">
            <span>Item total</span>
            <span class="font-extrabold text-zinc-900" id="co-subtotal">₹0</span>
          </div>
          <div class="flex justify-between">
            <span>Delivery fee</span>
            <span class="font-extrabold text-zinc-900" id="co-delivery">₹49</span>
          </div>
          <div class="flex justify-between">
            <span>Platform fee</span>
            <span class="font-extrabold text-zinc-900" id="co-platform">₹5</span>
          </div>
          <div class="flex justify-between text-tertiary" id="co-disc-row" style="display:none!important">
            <span>Promo discount</span>
            <span class="font-extrabold" id="co-discount">-₹0</span>
          </div>
          <div class="flex justify-between">
            <span>GST (5%)</span>
            <span class="font-extrabold text-zinc-900" id="co-gst">₹0</span>
          </div>
        </div>

        <div class="border-t border-zinc-200/60 pt-5 mb-8">
          <div class="flex justify-between items-center">
            <span class="font-title text-lg font-bold text-zinc-900">Total</span>
            <span class="font-title text-2xl font-extrabold text-primary" id="co-total">₹0</span>
          </div>
        </div>

        <!-- Stages-Specific CTA Trigger button -->
        <button id="send-otp-btn" onclick="triggerSubmitDetails()" class="btn-primary w-full uppercase tracking-widest text-xs font-bold py-3.5">
          Send Verification Code
        </button>

        <div class="mt-4 flex items-center justify-center gap-2 text-xs font-bold text-zinc-400 uppercase tracking-wider">
          <span class="material-symbols-outlined text-[#526043]" style="font-size:16px">lock</span>
          256-bit SSL secure payment
        </div>

        <div class="mt-6 pt-5 border-t border-zinc-200/60">
          <p class="text-[10px] text-zinc-400 text-center leading-relaxed font-bold uppercase tracking-wider">
            By placing the order you agree to our
            <a href="#" class="text-primary hover:underline">Terms</a> &
            <a href="#" class="text-primary hover:underline">Privacy Policy</a>
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
  loadSavedDetails();
});

function loadCheckoutData() {
  const cart = ZyropCart.getCart();
  const meta = JSON.parse(localStorage.getItem('zyrop_order_meta') || '{}');

  // Item list
  document.getElementById('checkout-items').innerHTML = cart.map(item => `
    <div class="flex items-center gap-3">
      <img src="${item.image}" class="w-12 h-12 rounded object-cover flex-shrink-0 border border-zinc-200/60"
           onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&q=80'"/>
      <div class="flex-1 min-w-0">
        <p class="text-sm font-semibold text-zinc-800 truncate">${item.name}</p>
        <p class="text-xs text-zinc-400 font-bold uppercase tracking-wider mt-0.5">${item.qty} × ₹${item.price}</p>
      </div>
      <span class="text-sm font-extrabold text-zinc-900 flex-shrink-0">₹${item.qty * item.price}</span>
    </div>
  `).join('') || '<p class="text-sm text-zinc-400">No items</p>';

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

function loadSavedDetails() {
  // Try loading saved address from cart
  const loc = JSON.parse(localStorage.getItem('zyrop_delivery_address') || 'null');
  if (loc && loc.label) {
    document.getElementById('checkout-address').value = loc.label;
  }
  // Try loading saved email/phone
  const savedUser = JSON.parse(localStorage.getItem('zyrop_user') || 'null');
  if (savedUser && savedUser.loggedIn) {
    if (savedUser.email) document.getElementById('checkout-email').value = savedUser.email;
    if (savedUser.phone) document.getElementById('checkout-phone').value = savedUser.phone;
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
      form.classList.remove('hidden');
    } else {
      card.classList.remove('selected');
      check.textContent = 'radio_button_unchecked';
      check.className = 'material-symbols-outlined text-zinc-400';
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
    b.classList.remove('border-primary','bg-zinc-50');
  });
  event.currentTarget.classList.add('border-primary','bg-zinc-50');
}

/* ===== Form Details Trigger ===== */
function triggerSubmitDetails() {
  // Proactively trigger HTML form validation submit
  const btn = document.querySelector('#details-form button[type="submit"]');
  if (btn) btn.click();
}

function triggerVerification(e) {
  e.preventDefault();
  
  // 1. Validate payment details
  if (!validatePayment()) return;
  
  // 2. Hide Stage 1 & Show Stage 2 (OTP)
  document.getElementById('details-container').classList.add('hidden');
  document.getElementById('otp-container').classList.remove('hidden');
  
  // 3. Hide details submit button in sidebar order summary card
  document.getElementById('send-otp-btn').classList.add('hidden');
  
  // 4. Focus OTP input
  document.getElementById('otp-input').focus();
  
  showToast('Verification code sent!', 'success');
}

function resendCode() {
  showToast('A new 4-digit code has been sent!', 'success');
  document.getElementById('otp-input').value = '';
  document.getElementById('otp-input').focus();
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
  const otpVal = document.getElementById('otp-input').value.trim();
  if (otpVal.length < 4) {
    showToast('Please enter the 4-digit verification code.', 'error');
    return;
  }
  
  const btn = document.getElementById('place-order-btn');
  const origHTML = btn.innerHTML;
  btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;animation:spin 0.8s linear infinite">progress_activity</span> Processing…';
  btn.disabled = true;

  const cart = ZyropCart.getCart();
  const meta = JSON.parse(localStorage.getItem('zyrop_order_meta') || '{}');
  
  // Read details directly from inputs
  const email = document.getElementById('checkout-email').value.trim();
  const phone = document.getElementById('checkout-phone').value.trim();
  const addressLabel = document.getElementById('checkout-address').value.trim();
  
  const addressObj = {
    label: addressLabel,
    full: addressLabel
  };

  fetch('place_order_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      paymentMethod: selectedPayment,
      cart: cart,
      meta: meta,
      address: addressObj,
      email: email,
      phone: phone
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Clear the local cart
      ZyropCart.clearCart();
      
      // Save order details to localstorage
      localStorage.setItem('zyrop_last_order', JSON.stringify({
        orderId: data.order_id,
        paymentMethod: selectedPayment,
        cart: cart,
        meta: meta,
        address: addressObj,
        placedAt: new Date().toISOString()
      }));

      // Strip any emoji from the message
      const cleanMessage = data.message.replace(/[\uE000-\uF8FF]|\uD83C[\uDC00-\uDFFF]|\uD83D[\uDC00-\uDFFF]|[\u2011-\u26FF]|\uD83E[\uDD10-\uDDFF]/g, "").trim();
      showToast(cleanMessage, 'success');
      
      // Redirect to confirmation page
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
<?php include 'footer.php'; ?>
