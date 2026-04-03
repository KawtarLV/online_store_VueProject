<template>
  <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-500">Catalog</p>
      <h1 class="mt-2 text-3xl font-semibold text-slate-900">Products</h1>
      <p class="mt-2 text-sm text-slate-600">
        Browse products and filter by category.
      </p>
    </div>

    <ProductFilterBar
      :categories="categories"
      :model-value="selectedCategory"
      @update:model-value="applyCategory"
    />

    <div class="mt-8">
      <div v-if="loading" class="text-slate-600">Loading products...</div>
      <div v-else-if="error" class="text-red-600">{{ error }}</div>
      <div v-else>
        <div v-if="products.length === 0" class="rounded-2xl border border-slate-200 bg-white p-8 text-slate-600">
          No products found for this filter.
        </div>

        <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
            @view="$emit('view-product', $event)"
            @add-to-cart="$emit('add-to-cart', $event)"
          />
        </div>

        <div class="mt-8 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm text-slate-600">
            Page {{ meta.page }} of {{ meta.total_pages || 1 }} · {{ meta.total }} total products
          </p>

          <div class="flex gap-3">
            <button
              class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="meta.page <= 1"
              @click="changePage(meta.page - 1)"
            >
              Previous
            </button>
            <button
              class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
              :disabled="meta.page >= (meta.total_pages || 1)"
              @click="changePage(meta.page + 1)"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { get } from '../../../utils/api.js';
import ProductCard from '../../molecules/ProductCard/ProductCard.vue';
import ProductFilterBar from '../../organisms/ProductFilterBar.vue';

const props = defineProps({
  categoryId: {
    type: Number,
    default: null,
  },
  page: {
    type: Number,
    default: 1,
  },
});

defineEmits(['view-product', 'add-to-cart']);

const categories = ref([]);
const products = ref([]);
const loading = ref(false);
const error = ref('');
const selectedCategory = ref(props.categoryId);
const meta = ref({
  page: 1,
  per_page: 9,
  total: 0,
  total_pages: 1,
});

async function loadCategories() {
  try {
    const response = await get('/categories');
    const data = await response.json();
    if (response.ok) {
      categories.value = Array.isArray(data) ? data : [];
    }
  } catch {
    categories.value = [];
  }
}

async function loadProducts() {
  loading.value = true;
  error.value = '';

  const query = new URLSearchParams();
  query.set('page', String(props.page || 1));
  query.set('per_page', '9');
  if (selectedCategory.value) {
    query.set('category', String(selectedCategory.value));
  }

  try {
    const response = await get(`/products?${query.toString()}`);
    const data = await response.json();

    if (!response.ok) {
      error.value = data.error || 'Failed to load products';
      return;
    }

    products.value = Array.isArray(data.items) ? data.items : [];
    meta.value = {
      page: data.meta?.page || 1,
      per_page: data.meta?.per_page || 9,
      total: data.meta?.total || 0,
      total_pages: data.meta?.total_pages || 1,
    };
  } catch {
    error.value = 'Network error while loading products';
  } finally {
    loading.value = false;
  }
}

function applyCategory(categoryId) {
  const query = new URLSearchParams();
  if (categoryId) {
    query.set('category', String(categoryId));
  }
  query.set('page', '1');
  window.location.hash = `#/products?${query.toString()}`;
}

function changePage(page) {
  const query = new URLSearchParams();
  if (selectedCategory.value) {
    query.set('category', String(selectedCategory.value));
  }
  query.set('page', String(page));
  window.location.hash = `#/products?${query.toString()}`;
}

watch(
  () => [props.categoryId, props.page],
  () => {
    selectedCategory.value = props.categoryId;
    loadProducts();
  },
  { immediate: true }
);

onMounted(loadCategories);
</script>
