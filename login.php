<?php
session_start();
require_once 'db.php';
// If user is already logged in, redirect to home page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login & Sign Up — ZyropFoodOrder</title>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <meta name="description" content="Login or create your ZyropFoodOrder account to order delicious food delivered to your doorstep."/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/zyrop.css"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "surface-container-low":"#f5f3f3","on-primary-container":"#fffbff","on-error-container":"#93000a",
            "surface-container-lowest":"#ffffff","surface-bright":"#fbf9f8","on-secondary-fixed-variant":"#474646",
            "on-error":"#ffffff","on-primary":"#ffffff","on-tertiary-container":"#f7fff2",
            "on-secondary-container":"#656464","outline":"#907065","surface-container-high":"#e9e8e7",
            "on-tertiary":"#ffffff","on-tertiary-fixed-variant":"#00531e","surface-variant":"#e4e2e2",
            "tertiary":"#006b29","surface-dim":"#dbdad9","secondary-fixed-dim":"#c8c6c5",
            "on-secondary":"#ffffff","error":"#ba1a1a","surface":"#fbf9f8","primary-fixed":"#ffdbd0",
            "primary-container":"#d24200","primary":"#a83300","error-container":"#ffdad6",
            "on-surface-variant":"#5c4037","secondary":"#5f5e5e","tertiary-container":"#008735",
            "inverse-surface":"#303031","on-background":"#1b1c1c","on-secondary-fixed":"#1c1b1b",
            "background":"#fbf9f8","tertiary-fixed":"#69ff87","outline-variant":"#e5beb2",
            "inverse-on-surface":"#f2f0f0","tertiary-fixed-dim":"#3ce36a","surface-tint":"#ac3500",
            "secondary-container":"#e5e2e1","surface-container":"#efeded","primary-fixed-dim":"#ffb59d",
            "on-surface":"#1b1c1c","surface-container-highest":"#e4e2e2","on-primary-fixed-variant":"#832600",
            "inverse-primary":"#ffb59d","on-primary-fixed":"#390c00","secondary-fixed":"#e5e2e1","on-tertiary-fixed":"#002108"
          }
        }
      }
    }
  </script>
</head>
<body class="bg-surface min-h-screen flex">

