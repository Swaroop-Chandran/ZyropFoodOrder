<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZyropFoodOrder — Order Food Online</title>
  <meta name="description" content="Browse delicious food items from top restaurants near you. Order biryani, curry, dosa, pizza and more. Fast delivery at your doorstep."/>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/zyrop.css"/>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode:"class",
      theme:{extend:{colors:{
        "surface-container-low":"#f5f3f3","on-primary-container":"#fffbff","surface-container-lowest":"#ffffff",
        "surface-bright":"#fbf9f8","on-error":"#ffffff","on-primary":"#ffffff","outline":"#907065",
        "surface-container-high":"#e9e8e7","on-tertiary":"#ffffff","surface-variant":"#e4e2e2",
        "tertiary":"#006b29","surface-dim":"#dbdad9","on-secondary":"#ffffff","error":"#ba1a1a",
        "surface":"#fbf9f8","primary-fixed":"#ffdbd0","primary-container":"#d24200","primary":"#a83300",
        "error-container":"#ffdad6","on-surface-variant":"#5c4037","secondary":"#5f5e5e",
        "tertiary-container":"#008735","inverse-surface":"#303031","on-background":"#1b1c1c",
        "background":"#fbf9f8","outline-variant":"#e5beb2","inverse-on-surface":"#f2f0f0",
        "surface-tint":"#ac3500","secondary-container":"#e5e2e1","surface-container":"#efeded",
        "primary-fixed-dim":"#ffb59d","on-surface":"#1b1c1c","surface-container-highest":"#e4e2e2",
        "inverse-primary":"#ffb59d"
      }}}
    }
  </script>
</head>
<body class="bg-surface text-on-surface">

<!-- ===== HEADER ===== -->
<header class="bg-surface border-b border-outline-variant/30 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 flex items-center justify-between gap-4 py-4">

    <!-- Logo -->
    <a href="index.php" class="font-extrabold text-xl text-primary whitespace-nowrap flex-shrink-0">
      ZyropFoodOrder
    </a>

    <!-- Search bar (desktop) -->
    <div class="flex-1 max-w-lg hidden sm:flex items-center bg-surface-container rounded-full px-4 py-2.5 gap-2 border border-outline-variant/40">
      <span class="material-symbols-outlined text-secondary" style="font-size:20px">search</span>
      <input id="menu-search" type="text" placeholder="Search dishes, restaurants…"
        class="bg-transparent border-none outline-none text-sm w-full font-medium placeholder:text-secondary"
        oninput="filterItems()"/>
    </div>

    <!-- Right actions -->
    <div class="flex items-center gap-3">

      <!-- Location pill -->
      <div id="header-location" class="hidden lg:flex items-center gap-1.5 bg-surface-container rounded-full px-3 py-1.5 cursor-pointer hover:bg-surface-container-high transition-colors">
        <span class="material-symbols-outlined text-primary" style="font-size:16px">location_on</span>
        <span id="header-loc-text" class="text-xs font-semibold text-on-surface max-w-[120px] truncate">Detecting…</span>
        <span class="material-symbols-outlined text-secondary" style="font-size:14px">keyboard_arrow_down</span>
      </div>

      <!-- Cart button -->
      <a href="cart.php" class="relative flex items-center gap-2 bg-primary text-on-primary rounded-full px-5 py-2 text-sm font-bold hover:bg-primary-container transition-colors">
        <span class="material-symbols-outlined" style="font-size:18px">shopping_cart</span>
        <span class="hidden sm:inline">Cart</span>
        <span class="cart-count-badge" style="display:none; position:absolute; top:-8px; right:-6px; background:#1b1c1c; color:#fff; font-size:10px; font-weight:700; min-width:18px; height:18px; border-radius:9999px; align-items:center; justify-content:center; padding:0 4px;">0</span>
      </a>

      <!-- Account Info Header Menu -->
      <div class="flex items-center gap-2 border border-outline-variant/30 rounded-full px-3 py-1 bg-surface-container-low">
        <span class="material-symbols-outlined text-primary" style="font-size:18px">account_circle</span>
        <span class="text-xs font-bold text-on-surface truncate max-w-[100px]"><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
        <a href="logout.php" class="material-symbols-outlined text-secondary hover:text-error transition-colors" style="font-size:16px" title="Logout">logout</a>
      </div>
    </div>
  </div>

  <!-- Mobile search -->
  <div class="sm:hidden px-4 pb-3">
    <div class="flex items-center bg-surface-container rounded-full px-4 py-2.5 gap-2 border border-outline-variant/40">
      <span class="material-symbols-outlined text-secondary" style="font-size:20px">search</span>
      <input id="menu-search-mobile" type="text" placeholder="Search dishes…"
        class="bg-transparent border-none outline-none text-sm w-full font-medium placeholder:text-secondary"
        oninput="filterItems()"/>
    </div>
  </div>
