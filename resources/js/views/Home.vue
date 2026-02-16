<template>
  <div>
    <!-- Hero Section -->
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
          <button @click="scrollToProducts" class="bg-blue-600 text-white px-8 py-3.5 rounded-full font-bold hover:bg-blue-700 transition shadow-lg hover:shadow-blue-600/30 transform hover:-translate-y-1">
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

    <!-- Most Viewed Products -->
    <div v-if="loadingMostViewed || mostViewedProducts.length > 0" class="mb-16">
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
        
        <!-- Navigation Buttons -->
        <div v-if="!loadingMostViewed" class="hidden md:flex gap-2">
          <button @click="scrollSlider('left')" class="p-2 rounded-full border border-gray-200 hover:bg-gray-50 transition shadow-sm">
            <ChevronLeftIcon class="w-5 h-5 text-gray-600" />
          </button>
          <button @click="scrollSlider('right')" class="p-2 rounded-full border border-gray-200 hover:bg-gray-50 transition shadow-sm">
            <ChevronRightIcon class="w-5 h-5 text-gray-600" />
          </button>
        </div>
      </div>

      <div 
        v-if="loadingMostViewed"
        class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide"
      >
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
          v-for="product in mostViewedProducts" 
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

            <!-- Wishlist Button -->
            <button 
              @click.prevent.stop="wishlistStore.toggleWishlist(product)"
              class="absolute top-1.5 right-1.5 p-1.5 bg-white/90 backdrop-blur rounded-full shadow-sm transition-all duration-300 transform active:scale-90 opacity-0 group-hover:opacity-100"
              :class="wishlistStore.isInWishlist(product.id) ? 'text-red-500 opacity-100' : 'text-gray-400 hover:text-red-500'"
            >
              <HeartIcon class="w-3.5 h-3.5" :class="{ 'fill-current': wishlistStore.isInWishlist(product.id) }" />
            </button>
          </router-link>
          
          <div class="p-2.5">
            <router-link :to="'/product/' + product.slug" class="font-bold text-xs text-gray-900 hover:text-blue-600 transition block mb-1 line-clamp-1">
              {{ product.name }}
            </router-link>
            <div class="flex items-center justify-between mt-1">
              <span class="font-black text-blue-600 text-xs">${{ parseFloat(product.sale_price || product.price).toLocaleString() }}</span>
              <button @click="addToCart(product)" class="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all active:scale-90">
                <ShoppingBagIcon class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
      <!-- Sidebar Filters -->
      <aside class="w-full md:w-64 shrink-0">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
          <div class="mb-8">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
              <FunnelIcon class="w-5 h-5 text-blue-600" /> Filters
            </h3>
            
            <div class="mb-6">
              <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Categories</h4>
              <div class="space-y-2">
                <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
                  <div class="relative flex items-center">
                    <input 
                      type="checkbox" 
                      :value="cat.slug" 
                      v-model="selectedCategories"
                      class="peer h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition"
                    >
                  </div>
                  <span class="text-gray-600 group-hover:text-blue-600 transition">{{ cat.name }}</span>
                </label>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Brands</h4>
               <!-- Hardcoded brands for demo if API doesn't support yet, ideally fetch from API -->
               <div class="space-y-2">
                 <label v-for="brand in brands" :key="brand" class="flex items-center gap-3 cursor-pointer group">
                  <input type="checkbox" :value="brand.toLowerCase()" v-model="selectedBrands" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                  <span class="text-gray-600 group-hover:text-blue-600 transition">{{ brand }}</span>
                 </label>
               </div>
            </div>
          </div>
        </div>
      </aside>

      <!-- Product Grid -->
      <div id="products-grid" class="flex-1">
        <div class="flex flex-col mb-6">
          <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900">
              {{ $route.query.search ? 'Search Results' : 'Latest Arrivals' }}
            </h2>
            <span class="text-gray-500 text-sm">{{ totalProducts }} products found</span>
          </div>
          <p v-if="$route.query.search" class="text-gray-500 mt-1">
            Showing results for "<span class="text-blue-600 font-medium">{{ $route.query.search }}</span>"
          </p>
        </div>

        <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
           <!-- Skeletons -->
           <div v-for="i in 6" :key="i" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-96 animate-pulse">
             <div class="bg-gray-200 h-48 w-full"></div>
             <div class="p-5 space-y-3">
               <div class="h-4 bg-gray-200 rounded w-1/4"></div>
               <div class="h-6 bg-gray-200 rounded w-3/4"></div>
               <div class="h-4 bg-gray-200 rounded w-full"></div>
             </div>
           </div>
        </div>

        <div v-else-if="filteredProducts.length === 0" class="text-center py-20 bg-white rounded-xl border border-gray-100">
            <p class="text-gray-500 text-lg">No products found matching your filters.</p>
            <button @click="clearFilters" class="mt-4 text-blue-600 font-medium hover:underline">Clear all filters</button>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="product in filteredProducts" :key="product.id" class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col">
            <div class="relative h-48 overflow-hidden bg-gray-100">
              <img :src="product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
              
              <div v-if="product.sale_price && product.original_price && product.sale_price < product.original_price" class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-2 py-1 rounded-full uppercase shadow-lg z-10">
                Sale {{ Math.round((1 - product.sale_price / product.original_price) * 100) }}% Off
              </div>

              <!-- Wishlist Button -->
              <button 
                @click.stop="wishlistStore.toggleWishlist(product)"
                class="absolute top-3 right-3 p-2 bg-white/90 backdrop-blur rounded-full shadow-lg transition-all duration-300 transform active:scale-90 z-20"
                :class="wishlistStore.isInWishlist(product.id) ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
              >
                <HeartIcon class="w-5 h-5" :class="{ 'fill-current': wishlistStore.isInWishlist(product.id) }" />
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
                    @click="addToCart(product)" 
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

        <!-- Pagination -->
        <div v-if="!loading && filteredProducts.length > 0" class="py-10 flex flex-col md:flex-row items-center justify-between gap-4">
          <p class="text-sm text-gray-500">
            Page {{ currentPage }} of {{ lastPage }}
          </p>

          <div class="flex items-center gap-2 flex-wrap justify-center">
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-2 rounded-lg border text-sm font-bold transition"
              :class="currentPage === 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              Prev
            </button>

            <button
              v-for="page in visiblePages"
              :key="page"
              @click="goToPage(page)"
              class="w-9 px-3 py-2 rounded-lg border text-sm font-bold transition"
              :class="page === currentPage ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              {{ page }}
            </button>

            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === lastPage"
              class="px-3 py-2 rounded-lg border text-sm font-bold transition"
              :class="currentPage === lastPage ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { useCartStore } from '../stores/cart';
