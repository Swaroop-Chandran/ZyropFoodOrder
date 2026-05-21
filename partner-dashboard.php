<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Partner dashboard | ZyropFoodOrder</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="css/zyrop.css" rel="stylesheet"/>
<script src="js/tailwind-theme.js"></script>
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col">
<header class="bg-surface dark:bg-on-background shadow-sm docked full-width top-0 sticky z-50">
<div class="flex justify-between items-center w-full px-margin-desktop h-20 max-w-container-max-width mx-auto">
<a href="index.php" class="text-headline-md font-headline-md font-extrabold text-primary dark:text-primary-fixed">ZyropFoodOrder</a>
<nav class="hidden lg:flex items-center gap-6">
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="index.php">Browse</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="order-track.php">Track</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="cart.php">Cart</a>
<a class="text-primary font-bold border-b-2 border-primary pb-1 font-label-md text-label-md whitespace-nowrap" href="partner-dashboard.php">Partner</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="admin-panel.php">Admin</a>
</nav>
<span class="text-label-sm text-secondary dark:text-secondary-fixed-dim hidden sm:block">Partner</span>
</div>
</header>

<main class="flex-1 max-w-container-max-width mx-auto w-full px-margin-desktop py-10">
<div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-8">
<div>
<p class="text-label-sm text-secondary">Partner location</p>
<h1 class="text-headline-lg font-headline-lg">The Steakhouse Grill</h1>
<p class="text-body-md text-on-surface-variant mt-1">Store #ZY-104 · Open until 11:00 PM</p>
</div>
<div class="flex flex-wrap gap-2">
<a href="menu-manager.php" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary text-on-primary font-label-md">Menu manager</a>
<a href="restaurant-menu.php" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-outline-variant font-label-md hover:bg-surface-container-high">Live menu preview</a>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-gutter mb-10">
<div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant/30">
<p class="text-label-sm text-secondary">Net sales today</p>
<p class="text-headline-md font-bold text-on-surface mt-1">₹48,120</p>
</div>
<div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant/30">
<p class="text-label-sm text-secondary">Orders</p>
<p class="text-headline-md font-bold mt-1">86</p>
</div>
<div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant/30">
<p class="text-label-sm text-secondary">Avg prep time</p>
<p class="text-headline-md font-bold text-tertiary mt-1">14 min</p>
</div>
</div>
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 overflow-hidden">
<div class="px-5 py-4 border-b border-outline-variant/40 flex flex-wrap justify-between gap-2 items-center">
<h2 class="text-headline-md font-headline-md">Live orders</h2>
<a href="board.php" class="text-label-sm font-semibold text-primary">Open kitchen board</a>
</div>
<table class="w-full text-left text-label-sm">
<thead class="bg-surface-container text-on-surface-variant uppercase text-[11px]">
<tr>
<th class="px-4 py-3 font-semibold">Order</th>
<th class="px-4 py-3 font-semibold">Items</th>
<th class="px-4 py-3 font-semibold">Channel</th>
<th class="px-4 py-3 font-semibold">Status</th>
</tr>
</thead>
<tbody class="text-body-md divide-y divide-outline-variant/30">
<tr class="hover:bg-surface-container/40">
<td class="px-4 py-3 font-semibold">#ZY-20418</td>
<td class="px-4 py-3 text-secondary">2</td>
<td class="px-4 py-3">Delivery</td>
<td class="px-4 py-3"><span class="rounded-full bg-primary-fixed/40 px-2 py-0.5 text-label-sm font-semibold text-on-primary-fixed-variant">In kitchen</span></td>
</tr>
<tr class="hover:bg-surface-container/40">
<td class="px-4 py-3 font-semibold">#ZY-20405</td>
<td class="px-4 py-3 text-secondary">5</td>
<td class="px-4 py-3">Pickup</td>
<td class="px-4 py-3"><span class="rounded-full bg-tertiary/15 px-2 py-0.5 text-label-sm font-semibold text-tertiary">Ready</span></td>
</tr>
<tr class="hover:bg-surface-container/40">
<td class="px-4 py-3 font-semibold">#ZY-20398</td>
<td class="px-4 py-3 text-secondary">3</td>
<td class="px-4 py-3">Delivery</td>
<td class="px-4 py-3 text-on-surface-variant">New</td>
</tr>
</tbody>
</table>
</div>
</main>
<footer class="bg-surface-container-highest border-t mt-auto">
<div class="py-6 px-margin-desktop max-w-container-max-width mx-auto text-label-sm text-on-surface-variant flex justify-between">
<a href="index.php" class="hover:text-primary">View storefront</a>
<p>© 2026 ZyropFoodOrder</p>
</div>
</footer>
</body>
</html>
