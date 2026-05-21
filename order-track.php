<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Order tracking | ZyropFoodOrder</title>
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
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="index.php">Browse</a>
<a class="text-primary font-bold border-b-2 border-primary pb-1 font-label-md text-label-md whitespace-nowrap" href="order-track.php">Track Order</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="cart.php">Cart</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="checkout.php">Checkout</a>
</nav>
<div class="flex items-center gap-6">
<div class="flex items-center gap-4">
<?php echo renderNotifications(); ?>
<?php echo renderAccountMenu(); ?>
</div>
<div class="h-8 w-[1px] bg-outline-variant"></div>
<a class="bg-primary text-on-primary px-6 py-2 rounded-full font-label-md text-label-md active:scale-95 transition-transform duration-150 inline-block text-center" href="cart.php">Cart (<?php echo getCartTotalCount(); ?>)</a>
</div>
</div>
</header>

<?php
$order_items = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : [1 => 1, 3 => 1];
$order_id = isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : 'ZY-20418';
$subtotal = 0;
$restaurant = 'The Steakhouse Grill';

foreach ($order_items as $itemId => $qty) {
    if (isset($catalog[$itemId])) {
        $subtotal += $catalog[$itemId]['price'] * $qty;
        $restaurant = $catalog[$itemId]['restaurant'];
    }
}
?>

<main class="flex-1 max-w-container-max-width mx-auto w-full px-margin-desktop py-10 flex flex-col gap-10">
<section class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
<div>
<p class="text-label-sm font-label-sm text-secondary mb-1">Live order</p>
<h1 class="text-headline-lg font-headline-lg text-on-surface">Order <span class="text-primary">#<?php echo htmlspecialchars($order_id); ?></span></h1>
<p class="text-body-md font-body-md text-on-surface-variant mt-2"><?php echo htmlspecialchars($restaurant); ?> · Est. arrival <strong class="text-on-surface">6:42 PM</strong></p>
</div>
<button type="button" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border border-outline-variant text-label-md font-label-md hover:bg-surface-container-high transition-colors w-fit">
<span class="material-symbols-outlined text-[20px]">support_agent</span> Help with this order
</button>
</section>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter">
<div class="lg:col-span-2 space-y-6">
<div class="bg-surface-container-low rounded-xl p-6 md:p-8 border border-outline-variant/30">
<h2 class="text-headline-md font-headline-md mb-6">Status</h2>
<ol class="relative border-s-2 border-outline-variant ms-3 space-y-8 ps-8">
<li class="relative">
<span class="absolute -start-[25px] top-1 flex h-4 w-4 rounded-full bg-tertiary ring-4 ring-surface"></span>
<p class="text-label-md font-label-md text-on-surface">Out for delivery</p>
<p class="text-body-md font-body-md text-secondary">Alex M. is 4 min away · Honda Civic · plate ZYR 902</p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-1">Updated 2 min ago</p>
</li>
<li class="relative">
<span class="absolute -start-[25px] top-1 flex h-4 w-4 rounded-full bg-surface-container-high ring-4 ring-surface"></span>
<p class="text-label-md font-label-md text-on-surface">Ready for pickup</p>
<p class="text-body-md font-body-md text-secondary">Restaurant handed off to courier</p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-1">6:12 PM</p>
</li>
<li class="relative">
<span class="absolute -start-[25px] top-1 flex h-4 w-4 rounded-full bg-surface-container-high ring-4 ring-surface"></span>
<p class="text-label-md font-label-md text-on-surface">Preparing</p>
<p class="text-body-md font-body-md text-secondary">Kitchen confirmed your items</p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-1">5:48 PM</p>
</li>
<li class="relative">
<span class="absolute -start-[25px] top-1 flex h-4 w-4 rounded-full bg-surface-container-high ring-4 ring-surface"></span>
<p class="text-label-md font-label-md text-on-surface">Order placed</p>
<p class="text-body-md font-body-md text-secondary">Payment authorized</p>
<p class="text-label-sm font-label-sm text-on-surface-variant mt-1">5:35 PM</p>
</li>
</ol>
</div>
<div class="bg-surface-container-low rounded-xl overflow-hidden border border-outline-variant/30 h-64 md:h-80 flex items-center justify-center">
<span class="material-symbols-outlined text-6xl text-outline-variant">map</span>
<p class="text-label-sm font-label-sm text-on-surface-variant ml-2">Map preview · live driver route</p>
</div>
</div>
<aside class="space-y-6">
<div class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/30">
<h3 class="text-label-md font-label-md mb-4">Order summary</h3>
<ul class="space-y-3 text-body-md font-body-md text-secondary max-h-60 overflow-y-auto">
<?php 
foreach ($order_items as $itemId => $qty): 
    if (isset($catalog[$itemId])):
        $item = $catalog[$itemId];
?>
<li class="flex justify-between">
    <span><?php echo htmlspecialchars($item['name']); ?> <?php echo $qty > 1 ? "x{$qty}" : ""; ?></span>
    <span class="text-on-surface">₹<?php echo $item['price'] * $qty; ?></span>
</li>
<?php 
    endif;
endforeach; 
?>
<li class="flex justify-between"><span>Delivery</span><span class="text-tertiary font-semibold">Free</span></li>
<li class="flex justify-between border-t border-outline-variant pt-3 mt-3 text-label-md font-label-md text-on-surface"><span>Total</span><span>₹<?php echo $subtotal; ?></span></li>
</ul>
</div>
</aside>
</div>
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
