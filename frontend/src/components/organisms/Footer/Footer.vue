<template>
  <footer class="bg-slate-900 text-slate-100 mt-12">
    <div class="max-w-6xl mx-auto px-4 py-10 flex flex-col sm:flex-row items-center justify-between text-sm space-y-3 sm:space-y-0">
      <div class="flex items-center space-x-2">
        <span class="h-9 w-9 rounded-lg bg-white text-slate-900 grid place-items-center text-sm font-semibold">SS</span>
        <span class="font-semibold">Simple Shop</span>
      </div>
      <p class="text-slate-300">Dummy JSON API + Vue frontend starter</p>
      <p class="text-slate-400">© {{ currentYear }} Simple Shop</p>
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear();
</script>