</header>

<!-- ===== CATEGORY FILTER BAR ===== -->
<div class="sticky top-[69px] sm:top-[73px] z-40 bg-surface border-b border-outline-variant/20">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">
    <div id="category-bar" class="flex gap-2 overflow-x-auto hide-scrollbar py-3">
      <!-- generated by JS -->
    </div>
  </div>
</div>

<!-- ===== FILTER / SORT BAR ===== -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 py-5 flex items-center justify-between gap-4 flex-wrap">
  <div class="flex items-center gap-2 flex-wrap">
    <span class="text-sm text-secondary font-medium" id="results-count">Loading…</span>
    <div class="flex gap-2">
      <button onclick="setDietFilter('all')" id="diet-all"
        class="diet-btn active px-3 py-1 rounded-full text-xs font-bold border border-outline-variant transition-all bg-primary text-on-primary">All</button>
      <button onclick="setDietFilter('veg')" id="diet-veg"
        class="diet-btn px-3 py-1 rounded-full text-xs font-bold border border-outline-variant transition-all hover:bg-surface-container">🌿 Veg</button>
      <button onclick="setDietFilter('nonveg')" id="diet-nonveg"
        class="diet-btn px-3 py-1 rounded-full text-xs font-bold border border-outline-variant transition-all hover:bg-surface-container">🍗 Non-Veg</button>
    </div>
  </div>
  <div class="flex items-center gap-2">
    <label class="text-xs font-semibold text-secondary">Sort:</label>
    <select onchange="setSortOrder(this.value)"
      class="text-xs font-semibold text-on-surface border border-outline-variant rounded-full px-3 py-1.5 bg-surface outline-none cursor-pointer">
      <option value="popular">Popular</option>
      <option value="price-asc">Price ↑</option>
      <option value="price-desc">Price ↓</option>
      <option value="rating">Top Rated</option>
    </select>
  </div>
</div>

<!-- ===== FOOD GRID ===== -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 pb-28">
  <div id="food-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <!-- Food cards rendered by JS -->
  </div>
  <div id="empty-state" class="hidden flex-col items-center justify-center py-24 gap-4 text-center">
    <div class="text-6xl">🍽️</div>
    <h3 class="text-xl font-bold text-on-surface">No items found</h3>
    <p class="text-secondary text-sm">Try a different category or search term.</p>
    <button onclick="resetFilters()" class="btn-primary mt-2">Show all items</button>
  </div>
</main>

<!-- ===== FLOATING CART (mobile) ===== -->
<div id="floating-cart" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden">
  <a href="cart.php" class="flex items-center gap-4 bg-on-background text-surface rounded-2xl px-6 py-4 shadow-2xl font-bold text-sm animate-bounce-in">
    <span class="material-symbols-outlined" style="font-size:20px">shopping_cart</span>
    <span id="float-cart-info">View Cart</span>
    <span class="bg-primary text-on-primary rounded-full px-3 py-0.5 text-xs" id="float-cart-price">₹0</span>
    <span class="material-symbols-outlined" style="font-size:18px">arrow_forward</span>
  </a>
</div>

<div id="toast-container"></div>
<script src="js/cart.js"></script>
<script>
/* ===================================================
   Categories
=================================================== */
const CATEGORIES = [
  { id:'all',          label:'All',            emoji:'🍽️' },
  { id:'bread',        label:'Breads',         emoji:'🍞' },
  { id:'rice',         label:'Rice Dishes',    emoji:'🍚' },
  { id:'eggs',         label:'Eggs',           emoji:'🥚' },
  { id:'indian',       label:'Indian',         emoji:'🍛' },
  { id:'international',label:'International',  emoji:'🌍' },
];

