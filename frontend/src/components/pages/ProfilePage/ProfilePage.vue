<template>
  <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
      <h1 class="text-3xl font-semibold text-slate-900">Profile</h1>
      <p class="mt-2 text-sm text-slate-600">Basic information about the current session.</p>

      <div v-if="!user" class="mt-6 text-slate-600">You are not signed in.</div>

      <dl v-else class="mt-6 grid gap-6 md:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-slate-500">Name</dt>
          <dd class="mt-1 text-lg font-semibold text-slate-900">{{ user.name }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-slate-500">Email</dt>
          <dd class="mt-1 text-lg font-semibold text-slate-900">{{ user.email }}</dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-slate-500">Role</dt>
          <dd class="mt-1 text-lg font-semibold capitalize text-slate-900">{{ user.role }}</dd>
        </div>
      </dl>
    </div>
  </section>
</template>

<script setup>
defineProps({
  user: {
    type: Object,
    default: null,
  },
});
</script>
