<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? '';
$firstName = $isLoggedIn ? explode(' ', trim($userName))[0] : '';
$pageTitle = $pageTitle ?? 'Zesto — Delicious Food Delivered Fast';
$pageDesc = $pageDesc ?? 'Order your favorite meals from top local restaurants near you. Fresh, fast, and always delicious.';
?><!DOCTYPE html>
<html lang="en" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDesc); ?>"/>
  
  <!-- Global CDNs -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  
  <link rel="stylesheet" href="css/zyrop.css"/>
  
  <!-- Tailwind Dynamic Configuration (LUXE Premium Neutral & Terracotta Palette) -->
  <script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#8a4f35", // Muted terracotta warm brown
            "primary-container": "#733f28",
            secondary: "#736f6c", // Muted gray
            tertiary: "#526043", // Olive green
            "tertiary-container": "#434f36",
            error: "#ba1a1a",
            background: "#fdfbf9", // Warm off-white / light cream
            surface: "rgba(255, 255, 255, 0.9)",
            "on-surface": "#221f1d", // Dark charcoal
            "outline-variant": "#e8e5e0" // Soft thin grey borders
          },
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            title: ['Playfair Display', 'serif']
          }
        }
      }
    }
  </script>
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #fdfbf9;
    }
    .zesto-glass {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid #e8e5e0;
      box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.02);
    }
    .zesto-glass-card {
      background: #ffffff;
      border: 1px solid #e8e5e0;
      box-shadow: 0 8px 30px 0 rgba(0, 0, 0, 0.03);
    }
    .text-glow {
      text-shadow: none;
    }
  </style>
</head>
<body class="bg-background text-[#221f1d] min-h-screen relative overflow-x-hidden">

<!-- Standard Zesto Ambient background canvas -->
<div class="fixed inset-0 w-full h-full -z-50 bg-[#fdfbf9]"></div>

<!-- Dynamic Unified Header / Glass Navbar -->
<header class="relative z-50 px-4 md:px-8 lg:px-12 pt-6">
  <nav class="zesto-glass rounded-2xl px-6 py-4 flex items-center justify-between transition-all duration-300">
    <!-- Brand Logo -->
    <a href="index.php" class="flex items-center gap-3">
      <span class="text-3xl font-extrabold tracking-tight font-title text-zinc-900 drop-shadow-sm">
        Zesto
      </span>
    </a>

    <!-- Center Navigation Links -->
    <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-zinc-600">
      <a href="index.php" class="hover:text-primary transition-colors flex items-center gap-1.5 hover:scale-105 duration-200">
        <span class="material-symbols-outlined" style="font-size:18px">home</span> Home
      </a>
      <a href="index.php#menu" class="hover:text-primary transition-colors flex items-center gap-1.5 hover:scale-105 duration-200">
        <span class="material-symbols-outlined" style="font-size:18px">restaurant_menu</span> Menu
      </a>
      <a href="orders.php" class="hover:text-primary transition-colors flex items-center gap-1.5 hover:scale-105 duration-200">
        <span class="material-symbols-outlined" style="font-size:18px">history</span> Orders
      </a>
      <a href="cart.php" class="hover:text-primary transition-colors flex items-center gap-1.5 hover:scale-105 duration-200 relative">
        <span class="material-symbols-outlined" style="font-size:18px">shopping_cart</span> Cart
        <span class="cart-count-badge bg-primary text-white rounded-full text-[10px] w-4.5 h-4.5 flex items-center justify-center font-extrabold absolute -top-2 -right-3 border border-background shadow-lg" style="display:none;">0</span>
      </a>
    </div>

    <!-- Right Side: Account state & cart indicator -->
    <div class="flex items-center gap-4">
      <!-- Cart Icon with badge (visible on mobile / desktop right) -->
      <a href="cart.php" class="relative p-2 rounded-lg hover:bg-black/5 transition-all md:hidden">
        <span class="material-symbols-outlined text-zinc-800" style="font-size:22px">shopping_cart</span>
        <span class="cart-count-badge bg-primary text-white rounded-full text-[9px] w-4 h-4 flex items-center justify-center font-extrabold absolute top-0.5 right-0.5 border border-background shadow-lg" style="display:none;">0</span>
      </a>

      <?php if ($isLoggedIn): ?>
        <!-- Logged In state glass dropdown menu -->
        <div class="flex items-center gap-3 bg-black/5 border border-black/5 rounded-full pl-3 pr-3 py-1.5">
          <span class="material-symbols-outlined text-primary" style="font-size:20px">account_circle</span>
          <span class="text-xs font-bold text-zinc-800 hidden sm:inline-block max-w-[100px] truncate">
            Hey, <?php echo htmlspecialchars($firstName); ?>
          </span>
          <a href="logout.php" class="material-symbols-outlined text-zinc-500 hover:text-rose-600 transition-all ml-1" style="font-size:18px" title="Logout">
            logout
          </a>
        </div>
      <?php else: ?>
        <!-- Guest State -->
        <a href="login.php" class="bg-primary text-white hover:bg-primary-container border border-primary/20 px-5 py-2 rounded-xl text-xs font-bold transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center gap-1.5 shadow-sm hover:shadow-primary/25">
          <span class="material-symbols-outlined" style="font-size:16px">login</span> Login
        </a>
      <?php endif; ?>
    </div>
  </nav>
</header>
