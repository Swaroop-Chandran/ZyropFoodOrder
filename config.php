<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Authentication Guards and Routing
$current_page = basename($_SERVER['PHP_SELF']);

$admin_pages = [
    'admin-panel.php',
    'delivery-queue.php',
    'menu-manager.php',
    'board.php',
    'partner-dashboard.php'
];

$customer_pages = [
    'index.php',
    'restaurant-menu.php',
    'cart.php',
    'checkout.php',
    'order-track.php'
];

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        $_SESSION['admin_logged_in'] = false;
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_email']);
        header('Location: login.php?tab=admin');
        exit;
    } else {
        $_SESSION['user_logged_in'] = false;
        unset($_SESSION['user_logged_in']);
        unset($_SESSION['user_email']);
        header('Location: login.php?tab=user');
        exit;
    }
}

// Guards redirection
if (in_array($current_page, $admin_pages)) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php?tab=admin');
        exit;
    }
} elseif (in_array($current_page, $customer_pages)) {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        header('Location: login.php?tab=user');
        exit;
    }
}

// Global Food Catalog
$catalog = [
    1 => [
        'id' => 1,
        'name' => 'Ribeye 12oz',
        'price' => 340,
        'restaurant' => 'The Steakhouse Grill',
        'description' => 'Charred crust, maître d’ butter, seasonal vegetables.',
        'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAD0XAp8nptdYGRpXaXlDxP0LF6wgMjgl_XUd1PUTqNVFD5wCpaRAcP9RrtJ_RzxEFCdvyZG1t6h2l3zumlkNEQL70h0ffZe89GQnqwU40u0rXJplohhbgN4tQf7HYJEiAUCGpF_uozV0_2frD6zgKOuFHbSut4pKI7StvE428nCqKdqLhoGFGVSxTi5KFVIW1fPpkf7zcimRHsy8bYejS30SJgcPPvUC2NUwqC9RtGAw_PjWabuKcjIMslXFf0-6H8YrAuP_o-izc',
    ],
    2 => [
        'id' => 2,
        'name' => 'Filet mignon 8oz',
        'price' => 420,
        'restaurant' => 'The Steakhouse Grill',
        'description' => 'Center cut, red wine reduction, mashed potato.',
        'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuACPe1OwcnqiSYz6mGkYPwpTwUZkoQT8Jeq336MHTLd5-szfhdGafbxKuJ3QVMBjxqcxm4UwTDipbBKsEECFSl_VHIJI58oJjjfYhQRcILi8-eedqeW9Mmlq_MJCKbX6yX6excKavJXTN1YruIGDT445j8SmCA9w4wNuJUqWrKgCGPpn5cc-E6Ph19OOcwM0Lu_vntB6rnd88Rr2jXfoBPCYqOX-gehGl-S_UIFfvPKeRPs0iP4Kc_0ZbV9KJ8H6mFYWZPD6gO7v2U',
    ],
    3 => [
        'id' => 3,
        'name' => 'Truffle fries',
        'price' => 95,
        'restaurant' => 'The Steakhouse Grill',
        'description' => 'Share size',
        'img' => '',
    ],
    4 => [
        'id' => 4,
        'name' => 'Grilled asparagus',
        'price' => 80,
        'restaurant' => 'The Steakhouse Grill',
        'description' => 'Lemon zest, parmesan',
        'img' => '',
    ],
    5 => [
        'id' => 5,
        'name' => 'Caesar salad',
        'price' => 120,
        'restaurant' => 'The Steakhouse Grill',
        'description' => 'Add chicken +₹60',
        'img' => '',
    ],
    6 => [
        'id' => 6,
        'name' => 'Large Pepperoni Feast',
        'price' => 249,
        'restaurant' => "Mama's Pizzeria",
        'description' => 'Classic pepperoni pizza',
        'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAD0XAp8nptdYGRpXaXlDxP0LF6wgMjgl_XUd1PUTqNVFD5wCpaRAcP9RrtJ_RzxEFCdvyZG1t6h2l3zumlkNEQL70h0ffZe89GQnqwU40u0rXJplohhbgN4tQf7HYJEiAUCGpF_uozV0_2frD6zgKOuFHbSut4pKI7StvE428nCqKdqLhoGFGVSxTi5KFVIW1fPpkf7zcimRHsy8bYejS30SJgcPPvUC2NUwqC9RtGAw_PjWabuKcjIMslXFf0-6H8YrAuP_o-izc',
    ],
    7 => [
        'id' => 7,
        'name' => 'Salmon Poke Bowl',
        'price' => 185,
        'restaurant' => 'Ocean Harvest',
        'description' => 'Fresh salmon poke bowl',
        'img' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBd2ImYUdZvqqlxfsUHmelSsFKysZh2ukQdon-w3SAILlVOSrmQUemzY1si74PSMkdDEtfu1xtacmTCI7yKynr6_GyLj9HpaP9ka8fed2qXO8wDk4JrJvMgSuHCD-RUDCw2HTNr2Kh_RBDf2Rmex--MYNvQzldw-TN3riylJ__MhQmw70ZGWnD33YMEcKl7rplRHUWBfpgq6LkQQ5PyAU2tAv8lFVBc8J0g9KgGHmV1yD-x3QSOcQKCif50E-UeUw9n88D8qf_jk3E',
    ]
];

