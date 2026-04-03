<template>
  <div class="rounded-lg border border-slate-200 bg-white p-6">
    <div class="mb-3 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-slate-900">My Orders</h3>
      <button
        v-if="token"
        class="text-sm font-semibold text-slate-700 hover:text-slate-900"
        @click="loadOrders"
      >
        Refresh
      </button>
    </div>

    <p v-if="!token" class="text-sm text-slate-600">Login to see your saved orders.</p>
    <p v-else-if="loading" class="text-sm text-slate-600">Loading orders...</p>
    <p v-else-if="error" class="text-sm text-red-600">{{ error }}</p>
    <div v-else-if="orders.length === 0" class="text-sm text-slate-600">No orders yet.</div>

    <div v-else class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="rounded-xl border border-slate-200 p-4"
      >
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="font-semibold text-slate-900">Order #{{ order.id }}</p>
            <p class="text-xs text-slate-500">{{ order.created_at || 'No date' }}</p>
          </div>
          <div class="text-sm text-slate-700">
            <span class="font-semibold">${{ Number(order.total).toFixed(2) }}</span>
            · {{ order.status }}
          </div>
        </div>

        <div class="mt-3 space-y-1 text-sm text-slate-600">
          <p v-for="item in order.items" :key="item.id">
            {{ item.product_name }} x{{ item.quantity }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { get } from '../../utils/api.js';

const props = defineProps({
  token: {
    type: String,
    default: '',
  },
  refreshKey: {
    type: Number,
    default: 0,
  },
});

const orders = ref([]);
const loading = ref(false);
const error = ref('');

async function loadOrders() {
  if (!props.token) {
    orders.value = [];
    return;
  }

  loading.value = true;
  error.value = '';
  try {
    const response = await get('/my-orders', {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    const data = await response.json().catch(() => []);
    if (!response.ok) {
      error.value = data.error || 'Failed to load orders';
      return;
    }
    orders.value = Array.isArray(data) ? data : [];
  } catch {
    error.value = 'Network error while loading orders';
  } finally {
    loading.value = false;
  }
}

watch(() => props.refreshKey, loadOrders);
watch(() => props.token, loadOrders);
onMounted(loadOrders);
</script>
