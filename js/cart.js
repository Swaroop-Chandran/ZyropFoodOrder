/* =========================================================
   ZyropFoodOrder — Cart & App Logic (localStorage)
   ========================================================= */

const ZyropCart = (() => {
  const STORAGE_KEY = 'zyrop_cart';

  function getCart() {
    try {
      return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
    } catch { return []; }
  }

  function saveCart(cart) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    _dispatchChange(cart);
  }

  function addItem(item) {
    // item: { id, name, price, image, restaurant, veg }
    const cart = getCart();
    const existing = cart.find(c => c.id === item.id);
    if (existing) {
      existing.qty += 1;
    } else {
      cart.push({ ...item, qty: 1 });
    }
    saveCart(cart);
    return getItemQty(item.id);
  }

  function removeItem(id) {
    const cart = getCart().filter(c => c.id !== id);
    saveCart(cart);
  }

  function updateQty(id, qty) {
    const cart = getCart();
    const item = cart.find(c => c.id === id);
    if (!item) return;
    if (qty <= 0) {
      removeItem(id);
    } else {
      item.qty = qty;
      saveCart(cart);
    }
  }

  function incrementItem(id) {
    const cart = getCart();
    const item = cart.find(c => c.id === id);
    if (item) { item.qty += 1; saveCart(cart); }
  }

  function decrementItem(id) {
    const cart = getCart();
    const item = cart.find(c => c.id === id);
    if (!item) return;
    if (item.qty <= 1) removeItem(id);
    else { item.qty -= 1; saveCart(cart); }
  }

  function getItemQty(id) {
    const item = getCart().find(c => c.id === id);
    return item ? item.qty : 0;
  }

  function getTotalCount() {
    return getCart().reduce((sum, c) => sum + c.qty, 0);
  }

  function getSubtotal() {
    return getCart().reduce((sum, c) => sum + c.price * c.qty, 0);
  }

  function clearCart() {
    saveCart([]);
  }

  function _dispatchChange(cart) {
    window.dispatchEvent(new CustomEvent('zyrop:cart-updated', { detail: cart }));
  }

  return {
    getCart, addItem, removeItem, updateQty,
    incrementItem, decrementItem, getItemQty,
    getTotalCount, getSubtotal, clearCart
  };
})();


/* =========================================================
   Toast Notifications
   ========================================================= */
function showToast(message, type = 'info', duration = 3000) {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = { success: 'check_circle', error: 'error', info: 'shopping_cart' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `
    <span class="material-symbols-outlined toast-icon" style="font-size:20px">${icons[type] || 'info'}</span>
    <span>${message}</span>
  `;
  container.appendChild(toast);

  setTimeout(() => {
    toast.classList.add('hide');
    setTimeout(() => toast.remove(), 320);
  }, duration);
}


/* =========================================================
   Cart Badge Updater (runs on every page)
   ========================================================= */
function updateCartBadge() {
  const badges = document.querySelectorAll('.cart-count-badge');
  const count = ZyropCart.getTotalCount();
  badges.forEach(badge => {
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
    badge.classList.remove('bump');
    void badge.offsetWidth; // reflow
    if (count > 0) badge.classList.add('bump');
  });
}

window.addEventListener('zyrop:cart-updated', updateCartBadge);
document.addEventListener('DOMContentLoaded', updateCartBadge);


/* =========================================================
   Geolocation Helper
   ========================================================= */
const ZyropLocation = {
  STORAGE_KEY: 'zyrop_location',

  save(data) {
    localStorage.setItem(this.STORAGE_KEY, JSON.stringify(data));
  },

  get() {
    try { return JSON.parse(localStorage.getItem(this.STORAGE_KEY)); }
    catch { return null; }
  },

  async detect(onSuccess, onError) {
    if (!navigator.geolocation) {
      onError('Geolocation not supported by your browser.');
      return;
    }
    navigator.geolocation.getCurrentPosition(
      async (pos) => {
        const { latitude: lat, longitude: lng } = pos.coords;
        // Reverse geocode using nominatim (free, no key needed)
        try {
          const res = await fetch(
            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`,
            { headers: { 'Accept-Language': 'en' } }
          );
          const data = await res.json();
          const addr = data.address;
          const label = [
            addr.road || addr.pedestrian || addr.neighbourhood,
            addr.suburb || addr.city_district,
            addr.city || addr.town || addr.village,
            addr.state
          ].filter(Boolean).join(', ');
          const location = { lat, lng, label, full: data.display_name };
          ZyropLocation.save(location);
          onSuccess(location);
        } catch {
          const location = { lat, lng, label: `${lat.toFixed(4)}, ${lng.toFixed(4)}`, full: '' };
          ZyropLocation.save(location);
          onSuccess(location);
        }
      },
      (err) => {
        const msgs = {
          1: 'Location permission denied.',
          2: 'Location unavailable.',
          3: 'Location request timed out.'
        };
        onError(msgs[err.code] || 'Could not get location.');
      },
      { timeout: 10000 }
    );
  }
};


/* =========================================================
   Mobile Nav Toggle
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
  const mobileMenuBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu    = document.getElementById('mobile-menu');
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }
});