// Process Cart Actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($action === 'add' && isset($catalog[$itemId])) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (!isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId] = 0;
        }
        $_SESSION['cart'][$itemId]++;
        
        // Return to referrer or same page without action params to avoid resubmission
        header('Location: ' . strtok($_SERVER['HTTP_REFERER'] ?? 'index.php', '?'));
        exit;
    }
    
    if ($action === 'update' && isset($catalog[$itemId])) {
        $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 0;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$itemId]);
        } else {
            $_SESSION['cart'][$itemId] = $qty;
        }
        header('Location: ' . strtok($_SERVER['HTTP_REFERER'] ?? 'cart.php', '?'));
        exit;
    }
    
    if ($action === 'remove' && isset($catalog[$itemId])) {
        unset($_SESSION['cart'][$itemId]);
        header('Location: ' . strtok($_SERVER['HTTP_REFERER'] ?? 'cart.php', '?'));
        exit;
    }
    
    if ($action === 'place_order') {
        if (!empty($_SESSION['cart'])) {
            $_SESSION['last_order'] = $_SESSION['cart'];
            $_SESSION['last_order_id'] = 'ZY-' . rand(10000, 99999);
            $_SESSION['cart'] = [];
        }
        header('Location: order-track.php');
        exit;
    }
}

// Get Cart item quantity
function getCartQty($itemId) {
    return isset($_SESSION['cart'][$itemId]) ? $_SESSION['cart'][$itemId] : 0;
}

// Render dynamic Add to Cart / Increment Quantity Controls
function renderCartControls($itemId, $btnClass = "text-primary border border-primary px-3 py-1.5 rounded-full text-label-sm font-semibold hover:bg-primary hover:text-on-primary transition-colors") {
    $qty = getCartQty($itemId);
    if ($qty === 0) {
        return '<a href="?action=add&id=' . $itemId . '" class="' . $btnClass . '">Add</a>';
    } else {
        return '<div class="flex items-center gap-3 bg-surface-container rounded-full px-3 py-1 border border-outline-variant/50">' .
               '<a href="?action=update&id=' . $itemId . '&qty=' . ($qty - 1) . '" class="text-primary font-extrabold px-1 text-headline-sm hover:scale-110 active:scale-95 transition-transform duration-100">-</a>' .
               '<span class="text-label-md font-bold text-on-surface select-none">' . $qty . '</span>' .
               '<a href="?action=add&id=' . $itemId . '" class="text-primary font-extrabold px-1 text-headline-sm hover:scale-110 active:scale-95 transition-transform duration-100">+</a>' .
               '</div>';
    }
}

// Helper to count total items in cart
function getCartTotalCount() {
    $total = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $qty) {
            $total += $qty;
        }
    }
    return $total;
}

