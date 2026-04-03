<template>
  <header class="border-b border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
      <a href="#/" class="flex items-center gap-3">
        <div class="grid h-10 w-10 place-items-center overflow-hidden rounded-lg border border-slate-200 bg-white">
          <img src="/online-shop.png" alt="Shop logo" class="h-full w-full object-contain p-1" />
        </div>
        <span class="font-semibold text-slate-900">{{ storeName }}</span>
      </a>

      <button class="rounded-lg border border-slate-300 px-3 py-2 text-sm sm:hidden" @click="mobileOpen = !mobileOpen">
        Menu
      </button>

      <nav class="hidden items-center gap-6 text-sm text-slate-700 sm:flex">
        <a href="#/" class="hover:text-slate-900">Home</a>
        <a href="#/products" class="hover:text-slate-900">Products</a>
        <a href="#/cart" class="hover:text-slate-900">Cart ({{ cartCount }})</a>
        <a v-if="isAdmin" href="#/dashboard" class="hover:text-slate-900">Dashboard</a>

        <template v-if="user">
          <a href="#/profile" class="hover:text-slate-900">Profile</a>

          <div v-if="isAdmin" class="relative">
            <button
              class="rounded-lg border border-slate-300 px-3 py-2 font-semibold text-slate-900 hover:bg-slate-100"
              @click="adminOpen = !adminOpen"
            >
              Admin
            </button>

            <div
              v-if="adminOpen"
              class="absolute right-0 top-12 w-44 rounded-xl border border-slate-200 bg-white p-2 shadow-lg"
            >
              <a href="#/profile" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Profile</a>
              <a href="#/settings" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Settings</a>
              <a href="#/admin/new-product" class="block rounded-lg px-3 py-2 hover:bg-slate-100">Add product</a>
            </div>
          </div>

          <button class="font-semibold text-slate-900 hover:underline" @click="$emit('logout')">
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

    <div v-if="mobileOpen" class="border-t border-slate-200 bg-white px-4 py-4 sm:hidden">
      <div class="flex flex-col gap-3 text-sm text-slate-700">
        <a href="#/" @click="mobileOpen = false">Home</a>
        <a href="#/products" @click="mobileOpen = false">Products</a>
        <a href="#/cart" @click="mobileOpen = false">Cart ({{ cartCount }})</a>
        <a v-if="user" href="#/profile" @click="mobileOpen = false">Profile</a>
        <a v-if="isAdmin" href="#/dashboard" @click="mobileOpen = false">Dashboard</a>
        <a v-if="isAdmin" href="#/settings" @click="mobileOpen = false">Settings</a>
        <a v-if="isAdmin" href="#/admin/new-product" @click="mobileOpen = false">Add product</a>
        <a v-if="!user" href="#/login" @click="mobileOpen = false">Sign in</a>
        <a v-if="!user" href="#/register" @click="mobileOpen = false">Register</a>
        <button v-if="user" class="text-left font-semibold text-slate-900" @click="$emit('logout')">
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  settings: {
    type: Object,
    default: null,
  },
  cartCount: {
    type: Number,
    default: 0,
  },
});

defineEmits(['logout']);

const mobileOpen = ref(false);
const adminOpen = ref(false);
const isAdmin = computed(() => props.user?.role === 'admin');
const storeName = computed(() => props.settings?.store_name || 'Simple Shop');
</script>
