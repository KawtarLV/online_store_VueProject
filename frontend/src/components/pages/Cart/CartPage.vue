<template>
  <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-slate-900">Your Cart</h2>
        <p class="text-sm text-slate-600">Simple checkout flow with a saved order and a success message.</p>
      </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
      <div class="rounded-lg border border-slate-200 bg-white p-6">
        <h3 class="mb-2 text-lg font-semibold text-slate-900">Customer info</h3>
        <p class="text-slate-700">Name: <span class="font-medium">{{ user?.name || 'Guest user' }}</span></p>
        <p class="text-slate-700">Email: <span class="font-medium">{{ user?.email || 'Not signed in' }}</span></p>
        <p class="text-slate-700">Role: <span class="font-medium capitalize">{{ user?.role || 'guest' }}</span></p>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white p-6">
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-slate-900">Cart items</h3>
          <button
            v-if="cartItems.length > 0"
            class="text-sm font-semibold text-slate-700 hover:text-slate-900"
            @click="$emit('clear-cart')"
          >
            Clear cart
          </button>
        </div>

        <div v-if="cartItems.length === 0" class="text-slate-600">
          Your cart is empty.
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="item in cartItems"
            :key="item.id"
            class="flex items-center gap-4 rounded-xl border border-slate-200 p-3"
          >
            <img :src="resolveImage(item.image)" :alt="item.name" class="h-20 w-20 rounded-lg bg-slate-100 object-cover" />
            <div class="flex-1">
              <p class="font-semibold text-slate-900">{{ item.name }}</p>
              <p class="text-sm text-slate-600">${{ Number(item.price).toFixed(2) }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button class="h-8 w-8 rounded-lg border border-slate-300 hover:bg-slate-100" @click="changeQuantity(item, item.quantity - 1)">
                -
              </button>
              <span class="w-6 text-center text-sm font-semibold">{{ item.quantity }}</span>
              <button class="h-8 w-8 rounded-lg border border-slate-300 hover:bg-slate-100" @click="changeQuantity(item, item.quantity + 1)">
                +
              </button>
            </div>
            <button class="text-sm text-red-600 hover:underline" @click="$emit('remove-from-cart', item.id)">
              Remove
            </button>
          </div>
        </div>
      </div>

      <div class="rounded-lg border border-slate-200 bg-white p-6 md:col-span-2">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <h3 class="text-lg font-semibold text-slate-900">Summary</h3>
            <p class="text-sm text-slate-600">No real payment. The order is saved directly in the database.</p>
          </div>
          <div class="text-right">
            <p class="text-sm text-slate-600">Items: {{ totalItems }}</p>
            <p class="text-2xl font-bold text-slate-900">${{ totalPrice }}</p>
          </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
          <button
            class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
            :disabled="cartItems.length === 0 || !token || placingOrder"
            @click="checkout"
          >
            {{ placingOrder ? 'Placing order...' : 'Place order' }}
          </button>
          <p v-if="!token" class="text-sm text-slate-600">Login to save the order in the database.</p>
          <p v-if="success" class="text-sm text-green-600">Order placed successfully.</p>
          <p v-if="error" class="text-sm text-red-600">{{ error }}</p>
        </div>
      </div>

      <div class="md:col-span-2">
        <MyOrders :token="token" :refresh-key="ordersRefreshKey" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import config from '../../../config.js';
import { post } from '../../../utils/api.js';
import MyOrders from '../../cart/MyOrders.vue';

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  token: {
    type: String,
    default: '',
  },
  cartItems: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['update-cart-quantity', 'remove-from-cart', 'clear-cart']);
const success = ref(false);
const error = ref('');
const placingOrder = ref(false);
const ordersRefreshKey = ref(0);

const totalItems = computed(() =>
  props.cartItems.reduce((total, item) => total + (item.quantity || 0), 0)
);

const totalPrice = computed(() =>
  props.cartItems
    .reduce((total, item) => total + Number(item.price || 0) * Number(item.quantity || 0), 0)
    .toFixed(2)
);

function resolveImage(src) {
  if (!src) return '';
  if (src.startsWith('http://') || src.startsWith('https://')) return src;
  const base = config.apiDomain.replace(/\/$/, '');
  return `${base}${src.startsWith('/') ? '' : '/'}${src}`;
}

function changeQuantity(item, nextQuantity) {
  success.value = false;
  error.value = '';
  if (nextQuantity < 1) {
    emit('remove-from-cart', item.id);
    return;
  }
  emit('update-cart-quantity', item.id, nextQuantity);
}

async function checkout() {
  if (props.cartItems.length === 0 || !props.token) {
    return;
  }

  success.value = false;
  error.value = '';
  placingOrder.value = true;

  try {
    // Send only what the backend needs: product id and quantity.
    const response = await post('/orders', {
      items: props.cartItems.map((item) => ({
        product_id: item.id,
        quantity: item.quantity,
      })),
    }, {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });

    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      error.value = data.error || 'Order failed';
      return;
    }

    success.value = true;
    emit('clear-cart');
    // Bump the key so the "My Orders" box reloads right away.
    ordersRefreshKey.value += 1;
  } catch {
    error.value = 'Network error while placing order';
  } finally {
    placingOrder.value = false;
  }
}
</script>
