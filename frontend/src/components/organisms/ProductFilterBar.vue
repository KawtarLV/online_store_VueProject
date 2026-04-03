<template>
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Filter products</h2>
        <p class="text-sm text-slate-600">Pick a category or show everything.</p>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <select
          :value="modelValue ?? ''"
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
          @change="onSelect"
        >
          <option value="">All categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>

        <div class="flex flex-wrap gap-2">
          <button
            class="rounded-lg px-3 py-2 text-sm font-medium"
            :class="buttonClass(null)"
            @click="$emit('update:modelValue', null)"
          >
            All
          </button>
          <button
            v-for="category in categories"
            :key="category.id"
            class="rounded-lg px-3 py-2 text-sm font-medium"
            :class="buttonClass(category.id)"
            @click="$emit('update:modelValue', category.id)"
          >
            {{ category.name }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  categories: {
    type: Array,
    default: () => [],
  },
  modelValue: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(['update:modelValue']);

function onSelect(event) {
  const value = event.target.value;
  const parsed = value === '' ? null : Number(value);
  emit('update:modelValue', parsed);
}

function buttonClass(categoryId) {
  const active = props.modelValue === categoryId;
  return active
    ? 'bg-slate-900 text-white'
    : 'border border-slate-300 text-slate-700 hover:bg-slate-100';
}
</script>
