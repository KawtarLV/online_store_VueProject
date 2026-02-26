<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <NavBar />

    <main class="flex-1">
      <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
          <p class="text-sm uppercase tracking-[0.2em] text-slate-300 mb-3">Starter</p>
          <h1 class="text-3xl sm:text-4xl font-bold mb-3">Simple shop blueprint</h1>
          <p class="text-slate-200 max-w-2xl">
            PHP MVC backend + JSON dummy DB, consumed by a Vue frontend. Use this as a foundation and extend.
          </p>
          <div class="mt-6 flex gap-3">
            <a href="#products" class="inline-flex items-center px-4 py-2 bg-white text-slate-900 font-semibold rounded-lg shadow-sm hover:-translate-y-0.5 transform transition">
              View products
            </a>
            <a href="http://localhost/products" class="inline-flex items-center px-4 py-2 border border-white/60 text-white font-semibold rounded-lg hover:bg-white/10 transition">
              API: /products
            </a>
          </div>
        </div>
      </section>

      <section id="products" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl font-semibold text-slate-900">Products</h2>
            <p class="text-sm text-slate-600">Served from the PHP dummy store.</p>
          </div>
          <button @click="loadProducts" class="px-3 py-2 text-sm font-medium border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-100">
            Refresh
          </button>
        </div>

        <div v-if="loading" class="text-slate-600">Loading products...</div>
        <div v-else-if="error" class="text-red-600">{{ error }}</div>
        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
            @view="emit('view-product', $event)"
          />
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { get } from '../../../utils/api.js';
import NavBar from '../../organisms/NavBar/NavBar.vue';
import Footer from '../../organisms/Footer/Footer.vue';
import ProductCard from '../../molecules/ProductCard/ProductCard.vue';

const emit = defineEmits(['view-product']);

const products = ref([]);
const loading = ref(true);
const error = ref('');

async function loadProducts() {
  loading.value = true;
  error.value = '';
  try {
    const response = await get('/products');
    if (!response.ok) throw new Error(`API responded with ${response.status}`);
    products.value = await response.json();
  } catch (err) {
    error.value = err.message || 'Failed to load products';
  } finally {
    loading.value = false;
  }
}

onMounted(loadProducts);
</script>
