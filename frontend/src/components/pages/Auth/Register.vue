<template>
  <main class="min-h-screen bg-slate-50">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-semibold text-slate-900">Create Account</h2>
          <p class="text-sm text-slate-600">Sign up to start shopping.</p>
        </div>
      </div>

      <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <form @submit.prevent="handleRegister" class="space-y-6">
          <div>
            <label for="name" class="block text-sm font-medium text-slate-700">Name</label>
            <input v-model="name" type="text" id="name" required
                   class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500">
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input v-model="email" type="email" id="email" required
                   class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500">
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input v-model="password" type="password" id="password" minlength="6" required
                   class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500">
          </div>

          <div>
            <label for="confirm" class="block text-sm font-medium text-slate-700">Confirm Password</label>
            <input v-model="confirm" type="password" id="confirm" minlength="6" required
                   class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500">
          </div>

          <div class="text-sm text-slate-600">
            Already have an account?
            <a href="#/login" class="text-slate-900 font-semibold hover:underline">Sign in</a>
          </div>

          <div class="space-y-2">
            <button type="submit"
                    class="w-full px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800 disabled:opacity-60 disabled:cursor-not-allowed"
                    :disabled="submitting">
              {{ submitting ? 'Creating...' : 'Create Account' }}
            </button>
            <p v-if="error" class="text-sm text-red-600 text-center">{{ error }}</p>
            <p v-if="success" class="text-sm text-green-600 text-center">Account created. Redirecting to login…</p>
          </div>
        </form>
      </div>
    </section>
  </main>
</template>

<script setup>
import { ref } from 'vue';
import { post } from '../../../utils/api.js';

const name = ref('');
const email = ref('');
const password = ref('');
const confirm = ref('');
const submitting = ref(false);
const error = ref('');
const success = ref(false);

async function handleRegister() {
  error.value = '';
  success.value = false;

  if (password.value !== confirm.value) {
    error.value = 'Passwords do not match';
    return;
  }

  submitting.value = true;
  try {
    const response = await post('/register', {
      name: name.value,
      email: email.value,
      password: password.value,
    });

    const body = await response.json().catch(() => ({}));
    if (!response.ok) {
      error.value = body.error || `Signup failed (${response.status})`;
      return;
    }

    success.value = true;
    setTimeout(() => {
      window.location.hash = '#/login';
    }, 1000);
  } catch (e) {
    error.value = 'Network error. Please try again.';
  } finally {
    submitting.value = false;
  }
}
</script>
