<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Your cart | ZyropFoodOrder</title>
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
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-primary-fixed-dim transition-colors duration-200 font-label-md text-label-md whitespace-nowrap" href="order-track.php">Track Order</a>
<a class="text-primary font-bold border-b-2 border-primary pb-1 font-label-md text-label-md whitespace-nowrap" href="cart.php">Cart</a>
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

<main class="flex-1 max-w-container-max-width mx-auto w-full px-margin-desktop py-10">
<h1 class="text-headline-lg font-headline-lg mb-2">Your cart</h1>
<p class="text-body-md text-on-surface-variant mb-8">Review items in your bag</p>

<?php
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;
?>

<?php if (empty($cart_items)): ?>
    <div class="flex flex-col items-center justify-center py-16 px-4 text-center bg-surface-container-low rounded-3xl border border-outline-variant/30 max-w-lg mx-auto shadow-sm">
        <span class="material-symbols-outlined text-[80px] text-outline mb-6" style="font-variation-settings: 'FILL' 0, 'wght' 200;">shopping_cart_off</span>
        <h2 class="text-headline-md font-bold mb-3 text-on-surface">Your cart is empty</h2>
        <p class="text-body-lg text-secondary mb-8 max-w-sm">Looks like you haven't added anything to your cart yet. Discover delicious foods near you!</p>
        <a href="index.php" class="bg-primary text-on-primary px-8 py-4 rounded-full font-label-md hover:opacity-95 transition-opacity inline-block">Browse restaurants</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gutter items-start">
        <div class="lg:col-span-2 space-y-4">
            <?php 
            foreach ($cart_items as $itemId => $qty): 
                if (isset($catalog[$itemId])):
                    $item = $catalog[$itemId];
                    $itemTotal = $item['price'] * $qty;
                    $subtotal += $itemTotal;
            ?>
            <div class="bg-surface-container-low rounded-xl p-4 md:p-6 flex gap-4 border border-outline-variant/30">
                <?php if (!empty($item['img'])): ?>
                    <img class="w-24 h-24 rounded-lg object-cover shrink-0" alt="<?php echo htmlspecialchars($item['name']); ?>" src="<?php echo htmlspecialchars($item['img']); ?>"/>
                <?php else: ?>
                    <div class="w-24 h-24 rounded-lg bg-surface-container-high shrink-0 flex items-center justify-center text-outline">
                        <span class="material-symbols-outlined text-4xl">restaurant</span>
                    </div>
                <?php endif; ?>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between gap-2">
                        <h2 class="text-label-md font-label-md"><?php echo htmlspecialchars($item['name']); ?></h2>
                        <p class="text-label-md font-bold text-primary shrink-0">₹<?php echo $item['price']; ?></p>
                    </div>
                    <p class="text-label-sm text-secondary mt-1"><?php echo htmlspecialchars($item['restaurant']); ?> • <?php echo htmlspecialchars($item['description']); ?></p>
                    <div class="flex items-center gap-3 mt-4">
                        <div class="inline-flex items-center rounded-full border border-outline-variant">
                            <a href="?action=update&id=<?php echo $itemId; ?>&qty=<?php echo ($qty - 1); ?>" class="px-3 py-1.5 text-on-surface-variant hover:bg-surface-container-high rounded-l-full select-none" aria-label="Decrease">−</a>
                            <span class="px-3 text-label-md font-semibold"><?php echo $qty; ?></span>
                            <a href="?action=add&id=<?php echo $itemId; ?>" class="px-3 py-1.5 text-on-surface-variant hover:bg-surface-container-high rounded-r-full select-none" aria-label="Increase">+</a>
                        </div>
                        <a href="?action=remove&id=<?php echo $itemId; ?>" class="text-label-sm text-error hover:underline ml-auto">Remove</a>
                    </div>
                </div>
            </div>
            <?php 
                endif; 
            endforeach; 
            ?>
            <a href="restaurant-menu.php" class="inline-flex items-center gap-2 text-primary font-label-md hover:underline">
                <span class="material-symbols-outlined text-[20px]">add</span> Add more items
            </a>
        </div>
        
        <aside class="bg-surface-container-low rounded-xl p-6 border border-outline-variant/30 lg:sticky lg:top-28">
            <h3 class="text-headline-md font-headline-md mb-4">Summary</h3>
            <div class="space-y-3 text-body-md">
                <div class="flex justify-between text-secondary"><span>Subtotal</span><span class="text-on-surface">₹<?php echo $subtotal; ?></span></div>
                <div class="flex justify-between text-secondary"><span>Delivery</span><span class="text-tertiary font-semibold">₹0</span></div>
                <div class="flex justify-between text-secondary"><span>Service fee</span><span class="text-on-surface">₹22</span></div>
                <div class="flex justify-between text-secondary"><span>Tax</span><span class="text-on-surface">₹36</span></div>
                <div class="border-t border-outline-variant pt-4 mt-4 flex justify-between text-label-md font-bold">
                    <span>Total</span>
                    <span class="text-primary">₹<?php echo ($subtotal + 22 + 36); ?></span>
                </div>
            </div>
            <a href="checkout.php" class="mt-6 block w-full text-center bg-primary text-on-primary py-3.5 rounded-full font-label-md hover:opacity-95 transition-opacity">Proceed to checkout</a>
            <p class="text-label-sm text-on-surface-variant text-center mt-4">Promo codes can be applied at checkout.</p>
        </aside>
    </div>
<?php endif; ?>
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
