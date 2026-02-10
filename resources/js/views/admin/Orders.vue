<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Orders</h1>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-900"></div>
    </div>

    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-gray-50 text-gray-500 text-sm uppercase">
          <tr>
            <th class="px-6 py-4">ID</th>
            <th class="px-6 py-4">Customer</th>
            <th class="px-6 py-4">Total</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4">Date</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4 font-medium">#{{ order.id }}</td>
            <td class="px-6 py-4">
                <div class="font-medium">{{ order.user?.name || 'Guest' }}</div>
                <div class="text-xs text-gray-500">{{ order.shipping_address }}</div>
            </td>
            <td class="px-6 py-4 font-bold">${{ parseFloat(order.total_price).toLocaleString() }}</td>
            <td class="px-6 py-4">
              <select 
                @change="updateStatus(order, $event.target.value)" 
                class="px-3 py-1 rounded-full text-xs font-bold uppercase border-none focus:ring-2 focus:ring-blue-500 cursor-pointer"
                :class="{
                  'bg-yellow-100 text-yellow-800': order.status === 'pending',
                  'bg-blue-100 text-blue-800': order.status === 'processing',
                  'bg-purple-100 text-purple-800': order.status === 'shipped',
                  'bg-green-100 text-green-800': order.status === 'delivered',
                  'bg-red-100 text-red-800': order.status === 'cancelled'
                }"
                :value="order.status"
              >
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </td>
            <td class="px-6 py-4 text-gray-500 text-sm">{{ new Date(order.created_at).toLocaleDateString() }}</td>
            <td class="px-6 py-4 text-right">
              <button @click="viewOrder(order)" class="text-blue-600 hover:text-blue-800 font-medium">View Items</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Order Details Modal -->
    <div v-if="selectedOrder" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">Order #{{ selectedOrder.id }} Items</h2>
          <button @click="selectedOrder = null" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>
        
        <div class="space-y-4">
            <div v-for="item in selectedOrder.items" :key="item.id" class="flex justify-between items-center border-b border-gray-100 pb-4 last:border-0">
                <div class="flex items-center gap-4">
                    <img :src="item.product?.image" class="w-12 h-12 object-cover rounded bg-gray-50">
                    <div>
                        <div class="font-bold text-gray-900">{{ item.product?.name }}</div>
                        <div class="text-sm text-gray-500">${{ parseFloat(item.price).toLocaleString() }} x {{ item.quantity }}</div>
                    </div>
                </div>
                <div class="font-bold text-gray-900">
                    ${{ (item.price * item.quantity).toFixed(2) }}
                </div>
            </div>
            
            <div class="flex justify-between pt-4 font-bold text-lg border-t border-gray-100">
                <span>Total</span>
                <span>${{ parseFloat(selectedOrder.total_price).toLocaleString() }}</span>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useToastStore } from '../../stores/toast';
import { XMarkIcon } from '@heroicons/vue/24/outline';

const orders = ref([]);
const loading = ref(true);
const selectedOrder = ref(null);
const toastStore = useToastStore();

onMounted(async () => {
  await fetchOrders();
});

async function fetchOrders() {
  try {
    const response = await axios.get('/api/admin/orders');
    orders.value = response.data;
  } catch (error) {
    console.error('Error fetching orders:', error);
    toastStore.addToast('Failed to load orders', 'error');
  } finally {
    loading.value = false;
  }
}

async function updateStatus(order, newStatus) {
  try {
    await axios.put(`/api/admin/orders/${order.id}/status`, { status: newStatus });
    order.status = newStatus;
    toastStore.addToast(`Order #${order.id} status updated to ${newStatus}`);
  } catch (error) {
    console.error('Update failed:', error);
    toastStore.addToast('Failed to update status', 'error');
    // Revert logic could go here
  }
}

function viewOrder(order) {
    selectedOrder.value = order;
}
</script>