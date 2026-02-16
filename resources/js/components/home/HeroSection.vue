<template>
  <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl overflow-hidden mb-12 shadow-2xl relative">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative z-10 px-8 py-16 md:py-24 md:px-16 text-center md:text-left">
      <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
        Capture Your <br class="hidden md:block" />
        <span class="text-blue-400">Perfect Moment</span>
      </h1>
      <p class="text-gray-300 text-lg md:text-xl mb-8 max-w-xl">
        Discover professional-grade cameras and lenses for every photographer.
      </p>
      <div class="flex flex-wrap gap-4">
        <button @click="$emit('scroll-to-products')" class="bg-blue-600 text-white px-8 py-3.5 rounded-full font-bold hover:bg-blue-700 transition shadow-lg hover:shadow-blue-600/30 transform hover:-translate-y-1">
          Shop Now
        </button>
        <router-link 
          v-if="!authStore.user" 
          to="/login" 
          class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-3.5 rounded-full font-bold hover:bg-white/20 transition shadow-lg transform hover:-translate-y-1"
        >
          Sign In
        </router-link>
        <router-link 
          v-else 
          to="/profile" 
          class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-3.5 rounded-full font-bold hover:bg-white/20 transition shadow-lg transform hover:-translate-y-1"
        >
          View Profile
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();

defineEmits(['scroll-to-products']);
</script>
