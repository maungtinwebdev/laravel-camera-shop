<template>
  <div class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 text-center">
      <div class="animate-pulse">
        <div class="h-8 bg-gray-200 rounded w-1/2 mx-auto mb-4"></div>
        <p class="text-gray-500">Completing Google sign in...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

onMounted(async () => {
  const token = route.query.token;
  
  if (token) {
    try {
      // Set the token and update axios headers
      authStore.setToken(token);
      
      // Fetch user data using the token
      await authStore.fetchUser();
      
      // Redirect to home or dashboard
      router.push('/');
    } catch (error) {
      console.error('Failed to fetch user after Google login:', error);
      router.push('/login?error=Authentication failed');
    }
  } else {
    router.push('/login?error=No token provided');
  }
});
</script>
