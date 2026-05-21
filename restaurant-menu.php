<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>The Steakhouse Grill · Menu | ZyropFoodOrder</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="css/zyrop.css" rel="stylesheet"/>
<script src="js/tailwind-theme.js"></script>
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col">
<header class="bg-surface dark:bg-on-background shadow-sm docked full-width top-0 sticky z-50">
<div class="flex justify-between items-center w-full px-margin-desktop h-20 max-w-container-max-width mx-auto">
<div class="flex items-center gap-gutter">
<a href="index.php" class="text-headline-md font-headline-md font-extrabold text-primary dark:text-primary-fixed">ZyropFoodOrder</a>
<div class="hidden md:flex items-center bg-surface-container rounded-full px-4 py-2 gap-2 w-80">
<span class="material-symbols-outlined text-secondary" data-icon="search">search</span>
<input class="bg-transparent border-none focus:ring-0 text-body-md font-body-md w-full" placeholder="Search for food, restaurants..." type="text"/>
</div>
</div>
<nav class="hidden lg:flex items-center gap-6 flex-wrap">
<a class="text-primary font-bold border-b-2 border-primary pb-1 font-label-md text-label-md whitespace-nowrap" href="index.php">Browse</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="order-track.php">Track</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="cart.php">Cart</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="partner-dashboard.php">Partner</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="admin-panel.php">Admin</a>
</nav>
<div class="flex items-center gap-6">
<div class="flex items-center gap-4">
<?php echo renderNotifications(); ?>
<button class="material-symbols-outlined text-on-surface-variant" data-icon="account_circle">account_circle</button>
</div>
<div class="h-8 w-[1px] bg-outline-variant"></div>
<a class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-md text-label-md active:scale-95 transition-transform duration-150 inline-block text-center" href="cart.php">Cart (<?php echo getCartTotalCount(); ?>)</a>
</div>
</div>
</header>

