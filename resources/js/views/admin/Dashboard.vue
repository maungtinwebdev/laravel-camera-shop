<template>
  <div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Admin Dashboard</h1>
        <p class="text-gray-500 mt-1">Real-time overview of your shop's performance.</p>
      </div>
      <div class="flex items-center gap-3">
        <button @click="fetchStats" class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-sm">
          <ArrowPathIcon :class="{'animate-spin': loading}" class="w-4 h-4" />
          Refresh
        </button>
        <div class="h-8 w-px bg-gray-200"></div>
        <span class="text-sm text-gray-500 font-medium">{{ currentTime }}</span>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="stat in statCards" :key="stat.label" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
        <div class="flex items-center justify-between mb-4">
          <div :class="stat.bgClass" class="p-3 rounded-xl">
            <component :is="stat.icon" :class="stat.iconClass" class="w-6 h-6" />
          </div>
          <span v-if="stat.trend" :class="stat.trend > 0 ? 'text-green-600' : 'text-red-600'" class="text-xs font-bold bg-gray-50 px-2 py-1 rounded-lg flex items-center gap-1">
            {{ stat.trend > 0 ? '↑' : '↓' }} {{ Math.abs(stat.trend) }}%
          </span>
        </div>
        <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">{{ stat.label }}</h3>
        <p class="text-3xl font-black text-gray-900 mt-1">{{ stat.value }}</p>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Sales Trend Chart -->
      <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-xl font-bold text-gray-900">Revenue Trend (Last 7 Days)</h3>
          <select class="bg-gray-50 border-0 rounded-lg text-sm font-bold px-4 py-2 outline-none cursor-pointer">
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
          </select>
        </div>
        <div class="h-[300px]">
          <Line v-if="!loading && chartData?.labels?.length > 0" :data="chartData" :options="chartOptions" />
          <div v-else class="h-full w-full bg-gray-50 animate-pulse rounded-2xl flex items-center justify-center">
            <span v-if="loading" class="text-gray-400 text-sm">Loading trend data...</span>
            <span v-else class="text-gray-400 text-sm">No sales data available</span>
          </div>
        </div>
      </div>

      <!-- Distribution / Categories -->
      <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
        <h3 class="text-xl font-bold text-gray-900 mb-8">Inventory Status</h3>
        <div class="h-[250px] flex items-center justify-center">
          <Doughnut v-if="!loading && doughnutData?.labels?.length > 0" :data="doughnutData" :options="doughnutOptions" />
          <div v-else class="h-48 w-48 rounded-full border-8 border-gray-100 flex items-center justify-center">
              <span class="text-[10px] text-gray-400 font-bold uppercase">No Data</span>
          </div>
        </div>
        <div class="mt-8 space-y-4">
          <div v-for="cat in stats.top_categories" :key="cat.id" class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-3 h-3 rounded-full bg-blue-500"></div>
              <span class="text-sm font-bold text-gray-700">{{ cat.name }}</span>
            </div>
            <span class="text-sm font-medium text-gray-500">{{ cat.products_count }} products</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Alerts & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Low Stock Alert -->
      <div v-if="stats.low_stock_count > 0" class="bg-orange-50 border border-orange-100 p-6 rounded-3xl flex items-start gap-4">
        <div class="p-3 bg-orange-100 rounded-2xl text-orange-600">
          <ExclamationTriangleIcon class="w-8 h-8" />
        </div>
        <div>
          <h3 class="text-lg font-bold text-orange-900">Inventory Warning</h3>
          <p class="text-orange-700 mt-1">{{ stats.low_stock_count }} items are currently below the safety threshold (5 units).</p>
          <router-link to="/admin/products" class="inline-block mt-4 text-orange-900 font-bold underline hover:no-underline">Restock Now →</router-link>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-blue-900 p-8 rounded-3xl shadow-xl shadow-blue-900/20 text-white relative overflow-hidden">
        <div class="relative z-10">
          <h3 class="text-xl font-bold mb-6">Quick Actions</h3>
          <div class="grid grid-cols-2 gap-4">
            <router-link to="/admin/products" class="bg-white/10 hover:bg-white/20 p-4 rounded-2xl transition group">
              <PlusCircleIcon class="w-6 h-6 mb-2 group-hover:scale-110 transition" />
              <span class="font-bold">Add Product</span>
            </router-link>
            <router-link to="/admin/orders" class="bg-white/10 hover:bg-white/20 p-4 rounded-2xl transition group">
              <DocumentTextIcon class="w-6 h-6 mb-2 group-hover:scale-110 transition" />
              <span class="font-bold">Check Orders</span>
            </router-link>
          </div>
        </div>
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="p-8 border-b border-gray-50 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-900">Recent Transactions</h3>
        <router-link to="/admin/orders" class="text-sm font-bold text-blue-600 hover:text-blue-800">View All Orders</router-link>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Order ID</th>
              <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
              <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
              <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
              <th class="px-8 py-4 text-xs font-black text-gray-400 uppercase tracking-widest">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="order in stats.recent_orders" :key="order.id" class="hover:bg-gray-50/50 transition cursor-pointer">
              <td class="px-8 py-5">
                <span class="font-mono font-bold text-gray-900">#{{ order.id }}</span>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-black text-xs">
                    {{ (order.user?.name || 'G')[0] }}
                  </div>
                  <span class="font-bold text-gray-700">{{ order.user?.name || 'Guest' }}</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <span class="font-black text-gray-900">${{ parseFloat(order.total_price).toLocaleString() }}</span>
              </td>
              <td class="px-8 py-5">
                <span :class="statusBadge(order.status)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-8 py-5 text-sm text-gray-500 font-medium">
                {{ formatDate(order.created_at) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { 
  BanknotesIcon, 
  ShoppingBagIcon, 
  UsersIcon, 
  CameraIcon,
  ArrowPathIcon,
  ExclamationTriangleIcon,
  PlusCircleIcon,
  DocumentTextIcon,
  PresentationChartLineIcon,
  CircleStackIcon
} from '@heroicons/vue/24/outline';
import { Line, Doughnut } from 'vue-chartjs';
import { 
  Chart as ChartJS, 
  Title, 
  Tooltip, 
  Legend, 
  LineElement, 
  CategoryScale, 
  LinearScale, 
  PointElement,
  ArcElement,
  Filler,
  LineController,
  DoughnutController
} from 'chart.js';

ChartJS.register(
  Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement, ArcElement, Filler, LineController, DoughnutController
);

const stats = ref({
  total_revenue: 0,
  total_cost: 0,
  total_profit: 0,
  total_orders: 0,
  avg_order_value: 0,
  total_products: 0,
  total_users: 0,
  low_stock_count: 0,
  recent_orders: [],
  sales_trend: [],
  top_categories: []
});

const loading = ref(true);
const currentTime = ref(new Date().toLocaleTimeString());

const fetchStats = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/dashboard');
    if (response.data && typeof response.data === 'object') {
      stats.value = {
        ...stats.value,
        ...response.data
      };
    }
  } catch (error) {
    console.error('Error fetching dashboard stats:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchStats();
  setInterval(() => {
    currentTime.value = new Date().toLocaleTimeString();
  }, 1000);
});

const statCards = computed(() => {
  const s = stats.value || {};
  return [
    {
      label: 'Total Revenue',
      value: `$${parseFloat(s.total_revenue || 0).toLocaleString()}`,
      icon: BanknotesIcon,
      bgClass: 'bg-blue-50',
      iconClass: 'text-blue-600',
    },
    {
      label: 'Total Cost',
      value: `$${parseFloat(s.total_cost || 0).toLocaleString()}`,
      icon: CircleStackIcon,
      bgClass: 'bg-orange-50',
      iconClass: 'text-orange-600',
    },
    {
      label: 'Total Profit',
      value: `$${parseFloat(s.total_profit || 0).toLocaleString()}`,
      icon: PresentationChartLineIcon,
      bgClass: 'bg-green-50',
      iconClass: 'text-green-600',
    },
    {
      label: 'Total Orders',
      value: s.total_orders || 0,
      icon: ShoppingBagIcon,
      bgClass: 'bg-purple-50',
      iconClass: 'text-purple-600',
    }
  ];
});

const chartData = computed(() => {
  const trend = stats.value?.sales_trend;
  const trendArray = Array.isArray(trend) ? trend : [];
  
  return {
    labels: trendArray.map(d => d?.date || ''),
    datasets: [
      {
        label: 'Revenue',
        data: trendArray.map(d => d?.revenue || 0),
        borderColor: '#3b82f6',
        backgroundColor: 'transparent',
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 4,
      },
      {
        label: 'Cost',
        data: trendArray.map(d => d?.cost || 0),
        borderColor: '#f97316',
        backgroundColor: 'transparent',
        tension: 0.4,
        borderWidth: 3,
        pointRadius: 4,
      },
      {
        label: 'Profit',
        data: trendArray.map(d => d?.profit || 0),
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        fill: true,
        tension: 0.4,
        borderWidth: 4,
        pointRadius: 6,
        pointBackgroundColor: '#fff',
        pointBorderWidth: 3
      }
    ]
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: '#1e293b',
      padding: 12,
      titleFont: { size: 14, weight: 'bold' },
      bodyFont: { size: 14 },
      displayColors: false
    }
  },
  scales: {
    x: { display: false },
    y: { 
      beginAtZero: true,
      grid: { color: '#f1f5f9' },
      ticks: {
        callback: (value) => '$' + value,
        font: { size: 12, weight: 'bold' },
        color: '#94a3b8'
      }
    }
  }
};

const doughnutData = computed(() => {
  const categories = stats.value?.top_categories;
  const categoriesArray = Array.isArray(categories) ? categories : [];

  return {
    labels: categoriesArray.map(c => c?.name || ''),
    datasets: [{
      data: categoriesArray.map(c => c?.products_count || 0),
      backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#10b981'],
      borderWidth: 0,
      cutout: '75%'
    }]
  };
});

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } }
};

const statusBadge = (status) => {
  const classes = {
    pending: 'bg-yellow-50 text-yellow-700',
    processing: 'bg-blue-50 text-blue-700',
    shipped: 'bg-purple-50 text-purple-700',
    delivered: 'bg-green-50 text-green-700',
    cancelled: 'bg-red-50 text-red-700'
  };
  return classes[status] || 'bg-gray-50 text-gray-700';
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};
</script>
