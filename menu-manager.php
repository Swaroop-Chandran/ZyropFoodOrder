<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Menu manager | ZyropFoodOrder</title>
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
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
<div>
<h1 class="text-headline-lg font-headline-lg">Menu manager</h1>
<p class="text-body-md text-on-surface-variant mt-1">The Steakhouse Grill · Categories, pricing, and availability</p>
</div>
<button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary text-on-primary font-label-md w-fit">
<span class="material-symbols-outlined text-[20px]">add</span> New item
</button>
</div>
<div class="flex gap-2 overflow-x-auto hide-scrollbar pb-4 mb-6">
<button type="button" class="shrink-0 px-5 py-2 rounded-full bg-primary text-on-primary font-label-md">Mains</button>
<button type="button" class="shrink-0 px-5 py-2 rounded-full border border-outline-variant font-label-md hover:bg-surface-container-high">Sides</button>
<button type="button" class="shrink-0 px-5 py-2 rounded-full border border-outline-variant font-label-md hover:bg-surface-container-high">Desserts</button>
<button type="button" class="shrink-0 px-5 py-2 rounded-full border border-outline-variant font-label-md hover:bg-surface-container-high">Beverages</button>
</div>
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant/40 overflow-hidden">
<table class="w-full text-left text-label-sm">
<thead class="bg-surface-container text-on-surface-variant uppercase text-[11px]">
<tr>
<th class="px-4 py-3 font-semibold">Item</th>
<th class="px-4 py-3 font-semibold">Price</th>
<th class="px-4 py-3 font-semibold">Prep</th>
<th class="px-4 py-3 font-semibold">Visible</th>
<th class="px-4 py-3 font-semibold text-right">Actions</th>
</tr>
</thead>
<tbody class="text-body-md divide-y divide-outline-variant/30">
<tr class="hover:bg-surface-container/40">
<td class="px-4 py-3">
<p class="font-semibold">Ribeye 12oz</p>
<p class="text-label-sm text-secondary">Signature cut · GF optional</p>
</td>
<td class="px-4 py-3 font-label-md">₹340</td>
<td class="px-4 py-3 text-secondary">18 min</td>
<td class="px-4 py-3"><span class="inline-flex h-6 w-11 items-center rounded-full bg-tertiary/30 px-0.5"><span class="h-5 w-5 translate-x-5 rounded-full bg-tertiary shadow"></span></span></td>
<td class="px-4 py-3 text-right space-x-2"><button type="button" class="text-primary font-label-md">Edit</button></td>
</tr>
<tr class="hover:bg-surface-container/40">
<td class="px-4 py-3">
<p class="font-semibold">Truffle fries</p>
<p class="text-label-sm text-secondary">Share size</p>
</td>
<td class="px-4 py-3 font-label-md">₹95</td>
<td class="px-4 py-3 text-secondary">8 min</td>
<td class="px-4 py-3"><span class="inline-flex h-6 w-11 items-center rounded-full bg-tertiary/30 px-0.5"><span class="h-5 w-5 translate-x-5 rounded-full bg-tertiary shadow"></span></span></td>
<td class="px-4 py-3 text-right space-x-2"><button type="button" class="text-primary font-label-md">Edit</button></td>
</tr>
<tr class="hover:bg-surface-container/40">
<td class="px-4 py-3">
<p class="font-semibold">Caesar salad</p>
<p class="text-label-sm text-secondary">Add chicken +₹60</p>
</td>
<td class="px-4 py-3 font-label-md">₹120</td>
<td class="px-4 py-3 text-secondary">10 min</td>
<td class="px-4 py-3"><span class="inline-flex h-6 w-11 items-center rounded-full bg-outline-variant/60 px-0.5"><span class="h-5 w-5 translate-x-0.5 rounded-full bg-surface-container-lowest shadow"></span></span></td>
<td class="px-4 py-3 text-right space-x-2"><button type="button" class="text-primary font-label-md">Edit</button></td>
</tr>
</tbody>
</table>
</div>
<p class="text-label-sm text-on-surface-variant mt-4">Changes publish to your <a href="restaurant-menu.php" class="text-primary font-semibold hover:underline">public restaurant menu</a> immediately after review.</p>
</main>
<footer class="bg-surface-container-highest border-t mt-auto">
<div class="py-6 px-margin-desktop max-w-container-max-width mx-auto text-label-sm text-on-surface-variant flex justify-between">
<a href="partner-dashboard.php" class="hover:text-primary">Back to partner dashboard</a>
<p>© 2026 ZyropFoodOrder</p>
</div>
</footer>
</body>
</html>
