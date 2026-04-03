<template>
  <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
    <div class="mb-3 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-slate-900" id="orders-table-heading">Orders</h3>
      <button
        class="text-sm text-slate-600 hover:text-slate-900"
        aria-label="Refresh order list"
        @click="$emit('refresh')"
      >
        Refresh
      </button>
    </div>

    <div v-if="loading" class="text-sm text-slate-600" role="status" aria-live="polite">Loading orders…</div>
    <div v-else-if="error" class="text-sm text-red-600" role="alert">{{ error }}</div>
    <div v-else class="overflow-x-auto">
      <table class="min-w-full text-sm" aria-labelledby="orders-table-heading">
        <thead>
          <tr class="border-b text-left text-slate-500">
            <th scope="col" class="py-2 pr-3">Order</th>
            <th scope="col" class="py-2 pr-3">Customer</th>
            <th scope="col" class="py-2 pr-3">Items</th>
            <th scope="col" class="py-2 pr-3">Total</th>
            <th scope="col" class="py-2 pr-3">Status</th>
            <th scope="col" class="py-2 pr-3">Date</th>
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
/**
 * OrderTable component
 * Admin table for viewing all orders, including customer details and order items
 *
 * @prop {Array} orders - list of order objects to display
 * @prop {Boolean} loading - shows a loading message when true
 * @prop {String} error - shows an error message if non-empty
 * @emits refresh - emitted when the refresh button is clicked
 */

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
