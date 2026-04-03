<template>
  <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <div class="mb-3 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-slate-900">Orders</h3>
      <button class="text-sm text-slate-600 hover:text-slate-900" @click="$emit('refresh')">
        Refresh
      </button>
    </div>

    <div v-if="loading" class="text-sm text-slate-600">Loading orders…</div>
    <div v-else-if="error" class="text-sm text-red-600">{{ error }}</div>
    <div v-else class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="border-b text-left text-slate-500">
            <th class="py-2 pr-3">Order</th>
            <th class="py-2 pr-3">Customer</th>
            <th class="py-2 pr-3">Items</th>
            <th class="py-2 pr-3">Total</th>
            <th class="py-2 pr-3">Status</th>
            <th class="py-2 pr-3">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in orders" :key="order.id" class="border-b last:border-0 align-top">
            <td class="py-2 pr-3 font-semibold text-slate-900">#{{ order.id }}</td>
            <td class="py-2 pr-3 text-slate-700">
              <p>{{ order.user_name }}</p>
              <p class="text-xs text-slate-500">{{ order.user_email }}</p>
            </td>
            <td class="py-2 pr-3 text-slate-700">
              <p v-for="item in order.items" :key="item.id">
                {{ item.product_name }} x{{ item.quantity }}
              </p>
            </td>
            <td class="py-2 pr-3 font-semibold text-slate-900">${{ Number(order.total).toFixed(2) }}</td>
            <td class="py-2 pr-3 text-slate-700">
              <p>{{ order.status }}</p>
              <p class="text-xs text-slate-500">{{ order.payment_status }}</p>
            </td>
            <td class="py-2 pr-3 text-slate-700">{{ order.created_at || '—' }}</td>
          </tr>
          <tr v-if="orders.length === 0">
            <td colspan="6" class="py-4 text-center text-slate-600">No orders found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
defineProps({
  orders: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: '',
  },
});

defineEmits(['refresh']);
</script>