<!-- Left Panel — Branding -->
<div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden flex-col justify-between p-12 bg-gradient-to-br from-[#a83300] via-[#c24000] to-[#7a2400]">
  <!-- Background pattern -->
  <div class="absolute inset-0 opacity-10">
    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <pattern id="dots" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
          <circle cx="20" cy="20" r="2" fill="white"/>
        </pattern>
      </defs>
      <rect width="100%" height="100%" fill="url(#dots)"/>
    </svg>
  </div>

  <!-- Floating food emojis -->
  <div class="absolute top-20 right-16 text-6xl opacity-20 animate-bounce" style="animation-duration:3s">🍕</div>
  <div class="absolute top-48 right-48 text-4xl opacity-20 animate-bounce" style="animation-duration:4s;animation-delay:1s">🍔</div>
  <div class="absolute bottom-48 right-20 text-5xl opacity-20 animate-bounce" style="animation-duration:3.5s;animation-delay:0.5s">🍜</div>
  <div class="absolute bottom-24 right-56 text-4xl opacity-20 animate-bounce" style="animation-duration:2.8s;animation-delay:1.5s">🥗</div>

  <!-- Logo -->
  <div class="relative z-10 animate-fade-in-up">
    <span class="text-white font-extrabold text-3xl tracking-tight">ZyropFoodOrder</span>
    <p class="text-white/70 text-sm mt-1 font-medium">Delivering happiness, one meal at a time</p>
  </div>

  <!-- Center content -->
  <div class="relative z-10 flex flex-col gap-8 animate-fade-in-up delay-200">
    <div>
      <h1 class="text-white font-extrabold text-4xl xl:text-5xl leading-tight mb-4">
        Your favourite food,<br/>delivered fast 
      </h1>
      <p class="text-white/80 text-lg leading-relaxed max-w-md">
        Join thousands of food lovers ordering from top restaurants near you. Fresh, fast, and always delicious.
      </p>
    </div>

    <!-- Feature pills -->
    <div class="flex flex-wrap gap-3">
      <div class="flex items-center gap-2 bg-white/15 backdrop-blur rounded-full px-4 py-2">
        <span class="material-symbols-outlined text-white text-[18px]">bolt</span>
        <span class="text-white text-sm font-semibold">30-min delivery</span>
      </div>
      <div class="flex items-center gap-2 bg-white/15 backdrop-blur rounded-full px-4 py-2">
        <span class="material-symbols-outlined text-white text-[18px]">restaurant</span>
        <span class="text-white text-sm font-semibold">500+ restaurants</span>
      </div>
      <div class="flex items-center gap-2 bg-white/15 backdrop-blur rounded-full px-4 py-2">
        <span class="material-symbols-outlined text-white text-[18px]">local_offer</span>
        <span class="text-white text-sm font-semibold">Daily offers</span>
      </div>
    </div>
  </div>

  <!-- Testimonial -->
  <div class="relative z-10 bg-white/15 backdrop-blur rounded-2xl p-6 animate-fade-in-up delay-400">
    <div class="flex items-start gap-4">
      <div class="w-10 h-10 rounded-full bg-white/30 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">P</div>
      <div>
        <p class="text-white/90 text-sm leading-relaxed italic">"Best food delivery app! Orders always arrive hot and on time. Love the variety!"</p>
        <p class="text-white/60 text-xs mt-2 font-semibold">— Priya S., Mumbai ⭐⭐⭐⭐⭐</p>
      </div>
    </div>
  </div>
</div>

<!-- Right Panel — Auth Form -->
<div class="flex-1 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-16 xl:px-20 overflow-y-auto">
  <div class="w-full max-w-md mx-auto">

    <!-- Mobile logo -->
    <div class="lg:hidden mb-8 animate-fade-in-up">
      <span class="text-primary font-extrabold text-2xl">ZyropFoodOrder</span>
    </div>

    <!-- Tab Switcher -->
    <div class="relative flex border-b border-outline-variant mb-8 animate-fade-in-up">
      <button id="tab-login" onclick="switchTab('login')"
        class="flex-1 pb-4 text-sm font-bold text-primary relative tab-btn">
        Login
      </button>
      <button id="tab-signup" onclick="switchTab('signup')"
        class="flex-1 pb-4 text-sm font-bold text-secondary relative tab-btn">
        Sign Up
      </button>
      <div id="tab-indicator" class="auth-tab-indicator w-1/2"></div>
    </div>

    <!-- ===== LOGIN FORM ===== -->
    <div id="form-login" class="animate-fade-in-up delay-100">
      <h2 class="text-2xl font-extrabold text-on-surface mb-2">Welcome back! </h2>
      <p class="text-secondary text-sm mb-8">Login to continue ordering your favourites.</p>

      <form id="login-form" onsubmit="handleLogin(event)" class="flex flex-col gap-5">
        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-on-surface-variant" for="login-email">Email address</label>
          <div class="input-group">
            <span class="material-symbols-outlined input-icon" style="font-size:20px">mail</span>
            <input id="login-email" type="email" class="form-input" placeholder="you@example.com" required autocomplete="email"/>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-on-surface-variant" for="login-password">Password</label>
          <div class="input-group">
            <span class="material-symbols-outlined input-icon" style="font-size:20px">lock</span>
            <input id="login-password" type="password" class="form-input" placeholder="Enter your password" required autocomplete="current-password"/>
            <button type="button" class="input-toggle" onclick="togglePwd('login-password', this)">
              <span class="material-symbols-outlined" style="font-size:20px">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="remember-me" class="w-4 h-4 accent-primary"/>
            <span class="text-sm text-secondary">Remember me</span>
          </label>
          <a href="#" class="text-sm font-semibold text-primary hover:underline">Forgot password?</a>
        </div>

        <button type="submit" id="login-btn" class="btn-primary w-full mt-2">
          <span class="material-symbols-outlined" style="font-size:20px">login</span>
          Login to your account
        </button>
      </form>

      <!-- Divider -->
      <div class="flex items-center gap-4 my-6">
        <div class="flex-1 h-px bg-outline-variant"></div>
        <span class="text-xs text-secondary font-medium">or continue with</span>
        <div class="flex-1 h-px bg-outline-variant"></div>
      </div>

      <!-- Social buttons -->
      <div class="flex justify-center w-full">
        <div id="g_id_onload"
             data-client_id="<?php echo GOOGLE_CLIENT_ID; ?>"
             data-context="signin"
             data-ux_mode="popup"
             data-callback="handleGoogleSignIn"
             data-auto_prompt="false">
        </div>
        <div class="g_id_signin w-full flex justify-center"
             data-type="standard"
             data-shape="rectangular"
             data-theme="outline"
             data-text="signin_with"
             data-size="large"
             data-logo_alignment="left"
             data-width="384">
        </div>
      </div>

      <p class="text-center text-sm text-secondary mt-6">
        Don't have an account? 
        <button onclick="switchTab('signup')" class="text-primary font-bold hover:underline">Sign up free</button>
      </p>
    </div>

    <!-- ===== SIGNUP FORM ===== -->
    <div id="form-signup" class="hidden animate-fade-in-up delay-100">
      <h2 class="text-2xl font-extrabold text-on-surface mb-2">Create account 🎉</h2>
      <p class="text-secondary text-sm mb-8">Join ZyropFoodOrder and start ordering today!</p>

      <form id="signup-form" onsubmit="handleSignup(event)" class="flex flex-col gap-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-semibold text-on-surface-variant" for="signup-fname">First name</label>
            <input id="signup-fname" type="text" class="form-input" placeholder="Rahul" required/>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-semibold text-on-surface-variant" for="signup-lname">Last name</label>
            <input id="signup-lname" type="text" class="form-input" placeholder="Sharma" required/>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-on-surface-variant" for="signup-email">Email address</label>
          <div class="input-group">
            <span class="material-symbols-outlined input-icon" style="font-size:20px">mail</span>
            <input id="signup-email" type="email" class="form-input" placeholder="rahul@example.com" required autocomplete="email"/>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-on-surface-variant" for="signup-phone">Phone number</label>
          <div class="input-group">
            <span class="material-symbols-outlined input-icon" style="font-size:20px">phone</span>
            <input id="signup-phone" type="tel" class="form-input" placeholder="+91 98765 43210" required/>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-on-surface-variant" for="signup-password">Password</label>
          <div class="input-group">
            <span class="material-symbols-outlined input-icon" style="font-size:20px">lock</span>
            <input id="signup-password" type="password" class="form-input" placeholder="Min. 8 characters" required autocomplete="new-password"/>
            <button type="button" class="input-toggle" onclick="togglePwd('signup-password', this)">
              <span class="material-symbols-outlined" style="font-size:20px">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-sm font-semibold text-on-surface-variant" for="signup-confirm">Confirm password</label>
          <div class="input-group">
            <span class="material-symbols-outlined input-icon" style="font-size:20px">lock_clock</span>
            <input id="signup-confirm" type="password" class="form-input" placeholder="Re-enter password" required/>
          </div>
          <p id="pwd-mismatch" class="text-xs text-error hidden">Passwords do not match.</p>
        </div>

        <!-- Location detection -->
        <div class="rounded-2xl border border-outline-variant bg-surface-container-low p-4 flex flex-col gap-3">
          <p class="text-sm font-semibold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary" style="font-size:20px">location_on</span>
            Delivery Location
          </p>
          <div id="location-status" class="text-sm text-secondary">Allow location access for faster delivery.</div>
          <div id="location-badge-wrap" class="hidden">
            <div class="location-badge">
              <span class="material-symbols-outlined" style="font-size:16px">where_to_vote</span>
              <span id="location-label">Detecting…</span>
            </div>
          </div>
          <button type="button" id="location-btn" onclick="detectLocation()"
            class="flex items-center gap-2 text-primary font-semibold text-sm border border-primary/30 bg-primary/5 rounded-xl px-4 py-2.5 hover:bg-primary/10 transition-colors w-fit">
            <span class="material-symbols-outlined" style="font-size:18px">my_location</span>
            <span id="location-btn-text">Detect my location</span>
          </button>
        </div>

        <label class="flex items-start gap-3 cursor-pointer">
          <input type="checkbox" id="agree-terms" class="w-4 h-4 mt-0.5 accent-primary" required/>
          <span class="text-sm text-secondary leading-relaxed">
            I agree to the <a href="#" class="text-primary font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-primary font-semibold hover:underline">Privacy Policy</a>
          </span>
        </label>

        <button type="submit" id="signup-btn" class="btn-primary w-full">
          <span class="material-symbols-outlined" style="font-size:20px">person_add</span>
          Create my account
        </button>
      </form>

      <p class="text-center text-sm text-secondary mt-6">
        Already have an account?
        <button onclick="switchTab('login')" class="text-primary font-bold hover:underline">Login here</button>
      </p>
    </div>

  </div>
</div>

<!-- Toast container -->
<div id="toast-container"></div>

<script src="js/cart.js"></script>
<script>
  /* ---------- Tab Switcher ---------- */
  function switchTab(tab) {
    const indicator = document.getElementById('tab-indicator');
    const loginForm = document.getElementById('form-login');
    const signupForm = document.getElementById('form-signup');
    const loginTab = document.getElementById('tab-login');
    const signupTab = document.getElementById('tab-signup');

    if (tab === 'login') {
      indicator.style.left = '0';
      loginForm.classList.remove('hidden');
      signupForm.classList.add('hidden');
      loginTab.classList.replace('text-secondary','text-primary');
      signupTab.classList.replace('text-primary','text-secondary');
    } else {
      indicator.style.left = '50%';
      signupForm.classList.remove('hidden');
      loginForm.classList.add('hidden');
      signupTab.classList.replace('text-secondary','text-primary');
      loginTab.classList.replace('text-primary','text-secondary');
    }
  }

  /* ---------- Password Toggle ---------- */
  function togglePwd(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('.material-symbols-outlined');
    if (input.type === 'password') {
      input.type = 'text';
      icon.textContent = 'visibility_off';
    } else {
      input.type = 'password';
      icon.textContent = 'visibility';
    }
  }

  /* ---------- Location Detection ---------- */
  function detectLocation() {
    const btn = document.getElementById('location-btn');
    const btnText = document.getElementById('location-btn-text');
    const status = document.getElementById('location-status');
    const badgeWrap = document.getElementById('location-badge-wrap');
    const label = document.getElementById('location-label');

    btnText.textContent = 'Detecting…';
    btn.disabled = true;

    ZyropLocation.detect(
      (loc) => {
        label.textContent = loc.label;
        badgeWrap.classList.remove('hidden');
        status.textContent = 'Location detected! Your orders will be delivered here.';
        btnText.textContent = 'Update location';
        btn.disabled = false;
        showToast('Location detected! 📍', 'success');
      },
      (err) => {
        status.textContent = err + ' Please enter your address manually during checkout.';
        btnText.textContent = 'Try again';
        btn.disabled = false;
        showToast(err, 'error');
      }
    );
  }

  /* ---------- Login Handler ---------- */
  function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const btn = document.getElementById('login-btn');
    const origHTML = btn.innerHTML;

    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;animation:spin 0.8s linear infinite">progress_activity</span> Logging in…';
    btn.disabled = true;

    fetch('auth_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'login', email, password })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        localStorage.setItem('zyrop_user', JSON.stringify({
          email: email,
          name: data.user.name,
          loggedIn: true
        }));
        showToast(data.message, 'success');
        setTimeout(() => { window.location.href = 'index.php'; }, 800);
      } else {
        showToast(data.message, 'error');
        btn.innerHTML = origHTML;
        btn.disabled = false;
      }
    })
    .catch(err => {
      showToast('An error occurred. Please try again.', 'error');
      btn.innerHTML = origHTML;
      btn.disabled = false;
    });
  }

  /* ---------- Google Sign-In Handler ---------- */
  function handleGoogleSignIn(response) {
    const id_token = response.credential;
    
    showToast('Signing in with Google…', 'info');

    fetch('auth_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'google_login', id_token })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        localStorage.setItem('zyrop_user', JSON.stringify({
          email: data.user.email,
          name: data.user.name,
          loggedIn: true
        }));
        showToast(data.message, 'success');
        setTimeout(() => { window.location.href = 'index.php'; }, 800);
      } else {
        showToast(data.message, 'error');
      }
    })
    .catch(err => {
      showToast('Google Sign-In failed. Please try again.', 'error');
    });
  }

  /* ---------- Signup Handler ---------- */
  function handleSignup(e) {
    e.preventDefault();
    const pwd = document.getElementById('signup-password').value;
    const confirm = document.getElementById('signup-confirm').value;
    const mismatch = document.getElementById('pwd-mismatch');

    if (pwd !== confirm) {
      mismatch.classList.remove('hidden');
      document.getElementById('signup-confirm').focus();
      return;
    }
    mismatch.classList.add('hidden');

    const fname = document.getElementById('signup-fname').value;
    const lname = document.getElementById('signup-lname').value;
    const email = document.getElementById('signup-email').value;
    const phone = document.getElementById('signup-phone').value;
    const loc = ZyropLocation.get();
    const address = loc ? loc.label : '';

    const btn = document.getElementById('signup-btn');
    const origHTML = btn.innerHTML;
    btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;animation:spin 0.8s linear infinite">progress_activity</span> Creating account…';
    btn.disabled = true;

    fetch('auth_api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'signup', fname, lname, email, phone, password: pwd, address })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        localStorage.setItem('zyrop_user', JSON.stringify({
          email: email,
          phone: phone,
          name: `${fname} ${lname}`,
          loggedIn: true
        }));
        showToast(data.message, 'success');
        setTimeout(() => { window.location.href = 'index.php'; }, 900);
      } else {
        showToast(data.message, 'error');
        btn.innerHTML = origHTML;
        btn.disabled = false;
      }
    })
    .catch(err => {
      showToast('Registration failed. Please try again.', 'error');
      btn.innerHTML = origHTML;
      btn.disabled = false;
    });
  }

  /* ---------- Auto-detect check from URL ---------- */
  const params = new URLSearchParams(location.search);
  if (params.get('tab') === 'signup') switchTab('signup');
</script>
</body>
</html>
