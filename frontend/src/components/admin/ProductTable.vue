<template>
  <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold text-slate-900">Products</h3>
      <button
        class="text-sm text-slate-600 hover:text-slate-900"
        @click="$emit('refresh')"
      >
        Refresh
      </button>
    </div>
    <div v-if="loading" class="text-slate-600 text-sm">Loading products…</div>
    <div v-else-if="error" class="text-red-600 text-sm">{{ error }}</div>
    <div v-else class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-slate-500 border-b">
            <th class="py-2 pr-3">Image</th>
            <th class="py-2 pr-3">Name</th>
            <th class="py-2 pr-3">Category</th>
            <th class="py-2 pr-3">Price</th>
            <th class="py-2 pr-3">Stock</th>
            <th class="py-2 pr-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="prod in products" :key="prod.id" class="border-b last:border-0">
            <td class="py-2 pr-3">
              <img
                :src="resolveImage(prod.image)"
                :alt="prod.name"
                class="h-12 w-12 rounded object-cover bg-slate-100"
                loading="lazy"
              />
            </td>
            <td class="py-2 pr-3 font-semibold text-slate-900">{{ prod.name }}</td>
            <td class="py-2 pr-3 text-slate-700">{{ categoryName(prod.category_id ?? prod.categoryId) }}</td>
            <td class="py-2 pr-3 text-slate-900 font-semibold">${{ prod.price?.toFixed?.(2) ?? prod.price }}</td>
            <td class="py-2 pr-3 text-slate-700">{{ prod.stock }}</td>
            <td class="py-2 pr-3 space-x-2">
              <button
                class="text-sm text-slate-700 hover:underline"
                @click="$emit('edit', prod.id)"
              >
                Edit
              </button>
              <button
                class="text-sm text-red-600 hover:underline"
                @click="$emit('delete', prod.id)"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="products.length === 0">
            <td colspan="6" class="py-4 text-center text-slate-600">No products found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import config from '../../config.js';

const props = defineProps({
  products: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  error: { type: String, default: '' },
  categories: { type: Array, default: () => [] },
});

defineEmits(['refresh', 'delete', 'edit']);

function resolveImage(src) {
  if (!src) return '';
  if (src.startsWith('http://') || src.startsWith('https://')) return src;
  const base = config.apiDomain.replace(/\/$/, '');
  return `${base}${src.startsWith('/') ? '' : '/'}${src}`;
}

function categoryName(id) {
  if (!id) return '—';
  const found = props.categories.find((c) => c.id === id);
  return found ? found.name : id;
}
</script>
