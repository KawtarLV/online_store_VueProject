<template>
  <footer class="bg-slate-900 text-slate-100 mt-12" role="contentinfo" aria-label="Site footer">
    <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col sm:flex-row items-center justify-between text-sm space-y-3 sm:space-y-0">
      <div class="flex items-center space-x-2">
        <div class="grid h-10 w-10 place-items-center overflow-hidden rounded-lg bg-white">
          <img src="/online-shop.png" alt="Shop logo" class="h-full w-full object-contain p-1" />
        </div>
        <span class="font-semibold">{{ storeName }}</span>
      </div>
      <p class="text-slate-300">Contact Us : {{ contactEmail || 'No contact email set' }}</p>
      <p class="text-slate-400">© {{ currentYear }} {{ storeName }}</p>
    </div>
  </footer>
</template>

<script setup>
/**
 * Footer component
 * Displays the store logo, contact email, and copyright year
 *
 * @prop {String} storeName - the name of the store
 * @prop {String} contactEmail - the store's contact email address
 */

defineProps({
  /** Store name shown in the footer */
  storeName: {
    type: String,
    default: 'Simple Shop',
  },
  /** Contact email shown in the footer */
  contactEmail: {
    type: String,
    default: '',
  },
});

/** Current year for the copyright notice */
const currentYear = new Date().getFullYear();
</script>
