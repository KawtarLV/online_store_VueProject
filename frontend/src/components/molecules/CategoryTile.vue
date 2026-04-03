<template>
  <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:flex-row md:items-center">
    <div class="flex h-28 w-full shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-100 md:w-40">
      <img :src="imageSrc" :alt="category.name" class="h-full w-full object-contain p-4" />
    </div>

    <div class="flex-1">
      <p class="text-xs font-semibold uppercase tracking-[0.2em] text-orange-500">Category</p>
      <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ category.name }}</h3>
      <p class="mt-2 text-sm text-slate-600">
        Browse all {{ category.name.toLowerCase() }} products.
      </p>
      <button
        class="mt-4 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
        @click="$emit('select', category.id)"
      >
        View {{ category.name }}
      </button>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  category: {
    type: Object,
    required: true,
  },
});

const imageSrc = computed(() => `/${props.category.name}.svg`);

defineEmits(['select']);
</script>
