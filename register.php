<?php
session_start();
require_once 'db.php';
// If user is already logged in, redirect to index or the requested redirect URL
if (isset($_SESSION['user_id'])) {
    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
    header("Location: " . $redirect);
    exit();
}

$pageTitle = 'Sign Up — Zesto';
$pageDesc = 'Create a free Zesto account and start ordering delicious meals today!';
include 'header.php';
?>

<main class="max-w-md mx-auto px-4 py-16 relative z-10">
  <div class="zesto-glass-card rounded-lg p-8 border border-zinc-200/60 shadow-sm bg-white">
    
    <!-- Tab Switcher -->
    <div class="relative flex border-b border-zinc-200/50 mb-8">
      <button id="tab-login" onclick="switchTab('login')"
        class="flex-1 pb-4 text-xs uppercase tracking-wider font-bold text-zinc-400 relative tab-btn">
        Login
      </button>
      <button id="tab-signup" onclick="switchTab('signup')"
        class="flex-1 pb-4 text-xs uppercase tracking-wider font-bold text-primary relative tab-btn">
        Sign Up
      </button>
      <div id="tab-indicator" class="auth-tab-indicator w-1/2 bg-primary" style="left: 50%;"></div>
    </div>

    <!-- ===== LOGIN FORM ===== -->
    <div id="form-login" class="hidden animate-fade-in-up">
      <h2 class="font-title text-3xl font-extrabold text-zinc-900 mb-2">Welcome Back</h2>
      <p class="text-zinc-500 text-xs uppercase tracking-wider font-medium mb-8">Login to continue ordering your favorites.</p>

      <form id="login-form" onsubmit="handleLogin(event)" class="flex flex-col gap-5">
        <div class="flex flex-col gap-1.5">
          <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="login-email">Email Address</label>
          <input id="login-email" type="email" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 w-full focus:border-primary focus:ring-0" placeholder="you@example.com" required autocomplete="email"/>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="login-password">Password</label>
          <div class="relative">
            <input id="login-password" type="password" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 pr-12 w-full focus:border-primary focus:ring-0" placeholder="Enter your password" required autocomplete="current-password"/>
            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-primary" onclick="togglePwd('login-password', this)">
              <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" id="remember-me" class="w-4 h-4 accent-primary rounded bg-white border-zinc-200"/>
            <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Remember me</span>
          </label>
          <a href="#" class="text-xs font-bold uppercase tracking-wider text-primary hover:underline">Forgot password?</a>
        </div>

        <button type="submit" id="login-btn" class="btn-primary w-full mt-2 uppercase tracking-widest text-xs font-bold py-3">
          Login to your account
        </button>
      </form>

      <!-- Divider -->
      <div class="flex items-center gap-4 my-6">
        <div class="flex-1 h-px bg-zinc-200/60"></div>
        <span class="text-[10px] uppercase tracking-widest text-zinc-400 font-bold">or continue with</span>
        <div class="flex-1 h-px bg-zinc-200/60"></div>
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

      <p class="text-center text-xs text-zinc-500 mt-6 font-bold uppercase tracking-wider">
        Don't have an account? 
        <button onclick="switchTab('signup')" class="text-primary font-extrabold hover:underline">Sign up free</button>
      </p>
    </div>

    <!-- ===== SIGNUP FORM ===== -->
    <div id="form-signup" class="animate-fade-in-up">
      <h2 class="font-title text-3xl font-extrabold text-zinc-900 mb-2">Create Account</h2>
      <p class="text-zinc-500 text-xs uppercase tracking-wider font-medium mb-8">Join Zesto and start ordering today!</p>

      <form id="signup-form" onsubmit="handleSignup(event)" class="flex flex-col gap-4">
        <div class="grid grid-cols-2 gap-3">
          <div class="flex flex-col gap-1.5">
            <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="signup-fname">First Name</label>
            <input id="signup-fname" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 w-full focus:border-primary focus:ring-0" placeholder="Rahul" required/>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="signup-lname">Last Name</label>
            <input id="signup-lname" type="text" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 w-full focus:border-primary focus:ring-0" placeholder="Sharma" required/>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="signup-email">Email Address</label>
          <input id="signup-email" type="email" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 w-full focus:border-primary focus:ring-0" placeholder="rahul@example.com" required autocomplete="email"/>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="signup-phone">Phone Number</label>
          <input id="signup-phone" type="tel" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 w-full focus:border-primary focus:ring-0" placeholder="+91 98765 43210" required/>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="signup-password">Password</label>
          <div class="relative">
            <input id="signup-password" type="password" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 pr-12 w-full focus:border-primary focus:ring-0" placeholder="Min. 8 characters" required autocomplete="new-password"/>
            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-primary" onclick="togglePwd('signup-password', this)">
              <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
            </button>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="text-xs uppercase tracking-wider font-bold text-zinc-500" for="signup-confirm">Confirm Password</label>
          <input id="signup-confirm" type="password" class="form-input bg-white border border-zinc-200 text-zinc-800 rounded px-4 py-3 w-full focus:border-primary focus:ring-0" placeholder="Re-enter password" required/>
          <p id="pwd-mismatch" class="text-xs text-error hidden">Passwords do not match.</p>
        </div>
        <label class="flex items-start gap-3 cursor-pointer">
          <input type="checkbox" id="agree-terms" class="w-4 h-4 mt-0.5 accent-primary rounded bg-white border-zinc-200" required/>
          <span class="text-xs text-zinc-500 leading-relaxed font-bold uppercase tracking-wide">
            I agree to the <a href="#" class="text-primary hover:underline">Terms of Service</a> and <a href="#" class="text-primary hover:underline">Privacy Policy</a>
          </span>
        </label>

        <button type="submit" id="signup-btn" class="btn-primary w-full uppercase tracking-widest text-xs font-bold py-3 mt-2">
          Create my account
        </button>
      </form>

      <p class="text-center text-xs text-zinc-500 mt-6 font-bold uppercase tracking-wider">
        Already have an account?
        <button onclick="switchTab('login')" class="text-primary font-extrabold hover:underline">Login here</button>
      </p>
    </div>

  </div>
</main>

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
      loginTab.className = 'flex-1 pb-4 text-xs uppercase tracking-wider font-bold text-primary relative tab-btn';
      signupTab.className = 'flex-1 pb-4 text-xs uppercase tracking-wider font-bold text-zinc-400 relative tab-btn';
    } else {
      indicator.style.left = '50%';
      signupForm.classList.remove('hidden');
      loginForm.classList.add('hidden');
      signupTab.className = 'flex-1 pb-4 text-xs uppercase tracking-wider font-bold text-primary relative tab-btn';
      loginTab.className = 'flex-1 pb-4 text-xs uppercase tracking-wider font-bold text-zinc-400 relative tab-btn';
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
        const params = new URLSearchParams(window.location.search);
        const redirectUrl = params.get('redirect') || 'index.php';
        setTimeout(() => { window.location.href = redirectUrl; }, 800);
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
        const params = new URLSearchParams(window.location.search);
        const redirectUrl = params.get('redirect') || 'index.php';
        setTimeout(() => { window.location.href = redirectUrl; }, 800);
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
    const address = '';

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
        const params = new URLSearchParams(window.location.search);
        const redirectUrl = params.get('redirect') || 'index.php';
        setTimeout(() => { window.location.href = redirectUrl; }, 900);
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

  // Force active signup tab for register.php
  switchTab('signup');
</script>
<?php include 'footer.php'; ?>
