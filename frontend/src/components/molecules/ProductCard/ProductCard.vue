<template>
  <article class="border border-slate-200 rounded-xl overflow-hidden bg-white shadow-sm hover:shadow-md transition-shadow">
    <img :src="product.image" :alt="product.name" class="h-40 w-full object-cover bg-slate-100" loading="lazy" />
    <div class="p-4 space-y-2">
      <h3 class="text-base font-semibold text-slate-900">{{ product.name }}</h3>
      <p class="text-sm text-slate-600 line-clamp-2">{{ product.description }}</p>
      <div class="flex items-center justify-between pt-2">
        <span class="text-lg font-bold text-slate-900">${{ product.price.toFixed(2) }}</span>
        <button class="px-3 py-2 text-sm font-medium bg-slate-900 text-white rounded-lg hover:bg-slate-800" @click="$emit('view', product.id)">
          View
        </button>
      </div>
      <p class="text-xs text-slate-500">In stock: {{ product.stock }}</p>
    </div>
  </article>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true,
  },
});

defineEmits(['view']);
</script>
