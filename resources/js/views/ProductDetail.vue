<template>
  <div v-if="product" class="relative">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8 overflow-x-auto whitespace-nowrap pb-2">
      <router-link to="/" class="hover:text-blue-600 transition">Shop</router-link>
      <ChevronRightIcon class="w-3 h-3" />
      <span class="hover:text-blue-600 cursor-pointer transition">{{ product.category?.name }}</span>
      <ChevronRightIcon class="w-3 h-3" />
      <span class="text-gray-900">{{ product.name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
      <!-- Left: Immersive Media Gallery -->
      <div class="lg:col-span-7 space-y-6">
        <div class="relative group aspect-[4/5] md:aspect-square bg-white rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-2xl shadow-gray-200/50">
          <!-- Floating Badge -->
          <div class="absolute top-8 left-8 z-10">
            <div class="bg-white/80 backdrop-blur-xl border border-white/50 px-5 py-2.5 rounded-2xl shadow-xl flex items-center gap-3">
              <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
              <span class="text-xs font-black text-gray-900 uppercase tracking-tighter">Premium Selection</span>
            </div>
          </div>

          <transition name="fade-scale" mode="out-in">
            <img 
              :key="activeImage"
              :src="activeImage" 
              :alt="product.name" 
              class="w-full h-full object-contain p-12 transition-transform duration-1000 group-hover:scale-110"
            >
          </transition>
          
          <!-- Navigation Arrows -->
          <div class="absolute inset-y-0 inset-x-4 flex items-center justify-between pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="prevImage" class="pointer-events-auto p-4 rounded-full bg-white/90 backdrop-blur-md shadow-xl text-gray-900 hover:bg-blue-600 hover:text-white transition-all transform hover:-translate-x-1">
              <ChevronLeftIcon class="w-6 h-6" />
            </button>
            <button @click="nextImage" class="pointer-events-auto p-4 rounded-full bg-white/90 backdrop-blur-md shadow-xl text-gray-900 hover:bg-blue-600 hover:text-white transition-all transform hover:translate-x-1">
              <ChevronRightIcon class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Horizontal Thumbnail Bar -->
        <div class="flex items-center gap-4 overflow-x-auto py-4 scrollbar-hide px-2">
          <button 
            v-for="(img, index) in allImages" 
            :key="index"
            @click="activeImageIndex = index"
            class="relative w-24 h-24 rounded-3xl overflow-hidden flex-shrink-0 transition-all duration-300 transform"
            :class="activeImageIndex === index ? 'ring-4 ring-blue-600 ring-offset-4 scale-105 shadow-2xl' : 'opacity-60 hover:opacity-100 grayscale hover:grayscale-0'"
          >
            <img :src="img" class="w-full h-full object-cover">
          </button>
        </div>
      </div>

      <!-- Right: Product Intelligence & Actions -->
      <div class="lg:col-span-5 flex flex-col pt-4">
        <div class="sticky top-32 space-y-10">
          <header class="space-y-4">
            <div class="flex items-center gap-4">
              <div class="px-4 py-1.5 bg-blue-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-600/30">
                {{ product.brand?.name }}
              </div>
              <div v-if="product.stock < 5 && product.stock > 0" class="text-orange-600 text-[10px] font-black uppercase tracking-widest animate-pulse">
                Low Stock: Only {{ product.stock }} Left
              </div>
            </div>
            
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-[1.1] tracking-tighter">
              {{ product.name }}
            </h1>
            
            <div class="flex items-baseline gap-4">
              <span class="text-5xl font-black text-gray-900 tracking-tighter">
                ${{ parseFloat(product.price).toLocaleString() }}
              </span>
              <span class="text-gray-400 font-bold line-through text-xl">
                ${{ (parseFloat(product.price) * 1.2).toLocaleString() }}
              </span>
            </div>
          </header>

          <!-- Dynamic Feature Pills -->
          <div v-if="featureSpecs.length > 0" class="grid grid-cols-2 gap-3">
            <div 
              v-for="spec in featureSpecs" 
              :key="spec.key"
              class="bg-gray-50/50 border border-gray-100 p-4 rounded-3xl flex items-center gap-4 transition hover:bg-white hover:shadow-xl hover:shadow-gray-200/50 group"
            >
              <div 
                class="p-2.5 rounded-2xl transition-transform group-hover:scale-110"
                :class="{
                  'bg-blue-100 text-blue-600': spec.color === 'blue',
                  'bg-indigo-100 text-indigo-600': spec.color === 'indigo',
                  'bg-amber-100 text-amber-600': spec.color === 'amber',
                  'bg-rose-100 text-rose-600': spec.color === 'rose',
                  'bg-emerald-100 text-emerald-600': spec.color === 'emerald'
                }"
              >
                <component :is="spec.icon" class="w-6 h-6" />
              </div>
              <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ spec.label }}</p>
                <p class="text-sm font-black text-gray-900">{{ spec.value }}</p>
              </div>
            </div>
          </div>

          <!-- Purchase Card -->
          <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 shadow-2xl shadow-gray-200/60 space-y-6">
            <div class="flex items-center justify-between">
              <span class="text-sm font-bold text-gray-900">Choose Quantity</span>
              <div class="flex items-center bg-gray-50 rounded-2xl p-1.5 border border-gray-100">
                <button @click="quantity > 1 && quantity--" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm transition active:scale-90">
                  <MinusIcon class="w-5 h-5 text-gray-400 hover:text-blue-600" />
                </button>
                <span class="w-12 text-center font-black text-gray-900">{{ quantity }}</span>
                <button @click="quantity < product.stock && quantity++" class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white hover:shadow-sm transition active:scale-90">
                  <PlusIcon class="w-5 h-5 text-gray-400 hover:text-blue-600" />
                </button>
              </div>
            </div>

            <div class="space-y-3">
              <button 
                @click="addToCart" 
                :disabled="product.stock <= 0"
                class="w-full group relative overflow-hidden bg-gray-900 text-white py-6 rounded-[1.5rem] font-black text-lg transition-all hover:bg-blue-600 active:scale-95 disabled:opacity-30 disabled:pointer-events-none"
              >
                <div class="relative z-10 flex items-center justify-center gap-3">
                  <ShoppingBagIcon class="w-6 h-6 group-hover:animate-bounce" />
                  Add to Cart — ${{ (parseFloat(product.price) * quantity).toLocaleString() }}
                </div>
              </button>
              
              <button class="w-full bg-blue-50 text-blue-600 py-6 rounded-[1.5rem] font-black text-lg hover:bg-blue-100 transition-colors active:scale-95">
                Instant Checkout
              </button>
            </div>

            <div class="flex items-center justify-center gap-6 pt-4 border-t border-gray-50">
              <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <ShieldCheckIcon class="w-4 h-4 text-green-500" /> Secure Payment
              </div>
              <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <TruckIcon class="w-4 h-4 text-blue-500" /> Fast Delivery
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Secondary Navigation: Tabs -->
    <div class="mt-24 space-y-12">
      <div class="flex justify-center border-b border-gray-100">
        <div class="flex gap-12">
          <button 
            v-for="tab in ['overview', 'specifications', 'reviews']" 
            :key="tab"
            @click="activeTab = tab"
            class="pb-6 text-sm font-black uppercase tracking-[0.2em] transition-all relative"
            :class="activeTab === tab ? 'text-blue-600' : 'text-gray-400 hover:text-gray-600'"
          >
            {{ tab }}
            <div v-if="activeTab === tab" class="absolute bottom-0 left-0 right-0 h-1 bg-blue-600 rounded-full animate-in slide-in-from-bottom-2"></div>
          </button>
        </div>
      </div>

      <transition name="fade-slide" mode="out-in">
        <div :key="activeTab" class="max-w-4xl mx-auto">
          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-12">
            <div class="prose prose-2xl prose-gray max-w-none">
              <p class="text-2xl md:text-3xl text-gray-600 leading-[1.6] font-medium italic text-center">
                "{{ product.description }}"
              </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
              <div class="bg-white p-8 rounded-[2rem] border border-gray-100 text-center space-y-4 shadow-xl shadow-gray-200/30">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto"><SparklesIcon class="w-8 h-8 text-blue-600" /></div>
                <h4 class="font-black text-gray-900 uppercase tracking-widest text-xs">Innovation</h4>
                <p class="text-sm text-gray-500">Industry-leading tech designed for professionals.</p>
              </div>
              <div class="bg-white p-8 rounded-[2rem] border border-gray-100 text-center space-y-4 shadow-xl shadow-gray-200/30">
                <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto"><ArrowPathIcon class="w-8 h-8 text-indigo-600" /></div>
                <h4 class="font-black text-gray-900 uppercase tracking-widest text-xs">Durability</h4>
                <p class="text-sm text-gray-500">Built to withstand the most demanding environments.</p>
              </div>
              <div class="bg-white p-8 rounded-[2rem] border border-gray-100 text-center space-y-4 shadow-xl shadow-gray-200/30">
                <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mx-auto"><UserGroupIcon class="w-8 h-8 text-purple-600" /></div>
                <h4 class="font-black text-gray-900 uppercase tracking-widest text-xs">Precision</h4>
                <p class="text-sm text-gray-500">Unmatched detail in every single capture.</p>
              </div>
            </div>
          </div>

          <!-- Specs Tab -->
          <div v-if="activeTab === 'specifications'" class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-2xl shadow-gray-200/40">
            <div class="divide-y divide-gray-50">
              <div v-for="(value, label) in product.specifications" :key="label" class="grid grid-cols-3 p-8 hover:bg-gray-50/50 transition">
                <div class="text-xs font-black text-gray-400 uppercase tracking-widest">{{ label.replace('_', ' ') }}</div>
                <div class="col-span-2 text-lg font-bold text-gray-900">{{ value || '-' }}</div>
              </div>
              <div v-if="!product.specifications || Object.keys(product.specifications).length === 0" class="p-20 text-center">
                <p class="text-gray-400 font-bold">Full technical sheet coming soon.</p>
              </div>
            </div>
          </div>

          <!-- Reviews Tab (Mock) -->
          <div v-if="activeTab === 'reviews'" class="space-y-8">
            <div class="bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/30 flex flex-col md:flex-row items-center gap-12">
              <div class="text-center md:text-left space-y-2">
                <div class="text-7xl font-black text-gray-900 tracking-tighter">4.9</div>
                <div class="flex items-center justify-center md:justify-start gap-1 text-yellow-400">
                  <StarIcon v-for="i in 5" :key="i" class="w-5 h-5 fill-current" />
                </div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Based on 128 Reviews</p>
              </div>
              <div class="flex-1 w-full space-y-4">
                <div v-for="i in 5" :key="i" class="flex items-center gap-4">
                  <span class="text-xs font-bold text-gray-400 w-4">{{ 6 - i }}</span>
                  <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 rounded-full" :style="{ width: [90, 8, 2, 0, 0][i-1] + '%' }"></div>
                  </div>
                  <span class="text-xs font-bold text-gray-400 w-8">{{ [90, 8, 2, 0, 0][i-1] }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </div>

    <!-- Mobile Sticky Purchase Bar -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 p-4 bg-white/80 backdrop-blur-2xl border-t border-gray-100 animate-in slide-in-from-bottom-10 duration-500">
      <div class="flex items-center gap-4">
        <div class="flex-1">
          <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Price</p>
          <p class="text-xl font-black text-gray-900">${{ (parseFloat(product.price) * quantity).toLocaleString() }}</p>
        </div>
        <button @click="addToCart" class="bg-gray-900 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-gray-900/20 active:scale-95 transition-transform">
          Add to Cart
        </button>
      </div>
    </div>
  </div>

  <!-- Loading State -->
  <div v-else class="min-h-[70vh] flex flex-col justify-center items-center space-y-8">
    <div class="relative w-24 h-24">
      <div class="absolute inset-0 rounded-full border-4 border-blue-50 animate-ping"></div>
      <div class="absolute inset-0 rounded-full border-t-4 border-blue-600 animate-spin"></div>
      <div class="absolute inset-0 flex items-center justify-center">
        <CameraIcon class="w-8 h-8 text-blue-600" />
      </div>
    </div>
    <div class="text-center space-y-2">
      <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Preparing Experience</h3>
      <p class="text-xs font-bold text-gray-400 uppercase tracking-widest animate-pulse">Synchronizing Visual Assets...</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useCartStore } from '../stores/cart';