// Helper to render Workable Notification dropdown
function renderNotifications() {
    return <<<HTML
    <div class="relative inline-block text-left">
        <button class="material-symbols-outlined text-on-surface-variant align-middle hover:text-primary transition-colors duration-150 relative" data-icon="notifications" onclick="toggleNotifications(event)">
            notifications
            <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-primary ring-2 ring-surface"></span>
        </button>
        <div id="notifications-dropdown" class="hidden absolute right-0 mt-3 w-80 bg-surface dark:bg-on-background border border-outline-variant/30 rounded-2xl shadow-xl z-50 py-2 transition-all duration-200 transform scale-95 origin-top-right">
            <div class="px-4 py-3 border-b border-outline-variant/30 flex justify-between items-center">
                <span class="text-label-md font-bold">Notifications</span>
                <span class="bg-primary text-on-primary text-[10px] px-2 py-0.5 rounded-full font-semibold">2 New</span>
            </div>
            <div class="max-h-72 overflow-y-auto divide-y divide-outline-variant/10">
                <a href="#" class="block px-4 py-3.5 hover:bg-surface-container/50 transition-colors">
                    <p class="text-label-sm font-semibold">Order status update</p>
                    <p class="text-body-md text-secondary mt-0.5 leading-normal">Your order #ZY-20418 has been handed to the courier.</p>
                    <p class="text-[10px] text-on-surface-variant mt-1.5">5 mins ago</p>
                </a>
                <a href="#" class="block px-4 py-3.5 hover:bg-surface-container/50 transition-colors">
                    <p class="text-label-sm font-semibold">Special Promotion!</p>
                    <p class="text-body-md text-secondary mt-0.5 leading-normal">Use code <strong class="text-primary">50OFF</strong> for 50% discount on your next 3 orders!</p>
                    <p class="text-[10px] text-on-surface-variant mt-1.5">2 hours ago</p>
                </a>
            </div>
        </div>
    </div>
    <script>
    function toggleNotifications(event) {
        event.stopPropagation();
        const dropdown = document.getElementById("notifications-dropdown");
        dropdown.classList.toggle("hidden");
    }
    </script>
HTML;
}

// Helper to render dynamic customer profile and account dropdown
function renderAccountMenu() {
    $email = $_SESSION['user_email'] ?? 'customer@zyrop.com';
    $shortName = strtoupper(substr(explode('@', $email)[0], 0, 2));
    
    return <<<HTML
    <div class="relative inline-block text-left">
        <button id="account-menu-btn" onclick="toggleAccountMenu(event)" class="w-10 h-10 rounded-full bg-primary/10 text-primary hover:bg-primary/20 transition-all font-bold flex items-center justify-center text-label-md uppercase border border-primary/20 select-none">
            {$shortName}
        </button>
        <div id="account-menu-dropdown" class="hidden absolute right-0 mt-3 w-56 bg-surface dark:bg-on-background border border-outline-variant/30 rounded-2xl shadow-xl z-50 py-2 transition-all duration-200 transform scale-95 origin-top-right">
            <div class="px-4 py-3 border-b border-outline-variant/30">
                <p class="text-label-sm font-semibold text-secondary">Logged in as</p>
                <p class="text-label-md font-bold text-on-surface truncate mt-0.5">{$email}</p>
            </div>
            <div class="py-1">
                <a href="order-track.php" class="flex items-center gap-3 px-4 py-2.5 hover:bg-surface-container/50 transition-colors text-label-md text-on-surface">
                    <span class="material-symbols-outlined text-secondary text-[20px]" data-icon="local_shipping">local_shipping</span>
                    Track Orders
                </a>
                <a href="cart.php" class="flex items-center gap-3 px-4 py-2.5 hover:bg-surface-container/50 transition-colors text-label-md text-on-surface">
                    <span class="material-symbols-outlined text-secondary text-[20px]" data-icon="shopping_basket">shopping_basket</span>
                    My Cart
                </a>
                <div class="border-t border-outline-variant/10 my-1"></div>
                <a href="?action=logout" class="flex items-center gap-3 px-4 py-2.5 hover:bg-error/10 text-error hover:text-error transition-colors text-label-md font-bold">
                    <span class="material-symbols-outlined text-error text-[20px]" data-icon="logout">logout</span>
                    Log Out
                </a>
            </div>
        </div>
    </div>
    <script>
    function toggleAccountMenu(event) {
        event.stopPropagation();
        const dropdown = document.getElementById("account-menu-dropdown");
        const notifDropdown = document.getElementById("notifications-dropdown");
        if (notifDropdown) notifDropdown.classList.add("hidden");
        dropdown.classList.toggle("hidden");
    }
    document.addEventListener("click", function(event) {
        const dropdown = document.getElementById("account-menu-dropdown");
        if (dropdown && !dropdown.contains(event.target)) {
            dropdown.classList.add("hidden");
        }
    });
    </script>
HTML;
}
?>
