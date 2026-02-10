<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard</h1>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-900"></div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-gray-500 text-sm font-medium uppercase mb-2">Total Revenue</h3>
        <p class="text-3xl font-bold text-gray-900">${{ parseFloat(stats.total_revenue || 0).toLocaleString() }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-gray-500 text-sm font-medium uppercase mb-2">Total Orders</h3>
        <p class="text-3xl font-bold text-gray-900">{{ stats.total_orders }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-gray-500 text-sm font-medium uppercase mb-2">Products</h3>
        <p class="text-3xl font-bold text-gray-900">{{ stats.total_products }}</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-gray-500 text-sm font-medium uppercase mb-2">Registered Users</h3>
        <p class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</p>
      </div>
    </div>

    <div v-if="!loading" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">Recent Orders</h3>
      </div>
      <table class="w-full text-left">
        <thead class="bg-gray-50 text-gray-500 text-sm uppercase">
          <tr>
            <th class="px-6 py-4">Order ID</th>
            <th class="px-6 py-4">Customer</th>
            <th class="px-6 py-4">Total</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4">Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="order in stats.recent_orders" :key="order.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 font-medium">#{{ order.id }}</td>
            <td class="px-6 py-4">{{ order.user?.name || 'Guest' }}</td>
            <td class="px-6 py-4 font-bold">${{ parseFloat(order.total_price).toLocaleString() }}</td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 rounded-full text-xs font-bold uppercase"
                :class="{
                  'bg-yellow-100 text-yellow-800': order.status === 'pending',
                  'bg-blue-100 text-blue-800': order.status === 'processing',
                  'bg-purple-100 text-purple-800': order.status === 'shipped',
                  'bg-green-100 text-green-800': order.status === 'delivered',
                  'bg-red-100 text-red-800': order.status === 'cancelled'
                }">
                {{ order.status }}
              </span>
            </td>
            <td class="px-6 py-4 text-gray-500 text-sm">{{ new Date(order.created_at).toLocaleDateString() }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({});
const loading = ref(true);

onMounted(async () => {
  try {
    const response = await axios.get('/api/admin/dashboard');
    stats.value = response.data;
  } catch (error) {
    console.error('Error fetching dashboard stats:', error);
  } finally {
    loading.value = false;
  }
});
</script>