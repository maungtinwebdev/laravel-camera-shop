<template>
  <div class="max-w-6xl mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Sidebar Info -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sticky top-24">
          <div class="text-center mb-8">
            <div class="relative inline-block mb-4">
              <img 
                :src="user?.avatar || 'https://ui-avatars.com/api/?name=' + user?.name" 
                class="w-24 h-24 rounded-full object-cover border-4 border-blue-50 shadow-sm"
              >
              <div v-if="user?.google_id" class="absolute bottom-0 right-0 bg-white rounded-full p-1 shadow-sm border border-gray-100">
                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5" alt="Google">
              </div>
            </div>
            <h2 class="text-2xl font-black text-gray-900">{{ user?.name }}</h2>
            <p class="text-gray-500">{{ user?.email }}</p>
          </div>

          <nav class="space-y-2">
            <button 
              @click="activeTab = 'profile'"
              :class="activeTab === 'profile' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
              class="w-full text-left px-4 py-3 rounded-xl font-bold transition flex items-center gap-3"
            >
              <UserIcon class="w-5 h-5" />
              Profile Details
            </button>
            <button 
              @click="activeTab = 'orders'"
              :class="activeTab === 'orders' ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50'"
              class="w-full text-left px-4 py-3 rounded-xl font-bold transition flex items-center gap-3"
            >
              <ShoppingBagIcon class="w-5 h-5" />
              Order History
            </button>
            <button 
              @click="handleLogout"
              class="w-full text-left px-4 py-3 rounded-xl font-bold text-red-600 hover:bg-red-50 transition flex items-center gap-3"
            >
              <ArrowLeftOnRectangleIcon class="w-5 h-5" />
              Logout
            </button>
          </nav>
        </div>
      </div>

      <!-- Main Content -->
      <div class="lg:col-span-2">
        <div v-if="activeTab === 'profile'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
          <h3 class="text-xl font-black text-gray-900 mb-6">Edit Profile</h3>
          
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
              <input 
                v-model="profileForm.name"
                type="text" 
                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 outline-none transition"
              >
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
              <input 
                v-model="profileForm.email"
                type="email" 
                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-4 focus:ring-blue-500/5 focus:border-blue-500 outline-none transition"
              >
            </div>

            <div v-if="successMsg" class="bg-green-50 text-green-600 p-4 rounded-xl text-sm font-bold">
              {{ successMsg }}
            </div>

            <button 
              type="submit"
              :disabled="updating"
              class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 disabled:opacity-50"
            >
              {{ updating ? 'Saving...' : 'Save Changes' }}
            </button>
          </form>
        </div>

        <div v-if="activeTab === 'orders'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
          <h3 class="text-xl font-black text-gray-900 mb-6">Order History</h3>
          
          <div v-if="loadingOrders" class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-32 bg-gray-50 rounded-2xl animate-pulse"></div>
          </div>

          <div v-else-if="orders.length === 0" class="text-center py-12">
            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <ShoppingBagIcon class="w-8 h-8 text-gray-300" />
            </div>
            <p class="text-gray-500 font-medium">You haven't placed any orders yet.</p>
            <router-link to="/" class="text-blue-600 font-bold hover:underline mt-2 inline-block">Start Shopping</router-link>
          </div>

          <div v-else class="space-y-6">
            <div v-for="order in orders" :key="order.id" class="border border-gray-100 rounded-2xl overflow-hidden hover:border-blue-100 transition-colors">
              <div class="bg-gray-50/50 p-4 flex justify-between items-center">
                <div>
                  <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Order #{{ order.id }}</span>
                  <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                </div>
                <div class="text-right">
                  <span 
                    :class="{
                      'bg-yellow-100 text-yellow-700': order.status === 'pending',
                      'bg-blue-100 text-blue-700': order.status === 'processing',
                      'bg-green-100 text-green-700': order.status === 'completed',
                      'bg-red-100 text-red-700': order.status === 'cancelled'
                    }"
                    class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full"
                  >
                    {{ order.status }}
                  </span>
                  <p class="text-sm font-black text-gray-900 mt-1">${{ parseFloat(order.total_price).toLocaleString() }}</p>
                </div>
              </div>
              <div class="p-4 space-y-4">
                <div v-for="item in order.items" :key="item.id" class="flex gap-4">
                  <img :src="item.product?.image" class="w-16 h-16 rounded-xl object-cover bg-gray-100">
                  <div class="flex-1">
                    <h4 class="text-sm font-bold text-gray-900">{{ item.product?.name }}</h4>
                    <p class="text-xs text-gray-500">Qty: {{ item.quantity }} × ${{ parseFloat(item.price).toLocaleString() }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { 
  UserIcon, 
  ShoppingBagIcon, 
  ArrowLeftOnRectangleIcon 
} from '@heroicons/vue/24/outline';
import axios from 'axios';

const authStore = useAuthStore();
const router = useRouter();

const user = computed(() => authStore.user);
const activeTab = ref('profile');
const profileForm = ref({
  name: user.value?.name || '',
  email: user.value?.email || ''
});

const updating = ref(false);
const successMsg = ref('');
const orders = ref([]);
const loadingOrders = ref(false);

onMounted(async () => {
  if (!authStore.token) {
    router.push('/login');
    return;
  }
  fetchOrders();
});

async function updateProfile() {
  updating.value = true;
  successMsg.value = '';
  try {
    const response = await axios.put('/api/user/profile', profileForm.value);
    authStore.user = response.data.user;
    localStorage.setItem('user', JSON.stringify(response.data.user));
    successMsg.value = 'Profile updated successfully!';
    setTimeout(() => successMsg.value = '', 3000);
  } catch (error) {
    console.error('Failed to update profile', error);
  } finally {
    updating.value = false;
  }
}

async function fetchOrders() {
  loadingOrders.value = true;
  try {
    const response = await axios.get('/api/orders');
    orders.value = response.data;
  } catch (error) {
    console.error('Failed to fetch orders', error);
  } finally {
    loadingOrders.value = false;
  }
}

function handleLogout() {
  authStore.logout();
}

function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}
</script>
