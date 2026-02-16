<template>
  <div id="products-grid" class="flex-1">
    <div class="flex flex-col mb-6">
      <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">
          {{ searchQuery ? 'Search Results' : 'Latest Arrivals' }}
        </h2>
        <span class="text-gray-500 text-sm">{{ totalProducts }} products found</span>
      </div>
      <p v-if="searchQuery" class="text-gray-500 mt-1">
        Showing results for "<span class="text-blue-600 font-medium">{{ searchQuery }}</span>"
      </p>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="i in 6" :key="i" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-96 animate-pulse">
        <div class="bg-gray-200 h-48 w-full"></div>
        <div class="p-5 space-y-3">
          <div class="h-4 bg-gray-200 rounded w-1/4"></div>
          <div class="h-6 bg-gray-200 rounded w-3/4"></div>
          <div class="h-4 bg-gray-200 rounded w-full"></div>
        </div>
      </div>
    </div>

    <div v-else-if="products.length === 0" class="text-center py-20 bg-white rounded-xl border border-gray-100">
      <p class="text-gray-500 text-lg">No products found matching your filters.</p>
      <button @click="$emit('clear-filters')" class="mt-4 text-blue-600 font-medium hover:underline">Clear all filters</button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="product in products" :key="product.id" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col">
        <div class="relative h-48 overflow-hidden bg-gray-100">
          <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

          <div v-if="product.sale_price && product.original_price && product.sale_price < product.original_price" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded-full uppercase shadow-lg z-10">
            Sale {{ Math.round((1 - product.sale_price / product.original_price) * 100) }}% Off
          </div>

          <button
            @click.stop="$emit('toggle-wishlist', product)"
            class="absolute top-3 right-3 p-2 bg-white/90 backdrop-blur rounded-full shadow-lg transition-all duration-300 transform active:scale-90 z-20"
            :class="isInWishlist(product.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
          >
            <HeartIcon class="w-5 h-5" :class="{ 'fill-current': isInWishlist(product.id) }" />
          </button>
        </div>

        <div class="p-5 flex flex-col flex-1">
          <div class="text-xs font-bold text-blue-600 mb-1 uppercase tracking-wide">{{ product.brand?.name }}</div>
          <router-link :to="'/product/' + product.slug" class="block">
            <h2 class="text-lg font-bold text-gray-900 mb-2 leading-tight hover:text-blue-600 transition">{{ product.name }}</h2>
          </router-link>
          <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ product.description }}</p>

          <div class="mt-auto flex justify-between items-center pt-4 border-t border-gray-50">
            <div class="flex flex-col">
              <span v-if="product.original_price && product.sale_price && product.sale_price < product.original_price" class="text-xs text-gray-400 line-through mb-0.5">
                ${{ parseFloat(product.original_price).toLocaleString() }}
              </span>
              <span class="text-xl font-bold text-gray-900">
                ${{ parseFloat(product.sale_price || product.price).toLocaleString() }}
              </span>
            </div>

            <div class="flex flex-col items-end gap-2">
              <span class="text-[10px] text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full uppercase tracking-wider">In Stock</span>
              <button
                @click="$emit('add-to-cart', product)"
                class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-blue-700 transition-all shadow-md hover:shadow-blue-200 active:scale-95"
              >
                <ShoppingBagIcon class="w-4 h-4" />
                <span>Add</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <PaginatorControls
      v-if="!loading && products.length > 0"
      :current-page="currentPage"
      :last-page="lastPage"
      :visible-pages="visiblePages"
      @go-to-page="$emit('go-to-page', $event)"
    />
  </div>
</template>

<script setup>
import { HeartIcon, ShoppingBagIcon } from '@heroicons/vue/24/outline';
import PaginatorControls from './PaginatorControls.vue';

defineProps({
  loading: {
    type: Boolean,
    required: true,
  },
  products: {
    type: Array,
    required: true,
  },
  totalProducts: {
    type: Number,
    required: true,
  },
  searchQuery: {
    type: String,
    default: '',
  },
  currentPage: {
    type: Number,
    required: true,
  },
  lastPage: {
    type: Number,
    required: true,
  },
  visiblePages: {
    type: Array,
    required: true,
  },
  isInWishlist: {
    type: Function,
    required: true,
  },
});

defineEmits(['add-to-cart', 'toggle-wishlist', 'clear-filters', 'go-to-page']);
</script>