import { useToastStore } from '../stores/toast';
import { useWishlistStore } from '../stores/wishlist';
import { useProductsStore } from '../stores/products';
import { useMostViewedStore } from '../stores/mostViewed';
import { useCategoryFilterStore } from '../stores/categoryFilter';
import { useBrandFilterStore } from '../stores/brandFilter';
import { 
  FunnelIcon, 
  HeartIcon, 
  FireIcon, 
  EyeIcon, 
  ShoppingBagIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const cartStore = useCartStore();
const toastStore = useToastStore();
const wishlistStore = useWishlistStore();
const productsStore = useProductsStore();
const mostViewedStore = useMostViewedStore();
const categoryFilterStore = useCategoryFilterStore();
const brandFilterStore = useBrandFilterStore();
const {
  products,
  loading,
  currentPage,
  lastPage,
  totalProducts
} = storeToRefs(productsStore);
const {
  mostViewedProducts,
  loading: loadingMostViewed
} = storeToRefs(mostViewedStore);

const {
  categories,
  selectedCategories
} = storeToRefs(categoryFilterStore);

const {
  brands,
  selectedBrands
} = storeToRefs(brandFilterStore);

const popularSlider = ref(null);

function scrollSlider(direction) {
  if (!popularSlider.value) return;
  const scrollAmount = 200;
  popularSlider.value.scrollBy({
    left: direction === 'left' ? -scrollAmount : scrollAmount,
    behavior: 'smooth'
  });
}

categoryFilterStore.setFromQuery(route.query.category);
brandFilterStore.setFromQuery(route.query.brand);

function scrollToProducts() {
  const el = document.getElementById('products-grid');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
}

// Sync filters with URL
watch(() => [selectedCategories.value, selectedBrands.value], () => {
  const query = { ...route.query };
  
  if (selectedCategories.value.length > 0) {
    query.category = selectedCategories.value.join(',');
  } else {
    delete query.category;
  }
  
  if (selectedBrands.value.length > 0) {
    query.brand = selectedBrands.value.join(',');
  } else {
    delete query.brand;
  }
  
  // Use replace instead of push to avoid cluttering history
  router.replace({ query });
}, { deep: true });

function getProductFilters() {
  return {
    search: route.query.search || '',
    category: route.query.category || '',
    brand: route.query.brand || '',
  };
}

async function fetchProducts(page = 1) {
  try {
    await productsStore.fetchProducts({
      page,
      ...getProductFilters()
    });
  } catch (error) {
    console.error('Error fetching data:', error);
    toastStore.addToast('Failed to load products', 'error');
  }
}

async function fetchMostViewed() {
  try {
    await mostViewedStore.fetchMostViewed();
  } catch (error) {
    console.error('Failed to fetch most viewed products', error);
  }
}

async function fetchCategories() {
  try {
    await categoryFilterStore.fetchCategories();
  } catch (error) {
    console.error('Failed to load categories', error);
    toastStore.addToast('Failed to load categories', 'error');
  }
}

async function goToPage(page) {
  if (loading.value) return;
  if (page < 1 || page > lastPage.value || page === currentPage.value) return;
  await fetchProducts(page);
  scrollToProducts();
}

const visiblePages = computed(() => {
  const total = lastPage.value;
  const current = currentPage.value;
  if (total <= 1) return [1];

  const start = Math.max(1, current - 2);
  const end = Math.min(total, start + 4);
  const normalizedStart = Math.max(1, end - 4);

  return Array.from({ length: end - normalizedStart + 1 }, (_, i) => normalizedStart + i);
});

onMounted(async () => {
  // Always fetch latest arrivals and popular products for both auth and guest users.
  await Promise.allSettled([
    fetchProducts(1),
    fetchMostViewed(),
    fetchCategories()
  ]);

  // Keep auth-specific data isolated so it never blocks shop page product data.
  if (authStore.token || authStore.user) {
    wishlistStore.fetchWishlist();
  }
});

// Re-fetch products when search or filters change in URL
watch(() => [route.query.search, route.query.category, route.query.brand], () => {
  categoryFilterStore.setFromQuery(route.query.category);
  brandFilterStore.setFromQuery(route.query.brand);
  fetchProducts(1);
});

const filteredProducts = computed(() => {
  const items = Array.isArray(products.value) ? products.value : [];
  
  const cats = Array.isArray(selectedCategories.value) ? selectedCategories.value : [];
  const brs = Array.isArray(selectedBrands.value) ? selectedBrands.value : [];

  if (cats.length === 0 && brs.length === 0) {
    return items;
  }

  return items.filter(product => {
    const categoryMatch = cats.length === 0 || 
                         (product.category && cats.includes(product.category.slug));
    
    const brandMatch = brs.length === 0 || 
                      (product.brand && brs.includes(product.brand.slug));
    
    return categoryMatch && brandMatch;
  });
});

function addToCart(product) {
  if (!authStore.user) {
    router.push('/login');
    return;
  }
  cartStore.addToCart(product);
  toastStore.addToast(`Added ${product.name} to cart`);
}

function clearFilters() {
  categoryFilterStore.clear();
  brandFilterStore.clear();
  router.push({ path: '/', query: {} });
}
</script>
