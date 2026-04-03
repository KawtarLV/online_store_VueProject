<template>
  <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-slate-900">Admin Dashboard</h2>
        <p class="text-sm text-slate-600">Manage products, users, and orders.</p>
      </div>
      <div v-if="isAdmin" class="flex gap-3">
        <a
          href="#/admin/new-product"
          class="rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
        >
          + Add Product
        </a>
      </div>
    </div>

    <div v-if="!isAdmin" class="rounded-lg border border-red-200 bg-white p-6 text-center text-red-700">
      Access denied. Admins only.
    </div>

    <div v-else class="space-y-6">
      <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white p-6">
          <h3 class="text-sm uppercase tracking-wide text-slate-500">Welcome</h3>
          <p class="mt-2 text-xl font-semibold text-slate-900">{{ user?.name }}</p>
          <p class="text-sm text-slate-600">Role: {{ user?.role }}</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-6">
          <h3 class="text-sm uppercase tracking-wide text-slate-500">Products</h3>
          <p class="text-3xl font-bold text-slate-900">{{ products.length }}</p>
          <p class="text-sm text-slate-600">Total products</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white p-6">
          <h3 class="text-sm uppercase tracking-wide text-slate-500">Stock</h3>
          <p class="text-3xl font-bold text-slate-900">{{ totalStock }}</p>
          <p class="text-sm text-slate-600">Units in stock</p>
        </div>
      </div>

      <ProductTable
        :products="products"
        :loading="loading"
        :error="error"
        :categories="categories"
        @refresh="loadProducts"
        @edit="goEdit"
        @delete="deleteProduct"
      />

      <UserTable
        :users="users"
        :loading="usersLoading"
        :load-error="usersError"
        @refresh="loadUsers"
        @delete="deleteUser"
        @create="createUser"
      />

      <OrderTable
        :orders="orders"
        :loading="ordersLoading"
        :error="ordersError"
        @refresh="loadOrders"
      />
    </div>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { del, get, post } from '../../../utils/api.js';
import OrderTable from '../../admin/OrderTable.vue';
import ProductTable from '../../admin/ProductTable.vue';
import UserTable from '../../admin/UserTable.vue';

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  token: {
    type: String,
    default: '',
  },
});

const isAdmin = computed(() => props.user?.role === 'admin');
const products = ref([]);
const loading = ref(false);
const error = ref('');
const categories = ref([]);
const users = ref([]);
const usersLoading = ref(false);
const usersError = ref('');
const orders = ref([]);
const ordersLoading = ref(false);
const ordersError = ref('');

const totalStock = computed(() =>
  products.value.reduce((total, product) => total + (product.stock ?? 0), 0)
);

async function loadProducts() {
  if (!isAdmin.value) return;
  loading.value = true;
  error.value = '';
  try {
    const response = await get('/products?per_page=50&page=1');
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      error.value = data.error || 'Failed to load products';
      return;
    }
    products.value = Array.isArray(data.items) ? data.items : [];
  } catch {
    error.value = 'Network error while loading products';
  } finally {
    loading.value = false;
  }
}

async function loadCategories() {
  try {
    const response = await get('/categories');
    const data = await response.json().catch(() => []);
    if (response.ok) {
      categories.value = Array.isArray(data) ? data : [];
    }
  } catch {
    categories.value = [];
  }
}

async function deleteProduct(id) {
  if (!confirm('Delete this product?')) return;

  try {
    const response = await del(`/products/${id}`, {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });

    if (!response.ok) {
      alert('Delete failed');
      return;
    }

    products.value = products.value.filter((product) => product.id !== id);
  } catch {
    alert('Network error');
  }
}

function goEdit(id) {
  window.location.hash = `#/admin/edit-product/${id}`;
}

async function loadUsers() {
  usersLoading.value = true;
  usersError.value = '';
  try {
    const response = await get('/users', {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    const data = await response.json().catch(() => []);
    if (!response.ok) {
      usersError.value = data.error || 'Failed to load users';
      return;
    }
    users.value = Array.isArray(data) ? data : [];
  } catch {
    usersError.value = 'Network error while loading users';
  } finally {
    usersLoading.value = false;
  }
}

async function createUser(payload) {
  try {
    const response = await post('/users', payload, {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
      usersError.value = data.error || 'Create user failed';
      throw new Error(usersError.value);
    }
    await loadUsers();
    return data;
  } catch (errorValue) {
    if (!usersError.value) {
      usersError.value = 'Network error while creating user';
    }
    throw errorValue;
  }
}

async function deleteUser(id) {
  if (!confirm('Delete this user?')) return;

  try {
    const response = await del(`/users/${id}`, {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    if (!response.ok) {
      alert('Delete failed');
      return;
    }
    users.value = users.value.filter((user) => user.id !== id);
  } catch {
    alert('Network error');
  }
}

async function loadOrders() {
  ordersLoading.value = true;
  ordersError.value = '';
  try {
    const response = await get('/orders', {
      headers: {
        Authorization: `Bearer ${props.token}`,
      },
    });
    const data = await response.json().catch(() => []);
    if (!response.ok) {
      ordersError.value = data.error || 'Failed to load orders';
      return;
    }
    orders.value = Array.isArray(data) ? data : [];
  } catch {
    ordersError.value = 'Network error while loading orders';
  } finally {
    ordersLoading.value = false;
  }
}

onMounted(loadProducts);
onMounted(loadCategories);
onMounted(loadUsers);
onMounted(loadOrders);
</script>