/* ===================================================
   Food Data — 21 items from reference image
   Each with a unique, verified Wikipedia image
=================================================== */
const FOODS = [

  /* ── BREADS ──────────────────────────────────────── */
  {
    id:'f01', name:'Bread',
    restaurant:"Baker's Basket",
    cat:'bread', price:49, rating:4.2, ratingCount:3.1,
    veg:true, time:'5-10',
    image:'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=400&q=80',
    badge:''
  },
  {
    id:'f02', name:'Roti',
    restaurant:'Dhaba Junction',
    cat:'bread', price:15, rating:4.5, ratingCount:8.3,
    veg:true, time:'5-10',
    image:'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=400&q=80',
    badge:'Daily Staple'
  },
  {
    id:'f03', name:'Paratha',
    restaurant:'Dhaba Junction',
    cat:'bread', price:45, rating:4.7, ratingCount:6.2,
    veg:true, time:'10-15',
    image:'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=400&q=80',
    badge:'Morning Special'
  },

  /* ── RICE DISHES ─────────────────────────────────── */
  {
    id:'f04', name:'Rice',
    restaurant:'Spice Garden',
    cat:'rice', price:59, rating:4.1, ratingCount:4.8,
    veg:true, time:'15-20',
    image:'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=400&q=80',
    badge:''
  },
  {
    id:'f05', name:'Pulao',
    restaurant:'Spice Garden',
    cat:'rice', price:119, rating:4.5, ratingCount:3.4,
    veg:true, time:'20-25',
    image:'https://images.unsplash.com/photo-1630851840633-f96999247032?w=400&q=80',
    badge:'Veg Special'
  },
  {
    id:'f06', name:'Biryani',
    restaurant:'Biryani House',
    cat:'rice', price:249, rating:4.8, ratingCount:9.2,
    veg:false, time:'30-40',
    image:'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?w=400&q=80',
    badge:'Bestseller'
  },
  {
    id:'f07', name:'Fried Rice',
    restaurant:'Wok & Roll',
    cat:'rice', price:149, rating:4.5, ratingCount:4.7,
    veg:true, time:'15-20',
    image:'https://images.unsplash.com/photo-1512058564366-18510be2db19?w=400&q=80',
    badge:'Indo-Chinese'
  },

  /* ── EGGS ────────────────────────────────────────── */
  {
    id:'f08', name:'Boiled Egg',
    restaurant:'Egg Station',
    cat:'eggs', price:29, rating:4.2, ratingCount:2.8,
    veg:false, time:'10-15',
    image:'https://images.unsplash.com/photo-1482049016688-2d3e1b311543?w=400&q=80',
    badge:'Protein Rich'
  },
  {
    id:'f09', name:'Omelette',
    restaurant:'Egg Station',
    cat:'eggs', price:59, rating:4.5, ratingCount:3.6,
    veg:false, time:'10-15',
    image:'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=400&q=80',
    badge:'Fresh Made'
  },
  {
    id:'f10', name:'Scrambled Eggs',
    restaurant:'Egg Station',
    cat:'eggs', price:69, rating:4.4, ratingCount:2.2,
    veg:false, time:'10-15',
    image:'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?w=400&q=80',
    badge:'Breakfast'
  },

  /* ── INDIAN ──────────────────────────────────────── */
  {
    id:'f11', name:'Idli',
    restaurant:'Saravana Bhavan',
    cat:'indian', price:69, rating:4.6, ratingCount:5.3,
    veg:true, time:'15-20',
    image:'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?w=400&q=80',
    badge:'South Indian'
  },
  {
    id:'f12', name:'Dosa',
    restaurant:'Saravana Bhavan',
    cat:'indian', price:89, rating:4.7, ratingCount:7.1,
    veg:true, time:'15-20',
    image:'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?w=400&q=80',
    badge:'Crispy'
  },
  {
    id:'f13', name:'Paneer Curry',
    restaurant:'Shahi Rasoi',
    cat:'indian', price:199, rating:4.6, ratingCount:4.4,
    veg:true, time:'20-30',
    image:'https://images.unsplash.com/photo-1567188040759-fb8a883dc6d8?w=400&q=80',
    badge:'Veg Favourite'
  },
  {
    id:'f14', name:'Chicken Curry',
    restaurant:'Punjab Grill',
    cat:'indian', price:249, rating:4.8, ratingCount:7.9,
    veg:false, time:'25-35',
    image:'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=400&q=80',
    badge:'Must Try'
  },
  {
    id:'f15', name:'Mutton Korma',
    restaurant:"Nawab's Kitchen",
    cat:'indian', price:329, rating:4.8, ratingCount:3.6,
    veg:false, time:'35-45',
    image:'https://images.unsplash.com/photo-1574653853027-5382a3d23a15?w=400&q=80',
    badge:'Rich & Creamy'
  },

  /* ── INTERNATIONAL ───────────────────────────────── */
  {
    id:'f16', name:'Noodles',
    restaurant:'Wok & Roll',
    cat:'international', price:129, rating:4.4, ratingCount:5.8,
    veg:true, time:'15-20',
    image:'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=400&q=80',
    badge:'Indo-Chinese'
  },
  {
    id:'f17', name:'Pasta',
    restaurant:'Pasta Fresca',
    cat:'international', price:189, rating:4.6, ratingCount:4.2,
    veg:true, time:'20-25',
    image:'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?w=400&q=80',
    badge:''
  },
  {
    id:'f18', name:'Pizza',
    restaurant:'Pizza Primo',
    cat:'international', price:249, rating:4.7, ratingCount:6.3,
    veg:true, time:'20-30',
    image:'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&q=80',
    badge:'Cheesy'
  },
  {
    id:'f19', name:'Mashed Potatoes',
    restaurant:'Green Plates',
    cat:'international', price:89, rating:4.3, ratingCount:1.9,
    veg:true, time:'15-20',
    image:'https://images.unsplash.com/photo-1632778149955-e80f8ceca2e8?w=400&q=80',
    badge:'Comfort Food'
  },
  {
    id:'f20', name:'Grilled Chicken',
    restaurant:'Frontier Grill',
    cat:'international', price:279, rating:4.7, ratingCount:5.5,
    veg:false, time:'25-35',
    image:'https://images.unsplash.com/photo-1598103442097-8b74394b95c3?w=400&q=80',
    badge:'Grilled Fresh'
  },
  {
    id:'f21', name:'Roasted Vegetables',
    restaurant:'Green Plates',
    cat:'international', price:149, rating:4.4, ratingCount:2.1,
    veg:true, time:'20-25',
    image:'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400&q=80',
    badge:'Healthy'
  },
];

