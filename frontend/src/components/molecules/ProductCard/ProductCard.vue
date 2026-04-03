<template>
  <article class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
    <img :src="resolvedImage" :alt="product.name" class="h-40 w-full object-cover bg-slate-100" loading="lazy" />
    <div class="p-4 space-y-2">
      <h3 class="text-base font-semibold text-slate-900">{{ product.name }}</h3>
      <p class="text-sm text-slate-600 line-clamp-2">{{ product.description }}</p>
      <div class="flex items-center justify-between pt-2">
        <span class="text-lg font-bold text-slate-900">${{ product.price.toFixed(2) }}</span>
        <div class="flex gap-2">
          <button class="px-3 py-2 text-sm font-medium border border-slate-300 rounded-lg hover:bg-slate-100" @click="$emit('view', product.id)">
            View
          </button>
          <button class="px-3 py-2 text-sm font-medium bg-slate-900 text-white rounded-lg hover:bg-slate-800" @click="$emit('add-to-cart', product)">
            Add
          </button>
        </div>
      </div>
      <p class="text-xs text-slate-500">In stock: {{ product.stock }}</p>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import config from '../../../config.js';

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
});

const resolvedImage = computed(() => {
  const src = props.product?.image || '';
  if (!src) return '';
  if (src.startsWith('http://') || src.startsWith('https://')) return src;
  const base = config.apiDomain.replace(/\/$/, '');
  return `${base}${src.startsWith('/') ? '' : '/'}${src}`;
});

defineEmits(['view', 'add-to-cart']);
</script>
