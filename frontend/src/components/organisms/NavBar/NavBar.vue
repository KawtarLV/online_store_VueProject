<template>
  <header class="border-b border-slate-200 bg-white" role="banner">
    <div class="max-w-6xl mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
      <a href="#/" class="flex items-center gap-3" aria-label="Go to homepage">
        <div class="grid h-10 w-10 place-items-center overflow-hidden rounded-lg border border-slate-200 bg-white">
          <img src="/online-shop.png" alt="Shop logo" class="h-full w-full object-contain p-1" />
        </div>
        <span class="font-semibold text-slate-900">{{ storeName }}</span>
      </a>

      <button
        class="rounded-lg border border-slate-300 px-3 py-2 text-sm sm:hidden"
        :aria-expanded="mobileOpen"
        aria-controls="mobile-menu"
        aria-label="Toggle navigation menu"
        @click="mobileOpen = !mobileOpen"
      >
        Menu
      </button>

      <nav class="hidden items-center gap-6 text-sm text-slate-700 sm:flex" aria-label="Main navigation">
        <a href="#/" class="hover:text-slate-900">Home</a>
        <a href="#/products" class="hover:text-slate-900">Products</a>
        <a href="#/cart" class="hover:text-slate-900" :aria-label="`Cart, ${cartCount} items`">Cart ({{ cartCount }})</a>
        <a v-if="isAdmin" href="#/dashboard" class="hover:text-slate-900">Dashboard</a>

        <template v-if="user">
          <a href="#/profile" class="hover:text-slate-900">Profile</a>

          <div v-if="isAdmin" class="relative">
            <button
              class="rounded-lg border border-slate-300 px-3 py-2 font-semibold text-slate-900 hover:bg-slate-100"
              :aria-expanded="adminOpen"
              aria-haspopup="true"
              aria-controls="admin-menu"
              @click="adminOpen = !adminOpen"
            >
              Admin
            </button>

            <div
              v-if="adminOpen"
              id="admin-menu"
              role="menu"
              class="absolute right-0 top-12 w-44 rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
            >
              <a href="#/profile" role="menuitem" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Profile</a>
              <a href="#/settings" role="menuitem" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Settings</a>
              <a href="#/admin/new-product" role="menuitem" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Add product</a>
            </div>
          </div>

          <button
            class="font-semibold text-slate-900 hover:underline"
            aria-label="Log out"
            @click="$emit('logout')"
          >
            Logout
          </button>
        </template>

        <template v-else>
          <a href="#/login" class="hover:text-slate-900">Sign in</a>
          <a href="#/register" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800">
            Register
          </a>
        </template>
      </nav>
    </div>

    <!-- Mobile menu -->
    <div
      v-if="mobileOpen"
      id="mobile-menu"
      class="border-t border-slate-200 bg-white px-4 py-4 sm:hidden"
      role="navigation"
      aria-label="Mobile navigation"
    >
      <div class="flex flex-col gap-3 text-sm text-slate-700">
        <a href="#/" @click="mobileOpen = false">Home</a>
        <a href="#/products" @click="mobileOpen = false">Products</a>
        <a href="#/cart" @click="mobileOpen = false" :aria-label="`Cart, ${cartCount} items`">Cart ({{ cartCount }})</a>
        <a v-if="user" href="#/profile" @click="mobileOpen = false">Profile</a>
        <a v-if="isAdmin" href="#/dashboard" @click="mobileOpen = false">Dashboard</a>
        <a v-if="isAdmin" href="#/settings" @click="mobileOpen = false">Settings</a>
        <a v-if="isAdmin" href="#/admin/new-product" @click="mobileOpen = false">Add product</a>
        <a v-if="!user" href="#/login" @click="mobileOpen = false">Sign in</a>
        <a v-if="!user" href="#/register" @click="mobileOpen = false">Register</a>
        <button
          v-if="user"
          class="text-left font-semibold text-slate-900"
          aria-label="Log out"
          @click="$emit('logout')"
        >
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue';

/**
 * NavBar component
 * The main site header with navigation links, cart count, and user/admin menus
 * Includes a responsive mobile menu that toggles on smaller screens
 *
 * @prop {Object|null} user - the logged-in user object, or null if not logged in
 * @prop {Object|null} settings - store settings (used for the store name in the logo)
 * @prop {Number} cartCount - number of items currently in the cart
 * @emits logout - emitted when the logout button is clicked
 */

const props = defineProps({
  /** Logged-in user, or null */
  user: {
    type: Object,
    default: null,
  },
  /** Store settings (store_name used for the logo label) */
  settings: {
    type: Object,
    default: null,
  },
  /** Number of items in the cart */
  cartCount: {
    type: Number,
    default: 0,
  },
});

defineEmits(['logout']);

const mobileOpen = ref(false);
const adminOpen  = ref(false);

/** Whether the current user has admin role */
const isAdmin = computed(() => props.user?.role === 'admin');

/** Store name shown next to the logo, falls back to 'Simple Shop' */
const storeName = computed(() => props.settings?.store_name || 'Simple Shop');
</script>