/* ===================================================
   State
=================================================== */
let activeCategory = 'all';
let activeDiet     = 'all';
let activeSort     = 'popular';
let searchQuery    = '';

/* ===================================================
   Init
=================================================== */
document.addEventListener('DOMContentLoaded', () => {
  buildCategoryBar();
  renderFoods();
  loadLocation();
  updateFloatingCart();
  updateCartBadge();

  // Sync mobile search with desktop
  const mobile = document.getElementById('menu-search-mobile');
  if (mobile) mobile.addEventListener('input', () => {
    searchQuery = mobile.value;
    const desk = document.getElementById('menu-search');
    if (desk) desk.value = mobile.value;
    renderFoods();
  });
});

window.addEventListener('zyrop:cart-updated', () => {
  updateFloatingCart();
  updateCartBadge();
});

/* ===================================================
   Category Bar
=================================================== */
function buildCategoryBar() {
  const bar = document.getElementById('category-bar');
  bar.innerHTML = CATEGORIES.map(c => `
    <button id="cat-${c.id}" onclick="setCategory('${c.id}')"
      class="flex-shrink-0 flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold border transition-all duration-200
             ${c.id==='all' ? 'bg-primary text-on-primary border-primary' : 'border-outline-variant text-secondary hover:bg-surface-container'}">
      <span>${c.emoji}</span> ${c.label}
    </button>
  `).join('');
}

function setCategory(id) {
  activeCategory = id;
  document.querySelectorAll('[id^="cat-"]').forEach(btn => {
    btn.className = 'flex-shrink-0 flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold border transition-all duration-200 border-outline-variant text-secondary hover:bg-surface-container';
  });
  const active = document.getElementById(`cat-${id}`);
  if (active) active.className = 'flex-shrink-0 flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold border transition-all duration-200 bg-primary text-on-primary border-primary';
  renderFoods();
}

/* ===================================================
   Filters & Sort
=================================================== */
function setDietFilter(diet) {
  activeDiet = diet;
  document.querySelectorAll('.diet-btn').forEach(b => {
    b.className = 'diet-btn px-3 py-1 rounded-full text-xs font-bold border border-outline-variant transition-all hover:bg-surface-container';
  });
  const el = document.getElementById(`diet-${diet}`);
  if (el) el.className = 'diet-btn active px-3 py-1 rounded-full text-xs font-bold border border-outline-variant transition-all bg-primary text-on-primary';
  renderFoods();
}

