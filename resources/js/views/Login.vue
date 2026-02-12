<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Sign In</h1>
        <p class="text-gray-500 text-sm">Welcome back to our shop</p>
      </div>

      <div class="space-y-4">
        <button 
          @click="loginWithGoogle"
          class="w-full flex items-center justify-center gap-3 bg-white border border-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg hover:bg-gray-50 transition shadow-sm"
        >
          <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5" alt="Google">
          Sign in with Google
        </button>

        <div class="relative flex items-center justify-center">
          <div class="border-t border-gray-300 w-full"></div>
          <span class="bg-white px-3 text-sm text-gray-500 absolute">or</span>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <input 
              v-model="email" 
              type="email" 
              required 
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
              placeholder="you@example.com"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input 
              v-model="password" 
              type="password" 
              required 
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
              placeholder="••••••••"
            >
          </div>

          <div v-if="error" class="bg-red-50 text-red-600 text-sm p-3 rounded-lg text-center">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="loading" 
            class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition flex justify-center items-center"
          >
            <span v-if="loading" class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></span>
            Sign In
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const authStore = useAuthStore();
const router = useRouter();

function loginWithGoogle() {
  window.location.href = 'http://localhost:8000/api/auth/google';
}

async function handleLogin() {
  loading.value = true;
  error.value = '';
  
  try {
    await authStore.login(email.value, password.value);
    router.push('/');
  } catch (e) {
    error.value = e.response?.data?.message || 'Login failed. Please check your credentials.';
  } finally {
    loading.value = false;
  }
}
</script>
