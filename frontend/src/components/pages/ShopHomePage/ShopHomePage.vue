<template>
  <div class="min-h-screen bg-slate-50 text-slate-900 selection:bg-orange-100">
    <section class="border-b border-slate-200 bg-gradient-to-br from-orange-50 via-white to-amber-50">
      <div class="max-w-7xl mx-auto px-6 py-16 lg:px-8 lg:py-24">
        <div class="grid items-center gap-10 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-orange-600">
              {{ props.settings?.store_name || 'Online Shop' }}
            </p>
            <h1 class="mt-4 text-4xl font-bold tracking-tight text-slate-950 sm:text-6xl">
              Simple shopping experience for customers and admins.
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
              Browse products by category, manage stock, place demo orders, and review activity
              from one clean dashboard. The landing page stays simple, responsive, and clear.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
              <a href="#/products" class="rounded-lg bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Browse Products
              </a>
              <a href="#/login" class="rounded-lg border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                Admin Login
              </a>
            </div>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-4 sm:grid-cols-2">
              <div class="rounded-2xl bg-slate-100 p-4">
                <img src="/Phones.svg" alt="Phones" class="h-32 w-full object-contain" />
              </div>
              <div class="rounded-2xl bg-slate-100 p-4">
                <img src="/Laptops.svg" alt="Laptops" class="h-32 w-full object-contain" />
              </div>
              <div class="rounded-2xl bg-slate-100 p-4">
                <img src="/Headphones.svg" alt="Headphones" class="h-32 w-full object-contain" />
              </div>
              <div class="rounded-2xl bg-slate-100 p-4">
                <img src="/TVs.svg" alt="TVs" class="h-32 w-full object-contain" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 py-16 lg:px-8">
      <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-500">Categories</p>
          <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Shop by category</h2>
          <p class="mt-3 max-w-2xl text-slate-600">
            Each category uses the same reusable component. Clicking a card opens the products
            page with the matching category filter already applied.
          </p>
        </div>
        <a href="#/products" class="text-sm font-semibold text-orange-600 hover:text-orange-700">
          View all products
        </a>
      </div>

      <div v-if="loading" class="space-y-4">
        <div v-for="i in 4" :key="i" class="h-40 animate-pulse rounded-2xl bg-slate-200"></div>
      </div>

      <div v-else-if="error" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
        {{ error }}
      </div>

      <div v-else class="space-y-5">
        <CategoryTile
          v-for="category in categories"
          :key="category.id"
          :category="category"
          @select="goToCategory"
        />
      </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 pb-24 lg:px-8">
      <div class="grid gap-6 lg:grid-cols-2">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <h3 class="text-xl font-semibold text-slate-900">Application concept</h3>
          <p class="mt-3 text-sm leading-7 text-slate-600">
            This is a small e-commerce application with category browsing, a products page with
            filtering, a simple cart, order saving, and an admin dashboard for products, users,
            settings, and orders.
          </p>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
          <h3 class="text-xl font-semibold text-slate-900">Technical structure</h3>
          <p class="mt-3 text-sm leading-7 text-slate-600">
            The frontend uses reusable Vue components and routing. The backend follows a simple
            MVC structure with controllers, services, repositories, and JSON REST endpoints.
          </p>
        </article>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { get } from '../../../utils/api.js';
import CategoryTile from '../../molecules/CategoryTile.vue';

const props = defineProps({
  settings: {
    type: Object,
    default: null,
  },
});

const categories = ref([]);
const loading = ref(true);
const error = ref('');

async function loadCategories() {
  loading.value = true;
  error.value = '';

  try {
    const response = await get('/categories');
    const data = await response.json();

    if (!response.ok) {
      error.value = data.error || 'Failed to load categories';
      return;
    }

    categories.value = Array.isArray(data) ? data : [];
  } catch {
    error.value = 'Network error while loading categories';
  } finally {
    loading.value = false;
  }
}

function goToCategory(categoryId) {
  window.location.hash = `#/products?category=${categoryId}`;
}

onMounted(loadCategories);
</script>
