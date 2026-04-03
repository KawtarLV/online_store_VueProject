<template>
  <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-lg font-semibold text-slate-900" id="users-table-heading">Users ({{ users.length }})</h3>
      <button class="text-sm text-slate-600 hover:text-slate-900" aria-label="Refresh user list" @click="$emit('refresh')">
        Refresh
      </button>
    </div>

    <!-- Add user form -->
    <form
      class="grid gap-3 mb-4 md:grid-cols-[1fr_1fr_auto_auto]"
      aria-label="Create new user"
      @submit.prevent="createUser"
    >
      <label class="sr-only" for="new-user-name">Name</label>
      <input
        id="new-user-name"
        v-model="form.name"
        placeholder="Name"
        autocomplete="name"
        class="rounded border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-slate-500"
      />

      <label class="sr-only" for="new-user-email">Email</label>
      <input
        id="new-user-email"
        v-model="form.email"
        placeholder="Email"
        type="email"
        autocomplete="email"
        class="rounded border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-slate-500"
      />

      <label class="sr-only" for="new-user-password">Password</label>
      <input
        id="new-user-password"
        v-model="form.password"
        type="password"
        placeholder="Password"
        autocomplete="new-password"
        class="rounded border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-slate-500"
      />

      <label class="sr-only" for="new-user-role">Role</label>
      <select
        id="new-user-role"
        v-model="form.role"
        class="rounded border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:border-slate-500"
      >
        <option value="customer">Customer</option>
        <option value="admin">Admin</option>
      </select>

      <button
        type="submit"
        class="px-4 py-2 rounded bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 md:col-span-4 md:w-32"
        :disabled="creating"
      >
        {{ creating ? 'Saving…' : 'Add User' }}
      </button>

      <p v-if="error" class="text-sm text-red-600 md:col-span-4" role="alert">{{ error }}</p>
      <p v-if="success" class="text-sm text-green-600 md:col-span-4" role="status">User added.</p>
    </form>

    <div v-if="loading" class="text-slate-600 text-sm" role="status" aria-live="polite">Loading users…</div>
    <div v-else-if="loadError" class="text-red-600 text-sm" role="alert">{{ loadError }}</div>
    <div v-else class="overflow-x-auto">
      <table class="min-w-full text-sm" aria-labelledby="users-table-heading">
        <thead>
          <tr class="text-left text-slate-500 border-b">
            <th scope="col" class="py-2 pr-3">Name</th>
            <th scope="col" class="py-2 pr-3">Email</th>
            <th scope="col" class="py-2 pr-3">Role</th>
            <th scope="col" class="py-2 pr-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users" :key="u.id" class="border-b last:border-0">
            <td class="py-2 pr-3 text-slate-900 font-semibold">{{ u.name }}</td>
            <td class="py-2 pr-3 text-slate-700 break-all">{{ u.email }}</td>
            <td class="py-2 pr-3 text-slate-700 capitalize">{{ u.role }}</td>
            <td class="py-2 pr-3">
              <button
                class="text-sm text-red-600 hover:underline"
                :aria-label="`Delete user ${u.name}`"
                @click="$emit('delete', u.id)"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="users.length === 0">
            <td colspan="4" class="py-4 text-center text-slate-600">No users found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';

/**
 * UserTable component
 * Admin table for viewing and deleting users, with a form to add new users
 *
 * @prop {Array} users - list of user objects to display
 * @prop {Boolean} loading - shows a loading message when true
 * @prop {String} loadError - shows an error message if non-empty
 * @emits refresh - emitted after creating a user to reload the list
 * @emits delete - emitted when "Delete" is clicked, carries the user ID
 * @emits create - emitted when the form is submitted, carries the new user data
 */

const props = defineProps({
  users:     { type: Array,   required: true },
  loading:   { type: Boolean, default: false },
  loadError: { type: String,  default: '' },
});

const emit = defineEmits(['refresh', 'delete', 'create']);

/** Form state for the create user form */
const form = reactive({
  name:     '',
  email:    '',
  password: '',
  role:     'customer',
});

const creating = ref(false);
const error    = ref('');
const success  = ref(false);

/**
 * Submits the create user form
 * Validates that all required fields are filled before emitting
 */
async function createUser() {
  error.value   = '';
  success.value = false;
  if (!form.name || !form.email || !form.password) {
    error.value = 'Name, email, and password are required';
    return;
  }
  creating.value = true;
  try {
    emit('create', { ...form });
    success.value = true;
    form.name     = '';
    form.email    = '';
    form.password = '';
    form.role     = 'customer';
    emit('refresh');
  } catch (e) {
    error.value = e?.message || 'Create failed';
  } finally {
    creating.value = false;
  }
}
</script>
