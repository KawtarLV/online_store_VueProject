<template>
  <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
      <p class="text-sm font-semibold uppercase tracking-[0.2em] text-orange-500">Admin</p>
      <h1 class="mt-2 text-3xl font-semibold text-slate-900">Store settings</h1>
      <p class="mt-2 text-sm text-slate-600">
        Update the store name and contact email.
      </p>
    </div>

    <div v-if="!isAdmin" class="rounded-2xl border border-red-200 bg-white p-6 text-red-700">
      Access denied. Only admins can update settings.
    </div>

    <form v-else class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" @submit.prevent="save">
      <div class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Store name</label>
          <input
            v-model="form.store_name"
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-700">Contact email</label>
          <input
            v-model="form.contact_email"
            type="email"
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-slate-500 focus:outline-none"
          />
        </div>
      </div>

      <div class="mt-6 flex items-center gap-3">
        <button
          type="submit"
          class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-60"
          :disabled="saving"
        >
          {{ saving ? 'Saving...' : 'Save settings' }}
        </button>
        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        <p v-if="success" class="text-sm text-green-600">Settings updated.</p>
      </div>
    </form>
  </section>
</template>

<script setup>
import { computed, reactive, watch, ref } from 'vue';
import { put } from '../../../utils/api.js';

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  token: {
    type: String,
    default: '',
  },
  settings: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['settings-updated']);

const form = reactive({
  store_name: '',
  contact_email: '',
});

const saving = ref(false);
const success = ref(false);
const error = ref('');
const isAdmin = computed(() => props.user?.role === 'admin');

watch(
  () => props.settings,
  (value) => {
    form.store_name = value?.store_name || '';
    form.contact_email = value?.contact_email || '';
  },
  { immediate: true }
);

async function save() {
  error.value = '';
  success.value = false;

  try {
    const response = await put('/settings', { ...form }, {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    const data = await response.json();

    if (!response.ok) {
      error.value = data.error || 'Save failed';
      return;
    }

    success.value = true;
    emit('settings-updated', data);
  } catch {
    error.value = 'Network error while saving settings';
  }
}
</script>