import { useToastStore } from '../stores/toast';
import { 
  ShoppingBagIcon, 
  PlusIcon,
  MinusIcon,
  ChevronRightIcon,
  ChevronLeftIcon,
  ShieldCheckIcon,
  TruckIcon,
  CameraIcon,
  CpuChipIcon,
  SparklesIcon,
  ArrowPathIcon,
  UserGroupIcon,
  StarIcon,
  Battery50Icon,
  SignalIcon,
  Square3Stack3DIcon
} from '@heroicons/vue/24/solid';

const route = useRoute();
const product = ref(null);
const cartStore = useCartStore();
const toastStore = useToastStore();
const quantity = ref(1);
const activeImageIndex = ref(0);
const activeTab = ref('overview');

const featureSpecs = computed(() => {
  if (!product.value || !product.value.specifications) return [];
  
  const defaults = [
    { key: 'quality', label: 'Quality', icon: CameraIcon, color: 'blue' },
    { key: 'engine', label: 'Engine', icon: CpuChipIcon, color: 'indigo' },
    { key: 'sensor', label: 'Sensor', icon: SparklesIcon, color: 'amber' },
    { key: 'iso', label: 'ISO Range', icon: SignalIcon, color: 'rose' },
    { key: 'battery', label: 'Battery', icon: Battery50Icon, color: 'emerald' }
  ];

  return defaults
    .map(spec => ({
      ...spec,
      value: product.value.specifications[spec.key] || product.value.specifications[spec.label.toLowerCase()]
    }))
    .filter(spec => spec.value && spec.value !== 'null');
});

const allImages = computed(() => {
  if (!product.value) return [];
  const gallery = Array.isArray(product.value.gallery) ? product.value.gallery : [];
  return [product.value.image, ...gallery].filter(Boolean);
});

const activeImage = computed(() => {
  return allImages.value[activeImageIndex.value] || product.value?.image;
});

function nextImage() {
  activeImageIndex.value = (activeImageIndex.value + 1) % allImages.value.length;
}

function prevImage() {
  activeImageIndex.value = (activeImageIndex.value - 1 + allImages.value.length) % allImages.value.length;
}

onMounted(async () => {
  try {
    const response = await axios.get(`/api/products/${route.params.slug}`);
    product.value = response.data;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  } catch (error) {
    console.error('Error fetching product:', error);
    toastStore.addToast('Failed to load product details', 'error');
  }
});

function addToCart() {
  if (product.value) {
    for(let i = 0; i < quantity.value; i++) {
      cartStore.addToCart(product.value);
    }
    toastStore.addToast(`Success! ${quantity.value} items added to your collection.`);
  }
}
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>