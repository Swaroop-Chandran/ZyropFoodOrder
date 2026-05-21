<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Checkout | ZyropFoodOrder</title>
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
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="order-track.php">Track</a>
<a class="text-primary font-bold border-b-2 border-primary pb-1 font-label-md text-label-md whitespace-nowrap" href="cart.php">Cart</a>
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
<p class="text-label-sm text-secondary mb-2"><a href="cart.php" class="hover:text-primary">Cart</a> · Checkout</p>
<h1 class="text-headline-lg font-headline-lg mb-8">Checkout</h1>

<?php
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
$total_qty = 0;
foreach ($cart_items as $itemId => $qty) {
    if (isset($catalog[$itemId])) {
        $subtotal += $catalog[$itemId]['price'] * $qty;
        $total_qty += $qty;
    }
}
$fees_and_tax = 58; // 22 service + 36 tax
$grand_total = $subtotal > 0 ? ($subtotal + $fees_and_tax) : 0;
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter items-start">
<div class="lg:col-span-2 space-y-8">
<section class="bg-surface-container-low rounded-xl p-6 md:p-8 border border-outline-variant/30">
<h2 class="text-headline-md font-headline-md mb-4">Delivery address</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<label class="block md:col-span-2">
<span class="text-label-sm font-label-sm text-secondary">Street</span>
<input class="mt-1 w-full rounded-lg border-outline-variant bg-surface-container-lowest px-3 py-2.5 text-body-md" type="text" value="412 Maple Ave, Apt 3B"/>
</label>
<label class="block">
<span class="text-label-sm text-secondary">City</span>
<input class="mt-1 w-full rounded-lg border-outline-variant bg-surface-container-lowest px-3 py-2.5" type="text" value="Columbus"/>
</label>
<label class="block">
<span class="text-label-sm text-secondary">ZIP</span>
<input class="mt-1 w-full rounded-lg border-outline-variant bg-surface-container-lowest px-3 py-2.5" type="text" value="43215"/>
</label>
<label class="block md:col-span-2">
<span class="text-label-sm text-secondary">Instructions for driver</span>
<textarea class="mt-1 w-full rounded-lg border-outline-variant bg-surface-container-lowest px-3 py-2.5 text-body-md" rows="2" placeholder="Gate code, parking, etc."></textarea>
</label>
</div>
</section>
<section class="bg-surface-container-low rounded-xl p-6 md:p-8 border border-outline-variant/30">
<h2 class="text-headline-md font-headline-md mb-4">Payment</h2>
<div class="space-y-3">
<label class="flex items-center gap-3 p-4 rounded-xl border-2 border-primary bg-primary-fixed/20 cursor-pointer">
<input type="radio" name="pay" class="text-primary" checked/>
<span class="material-symbols-outlined">credit_card</span>
<div>
<p class="text-label-md font-semibold">Visa ···· 4242</p>
<p class="text-label-sm text-secondary">Default</p>
</div>
</label>
<label class="flex items-center gap-3 p-4 rounded-xl border border-outline-variant cursor-pointer hover:bg-surface-container-high/50">
<input type="radio" name="pay" class="text-primary"/>
<span class="material-symbols-outlined">payments</span>
<p class="text-label-md">Pay on delivery</p>
</label>
</div>
</section>
<section class="bg-surface-container-low rounded-xl p-6 md:p-8 border border-outline-variant/30">
<h2 class="text-headline-md font-headline-md mb-4">Promo code</h2>
<div class="flex gap-2">
<input class="flex-1 rounded-full border-outline-variant bg-surface-container-lowest px-4 py-2.5" placeholder="Enter code" type="text"/>
<button type="button" class="px-6 py-2.5 rounded-full border border-outline-variant font-label-md hover:bg-surface-container-high">Apply</button>
</div>
</section>
</div>
<aside class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/30 lg:sticky lg:top-28">
<h3 class="text-headline-md font-headline-md mb-4">Order</h3>
<p class="text-label-sm text-secondary mb-4">Summary · <?php echo $total_qty; ?> items</p>
<ul class="space-y-2 text-body-md text-secondary mb-4 max-h-48 overflow-y-auto">
<?php 
if (empty($cart_items)): 
?>
<li class="text-body-md text-secondary italic">Your cart is empty</li>
<?php 
else:
    foreach ($cart_items as $itemId => $qty): 
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
endif;
?>
</ul>
<div class="space-y-2 text-body-md border-t border-outline-variant pt-4">
<div class="flex justify-between text-secondary"><span>Subtotal</span><span class="text-on-surface">₹<?php echo $subtotal; ?></span></div>
<div class="flex justify-between text-secondary"><span>Fees &amp; tax</span><span class="text-on-surface">₹<?php echo $subtotal > 0 ? $fees_and_tax : 0; ?></span></div>
<div class="flex justify-between text-label-md font-bold pt-2"><span>Total</span><span class="text-primary">₹<?php echo $grand_total; ?></span></div>
</div>
<?php if ($subtotal > 0): ?>
<a href="?action=place_order" class="mt-6 block w-full text-center bg-primary text-on-primary py-3.5 rounded-full font-label-md hover:opacity-95 transition-opacity">Place order</a>
<?php else: ?>
<button disabled class="mt-6 w-full text-center bg-surface-container border border-outline-variant/50 text-on-surface-variant/40 py-3.5 rounded-full font-label-md cursor-not-allowed">Cart is empty</button>
<?php endif; ?>
<p class="text-label-sm text-on-surface-variant text-center mt-4">By placing your order you agree to our terms and privacy policy.</p>
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
