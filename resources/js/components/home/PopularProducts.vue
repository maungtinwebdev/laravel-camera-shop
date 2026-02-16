<template>
  <div v-if="loading || products.length > 0" class="mb-16">
    <div class="flex items-center justify-between mb-8">
      <div class="flex items-center gap-3">
        <div class="p-2 bg-orange-50 rounded-xl">
          <FireIcon class="w-6 h-6 text-orange-500" />
        </div>
        <div>
          <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Popular Right Now</h2>
          <p class="text-sm text-gray-500 font-medium">The most viewed cameras this week</p>
        </div>
      </div>

      <div v-if="!loading" class="hidden md:flex gap-2">
        <button @click="scrollSlider('left')" class="p-2 rounded-full border border-gray-200 hover:bg-gray-50 transition shadow-sm">
          <ChevronLeftIcon class="w-5 h-5 text-gray-600" />
        </button>
        <button @click="scrollSlider('right')" class="p-2 rounded-full border border-gray-200 hover:bg-gray-50 transition shadow-sm">
          <ChevronRightIcon class="w-5 h-5 text-gray-600" />
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide">
      <div v-for="i in 5" :key="i" class="flex-none w-44 bg-white rounded-xl border border-gray-100 overflow-hidden animate-pulse">
        <div class="aspect-square bg-gray-200"></div>
        <div class="p-2.5 space-y-2">
          <div class="h-3 bg-gray-200 rounded w-3/4"></div>
          <div class="flex justify-between items-center">
            <div class="h-3 bg-gray-200 rounded w-1/4"></div>
            <div class="h-6 w-6 bg-gray-200 rounded-lg"></div>
          </div>
        </div>
      </div>
    </div>

    <div
      v-else
      ref="popularSlider"
      class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide snap-x snap-mandatory scroll-smooth"
      style="-ms-overflow-style: none; scrollbar-width: none;"
    >
      <div
        v-for="product in products"
        :key="product.id"
        class="flex-none w-44 snap-start group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300"
      >
        <router-link :to="'/product/' + product.slug" class="relative overflow-hidden aspect-square block">
          <img :src="product.image" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>

          <div class="absolute bottom-1.5 left-1.5 bg-white/90 backdrop-blur px-1.5 py-0.5 rounded-md text-[8px] font-black text-gray-600 flex items-center gap-1 shadow-sm">
            <EyeIcon class="w-2.5 h-2.5 text-blue-500" />
            {{ (product.views_count || 0).toLocaleString() }}
          </div>

          <button
            @click.prevent.stop="$emit('toggle-wishlist', product)"
            class="absolute top-1.5 right-1.5 p-1.5 bg-white/90 backdrop-blur rounded-full shadow-sm transition-all duration-300 transform active:scale-90 opacity-0 group-hover:opacity-100"
            :class="isInWishlist(product.id) ? 'text-red-500 opacity-100' : 'text-gray-400 hover:text-red-500'"
          >
            <HeartIcon class="w-3.5 h-3.5" :class="{ 'fill-current': isInWishlist(product.id) }" />
          </button>
        </router-link>

        <div class="p-2.5">
          <router-link :to="'/product/' + product.slug" class="font-bold text-xs text-gray-900 hover:text-blue-600 transition block mb-1 line-clamp-1">
            {{ product.name }}
          </router-link>
          <div class="flex items-center justify-between mt-1">
            <span class="font-black text-blue-600 text-xs">${{ parseFloat(product.sale_price || product.price).toLocaleString() }}</span>
            <button @click="$emit('add-to-cart', product)" class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all active:scale-90">
              <ShoppingBagIcon class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import {
  FireIcon,
  EyeIcon,
  HeartIcon,
  ShoppingBagIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline';

defineProps({
  loading: {
    type: Boolean,
    required: true,
  },
  products: {
    type: Array,
    required: true,
  },
  isInWishlist: {
    type: Function,
    required: true,
  },
});

defineEmits(['toggle-wishlist', 'add-to-cart']);

const popularSlider = ref(null);

function scrollSlider(direction) {
  if (!popularSlider.value) return;
  const scrollAmount = 200;
  popularSlider.value.scrollBy({
    left: direction === 'left' ? -scrollAmount : scrollAmount,
    behavior: 'smooth'
  });
}
</script>
