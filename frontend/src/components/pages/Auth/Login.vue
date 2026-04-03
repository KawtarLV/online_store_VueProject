<template>
  <main class="min-h-screen bg-slate-50">
    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-semibold text-slate-900">Login</h2>
          <p class="text-sm text-slate-600">Please enter your credentials to sign in.</p>
        </div>
      </div>
      <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
            <input v-model="email" type="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500">
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
            <input v-model="password" type="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-slate-300 rounded-md shadow-sm focus:outline-none focus:ring-slate-500 focus:border-slate-500">
          </div>
          <div class="space-y-2">
            <button type="submit"
              class="w-full px-4 py-2 bg-slate-900 text-white rounded-lg font-semibold hover:bg-slate-800 disabled:opacity-60 disabled:cursor-not-allowed"
              :disabled="submitting">
              {{ submitting ? 'Signing in...' : 'Sign In' }}
            </button>
            <p v-if="error" class="text-sm text-red-600 text-center">{{ error }}</p>
          </div>
        </form>
      </div>
      <div>
        <p class="text-sm text-slate-600 mt-4 text-center">Don't have an account? <a href="#/register" class="text-slate-900 hover:underline">Sign up</a></p>
      </div>
    </section>
  </main>
</template>

<script setup>
import { ref } from "vue";
import { post } from "../../../utils/api.js";

const emit = defineEmits(['logged-in']);

const email = ref("");
const password = ref("");
const submitting = ref(false);
const error = ref("");

async function handleLogin() {
  error.value = "";
  submitting.value = true;
  try {
    const response = await post('/login', {
      email: email.value,
      password: password.value,
    });

    if (!response.ok) {
      const body = await response.json().catch(() => ({}));
      error.value = body.error || `Login failed (${response.status})`;
      return;
    }

    const session = await response.json();
    emit('logged-in', session);
  } catch (e) {
    error.value = 'Network error. Please try again.';
  } finally {
    submitting.value = false;
  }
}
</script>