function setSortOrder(val) {
  activeSort = val;
  renderFoods();
}

function filterItems() {
  const desk   = document.getElementById('menu-search');
  const mobile = document.getElementById('menu-search-mobile');
  searchQuery  = (desk && document.activeElement === desk) ? desk.value : (mobile ? mobile.value : '');
  renderFoods();
}

function resetFilters() {
  activeCategory = 'all';
  activeDiet     = 'all';
  searchQuery    = '';
  const desk   = document.getElementById('menu-search');
  const mobile = document.getElementById('menu-search-mobile');
  if (desk)   desk.value   = '';
  if (mobile) mobile.value = '';
  buildCategoryBar();
  setDietFilter('all');
  renderFoods();
}

/* ===================================================
   Render Foods
=================================================== */
function renderFoods() {
  let items = [...FOODS];

  if (activeCategory !== 'all') items = items.filter(f => f.cat === activeCategory);
  if (activeDiet === 'veg')     items = items.filter(f => f.veg);
  if (activeDiet === 'nonveg')  items = items.filter(f => !f.veg);

  const q = searchQuery.trim().toLowerCase();
  if (q) {
    items = items.filter(f =>
      f.name.toLowerCase().includes(q) ||
      f.restaurant.toLowerCase().includes(q)
    );
  }

  if (activeSort === 'price-asc')  items.sort((a,b) => a.price - b.price);
  if (activeSort === 'price-desc') items.sort((a,b) => b.price - a.price);
  if (activeSort === 'rating')     items.sort((a,b) => b.rating - a.rating);

  const count = document.getElementById('results-count');
  if (count) count.textContent = `${items.length} item${items.length !== 1 ? 's' : ''} found`;

  const grid  = document.getElementById('food-grid');
  const empty = document.getElementById('empty-state');

  if (items.length === 0) {
    grid.classList.add('hidden');
    empty.classList.remove('hidden');
    empty.classList.add('flex');
    return;
  }
  empty.classList.add('hidden');
  empty.classList.remove('flex');
  grid.classList.remove('hidden');
  grid.innerHTML = items.map((f, i) => foodCard(f, i)).join('');
}

/* ===================================================
   Food Card HTML
=================================================== */
function foodCard(f, i) {
  const qty = ZyropCart.getItemQty(f.id);
  const rc  = f.ratingCount;
  const ratingStr = (typeof rc === 'number' && rc >= 1) ? `${rc}k ratings` : `${rc} ratings`;
  const vegClass  = f.veg
    ? 'border-green-600 text-green-600 bg-white'
    : 'border-red-600 text-red-600 bg-white';

  // Fallback image only if item.image is missing, and clear browser cache issue by adding ?v=2
  const imageUrl = f.image ? `${f.image}${f.image.includes('?') ? '&' : '?'}v=2` : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&q=80';

  return `
  <div class="food-card animate-fade-in-up" style="animation-delay:${i * 0.05}s">
    <div class="relative overflow-hidden" style="height:180px">
      <img src="${imageUrl}" alt="${f.name}" class="w-full h-full object-cover" loading="lazy"/>
      ${f.badge ? `<span class="absolute top-3 left-3 bg-on-background/85 text-white text-[11px] font-bold px-2.5 py-1 rounded-full">${f.badge}</span>` : ''}
      <span class="absolute top-3 right-3 w-5 h-5 rounded border-2 flex items-center justify-center text-[10px] font-bold ${vegClass}">●</span>
    </div>
    <div class="p-4 flex flex-col gap-2">
      <div>
        <h3 class="font-bold text-sm text-on-surface leading-snug">${f.name}</h3>
        <p class="text-xs text-secondary mt-0.5">${f.restaurant}</p>
      </div>
      <div class="flex items-center gap-3 text-xs text-secondary">
        <span class="flex items-center gap-1">
          <span class="material-symbols-outlined filled text-yellow-500" style="font-size:14px">star</span>
          <span class="font-semibold text-on-surface">${f.rating}</span>
          <span>(${ratingStr})</span>
        </span>
        <span>·</span>
        <span class="flex items-center gap-0.5">
          <span class="material-symbols-outlined" style="font-size:14px">schedule</span>
          ${f.time} min
        </span>
      </div>
      <div class="flex items-center justify-between mt-1">
        <span class="font-extrabold text-base text-on-surface">&#8377;${f.price}</span>
        ${qty === 0
          ? `<button onclick="addToCart('${f.id}')" id="add-btn-${f.id}"
               class="flex items-center gap-1.5 bg-primary text-on-primary rounded-full px-4 py-1.5 text-xs font-bold hover:bg-primary-container active:scale-95 transition-all">
               <span class="material-symbols-outlined" style="font-size:16px">add</span> Add
             </button>`
          : `<div class="qty-stepper" id="stepper-${f.id}">
               <button class="qty-btn" onclick="changeQty('${f.id}',-1)">−</button>
               <span class="qty-count" id="qty-${f.id}">${qty}</span>
               <button class="qty-btn" onclick="changeQty('${f.id}',1)">+</button>
             </div>`
        }
      </div>
    </div>
  </div>`;
}

