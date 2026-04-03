<template>
  <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold text-slate-900" id="products-table-heading">Products</h3>
      <button
        class="text-sm text-slate-600 hover:text-slate-900"
        aria-label="Refresh product list"
        @click="$emit('refresh')"
      >
        Refresh
      </button>
    </div>
    <div v-if="loading" class="text-slate-600 text-sm" role="status" aria-live="polite">Loading products…</div>
    <div v-else-if="error" class="text-red-600 text-sm" role="alert">{{ error }}</div>
    <div v-else class="overflow-x-auto">
      <table class="min-w-full text-sm" aria-labelledby="products-table-heading">
        <thead>
          <tr class="text-left text-slate-500 border-b">
            <th scope="col" class="py-2 pr-3">Image</th>
            <th scope="col" class="py-2 pr-3">Name</th>
            <th scope="col" class="py-2 pr-3">Category</th>
            <th scope="col" class="py-2 pr-3">Price</th>
            <th scope="col" class="py-2 pr-3">Stock</th>
            <th scope="col" class="py-2 pr-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="prod in products" :key="prod.id" class="border-b last:border-0">
            <td class="py-2 pr-3">
              <img
                :src="resolveImage(prod.image)"
                :alt="`Product image for ${prod.name}`"
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
                :aria-label="`Edit ${prod.name}`"
                @click="$emit('edit', prod.id)"
              >
                Edit
              </button>
              <button
                class="text-sm text-red-600 hover:underline"
                :aria-label="`Delete ${prod.name}`"
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

/**
 * ProductTable component
 * Admin table for viewing, editing, and deleting products
 *
 * @prop {Array} products - list of product objects to display
 * @prop {Boolean} loading - shows a loading message when true
 * @prop {String} error - shows an error message if non-empty
 * @prop {Array} categories - list of categories used to display the category name
 * @emits refresh - emitted when the refresh button is clicked
 * @emits edit - emitted when "Edit" is clicked, carries the product ID
 * @emits delete - emitted when "Delete" is clicked, carries the product ID
 */

const props = defineProps({
  products:   { type: Array,   required: true },
  loading:    { type: Boolean, default: false },
  error:      { type: String,  default: '' },
  categories: { type: Array,   default: () => [] },
});

defineEmits(['refresh', 'delete', 'edit']);

/**
 * Resolves the product image URL
 * Relative paths are prefixed with the API domain from config
 *
 * @param {string} src
 * @returns {string}
 */
function resolveImage(src) {
  if (!src) return '';
  if (src.startsWith('http://') || src.startsWith('https://')) return src;
  const base = config.apiDomain.replace(/\/$/, '');
  return `${base}${src.startsWith('/') ? '' : '/'}${src}`;
}

/**
 * Looks up a category name by ID
 * Returns "—" if the ID is missing or not found
 *
 * @param {Number} id
 * @returns {string}
 */
function categoryName(id) {
  if (!id) return '—';
  const found = props.categories.find((c) => c.id === id);
  return found ? found.name : id;
}
</script>
