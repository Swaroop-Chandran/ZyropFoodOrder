<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Operations board | ZyropFoodOrder</title>
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
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="partner-dashboard.php">Partner</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="admin-panel.php">Admin</a>
</nav>
<span class="text-label-sm text-secondary dark:text-secondary-fixed-dim hidden sm:block">Operations</span>
</div>
</header>

<main class="flex-1 max-w-container-max-width mx-auto w-full px-margin-desktop py-10">
<div class="mb-8">
<h1 class="text-headline-lg font-headline-lg">Operations board</h1>
<p class="text-body-md text-on-surface-variant mt-1">Restaurant back-of-house and expo · synced with delivery queue</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 overflow-x-auto pb-2">
<div class="min-w-[220px] flex flex-col gap-3">
<h2 class="text-label-md font-bold text-on-surface-variant uppercase tracking-wide px-1">New</h2>
<div class="bg-surface-container-low rounded-xl p-4 border border-outline-variant/30 shadow-sm">
<p class="text-label-md font-bold">#ZY-20398</p>
<p class="text-label-sm text-secondary mt-1">Delivery · 3 items</p>
<p class="text-label-sm text-on-surface-variant mt-2">Due 6:55 PM</p>
</div>
</div>
<div class="min-w-[220px] flex flex-col gap-3">
<h2 class="text-label-md font-bold text-on-surface-variant uppercase tracking-wide px-1">In kitchen</h2>
<div class="bg-surface-container-low rounded-xl p-4 border border-outline-variant/30 shadow-sm">
<p class="text-label-md font-bold">#ZY-20418</p>
<p class="text-label-sm text-secondary mt-1">Delivery · 2 items</p>
<p class="text-label-sm text-primary font-semibold mt-2">Ribeye on grill</p>
</div>
<div class="bg-surface-container-low rounded-xl p-4 border border-outline-variant/30 shadow-sm">
<p class="text-label-md font-bold">#ZY-20371</p>
<p class="text-label-sm text-secondary mt-1">Pickup · 1 item</p>
</div>
</div>
<div class="min-w-[220px] flex flex-col gap-3">
<h2 class="text-label-md font-bold text-on-surface-variant uppercase tracking-wide px-1">Ready</h2>
<div class="bg-tertiary/10 rounded-xl p-4 border border-tertiary/30 shadow-sm">
<p class="text-label-md font-bold">#ZY-20405</p>
<p class="text-label-sm text-secondary mt-1">Pickup · 5 items</p>
<p class="text-label-sm text-tertiary font-semibold mt-2">Bag on rack B</p>
</div>
</div>
<div class="min-w-[220px] flex flex-col gap-3">
<h2 class="text-label-md font-bold text-on-surface-variant uppercase tracking-wide px-1">Out</h2>
<div class="bg-surface-container-low rounded-xl p-4 border border-outline-variant/30 shadow-sm">
<p class="text-label-md font-bold">#ZY-20388</p>
<p class="text-label-sm text-secondary mt-1">Jamie L. · South</p>
</div>
</div>
<div class="min-w-[220px] flex flex-col gap-3">
<h2 class="text-label-md font-bold text-on-surface-variant uppercase tracking-wide px-1">Done</h2>
<div class="bg-surface-container rounded-xl p-4 border border-outline-variant/20 opacity-80">
<p class="text-label-md font-bold text-secondary">#ZY-20340</p>
<p class="text-label-sm text-on-surface-variant mt-1">Delivered 5:58 PM</p>
</div>
</div>
</div>
</main>
<footer class="bg-surface-container-highest border-t mt-auto">
<div class="py-6 px-margin-desktop max-w-container-max-width mx-auto text-label-sm text-on-surface-variant flex justify-between">
<a href="delivery-queue.php" class="hover:text-primary">Open delivery queue</a>
<p>© 2026 ZyropFoodOrder</p>
</div>
</footer>
</body>
</html>
