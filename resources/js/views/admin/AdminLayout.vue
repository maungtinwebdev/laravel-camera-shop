<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col fixed h-full">
      <div class="p-6 border-b border-blue-800">
        <h1 class="text-xl font-bold tracking-wider">Shop Admin</h1>
      </div>
      
      <nav class="flex-1 p-4 space-y-2">
        <router-link 
          to="/admin/dashboard" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden"
          active-class="bg-blue-800 text-white shadow-lg shadow-blue-950/50"
          exact-active-class="bg-blue-800"
        >
          <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 transform -translate-x-full group-[.router-link-active]:translate-x-0 transition-transform"></div>
          <ChartBarIcon class="w-5 h-5 opacity-70 group-hover:opacity-100 group-[.router-link-active]:opacity-100" />
          <span class="font-medium">Dashboard</span>
        </router-link>

        <router-link 
          to="/admin/products" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden"
          active-class="bg-blue-800 text-white shadow-lg shadow-blue-950/50"
        >
          <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 transform -translate-x-full group-[.router-link-active]:translate-x-0 transition-transform"></div>
          <TagIcon class="w-5 h-5 opacity-70 group-hover:opacity-100 group-[.router-link-active]:opacity-100" />
          <span class="font-medium">Products</span>
        </router-link>

        <router-link 
          to="/admin/orders" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden"
          active-class="bg-blue-800 text-white shadow-lg shadow-blue-950/50"
        >
          <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 transform -translate-x-full group-[.router-link-active]:translate-x-0 transition-transform"></div>
          <ShoppingBagIcon class="w-5 h-5 opacity-70 group-hover:opacity-100 group-[.router-link-active]:opacity-100" />
          <span class="font-medium">Orders</span>
        </router-link>
      </nav>

      <div class="p-4 border-t border-blue-800">
        <button @click="authStore.logout" class="flex items-center gap-3 px-4 py-3 w-full text-left rounded-lg hover:bg-blue-800 transition text-red-200 hover:text-red-100">
          <ArrowRightOnRectangleIcon class="w-5 h-5" /> Logout
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
      <div class="p-8 flex-grow">
        <router-view v-slot="{ Component }">
          <transition 
            name="fade-slide" 
            mode="out-in"
          >
            <component :is="Component" :key="route.path" />
          </transition>
        </router-view>
      </div>
      
      <!-- Admin Footer -->
      <footer class="p-8 border-t border-gray-200 text-center text-sm text-gray-500 bg-white">
        &copy; 2026 Camera Shop Admin. All rights reserved.
      </footer>
    </main>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import { ChartBarIcon, TagIcon, ShoppingBagIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline';

const route = useRoute();
const authStore = useAuthStore();
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>