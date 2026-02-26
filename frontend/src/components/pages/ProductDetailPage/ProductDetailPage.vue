<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <NavBar />

    <main class="flex-1">
      <section class="bg-slate-900 text-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <p class="text-sm uppercase tracking-[0.2em] text-slate-300 mb-3">Product detail</p>
          <h1 class="text-3xl sm:text-4xl font-bold">{{ product?.name || 'Loading…' }}</h1>
          <p class="text-slate-300 mt-3 max-w-2xl">Data served directly from the PHP dummy API.</p>
        </div>
      </section>

      <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div v-if="loading" class="text-slate-600">Loading product…</div>
        <div v-else-if="error" class="text-red-600">{{ error }}</div>
        <div v-else-if="product" class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
          <div>
            <img :src="product.image" :alt="product.name" class="w-full rounded-xl shadow-sm bg-slate-100 object-cover max-h-[420px]" />
          </div>
          <div class="space-y-5">
            <div>
              <h2 class="text-2xl font-semibold text-slate-900">{{ product.name }}</h2>
              <p class="text-slate-600 mt-2">{{ product.description }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-3xl font-bold text-slate-900">${{ product.price.toFixed(2) }}</span>
              <span class="text-sm text-slate-600">Stock: {{ product.stock }}</span>
            </div>
            <div class="flex gap-3">
              <button class="px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800">Add to cart</button>
              <button @click="$emit('back')" class="px-4 py-2 border border-slate-300 rounded-lg font-semibold text-slate-800 hover:bg-slate-100">Back to shop</button>
            </div>
          </div>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { get } from '../../../utils/api.js';
import NavBar from '../../organisms/NavBar/NavBar.vue';
import Footer from '../../organisms/Footer/Footer.vue';

const props = defineProps({
  id: {
    type: [String, Number],
    required: true,
  },
});

defineEmits(['back']);

const product = ref(null);
const loading = ref(true);
const error = ref('');

async function loadProduct(productId) {
  if (!productId) return;
  loading.value = true;
  error.value = '';
  try {
    const response = await get(`/products/${productId}`);
    if (!response.ok) throw new Error(`API responded with ${response.status}`);
    product.value = await response.json();
  } catch (err) {
    error.value = err.message || 'Failed to load product';
  } finally {
    loading.value = false;
  }
}

onMounted(() => loadProduct(props.id));
watch(() => props.id, (newId) => loadProduct(newId));
</script>
