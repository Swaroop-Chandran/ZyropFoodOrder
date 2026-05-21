<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect appropriately
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: index.php');
    exit;
}
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin-panel.php');
    exit;
}

$error = '';
$active_tab = $_GET['tab'] ?? 'user'; // 'user' or 'admin'

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? 'user';
    $active_tab = $role;

    if (isset($_POST['demo_login'])) {
        // Demo quick login
        if ($role === 'admin') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = 'admin@zyrop.com';
            header('Location: admin-panel.php');
        } else {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = 'swaroop.chandran@gmail.com';
            header('Location: index.php');
        }
        exit;
    }

    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        if ($role === 'admin') {
            // Mock admin auth
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header('Location: admin-panel.php');
        } else {
            // Mock user auth
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $email;
            header('Location: index.php');
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Sign In · Zyrop</title>
    <meta name="description" content="Sign in to your Zyrop account to order food from top restaurants near you."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="css/zyrop.css" rel="stylesheet"/>
    <script src="js/tailwind-theme.js"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(1deg); }
            66% { transform: translateY(-10px) rotate(-1deg); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.05); }
        }
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .float-anim { animation: float 8s ease-in-out infinite; }
        .float-anim-delay { animation: float 10s ease-in-out infinite 2s; }
        .slide-up { animation: slide-up 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }
        .fade-in { animation: fade-in 0.4s ease forwards; }
        .blob1 { animation: pulse-slow 8s ease-in-out infinite; }
        .blob2 { animation: pulse-slow 10s ease-in-out infinite 3s; }

        .tab-content { display: none; }
        .tab-content.active { display: block; animation: slide-up 0.35s cubic-bezier(0.16,1,0.3,1) forwards; }

        .glass-card {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
        }
        .input-field {
            transition: all 0.2s ease;
        }
        .input-field:focus-within {
            border-color: #a83300;
            box-shadow: 0 0 0 3px rgba(168,51,0,0.12);
        }
        .btn-primary {
            background: linear-gradient(135deg, #a83300 0%, #d24200 100%);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #c23d00 0%, #e04a00 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(168,51,0,0.35);
        }
        .btn-primary:active { transform: translateY(0); }

        .food-icon {
            font-size: 2.5rem;
            display: inline-block;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #1a0800 0%, #2d1200 40%, #1a0800 100%); min-height: 100vh;" class="font-['Plus_Jakarta_Sans'] flex items-center justify-center relative overflow-hidden">

    <!-- Animated Background Blobs -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="blob1 absolute -top-1/4 -left-1/4 w-[60vw] h-[60vw] rounded-full" style="background: radial-gradient(circle, rgba(168,51,0,0.35) 0%, transparent 70%);"></div>
        <div class="blob2 absolute -bottom-1/4 -right-1/4 w-[55vw] h-[55vw] rounded-full" style="background: radial-gradient(circle, rgba(210,66,0,0.25) 0%, transparent 70%);"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40vw] h-[40vw] rounded-full" style="background: radial-gradient(circle, rgba(255,181,157,0.08) 0%, transparent 70%);"></div>

        <!-- Floating Food Emojis -->
        <div class="float-anim absolute top-[12%] left-[8%] text-4xl opacity-20 select-none">🍕</div>
        <div class="float-anim-delay absolute top-[20%] right-[10%] text-3xl opacity-15 select-none">🍔</div>
        <div class="float-anim absolute bottom-[25%] left-[12%] text-3xl opacity-15 select-none">🍜</div>
        <div class="float-anim-delay absolute bottom-[15%] right-[8%] text-4xl opacity-20 select-none">🥗</div>
        <div class="float-anim absolute top-[50%] left-[5%] text-2xl opacity-10 select-none">🍣</div>
        <div class="float-anim-delay absolute top-[60%] right-[5%] text-2xl opacity-10 select-none">🌮</div>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-md mx-4 z-10 slide-up">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 shadow-xl" style="background: linear-gradient(135deg, #a83300, #d24200);">
                <span class="material-symbols-outlined text-white text-[32px]">restaurant</span>
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight" style="color: #ffb59d;">Zyrop</h1>
            <p class="text-sm mt-1" style="color: rgba(255,181,157,0.6);">Premium food, delivered fast</p>
        </div>

        <!-- Glass Card -->
        <div class="glass-card border rounded-3xl shadow-2xl overflow-hidden" style="border-color: rgba(255,255,255,0.2);">

            <!-- Top Gradient Bar -->
            <div class="h-1" style="background: linear-gradient(90deg, #a83300, #d24200, #ffb59d, #d24200, #a83300);"></div>

            <div class="p-8">

                <!-- Role Tabs -->
                <div class="flex gap-2 mb-7 p-1 rounded-2xl" style="background: rgba(168,51,0,0.08);">
                    <button id="tab-user-btn"
                        onclick="switchTab('user')"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-200"
                        style="background: <?= $active_tab === 'user' ? 'linear-gradient(135deg,#a83300,#d24200)' : 'transparent' ?>; color: <?= $active_tab === 'user' ? '#fff' : '#a83300' ?>;">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Customer
                    </button>
                    <button id="tab-admin-btn"
                        onclick="switchTab('admin')"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-bold transition-all duration-200"
                        style="background: <?= $active_tab === 'admin' ? 'linear-gradient(135deg,#a83300,#d24200)' : 'transparent' ?>; color: <?= $active_tab === 'admin' ? '#fff' : '#a83300' ?>;">
                        <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                        Admin
                    </button>
                </div>

                <!-- Error Message -->
                <?php if (!empty($error)): ?>
                <div class="mb-5 p-4 rounded-xl flex items-center gap-3" style="background: rgba(186,26,26,0.1); border: 1px solid rgba(186,26,26,0.3);">
                    <span class="material-symbols-outlined text-red-500 text-[20px]">error</span>
                    <span class="text-sm font-semibold text-red-600"><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>

                <!-- USER TAB -->
                <div id="tab-user" class="tab-content <?= $active_tab === 'user' ? 'active' : '' ?>">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Welcome back! 👋</h2>
                        <p class="text-sm text-gray-500 mt-1">Sign in to order your favourite food</p>
                    </div>

                    <form action="login.php" method="POST" class="flex flex-col gap-4">
                        <input type="hidden" name="role" value="user"/>

                        <!-- Email -->
                        <div>
                            <label for="user-email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email Address</label>
                            <div class="input-field flex items-center rounded-xl px-4 py-3 gap-3 border" style="background: rgba(168,51,0,0.04); border-color: rgba(168,51,0,0.15);">
                                <span class="material-symbols-outlined text-[20px]" style="color: #a83300;">mail</span>
                                <input type="email" id="user-email" name="email" class="bg-transparent border-none p-0 focus:ring-0 text-sm w-full text-gray-800 placeholder-gray-400" placeholder="you@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"/>
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <label for="user-password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                                <a href="#" class="text-xs font-semibold" style="color: #a83300;">Forgot password?</a>
                            </div>
                            <div class="input-field flex items-center rounded-xl px-4 py-3 gap-3 border" style="background: rgba(168,51,0,0.04); border-color: rgba(168,51,0,0.15);">
                                <span class="material-symbols-outlined text-[20px]" style="color: #a83300;">lock</span>
                                <input type="password" id="user-password" name="password" class="bg-transparent border-none p-0 focus:ring-0 text-sm w-full text-gray-800 placeholder-gray-400" placeholder="••••••••" required/>
                                <button type="button" onclick="togglePwd('user-password', this)" class="material-symbols-outlined text-[18px] text-gray-400 hover:text-gray-600 transition-colors">visibility</button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="remember-user" class="w-4 h-4 rounded" style="accent-color: #a83300;"/>
                            <label for="remember-user" class="text-xs text-gray-500 font-medium cursor-pointer">Keep me logged in</label>
                        </div>

                        <!-- Sign In Button -->
                        <button type="submit" id="user-signin-btn" class="btn-primary w-full text-white py-3.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 mt-1 shadow-lg">
                            Sign In as Customer
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-5">
                        <div class="flex-1 h-px" style="background: rgba(168,51,0,0.15);"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Or try demo</span>
                        <div class="flex-1 h-px" style="background: rgba(168,51,0,0.15);"></div>
                    </div>

                    <!-- Demo Login -->
                    <form action="login.php" method="POST">
                        <input type="hidden" name="role" value="user"/>
                        <button type="submit" name="demo_login" id="user-demo-btn" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl border text-sm font-bold transition-all hover:shadow-md" style="border-color: rgba(168,51,0,0.3); color: #a83300; background: rgba(168,51,0,0.05);">
                            <span class="material-symbols-outlined text-[20px]">account_circle</span>
                            Quick Customer Demo Login
                        </button>
                    </form>
                </div>

                <!-- ADMIN TAB -->
                <div id="tab-admin" class="tab-content <?= $active_tab === 'admin' ? 'active' : '' ?>">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg,#a83300,#d24200);">
                                <span class="material-symbols-outlined text-white text-[14px]">shield</span>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Admin Console</h2>
                        </div>
                        <p class="text-sm text-gray-500">Restricted access — authorized personnel only</p>
                    </div>

                    <form action="login.php" method="POST" class="flex flex-col gap-4">
                        <input type="hidden" name="role" value="admin"/>

                        <!-- Admin Email -->
                        <div>
                            <label for="admin-email" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Admin Email</label>
                            <div class="input-field flex items-center rounded-xl px-4 py-3 gap-3 border" style="background: rgba(168,51,0,0.04); border-color: rgba(168,51,0,0.15);">
                                <span class="material-symbols-outlined text-[20px]" style="color: #a83300;">badge</span>
                                <input type="email" id="admin-email" name="email" class="bg-transparent border-none p-0 focus:ring-0 text-sm w-full text-gray-800 placeholder-gray-400" placeholder="admin@zyrop.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"/>
                            </div>
                        </div>

                        <!-- Admin Password -->
                        <div>
                            <label for="admin-password" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Admin Password</label>
                            <div class="input-field flex items-center rounded-xl px-4 py-3 gap-3 border" style="background: rgba(168,51,0,0.04); border-color: rgba(168,51,0,0.15);">
                                <span class="material-symbols-outlined text-[20px]" style="color: #a83300;">lock</span>
                                <input type="password" id="admin-password" name="password" class="bg-transparent border-none p-0 focus:ring-0 text-sm w-full text-gray-800 placeholder-gray-400" placeholder="••••••••" required/>
                                <button type="button" onclick="togglePwd('admin-password', this)" class="material-symbols-outlined text-[18px] text-gray-400 hover:text-gray-600 transition-colors">visibility</button>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="flex items-start gap-2 p-3 rounded-xl" style="background: rgba(168,51,0,0.06); border: 1px solid rgba(168,51,0,0.12);">
                            <span class="material-symbols-outlined text-[16px] mt-0.5 shrink-0" style="color: #a83300;">info</span>
                            <p class="text-xs text-gray-500 leading-relaxed">This session is monitored. All admin actions are logged for security compliance.</p>
                        </div>

                        <!-- Admin Sign In -->
                        <button type="submit" id="admin-signin-btn" class="btn-primary w-full text-white py-3.5 rounded-xl font-bold text-sm flex items-center justify-center gap-2 mt-1 shadow-lg">
                            <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                            Sign In to Admin Console
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center gap-3 my-5">
                        <div class="flex-1 h-px" style="background: rgba(168,51,0,0.15);"></div>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Or try demo</span>
                        <div class="flex-1 h-px" style="background: rgba(168,51,0,0.15);"></div>
                    </div>

                    <!-- Admin Demo Login -->
                    <form action="login.php" method="POST">
                        <input type="hidden" name="role" value="admin"/>
                        <button type="submit" name="demo_login" id="admin-demo-btn" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl border text-sm font-bold transition-all hover:shadow-md" style="border-color: rgba(168,51,0,0.3); color: #a83300; background: rgba(168,51,0,0.05);">
                            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                            Quick Admin Demo Login
                        </button>
                    </form>
                </div>

                <!-- Footer Text -->
                <p class="text-center text-xs text-gray-400 mt-6">
                    By signing in, you agree to our
                    <a href="#" class="font-semibold" style="color: #a83300;">Terms</a> &amp;
                    <a href="#" class="font-semibold" style="color: #a83300;">Privacy Policy</a>
                </p>
            </div>
        </div>

        <!-- Bottom Tagline -->
        <p class="text-center text-xs mt-6" style="color: rgba(255,181,157,0.4);">© 2026 Zyrop · Made with ❤️ for food lovers</p>
    </div>

    <script>
        function switchTab(role) {
            // Update buttons
            const userBtn = document.getElementById('tab-user-btn');
            const adminBtn = document.getElementById('tab-admin-btn');
            const userTab = document.getElementById('tab-user');
            const adminTab = document.getElementById('tab-admin');

            if (role === 'user') {
                userBtn.style.background = 'linear-gradient(135deg,#a83300,#d24200)';
                userBtn.style.color = '#fff';
                adminBtn.style.background = 'transparent';
                adminBtn.style.color = '#a83300';
                userTab.classList.add('active');
                adminTab.classList.remove('active');
            } else {
                adminBtn.style.background = 'linear-gradient(135deg,#a83300,#d24200)';
                adminBtn.style.color = '#fff';
                userBtn.style.background = 'transparent';
                userBtn.style.color = '#a83300';
                adminTab.classList.add('active');
                userTab.classList.remove('active');
            }
        }

        function togglePwd(fieldId, btn) {
            const field = document.getElementById(fieldId);
            if (field.type === 'password') {
                field.type = 'text';
                btn.textContent = 'visibility_off';
            } else {
                field.type = 'password';
                btn.textContent = 'visibility';
            }
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            const nd = document.getElementById('notifications-dropdown');
            if (nd && !nd.contains(e.target)) nd.classList.add('hidden');
        });
    </script>
</body>
</html>