<main class="flex-1 max-w-container-max-width mx-auto w-full px-margin-desktop py-10">
<section class="relative rounded-2xl overflow-hidden mb-10 h-56 md:h-72 shadow-lg">
<img class="w-full h-full object-cover" alt="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuACPe1OwcnqiSYz6mGkYPwpTwUZkoQT8Jeq336MHTLd5-szfhdGafbxKuJ3QVMBjxqcxm4UwTDipbBKsEECFSl_VHIJI58oJjjfYhQRcILi8-eedqeW9Mmlq_MJCKbX6yX6excKavJXTN1YruIGDT445j8SmCA9w4wNuJUqWrKgCGPpn5cc-E6Ph19OOcwM0Lu_vntB6rnd88Rr2jXfoBPCYqOX-gehGl-S_UIFfvPKeRPs0iP4Kc_0ZbV9KJ8H6mFYWZPD6gO7v2U"/>
<div class="absolute inset-0 bg-gradient-to-t from-on-background/90 via-on-background/40 to-transparent flex flex-col justify-end p-6 md:p-10 text-white">
<p class="text-label-sm opacity-90">Premium Steaks · American · ₹₹₹</p>
<h1 class="text-headline-lg font-headline-lg md:text-display-lg md:font-display-lg mt-1">The Steakhouse Grill</h1>
<div class="flex flex-wrap gap-3 mt-4 items-center">
<span class="inline-flex items-center gap-1 bg-white/15 backdrop-blur px-3 py-1 rounded-full text-label-sm"><span class="material-symbols-outlined text-yellow-400 text-[18px]" style="font-variation-settings:'FILL' 1">star</span> 4.8 (2.1k)</span>
<span class="text-label-sm">25–35 min</span>
<span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-label-sm font-semibold">Free delivery</span>
</div>
</div>
</section>
<section class="mb-10">
<h2 class="text-headline-md font-headline-md mb-4">Mains</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<article class="bg-surface-container-low rounded-xl overflow-hidden flex border border-outline-variant/30 hover:shadow-md transition-shadow">
<img class="w-32 h-32 object-cover shrink-0" alt="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAD0XAp8nptdYGRpXaXlDxP0LF6wgMjgl_XUd1PUTqNVFD5wCpaRAcP9RrtJ_RzxEFCdvyZG1t6h2l3zumlkNEQL70h0ffZe89GQnqwU40u0rXJplohhbgN4tQf7HYJEiAUCGpF_uozV0_2frD6zgKOuFHbSut4pKI7StvE428nCqKdqLhoGFGVSxTi5KFVIW1fPpkf7zcimRHsy8bYejS30SJgcPPvUC2NUwqC9RtGAw_PjWabuKcjIMslXFf0-6H8YrAuP_o-izc"/>
<div class="p-4 flex flex-col flex-1 min-w-0">
<h3 class="text-label-md font-label-md">Ribeye 12oz</h3>
<p class="text-label-sm text-secondary mt-1 line-clamp-2">Charred crust, maître d’ butter, seasonal vegetables.</p>
<div class="flex justify-between items-center mt-auto pt-3">
<span class="text-primary font-bold text-label-md">₹340</span>
<?php echo renderCartControls(1); ?>
</div>
</div>
</article>
<article class="bg-surface-container-low rounded-xl overflow-hidden flex border border-outline-variant/30 hover:shadow-md transition-shadow">
<img class="w-32 h-32 object-cover shrink-0" alt="" src="https://lh3.googleusercontent.com/aida-public/AB6AXuACPe1OwcnqiSYz6mGkYPwpTwUZkoQT8Jeq336MHTLd5-szfhdGafbxKuJ3QVMBjxqcxm4UwTDipbBKsEECFSl_VHIJI58oJjjfYhQRcILi8-eedqeW9Mmlq_MJCKbX6yX6excKavJXTN1YruIGDT445j8SmCA9w4wNuJUqWrKgCGPpn5cc-E6Ph19OOcwM0Lu_vntB6rnd88Rr2jXfoBPCYqOX-gehGl-S_UIFfvPKeRPs0iP4Kc_0ZbV9KJ8H6mFYWZPD6gO7v2U"/>
<div class="p-4 flex flex-col flex-1 min-w-0">
<h3 class="text-label-md font-label-md">Filet mignon 8oz</h3>
<p class="text-label-sm text-secondary mt-1 line-clamp-2">Center cut, red wine reduction, mashed potato.</p>
<div class="flex justify-between items-center mt-auto pt-3">
<span class="text-primary font-bold text-label-md">₹420</span>
<?php echo renderCartControls(2); ?>
</div>
</div>
</article>
</div>
</section>
<section>
<h2 class="text-headline-md font-headline-md mb-4">Sides</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
<article class="bg-surface-container-low rounded-xl p-4 flex justify-between items-center border border-outline-variant/30">
<div>
<h3 class="text-label-md font-label-md">Truffle fries</h3>
<p class="text-label-sm text-secondary">Share size</p>
</div>
<div class="text-right flex flex-col items-end gap-2">
<span class="text-primary font-bold">₹95</span>
<?php echo renderCartControls(3, "text-primary border border-primary px-3 py-1 rounded-full text-label-sm font-semibold hover:bg-primary hover:text-on-primary transition-colors"); ?>
</div>
</article>
<article class="bg-surface-container-low rounded-xl p-4 flex justify-between items-center border border-outline-variant/30">
<div>
<h3 class="text-label-md font-label-md">Grilled asparagus</h3>
<p class="text-label-sm text-secondary">Lemon zest, parmesan</p>
</div>
<div class="text-right flex flex-col items-end gap-2">
<span class="text-primary font-bold">₹80</span>
<?php echo renderCartControls(4, "text-primary border border-primary px-3 py-1 rounded-full text-label-sm font-semibold hover:bg-primary hover:text-on-primary transition-colors"); ?>
</div>
</article>
</div>
</section>
<p class="text-label-sm text-on-surface-variant mt-10">Restaurant operators edit this view in the <a href="menu-manager.php" class="text-primary font-semibold hover:underline">menu manager</a>.</p>
</main>
<!-- Footer -->
<footer class="bg-surface-container-highest dark:bg-inverse-surface border-t border-outline-variant mt-20">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-gutter px-margin-desktop py-10 max-w-container-max-width mx-auto">
<div class="flex flex-col gap-4">
<span class="text-headline-sm font-headline-sm font-bold text-primary">ZyropFoodOrder</span>
<p class="text-body-md font-body-md text-on-surface-variant dark:text-surface-variant max-w-xs">Delivering your favorite meals from top-rated restaurants right to your doorstep, fresh and fast.</p>
<div class="flex gap-4 mt-2">
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors">public</button>
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors">campaign</button>
<button class="material-symbols-outlined text-secondary hover:text-primary transition-colors">share</button>
</div>
</div>
<div class="flex flex-col gap-4">
<h5 class="text-label-md font-bold text-on-surface">Company</h5>
<nav class="flex flex-col gap-2">
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">About Us</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="partner-dashboard.php">Partner With Us</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Careers</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Press</a>
</nav>
</div>
<div class="flex flex-col gap-4">
<h5 class="text-label-md font-bold text-on-surface">Platform</h5>
<nav class="flex flex-col gap-2">
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="order-track.php">Order tracking</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="cart.php">Cart</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="checkout.php">Checkout</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="delivery-queue.php">Delivery queue</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="board.php">Board</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="restaurant-menu.php">Restaurant menu</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="menu-manager.php">Menu manager</a>
</nav>
</div>
<div class="flex flex-col gap-4">
<h5 class="text-label-md font-bold text-on-surface">Legal &amp; Support</h5>
<nav class="flex flex-col gap-2">
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Privacy Policy</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Terms of Service</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Help Center</a>
<a class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant hover:text-primary dark:hover:text-primary-fixed transition-colors" href="#">Contact</a>
</nav>
</div>
<div class="flex flex-col gap-4">
<h5 class="text-label-md font-bold text-on-surface">Get App</h5>
<p class="text-label-sm font-label-sm text-on-surface-variant dark:text-surface-variant">Available on iOS and Android</p>
<div class="flex flex-col gap-3">
<div class="bg-inverse-surface dark:bg-surface-container rounded-lg px-4 py-2 flex items-center gap-3 cursor-pointer">
<span class="material-symbols-outlined text-surface dark:text-on-surface text-[24px]">phone_iphone</span>
<div>
<p class="text-[10px] text-surface-variant dark:text-secondary uppercase leading-none">Download on</p>
<p class="text-label-md font-bold text-surface dark:text-on-surface leading-tight">App Store</p>
</div>
</div>
<div class="bg-inverse-surface dark:bg-surface-container rounded-lg px-4 py-2 flex items-center gap-3 cursor-pointer">
<span class="material-symbols-outlined text-surface dark:text-on-surface text-[24px]">shop</span>
<div>
<p class="text-[10px] text-surface-variant dark:text-secondary uppercase leading-none">Get it on</p>
<p class="text-label-md font-bold text-surface dark:text-on-surface leading-tight">Google Play</p>
</div>
</div>
</div>
</div>
</div>
<div class="border-t border-outline-variant/30 py-6 px-margin-desktop max-w-container-max-width mx-auto">
<p class="text-label-sm font-label-sm text-on-surface-variant/70 dark:text-surface-variant/50">© 2026 ZyropFoodOrder. All rights reserved.</p>
</div>
</footer>
</body>
</html>
