<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col fixed h-full">
      <div class="p-6 border-b border-blue-800">
        <h1 class="text-xl font-bold tracking-wider">Shop Admin</h1>
      </div>
      
      <nav class="flex-1 p-4 space-y-2">
        <router-link to="/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition" active-class="bg-blue-800">
          <ChartBarIcon class="w-5 h-5" /> Dashboard
        </router-link>
        <router-link to="/admin/products" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition" active-class="bg-blue-800">
          <TagIcon class="w-5 h-5" /> Products
        </router-link>
        <router-link to="/admin/orders" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition" active-class="bg-blue-800">
          <ShoppingBagIcon class="w-5 h-5" /> Orders
        </router-link>
      </nav>

      <div class="p-4 border-t border-blue-800">
        <button @click="authStore.logout" class="flex items-center gap-3 px-4 py-3 w-full text-left rounded-lg hover:bg-blue-800 transition text-red-200 hover:text-red-100">
          <ArrowRightOnRectangleIcon class="w-5 h-5" /> Logout
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-8">
      <router-view />
    </main>
    <ToastManager />
  </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth';
import ToastManager from '../../components/ToastManager.vue';
import { ChartBarIcon, TagIcon, ShoppingBagIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';

const authStore = useAuthStore();
</script>