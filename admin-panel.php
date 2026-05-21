<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Auth guard - redirect to login if not admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php?tab=admin');
    exit;
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php?tab=admin');
    exit;
}

$admin_email = $_SESSION['admin_email'] ?? 'admin@zyrop.com';
$admin_initials = strtoupper(substr(explode('@', $admin_email)[0], 0, 2));
$active_section = $_GET['section'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard · Zyrop</title>
    <meta name="description" content="Zyrop Admin Console — manage orders, restaurants, users and analytics."/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link href="css/zyrop.css" rel="stylesheet"/>
    <script src="js/tailwind-theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8f6f5; }
        .sidebar { width: 260px; min-height: 100vh; background: linear-gradient(180deg, #1a0800 0%, #2d1200 100%); transition: transform 0.3s ease; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 10px 16px; border-radius: 12px; color: rgba(255,181,157,0.65); font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #ffb59d; }
        .sidebar-link.active { background: linear-gradient(135deg, rgba(168,51,0,0.7), rgba(210,66,0,0.5)); color: #fff; box-shadow: 0 4px 16px rgba(168,51,0,0.35); }
        .sidebar-link .material-symbols-outlined { font-size: 20px; }

        .stat-card { background: #fff; border-radius: 20px; padding: 24px; border: 1px solid rgba(0,0,0,0.06); transition: all 0.2s ease; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.1); }
        .stat-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; }

        .section { display: none; }
        .section.active { display: block; animation: fadeSlideIn 0.35s cubic-bezier(0.16,1,0.3,1) forwards; }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .badge { padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-success { background: rgba(0,135,53,0.12); color: #006b29; }
        .badge-warning { background: rgba(255,165,0,0.12); color: #b35c00; }
        .badge-error { background: rgba(186,26,26,0.12); color: #ba1a1a; }
        .badge-info { background: rgba(168,51,0,0.1); color: #a83300; }

        .data-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .data-table thead tr th { background: #f5f3f2; padding: 10px 16px; text-align: left; font-size: 11px; font-weight: 700; color: #5c4037; text-transform: uppercase; letter-spacing: 0.06em; }
        .data-table thead tr th:first-child { border-radius: 10px 0 0 10px; }
        .data-table thead tr th:last-child { border-radius: 0 10px 10px 0; }
        .data-table tbody tr { transition: background 0.15s ease; }
        .data-table tbody tr:hover { background: rgba(168,51,0,0.03); }
        .data-table tbody tr td { padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 14px; color: #1b1c1c; }

        .chart-card { background: #fff; border-radius: 20px; padding: 24px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 2px 12px rgba(0,0,0,0.05); }

        .sidebar-section-label { font-size: 10px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: rgba(255,181,157,0.35); padding: 16px 16px 6px; }

        @keyframes countUp { from { opacity: 0; transform: scale(0.8); } to { opacity: 1; transform: scale(1); } }
        .count-anim { animation: countUp 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

        .progress-bar { height: 6px; border-radius: 999px; background: rgba(168,51,0,0.12); overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #a83300, #d24200); transition: width 1s cubic-bezier(0.16,1,0.3,1); }

        .notification-dot { width: 8px; height: 8px; border-radius: 50%; background: #a83300; display: inline-block; }

        .mobile-menu-btn { display: none; }
        @media (max-width: 1024px) {
            .sidebar { position: fixed; top: 0; left: 0; z-index: 100; transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .mobile-menu-btn { display: flex; }
            .main-content { margin-left: 0 !important; }
        }
    </style>
</head>
<body class="font-['Plus_Jakarta_Sans']">
<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar shrink-0 flex flex-col z-50">
        <!-- Logo -->
        <div class="px-6 py-6 border-b" style="border-color: rgba(255,255,255,0.08);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" style="background: linear-gradient(135deg,#a83300,#d24200);">
                    <span class="material-symbols-outlined text-white text-[18px]">restaurant</span>
                </div>
                <div>
                    <p class="font-extrabold text-sm" style="color: #ffb59d;">Zyrop</p>
                    <p class="text-[10px]" style="color: rgba(255,181,157,0.45);">Admin Console</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 overflow-y-auto">
            <p class="sidebar-section-label">Main</p>
            <a href="?section=overview" id="nav-overview" class="sidebar-link <?= $active_section === 'overview' ? 'active' : '' ?>" onclick="showSection('overview')">
                <span class="material-symbols-outlined">dashboard</span> Overview
            </a>
            <a href="?section=orders" id="nav-orders" class="sidebar-link <?= $active_section === 'orders' ? 'active' : '' ?>" onclick="showSection('orders')">
                <span class="material-symbols-outlined">receipt_long</span> Orders
                <span class="ml-auto badge" style="background:rgba(168,51,0,0.15);color:#a83300;">24</span>
            </a>
            <a href="?section=analytics" id="nav-analytics" class="sidebar-link <?= $active_section === 'analytics' ? 'active' : '' ?>" onclick="showSection('analytics')">
                <span class="material-symbols-outlined">bar_chart</span> Analytics
            </a>

            <p class="sidebar-section-label">Manage</p>
            <a href="?section=restaurants" id="nav-restaurants" class="sidebar-link <?= $active_section === 'restaurants' ? 'active' : '' ?>" onclick="showSection('restaurants')">
                <span class="material-symbols-outlined">storefront</span> Restaurants
            </a>
            <a href="?section=users" id="nav-users" class="sidebar-link <?= $active_section === 'users' ? 'active' : '' ?>" onclick="showSection('users')">
                <span class="material-symbols-outlined">group</span> Users
            </a>
            <a href="?section=menu" id="nav-menu" class="sidebar-link <?= $active_section === 'menu' ? 'active' : '' ?>" onclick="showSection('menu')">
                <span class="material-symbols-outlined">menu_book</span> Menu Manager
            </a>
            <a href="?section=delivery" id="nav-delivery" class="sidebar-link <?= $active_section === 'delivery' ? 'active' : '' ?>" onclick="showSection('delivery')">
                <span class="material-symbols-outlined">delivery_dining</span> Delivery Queue
            </a>

            <p class="sidebar-section-label">Finance</p>
            <a href="?section=payments" id="nav-payments" class="sidebar-link <?= $active_section === 'payments' ? 'active' : '' ?>" onclick="showSection('payments')">
                <span class="material-symbols-outlined">payments</span> Payments
            </a>
            <a href="?section=reports" id="nav-reports" class="sidebar-link <?= $active_section === 'reports' ? 'active' : '' ?>" onclick="showSection('reports')">
                <span class="material-symbols-outlined">summarize</span> Reports
            </a>

            <p class="sidebar-section-label">System</p>
            <a href="?section=support" id="nav-support" class="sidebar-link <?= $active_section === 'support' ? 'active' : '' ?>" onclick="showSection('support')">
                <span class="material-symbols-outlined">support_agent</span> Support Queue
                <span class="ml-auto notification-dot"></span>
            </a>
            <a href="?section=settings" id="nav-settings" class="sidebar-link <?= $active_section === 'settings' ? 'active' : '' ?>" onclick="showSection('settings')">
                <span class="material-symbols-outlined">settings</span> Settings
            </a>
        </nav>

        <!-- Admin Profile -->
        <div class="px-3 py-4 border-t" style="border-color: rgba(255,255,255,0.08);">
            <div class="flex items-center gap-3 p-3 rounded-xl" style="background: rgba(255,255,255,0.06);">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0" style="background: linear-gradient(135deg,#a83300,#d24200); color:#fff;">
                    <?= $admin_initials ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold truncate" style="color: #ffb59d;"><?= htmlspecialchars($admin_email) ?></p>
                    <p class="text-[10px]" style="color: rgba(255,181,157,0.45);">Super Admin</p>
                </div>
                <a href="?action=logout" class="material-symbols-outlined text-[18px] hover:text-red-400 transition-colors" style="color: rgba(255,181,157,0.45);" title="Logout">logout</a>
            </div>
        </div>
    </aside>

    <!-- Sidebar Overlay (mobile) -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden" onclick="closeSidebar()"></div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col main-content" style="margin-left: 0;">

        <!-- Top Header Bar -->
        <header class="bg-white border-b sticky top-0 z-30 px-6 h-16 flex items-center justify-between gap-4" style="border-color: rgba(0,0,0,0.08);">
            <div class="flex items-center gap-4">
                <button class="mobile-menu-btn items-center justify-center w-9 h-9 rounded-xl hover:bg-gray-100 transition-colors" onclick="toggleSidebar()" id="mobile-menu-btn">
                    <span class="material-symbols-outlined text-gray-600">menu</span>
                </button>
                <div>
                    <h1 class="text-base font-bold text-gray-800" id="page-title">Dashboard Overview</h1>
                    <p class="text-xs text-gray-400 hidden sm:block">Today, <?= date('F j, Y') ?></p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex items-center bg-gray-50 border rounded-full px-4 py-2 gap-2 w-72" style="border-color: rgba(0,0,0,0.1);">
                <span class="material-symbols-outlined text-gray-400 text-[18px]">search</span>
                <input class="bg-transparent border-none focus:ring-0 text-sm w-full text-gray-700 placeholder-gray-400" placeholder="Search orders, users..." type="text"/>
            </div>

            <div class="flex items-center gap-3">
                <!-- Notifications Bell -->
                <div class="relative">
                    <button id="notif-btn" onclick="toggleNotifDropdown()" class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors relative">
                        <span class="material-symbols-outlined text-gray-600 text-[22px]">notifications</span>
                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full" style="background:#a83300;"></span>
                    </button>
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border overflow-hidden z-50" style="border-color: rgba(0,0,0,0.08);">
                        <div class="px-4 py-3 border-b flex justify-between items-center" style="border-color: rgba(0,0,0,0.08);">
                            <span class="font-bold text-sm text-gray-800">Notifications</span>
                            <span class="badge badge-error">3 new</span>
                        </div>
                        <div class="divide-y max-h-72 overflow-y-auto" style="border-color: rgba(0,0,0,0.05);">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <p class="text-xs font-bold text-gray-800">🚨 High volume alert</p>
                                <p class="text-xs text-gray-500 mt-0.5">Orders surging in South Zone — 340% above avg</p>
                                <p class="text-[10px] text-gray-400 mt-1">2 min ago</p>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <p class="text-xs font-bold text-gray-800">⚠️ Restaurant suspended</p>
                                <p class="text-xs text-gray-500 mt-0.5">Urban Bites flagged for quality violations</p>
                                <p class="text-[10px] text-gray-400 mt-1">18 min ago</p>
                            </a>
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 transition-colors">
                                <p class="text-xs font-bold text-gray-800">💰 Payout processed</p>
                                <p class="text-xs text-gray-500 mt-0.5">₹12,40,000 disbursed to 380 partners</p>
                                <p class="text-[10px] text-gray-400 mt-1">1 hour ago</p>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Admin Avatar -->
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm" style="background: linear-gradient(135deg,#a83300,#d24200); color:#fff;">
                    <?= $admin_initials ?>
                </div>

                <!-- Back to Site -->
                <a href="index.php" class="hidden sm:flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold transition-all border" style="border-color:rgba(168,51,0,0.3); color:#a83300;" onmouseover="this.style.background='rgba(168,51,0,0.06)'" onmouseout="this.style.background='transparent'">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    Storefront
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-y-auto">

            <!-- ===================== OVERVIEW ===================== -->
            <div id="section-overview" class="section <?= $active_section === 'overview' ? 'active' : '' ?>">
                <!-- KPI Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- GMV -->
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-icon" style="background: rgba(168,51,0,0.1);">
                                <span class="material-symbols-outlined text-[22px]" style="color:#a83300;">payments</span>
                            </div>
                            <span class="badge badge-success">↑ 8.2%</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">GMV Today</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1 count-anim">₹28.5L</p>
                        <div class="progress-bar mt-3"><div class="progress-fill" style="width:72%"></div></div>
                        <p class="text-[10px] text-gray-400 mt-1">72% of monthly target</p>
                    </div>

                    <!-- Orders -->
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-icon" style="background: rgba(0,107,41,0.1);">
                                <span class="material-symbols-outlined text-[22px]" style="color:#006b29;">receipt_long</span>
                            </div>
                            <span class="badge badge-success">↑ 12.4%</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders (24h)</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1 count-anim">12,480</p>
                        <div class="progress-bar mt-3"><div class="progress-fill" style="width:85%; background: linear-gradient(90deg,#006b29,#008735);"></div></div>
                        <p class="text-[10px] text-gray-400 mt-1">Peak: 7–9 PM</p>
                    </div>

                    <!-- Active Restaurants -->
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-icon" style="background: rgba(0,0,200,0.07);">
                                <span class="material-symbols-outlined text-[22px]" style="color:#1a4ecb;">storefront</span>
                            </div>
                            <span class="badge badge-error">3 suspended</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Restaurants</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1 count-anim">1,042</p>
                        <div class="progress-bar mt-3"><div class="progress-fill" style="width:91%; background: linear-gradient(90deg,#1a4ecb,#3b6eed);"></div></div>
                        <p class="text-[10px] text-gray-400 mt-1">91% uptime</p>
                    </div>

                    <!-- On-Time Rate -->
                    <div class="stat-card">
                        <div class="flex items-center justify-between mb-4">
                            <div class="stat-icon" style="background: rgba(255,165,0,0.1);">
                                <span class="material-symbols-outlined text-[22px]" style="color:#b35c00;">timer</span>
                            </div>
                            <span class="badge badge-success">Target: 93%</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">On-Time Rate</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1 count-anim">94.1%</p>
                        <div class="progress-bar mt-3"><div class="progress-fill" style="width:94%; background: linear-gradient(90deg,#b35c00,#e07a00);"></div></div>
                        <p class="text-[10px] text-gray-400 mt-1">Exceeding target</p>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    <!-- Revenue Chart -->
                    <div class="chart-card lg:col-span-2">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h2 class="font-bold text-gray-800">Revenue & Orders Trend</h2>
                                <p class="text-xs text-gray-400 mt-0.5">Last 7 days</p>
                            </div>
                            <div class="flex gap-2">
                                <button class="text-xs px-3 py-1 rounded-full font-semibold" style="background:rgba(168,51,0,0.1); color:#a83300;">7D</button>
                                <button class="text-xs px-3 py-1 rounded-full font-semibold text-gray-400 hover:bg-gray-100 transition-colors">30D</button>
                                <button class="text-xs px-3 py-1 rounded-full font-semibold text-gray-400 hover:bg-gray-100 transition-colors">90D</button>
                            </div>
                        </div>
                        <canvas id="revenueChart" height="200"></canvas>
                    </div>

                    <!-- Order Status Donut -->
                    <div class="chart-card">
                        <div class="mb-4">
                            <h2 class="font-bold text-gray-800">Order Status</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Live breakdown</p>
                        </div>
                        <canvas id="statusChart" height="200"></canvas>
                        <div class="mt-4 flex flex-col gap-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#a83300;"></span>Delivered</span>
                                <span class="font-bold text-gray-800">68%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#d24200;"></span>In Transit</span>
                                <span class="font-bold text-gray-800">18%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#ffb59d;"></span>Preparing</span>
                                <span class="font-bold text-gray-800">9%</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full inline-block" style="background:#e4e2e2;"></span>Cancelled</span>
                                <span class="font-bold text-gray-800">5%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tables Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Recent Orders -->
                    <div class="chart-card">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-bold text-gray-800">Recent Orders</h2>
                            <button onclick="showSection('orders')" class="text-xs font-semibold" style="color:#a83300;">View all →</button>
                        </div>
                        <table class="data-table">
                            <thead><tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr></thead>
                            <tbody>
                                <tr><td class="font-mono font-semibold text-xs">#ZY-84920</td><td>Arjun M.</td><td class="font-bold text-gray-800">₹845</td><td><span class="badge badge-success">Delivered</span></td></tr>
                                <tr><td class="font-mono font-semibold text-xs">#ZY-84919</td><td>Priya S.</td><td class="font-bold text-gray-800">₹320</td><td><span class="badge badge-info">In Transit</span></td></tr>
                                <tr><td class="font-mono font-semibold text-xs">#ZY-84918</td><td>Rahul K.</td><td class="font-bold text-gray-800">₹590</td><td><span class="badge badge-warning">Preparing</span></td></tr>
                                <tr><td class="font-mono font-semibold text-xs">#ZY-84917</td><td>Sneha V.</td><td class="font-bold text-gray-800">₹1,240</td><td><span class="badge badge-success">Delivered</span></td></tr>
                                <tr><td class="font-mono font-semibold text-xs">#ZY-84916</td><td>Kiran D.</td><td class="font-bold text-gray-800">₹180</td><td><span class="badge badge-error">Cancelled</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Top Restaurants -->
                    <div class="chart-card">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="font-bold text-gray-800">Top Restaurants</h2>
                            <button onclick="showSection('restaurants')" class="text-xs font-semibold" style="color:#a83300;">View all →</button>
                        </div>
                        <div class="flex flex-col gap-3">
                            <?php
                            $topRests = [
                                ['The Steakhouse Grill', '₹4,82,000', 94, 4.8],
                                ['Pasta Fresca', '₹3,10,000', 78, 4.9],
                                ['Miyabi Sushi', '₹2,85,000', 62, 4.7],
                                ['Urban Bites', '₹2,40,000', 57, 4.6],
                                ['Taco Fiesta', '₹1,92,000', 45, 4.4],
                            ];
                            foreach ($topRests as $i => $r): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0" style="background: rgba(168,51,0,<?= 0.15 - $i * 0.02 ?>); color:#a83300;"><?= $i+1 ?></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate"><?= $r[0] ?></p>
                                    <div class="progress-bar mt-1"><div class="progress-fill" style="width:<?= $r[2] ?>%"></div></div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xs font-bold text-gray-800"><?= $r[1] ?></p>
                                    <p class="text-[10px] text-yellow-500 font-semibold">★ <?= $r[3] ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== ORDERS ===================== -->
            <div id="section-orders" class="section <?= $active_section === 'orders' ? 'active' : '' ?>">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Order Management</h2>
                        <p class="text-sm text-gray-400 mt-1">Monitor and manage all customer orders</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 rounded-xl border text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors flex items-center gap-1.5" style="border-color:rgba(0,0,0,0.12);">
                            <span class="material-symbols-outlined text-[16px]">filter_list</span> Filter
                        </button>
                        <button class="px-4 py-2 rounded-xl text-sm font-semibold text-white flex items-center gap-1.5" style="background:linear-gradient(135deg,#a83300,#d24200);">
                            <span class="material-symbols-outlined text-[16px]">download</span> Export
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach (['All', 'Delivered', 'In Transit', 'Preparing', 'Cancelled'] as $f): ?>
                    <button class="px-4 py-1.5 rounded-full text-xs font-bold transition-all <?= $f === 'All' ? '' : 'bg-white border text-gray-500 hover:border-red-200' ?>" 
                            style="<?= $f === 'All' ? 'background:linear-gradient(135deg,#a83300,#d24200);color:#fff;' : 'border-color:rgba(0,0,0,0.1);' ?>">
                        <?= $f ?>
                    </button>
                    <?php endforeach; ?>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden border" style="border-color:rgba(0,0,0,0.06); box-shadow:0 2px 12px rgba(0,0,0,0.05);">
                    <table class="data-table">
                        <thead><tr>
                            <th>Order ID</th><th>Customer</th><th>Restaurant</th><th>Items</th><th>Amount</th><th>Time</th><th>Status</th><th>Action</th>
                        </tr></thead>
                        <tbody>
                        <?php
                        $orders = [
                            ['#ZY-84920','Arjun M.','The Steakhouse Grill','Ribeye 12oz, Fries','₹845','12:30 PM','Delivered'],
                            ['#ZY-84919','Priya S.','Pasta Fresca','Carbonara × 2','₹320','12:28 PM','In Transit'],
                            ['#ZY-84918','Rahul K.','Miyabi Sushi','Sushi Platter','₹590','12:25 PM','Preparing'],
                            ['#ZY-84917','Sneha V.','Urban Bites','Burger × 3, Fries × 2','₹1,240','12:18 PM','Delivered'],
                            ['#ZY-84916','Kiran D.','Taco Fiesta','Tacos × 4','₹180','12:10 PM','Cancelled'],
                            ['#ZY-84915','Meera J.','The Steakhouse Grill','Filet Mignon','₹420','11:55 AM','Delivered'],
                            ['#ZY-84914','Dev P.','Ocean Harvest','Poke Bowl × 2','₹370','11:40 AM','Delivered'],
                            ['#ZY-84913','Anu R.','Green Leaf Kitchen','Salad Bowl, Juice','₹280','11:32 AM','In Transit'],
                        ];
                        $statusMap = ['Delivered'=>'badge-success','In Transit'=>'badge-info','Preparing'=>'badge-warning','Cancelled'=>'badge-error'];
                        foreach ($orders as $o): ?>
                        <tr>
                            <td class="font-mono text-xs font-bold" style="color:#a83300;"><?= $o[0] ?></td>
                            <td class="font-medium"><?= $o[1] ?></td>
                            <td class="text-gray-500 text-sm"><?= $o[2] ?></td>
                            <td class="text-gray-500 text-xs"><?= $o[3] ?></td>
                            <td class="font-bold"><?= $o[4] ?></td>
                            <td class="text-gray-400 text-xs"><?= $o[5] ?></td>
                            <td><span class="badge <?= $statusMap[$o[6]] ?>"><?= $o[6] ?></span></td>
                            <td>
                                <button class="text-xs font-semibold px-3 py-1 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">Details</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="px-6 py-4 border-t flex justify-between items-center" style="border-color:rgba(0,0,0,0.06);">
                        <p class="text-xs text-gray-400">Showing 8 of 12,480 orders</p>
                        <div class="flex gap-1">
                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold" style="background:linear-gradient(135deg,#a83300,#d24200);color:#fff;">1</button>
                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-gray-500 hover:bg-gray-100 transition-colors">2</button>
                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-gray-500 hover:bg-gray-100 transition-colors">3</button>
                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-gray-500 hover:bg-gray-100 transition-colors">→</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== ANALYTICS ===================== -->
            <div id="section-analytics" class="section <?= $active_section === 'analytics' ? 'active' : '' ?>">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Analytics & Insights</h2>
                    <p class="text-sm text-gray-400 mt-1">Platform performance at a glance</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <div class="chart-card">
                        <h3 class="font-bold text-gray-800 mb-4">Weekly Revenue (₹ Lakhs)</h3>
                        <canvas id="weeklyRevenueChart" height="220"></canvas>
                    </div>
                    <div class="chart-card">
                        <h3 class="font-bold text-gray-800 mb-4">Category Performance</h3>
                        <canvas id="categoryChart" height="220"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="chart-card">
                        <h3 class="font-bold text-gray-800 mb-4">Hourly Order Volume</h3>
                        <canvas id="hourlyChart" height="200"></canvas>
                    </div>
                    <div class="chart-card lg:col-span-2">
                        <h3 class="font-bold text-gray-800 mb-4">Key Metrics Summary</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php
                            $metrics = [
                                ['Avg Order Value', '₹342', '↑ 5.2%', 'badge-success'],
                                ['Customer Retention', '71.3%', '↑ 2.1%', 'badge-success'],
                                ['Avg Delivery Time', '28 min', '↓ 3 min', 'badge-success'],
                                ['Cancellation Rate', '4.8%', '↓ 0.6%', 'badge-success'],
                                ['New Users (7d)', '3,842', '↑ 18%', 'badge-success'],
                                ['Support Tickets', '127', '↑ 12', 'badge-warning'],
                            ];
                            foreach ($metrics as $m): ?>
                            <div class="p-3 rounded-xl" style="background:rgba(168,51,0,0.04); border:1px solid rgba(168,51,0,0.08);">
                                <p class="text-xs text-gray-400 font-semibold"><?= $m[0] ?></p>
                                <p class="text-lg font-extrabold text-gray-800 mt-0.5"><?= $m[1] ?></p>
                                <span class="badge <?= $m[3] ?>"><?= $m[2] ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===================== RESTAURANTS ===================== -->
            <div id="section-restaurants" class="section <?= $active_section === 'restaurants' ? 'active' : '' ?>">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">Restaurant Management</h2>
                        <p class="text-sm text-gray-400 mt-1">1,042 active partners</p>
                    </div>
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold text-white flex items-center gap-1.5" style="background:linear-gradient(135deg,#a83300,#d24200);">
                        <span class="material-symbols-outlined text-[16px]">add</span> Add Restaurant
                    </button>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden border" style="border-color:rgba(0,0,0,0.06); box-shadow:0 2px 12px rgba(0,0,0,0.05);">
                    <table class="data-table">
                        <thead><tr><th>Restaurant</th><th>Cuisine</th><th>Rating</th><th>Orders (7d)</th><th>Revenue</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php
                        $rests = [
                            ['The Steakhouse Grill','American, Steaks',4.8,1240,'₹4,82,000','Active'],
                            ['Pasta Fresca','Italian, Pasta',4.9,980,'₹3,10,000','Active'],
                            ['Miyabi Sushi','Japanese, Seafood',4.7,820,'₹2,85,000','Active'],
                            ['Urban Bites','Burgers, Fast Food',4.6,760,'₹2,40,000','Suspended'],
                            ['Taco Fiesta','Mexican, Street',4.4,650,'₹1,92,000','Active'],
                            ['Green Leaf Kitchen','Vegetarian, Salads',4.5,430,'₹1,10,000','Active'],
                        ];
                        foreach ($rests as $r): ?>
                        <tr>
                            <td class="font-semibold"><?= $r[0] ?></td>
                            <td class="text-gray-500 text-sm"><?= $r[1] ?></td>
                            <td><span class="text-yellow-500 font-bold">★</span> <?= $r[2] ?></td>
                            <td class="font-medium"><?= number_format($r[3]) ?></td>
                            <td class="font-bold"><?= $r[4] ?></td>
                            <td><span class="badge <?= $r[5] === 'Active' ? 'badge-success' : 'badge-error' ?>"><?= $r[5] ?></span></td>
                            <td>
                                <div class="flex gap-1">
                                    <button class="text-xs font-semibold px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">Edit</button>
                                    <button class="text-xs font-semibold px-2 py-1 rounded-lg hover:bg-red-50 transition-colors text-red-500">Suspend</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===================== USERS ===================== -->
            <div id="section-users" class="section <?= $active_section === 'users' ? 'active' : '' ?>">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">User Management</h2>
                        <p class="text-sm text-gray-400 mt-1">48,290 registered customers</p>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex items-center bg-white border rounded-xl px-3 py-2 gap-2" style="border-color:rgba(0,0,0,0.1);">
                            <span class="material-symbols-outlined text-gray-400 text-[16px]">search</span>
                            <input class="border-none focus:ring-0 text-sm bg-transparent text-gray-700 placeholder-gray-400 w-40" placeholder="Search users..."/>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden border" style="border-color:rgba(0,0,0,0.06); box-shadow:0 2px 12px rgba(0,0,0,0.05);">
                    <table class="data-table">
                        <thead><tr><th>User</th><th>Email</th><th>Joined</th><th>Orders</th><th>Total Spend</th><th>Status</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php
                        $users = [
                            ['Arjun Mehta','arjun.m@gmail.com','Jan 2025',84,'₹28,420','Active'],
                            ['Priya Sharma','priya.s@outlook.com','Mar 2025',62,'₹19,840','Active'],
                            ['Rahul Kumar','rahul.k@yahoo.com','Feb 2025',45,'₹14,200','Active'],
                            ['Sneha Verma','sneha.v@gmail.com','Dec 2024',120,'₹41,800','Active'],
                            ['Kiran Das','kiran.d@gmail.com','Apr 2025',8,'₹2,100','Inactive'],
                            ['Meera Joshi','meera.j@gmail.com','Nov 2024',97,'₹32,400','Active'],
                        ];
                        foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shrink-0" style="background:rgba(168,51,0,0.12); color:#a83300;"><?= strtoupper(substr($u[0],0,2)) ?></div>
                                    <span class="font-semibold"><?= $u[0] ?></span>
                                </div>
                            </td>
                            <td class="text-gray-500 text-sm"><?= $u[1] ?></td>
                            <td class="text-gray-400 text-sm"><?= $u[2] ?></td>
                            <td class="font-medium"><?= $u[3] ?></td>
                            <td class="font-bold"><?= $u[4] ?></td>
                            <td><span class="badge <?= $u[5] === 'Active' ? 'badge-success' : 'badge-warning' ?>"><?= $u[5] ?></span></td>
                            <td>
                                <div class="flex gap-1">
                                    <button class="text-xs font-semibold px-2 py-1 rounded-lg hover:bg-gray-100 transition-colors text-gray-500">View</button>
                                    <button class="text-xs font-semibold px-2 py-1 rounded-lg hover:bg-red-50 transition-colors text-red-500">Block</button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===================== PAYMENTS ===================== -->
            <div id="section-payments" class="section <?= $active_section === 'payments' ? 'active' : '' ?>">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Payment & Finance</h2>
                    <p class="text-sm text-gray-400 mt-1">Monitor transactions and payouts</p>
                </div>

                <!-- Finance KPIs -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <?php
                    $financeKPIs = [
                        ['Total Revenue', '₹12.4Cr', '↑ 22%', '#a83300'],
                        ['Platform Commission', '₹1.86Cr', '15%', '#006b29'],
                        ['Partner Payouts', '₹10.54Cr', 'This month', '#1a4ecb'],
                        ['Refunds Issued', '₹28,400', '↓ 8%', '#b35c00'],
                    ];
                    foreach ($financeKPIs as $k): ?>
                    <div class="stat-card text-center">
                        <p class="text-2xl font-extrabold mt-1" style="color:<?= $k[3] ?>;"><?= $k[0] === 'Total Revenue' ? $k[1] : $k[1] ?></p>
                        <p class="text-xs font-semibold text-gray-500 mt-1"><?= $k[0] ?></p>
                        <p class="text-xs mt-1 font-bold text-gray-400"><?= $k[2] ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden border" style="border-color:rgba(0,0,0,0.06); box-shadow:0 2px 12px rgba(0,0,0,0.05);">
                    <div class="px-6 py-4 border-b" style="border-color:rgba(0,0,0,0.06);">
                        <h3 class="font-bold text-gray-800">Recent Transactions</h3>
                    </div>
                    <table class="data-table">
                        <thead><tr><th>Txn ID</th><th>Order</th><th>User</th><th>Method</th><th>Amount</th><th>Commission</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php
                        $txns = [
                            ['TXN-990821','#ZY-84920','Arjun M.','UPI','₹845','₹126','Success'],
                            ['TXN-990820','#ZY-84919','Priya S.','Card','₹320','₹48','Success'],
                            ['TXN-990819','#ZY-84918','Rahul K.','Wallet','₹590','₹88','Pending'],
                            ['TXN-990818','#ZY-84917','Sneha V.','UPI','₹1,240','₹186','Success'],
                            ['TXN-990817','#ZY-84916','Kiran D.','Card','₹180','₹27','Refunded'],
                        ];
                        $tMap = ['Success'=>'badge-success','Pending'=>'badge-warning','Refunded'=>'badge-error'];
                        foreach ($txns as $t): ?>
                        <tr>
                            <td class="font-mono text-xs font-bold text-gray-500"><?= $t[0] ?></td>
                            <td class="font-mono text-xs font-semibold" style="color:#a83300;"><?= $t[1] ?></td>
                            <td class="font-medium"><?= $t[2] ?></td>
                            <td class="text-gray-500 text-sm"><?= $t[3] ?></td>
                            <td class="font-bold"><?= $t[4] ?></td>
                            <td class="text-gray-500 text-sm"><?= $t[5] ?></td>
                            <td><span class="badge <?= $tMap[$t[6]] ?>"><?= $t[6] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===================== SUPPORT ===================== -->
            <div id="section-support" class="section <?= $active_section === 'support' ? 'active' : '' ?>">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Support Queue</h2>
                    <p class="text-sm text-gray-400 mt-1">127 open tickets</p>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden border" style="border-color:rgba(0,0,0,0.06); box-shadow:0 2px 12px rgba(0,0,0,0.05);">
                    <table class="data-table">
                        <thead><tr><th>Ticket</th><th>Customer</th><th>Topic</th><th>Priority</th><th>SLA</th><th>Agent</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php
                        $tickets = [
                            ['#8821','Arjun M.','Refund Request','High','18 min','Raj K.','Open'],
                            ['#8817','Priya S.','Driver Safety Issue','Critical','42 min','Unassigned','Escalated'],
                            ['#8812','Rahul K.','Partner Payout','Medium','2 hrs','Meena P.','In Progress'],
                            ['#8805','Sneha V.','Missing Items','High','30 min','Raj K.','Open'],
                            ['#8799','Kiran D.','App Bug Report','Low','24 hrs','Dev Team','Open'],
                            ['#8792','Meera J.','Account Issue','Medium','4 hrs','Meena P.','In Progress'],
                        ];
                        $pMap = ['Critical'=>'badge-error','High'=>'badge-warning','Medium'=>'badge-info','Low'=>'text-gray-400 bg-gray-100'];
                        $sMap = ['Open'=>'badge-warning','Escalated'=>'badge-error','In Progress'=>'badge-info'];
                        foreach ($tickets as $t): ?>
                        <tr>
                            <td class="font-mono text-xs font-bold" style="color:#a83300;"><?= $t[0] ?></td>
                            <td class="font-medium"><?= $t[1] ?></td>
                            <td class="text-gray-500 text-sm"><?= $t[2] ?></td>
                            <td><span class="badge <?= $pMap[$t[3]] ?>"><?= $t[3] ?></span></td>
                            <td class="text-sm <?= $t[3] === 'Critical' ? 'text-red-500 font-bold' : 'text-gray-500' ?>"><?= $t[4] ?></td>
                            <td class="text-sm text-gray-500"><?= $t[5] ?></td>
                            <td><span class="badge <?= $sMap[$t[6]] ?>"><?= $t[6] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===================== OTHER SECTIONS (Placeholders) ===================== -->
            <?php foreach (['menu', 'delivery', 'reports', 'settings'] as $sec): ?>
            <div id="section-<?= $sec ?>" class="section <?= $active_section === $sec ? 'active' : '' ?>">
                <div class="text-center py-24">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:rgba(168,51,0,0.08);">
                        <span class="material-symbols-outlined text-[28px]" style="color:#a83300;"><?= $sec === 'menu' ? 'menu_book' : ($sec === 'delivery' ? 'delivery_dining' : ($sec === 'reports' ? 'summarize' : 'settings')) ?></span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800"><?= ucfirst($sec) ?> section</h3>
                    <p class="text-sm text-gray-400 mt-1">This section is coming soon</p>
                </div>
            </div>
            <?php endforeach; ?>

        </main>
    </div>
</div>

<script>
// Section switching
const pageTitles = {
    overview: 'Dashboard Overview',
    orders: 'Order Management',
    analytics: 'Analytics & Insights',
    restaurants: 'Restaurant Management',
    users: 'User Management',
    menu: 'Menu Manager',
    delivery: 'Delivery Queue',
    payments: 'Payment & Finance',
    reports: 'Reports',
    support: 'Support Queue',
    settings: 'Settings'
};

function showSection(name) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
    const sec = document.getElementById('section-' + name);
    const nav = document.getElementById('nav-' + name);
    if (sec) sec.classList.add('active');
    if (nav) nav.classList.add('active');
    document.getElementById('page-title').textContent = pageTitles[name] || name;
    // Close sidebar on mobile
    closeSidebar();
}

// Notification dropdown
function toggleNotifDropdown() {
    const d = document.getElementById('notif-dropdown');
    d.classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const d = document.getElementById('notif-dropdown');
    const btn = document.getElementById('notif-btn');
    if (d && !d.contains(e.target) && !btn.contains(e.target)) d.classList.add('hidden');
});

// Mobile sidebar
function toggleSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebar-overlay');
    s.classList.toggle('open');
    o.classList.toggle('hidden');
}
function closeSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebar-overlay');
    s.classList.remove('open');
    o.classList.add('hidden');
}

// ─── CHARTS ───
window.addEventListener('load', function() {
    const primaryColor = '#a83300';
    const primaryLight = 'rgba(168,51,0,0.12)';

    // Revenue Chart
    const revCtx = document.getElementById('revenueChart');
    if (revCtx) {
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (₹L)',
                    data: [18.2, 22.4, 19.8, 25.1, 28.5, 34.2, 28.5],
                    borderColor: primaryColor,
                    backgroundColor: 'rgba(168,51,0,0.08)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: primaryColor,
                    pointRadius: 4,
                    borderWidth: 2
                }, {
                    label: 'Orders (K)',
                    data: [8.2, 9.8, 9.1, 10.5, 11.8, 14.2, 12.5],
                    borderColor: '#006b29',
                    backgroundColor: 'rgba(0,107,41,0.06)',
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#006b29',
                    pointRadius: 4,
                    borderWidth: 2
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'top', labels: { font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 } } } }, scales: { y: { beginAtZero: false, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } }
        });
    }

    // Status Donut
    const statCtx = document.getElementById('statusChart');
    if (statCtx) {
        new Chart(statCtx, {
            type: 'doughnut',
            data: {
                labels: ['Delivered', 'In Transit', 'Preparing', 'Cancelled'],
                datasets: [{ data: [68, 18, 9, 5], backgroundColor: ['#a83300', '#d24200', '#ffb59d', '#e4e2e2'], borderWidth: 0, hoverOffset: 6 }]
            },
            options: { responsive: true, cutout: '72%', plugins: { legend: { display: false } } }
        });
    }

    // Weekly Revenue
    const wkCtx = document.getElementById('weeklyRevenueChart');
    if (wkCtx) {
        new Chart(wkCtx, {
            type: 'bar',
            data: {
                labels: ['W1', 'W2', 'W3', 'W4'],
                datasets: [{ label: 'Revenue (₹L)', data: [112, 98, 145, 128], backgroundColor: ['rgba(168,51,0,0.8)', 'rgba(168,51,0,0.5)', 'rgba(168,51,0,0.9)', 'rgba(168,51,0,0.7)'], borderRadius: 8 }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } }
        });
    }

    // Category Chart
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'radar',
            data: {
                labels: ['Pizza', 'Burgers', 'Sushi', 'Pasta', 'Drinks', 'Healthy'],
                datasets: [{ label: 'Orders (K)', data: [28, 35, 18, 22, 15, 12], backgroundColor: 'rgba(168,51,0,0.15)', borderColor: '#a83300', pointBackgroundColor: '#a83300', borderWidth: 2 }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { r: { grid: { color: 'rgba(0,0,0,0.08)' }, ticks: { display: false } } } }
        });
    }

    // Hourly Chart
    const hrCtx = document.getElementById('hourlyChart');
    if (hrCtx) {
        new Chart(hrCtx, {
            type: 'bar',
            data: {
                labels: ['10','11','12','13','14','15','16','17','18','19','20','21'],
                datasets: [{ label: 'Orders', data: [120, 340, 680, 820, 590, 420, 380, 510, 920, 1480, 1620, 980], backgroundColor: (ctx) => { const v = ctx.raw; return v > 1000 ? '#a83300' : 'rgba(168,51,0,0.4)'; }, borderRadius: 4 }]
            },
            options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }, x: { grid: { display: false } } } }
        });
    }

    // Animate progress bars
    setTimeout(() => {
        document.querySelectorAll('.progress-fill').forEach(el => {
            const w = el.style.width;
            el.style.width = '0%';
            setTimeout(() => { el.style.width = w; }, 100);
        });
    }, 300);
});
</script>
</body>
</html>
