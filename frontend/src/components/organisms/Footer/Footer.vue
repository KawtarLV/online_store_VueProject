<template>
  <footer class="bg-slate-900 text-slate-100 mt-12">
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
defineProps({
  storeName: {
    type: String,
    default: 'Simple Shop',
  },
  contactEmail: {
    type: String,
    default: '',
  },
});

const currentYear = new Date().getFullYear();
</script>
