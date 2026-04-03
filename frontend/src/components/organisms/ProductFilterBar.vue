<template>
  <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" aria-label="Product filters">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-900">Filter products</h2>
        <p class="text-sm text-slate-600">Pick a category or show everything.</p>
      </div>

      <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <!-- Dropdown version of the filter (for smaller screens or keyboard users) -->
        <label for="category-select" class="sr-only">Filter by category</label>
        <select
          id="category-select"
          :value="modelValue ?? ''"
          class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
          aria-label="Filter by category"
          @change="onSelect"
        >
          <option value="">All categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>

        <!-- Button-based filter (visual shortcut) -->
        <div class="flex flex-wrap gap-2" role="group" aria-label="Category filter buttons">
          <button
            class="rounded-lg px-3 py-2 text-sm font-medium"
            :class="buttonClass(null)"
            :aria-pressed="modelValue === null"
            aria-label="Show all categories"
            @click="$emit('update:modelValue', null)"
          >
            All
          </button>
          <button
            v-for="category in categories"
            :key="category.id"
            class="rounded-lg px-3 py-2 text-sm font-medium"
            :class="buttonClass(category.id)"
            :aria-pressed="modelValue === category.id"
            :aria-label="`Filter by ${category.name}`"
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
/**
 * ProductFilterBar component
 * Shows a category filter with both a dropdown (accessible) and button group (visual)
 * Uses v-model to pass the selected category ID up to the parent
 *
 * @prop {Array} categories - list of category objects with id and name
 * @prop {Number|null} modelValue - the currently selected category ID, or null for all
 * @emits update:modelValue - emitted when the selected category changes (v-model compatible)
 */

const props = defineProps({
  /** List of categories to display as filter options */
  categories: {
    type: Array,
    default: () => [],
  },
  /** Currently selected category ID (null = show all) */
  modelValue: {
    type: Number,
    default: null,
  },
});

const emit = defineEmits(['update:modelValue']);

/**
 * Handles the dropdown change event
 * Converts the string value from the select to a number (or null for "all")
 *
 * @param {Event} event
 */
function onSelect(event) {
  const value  = event.target.value;
  const parsed = value === '' ? null : Number(value);
  emit('update:modelValue', parsed);
}

/**
 * Returns the CSS classes for a category filter button
 * Active button gets a dark background, inactive ones get a border
 *
 * @param {Number|null} categoryId
 * @returns {string}
 */
function buttonClass(categoryId) {
  const active = props.modelValue === categoryId;
  return active
    ? 'bg-slate-900 text-white'
    : 'border border-slate-300 text-slate-700 hover:bg-slate-100';
}
</script>
