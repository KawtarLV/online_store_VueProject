<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';
import NavBar from './components/organisms/NavBar/NavBar.vue';
import Footer from './components/organisms/Footer/Footer.vue';
import ShopHomePage from './components/pages/ShopHomePage/ShopHomePage.vue';
import ProductsPage from './components/pages/ProductsPage/ProductsPage.vue';
import ProductDetailPage from './components/pages/ProductDetailPage/ProductDetailPage.vue';
import LoginPage from './components/pages/Auth/Login.vue';
import RegisterPage from './components/pages/Auth/Register.vue';
import DashboardPage from './components/pages/Dashboard/DashboardPage.vue';
import CartPage from './components/pages/Cart/CartPage.vue';
import NewProductPage from './components/pages/Dashboard/NewProductPage.vue';
import EditProductPage from './components/pages/Dashboard/EditProductPage.vue';
import SettingsPage from './components/pages/SettingsPage/SettingsPage.vue';
import ProfilePage from './components/pages/ProfilePage/ProfilePage.vue';
import { get } from './utils/api.js';

const route = ref(parseRoute());
const session = ref(loadSession());
const cartItems = ref(loadCart());
const settings = ref({
  store_name: 'Simple Shop',
  contact_email: '',
});

function parseRoute() {
  // Read the hash and turn it into a simple page object.
  const hash = window.location.hash || '#/';
  const raw = hash.replace(/^#/, '');
  const [path, queryString = ''] = raw.split('?');
  const query = new URLSearchParams(queryString);

  if (path === '/login') {
    return { name: 'login' };
  }
  if (path === '/register') {
    return { name: 'register' };
  }
  if (path === '/dashboard') {
    return { name: 'dashboard' };
  }
  if (path === '/admin/new-product') {
    return { name: 'new-product' };
  }
  if (path.startsWith('/admin/edit-product/')) {
    const id = Number(path.replace('/admin/edit-product/', ''));
    return { name: 'edit-product', id: Number.isFinite(id) ? id : null };
  }
  if (path === '/cart') {
    return { name: 'cart' };
  }
  if (path === '/settings') {
    return { name: 'settings' };
  }
  if (path === '/profile') {
    return { name: 'profile' };
  }
  if (path.startsWith('/product/')) {
    const id = Number(path.replace('/product/', ''));
    return { name: 'detail', id: Number.isFinite(id) ? id : null };
  }
  if (path === '/products') {
    const category = query.get('category');
    const page = Number(query.get('page') || 1);
    return {
      name: 'products',
      categoryId: category ? Number(category) : null,
      page: Number.isFinite(page) && page > 0 ? page : 1,
    };
  }
  return { name: 'home' };
}

function navigateToDetail(id) {
  window.location.hash = `#/product/${id}`;
  route.value = { name: 'detail', id };
}

function navigateHome() {
  window.location.hash = '#/';
  route.value = { name: 'home' };
}

function handleLoginSuccess(nextSession) {
  session.value = nextSession;
  localStorage.setItem('session', JSON.stringify(nextSession));
  if (nextSession.user?.role === 'admin') {
    // Admin goes to the dashboard. Normal user goes home.
    window.location.hash = '#/dashboard';
    route.value = { name: 'dashboard' };
  } else {
    navigateHome();
  }
}

function handleLogout() {
  session.value = null;
  localStorage.removeItem('session');
  navigateHome();
}

function loadCart() {
  try {
    const stored = localStorage.getItem('cart');
    return stored ? JSON.parse(stored) : [];
  } catch {
    return [];
  }
}

function saveCart() {
  localStorage.setItem('cart', JSON.stringify(cartItems.value));
}

function addToCart(product) {
  if (!product?.id) {
    return;
  }

  // If the item is already there, just raise the quantity.
  const found = cartItems.value.find((item) => item.id === product.id);
  if (found) {
    found.quantity += 1;
  } else {
    cartItems.value.push({
      id: product.id,
      name: product.name,
      price: product.price,
      image: product.image,
      stock: product.stock,
      quantity: 1,
    });
  }

  saveCart();
}

function updateCartQuantity(productId, nextQuantity) {
  cartItems.value = cartItems.value.map((item) => {
    if (item.id !== productId) {
      return item;
    }

    return {
      ...item,
      quantity: Math.max(1, nextQuantity),
    };
  });

  saveCart();
}

function removeFromCart(productId) {
  cartItems.value = cartItems.value.filter((item) => item.id !== productId);
  saveCart();
}

function clearCart() {
  cartItems.value = [];
  saveCart();
}

function loadSession() {
  try {
    const stored = localStorage.getItem('session');
    return stored ? JSON.parse(stored) : null;
  } catch {
    return null;
  }
}

async function loadSettings() {
  try {
    const response = await get('/settings');
    const data = await response.json();
    if (response.ok) {
      settings.value = {
        store_name: data.store_name || 'Simple Shop',
        contact_email: data.contact_email || '',
      };
    }
  } catch {
    // If the API is down, keep the default shop name and email.
  }
}

function handleHashChange() {
  route.value = parseRoute();
}

function handleSettingsUpdated(nextSettings) {
  settings.value = {
    store_name: nextSettings.store_name || 'Simple Shop',
    contact_email: nextSettings.contact_email || '',
  };
}

onMounted(() => {
  window.addEventListener('hashchange', handleHashChange);
  loadSettings();
});

onBeforeUnmount(() => {
  window.removeEventListener('hashchange', handleHashChange);
});

const user = computed(() => session.value?.user || null);
const token = computed(() => session.value?.token || '');
const cartCount = computed(() =>
  cartItems.value.reduce((total, item) => total + (item.quantity || 0), 0)
);
const currentPage = computed(() => {
  if (route.value.name === 'login') return LoginPage;
  if (route.value.name === 'register') return RegisterPage;
  if (route.value.name === 'dashboard') return DashboardPage;
  if (route.value.name === 'new-product') return NewProductPage;
  if (route.value.name === 'edit-product') return EditProductPage;
  if (route.value.name === 'cart') return CartPage;
  if (route.value.name === 'detail') return ProductDetailPage;
  if (route.value.name === 'products') return ProductsPage;
  if (route.value.name === 'settings') return SettingsPage;
  if (route.value.name === 'profile') return ProfilePage;
  return ShopHomePage;
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <NavBar :user="user" :settings="settings" :cart-count="cartCount" @logout="handleLogout" />

    <main class="flex-1">
      <component
        :is="currentPage"
        :id="route.id"
        :user="user"
        :token="token"
        :settings="settings"
        :cart-items="cartItems"
        :category-id="route.categoryId"
        :page="route.page"
        @view-product="navigateToDetail"
        @back="navigateHome"
        @add-to-cart="addToCart"
        @update-cart-quantity="updateCartQuantity"
        @remove-from-cart="removeFromCart"
        @clear-cart="clearCart"
        @logged-in="handleLoginSuccess"
        @settings-updated="handleSettingsUpdated"
      />
    </main>

    <Footer :store-name="settings.store_name" :contact-email="settings.contact_email" />
  </div>
</template>
