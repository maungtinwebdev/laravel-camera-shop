<template>
  <div class="min-h-screen bg-gray-50 text-gray-900 font-sans flex flex-col">
    <Navbar v-if="!isAdminRoute" />
    <main :class="{'flex-grow': true, 'container mx-auto px-4 py-8': !isAdminRoute}">
      <router-view />
    </main>
    <Footer v-if="!isAdminRoute" />
    <ToastManager />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Navbar from './components/Navbar.vue';
import Footer from './components/Footer.vue';
import ToastManager from './components/ToastManager.vue';

const route = useRoute();
const isAdminRoute = computed(() => route.path.startsWith('/admin'));
</script>