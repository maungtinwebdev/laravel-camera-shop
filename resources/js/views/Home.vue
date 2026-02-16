<template>
  <div>
    <HeroSection @scroll-to-products="scrollToProducts" />

    <PopularProducts
      :products="mostViewedProducts"
      :loading="loadingMostViewed"
      :is-in-wishlist="wishlistStore.isInWishlist"
      @toggle-wishlist="wishlistStore.toggleWishlist"
      @add-to-cart="addToCart"
    />

    <div class="flex flex-col md:flex-row gap-8">
      <FilterSidebar
        :categories="categories"
        :brands="brands"
        v-model:selectedCategories="selectedCategories"
        v-model:selectedBrands="selectedBrands"
      />

      <LatestArrivals
        :loading="loading"
        :products="products"
        :total-products="totalProducts"
        :search-query="route.query.search"
        :current-page="currentPage"
        :last-page="lastPage"
        :visible-pages="visiblePages"
        :is-in-wishlist="wishlistStore.isInWishlist"
        @clear-filters="clearFilters"
        @toggle-wishlist="wishlistStore.toggleWishlist"
        @add-to-cart="addToCart"
        @go-to-page="goToPage"
      />
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, watch } from 'vue';
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

import HeroSection from '../components/home/HeroSection.vue';
import PopularProducts from '../components/home/PopularProducts.vue';
import FilterSidebar from '../components/home/FilterSidebar.vue';
import LatestArrivals from '../components/home/LatestArrivals.vue';

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
    fetchCategories();
    // toastStore.addToast('Failed to load categories', 'error');
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

function addToCart(product) {
  if (!authStore.user) {
    router.push('/login');
    return;
  }
  cartStore.addToCart(product);
  toastStore.addToast(`Added ${product.name} to cart`);
}

function clearFilters() {
  router.push({ query: {} });
}
</script>