/* ===================================================
   Cart Actions
=================================================== */
function addToCart(id) {
  const food = FOODS.find(f => f.id === id);
  if (!food) return;
  const qty = ZyropCart.addItem({ id:food.id, name:food.name, price:food.price, image:food.image, restaurant:food.restaurant, veg:food.veg });
  showToast(`${food.name} added to cart! 🛒`, 'success', 2000);
  updateFoodCardBtn(id, qty);
  updateFloatingCart();
}

function changeQty(id, delta) {
  if (delta > 0) ZyropCart.incrementItem(id);
  else           ZyropCart.decrementItem(id);
  const qty = ZyropCart.getItemQty(id);
  updateFoodCardBtn(id, qty);
  updateFloatingCart();
}

function updateFoodCardBtn(id, qty) {
  const addBtn  = document.getElementById(`add-btn-${id}`);
  const stepper = document.getElementById(`stepper-${id}`);

  if (qty === 0) {
    if (stepper) {
      stepper.outerHTML = `<button onclick="addToCart('${id}')" id="add-btn-${id}"
        class="flex items-center gap-1.5 bg-primary text-on-primary rounded-full px-4 py-1.5 text-xs font-bold hover:bg-primary-container active:scale-95 transition-all">
        <span class="material-symbols-outlined" style="font-size:16px">add</span> Add
      </button>`;
    }
  } else {
    if (addBtn) {
      addBtn.outerHTML = `<div class="qty-stepper" id="stepper-${id}">
        <button class="qty-btn" onclick="changeQty('${id}',-1)">−</button>
        <span class="qty-count" id="qty-${id}">${qty}</span>
        <button class="qty-btn" onclick="changeQty('${id}',1)">+</button>
      </div>`;
    } else if (document.getElementById(`qty-${id}`)) {
      document.getElementById(`qty-${id}`).textContent = qty;
    }
  }
}

/* ===================================================
   Floating Cart
=================================================== */
function updateFloatingCart() {
  const count    = ZyropCart.getTotalCount();
  const subtotal = ZyropCart.getSubtotal();
  const fc       = document.getElementById('floating-cart');
  if (count > 0) {
    fc.classList.remove('hidden');
    document.getElementById('float-cart-info').textContent  = `${count} item${count > 1 ? 's' : ''}`;
    document.getElementById('float-cart-price').textContent = `\u20B9${subtotal}`;
  } else {
    fc.classList.add('hidden');
  }
}

/* ===================================================
   Cart Badge
=================================================== */
function updateCartBadge() {
  const count   = ZyropCart.getTotalCount();
  const badges  = document.querySelectorAll('.cart-count-badge');
  badges.forEach(b => {
    if (count > 0) {
      b.style.display = 'flex';
      b.textContent   = count;
    } else {
      b.style.display = 'none';
    }
  });
}

/* ===================================================
   Location
=================================================== */
function loadLocation() {
  const loc  = ZyropLocation.get();
  const wrap = document.getElementById('header-location');
  const text = document.getElementById('header-loc-text');
  if (wrap) wrap.classList.remove('hidden');
  if (loc) {
    if (text) text.textContent = loc.label.split(',')[0];
  } else {
    if (text) text.textContent = 'Set location';
    ZyropLocation.detect(
      (l) => { if (text) text.textContent = l.label.split(',')[0]; },
      ()  => { if (text) text.textContent = 'Set location'; }
    );
  }
}
</script>
</body>
</html>
