<template>
  <div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <h1 class="text-3xl font-bold text-gray-800">Products</h1>
      <button @click="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
        <PlusIcon class="w-5 h-5" /> Add Product
      </button>
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Search Name</label>
          <div class="relative">
            <input 
              v-model="filters.name" 
              @input="debouncedFetch"
              type="text" 
              placeholder="Filter by name..." 
              class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition"
            >
            <MagnifyingGlassIcon class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
          </div>
        </div>
        
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Min Price</label>
            <input 
              v-model="filters.min_price" 
              @input="debouncedFetch"
              type="number" 
              placeholder="0" 
              class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition"
            >
          </div>
          <div>
            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Max Price</label>
            <input 
              v-model="filters.max_price" 
              @input="debouncedFetch"
              type="number" 
              placeholder="9999" 
              class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition"
            >
          </div>
        </div>

        <div>
          <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Stock Status</label>
          <select 
            v-model="filters.stock_status" 
            @change="fetchData"
            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition appearance-none cursor-pointer"
          >
            <option value="">All Stock Status</option>
            <option value="in_stock">In Stock (5+)</option>
            <option value="low_stock">Low Stock (< 5)</option>
            <option value="out_of_stock">Out of Stock</option>
          </select>
        </div>

        <div class="flex items-end">
          <button 
            @click="clearFilters" 
            class="w-full px-4 py-2 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition text-sm"
          >
            Clear Filters
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-900"></div>
    </div>

    <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
      <table class="w-full text-left">
        <thead class="bg-gray-50 text-gray-500 text-sm uppercase">
          <tr>
            <th class="px-6 py-4">Image</th>
            <th class="px-6 py-4">Name</th>
            <th class="px-6 py-4">Category</th>
            <th class="px-6 py-4">Brand</th>
            <th class="px-6 py-4">Price / Sale</th>
            <th class="px-6 py-4">Stock</th>
            <th class="px-6 py-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition">
            <td class="px-6 py-4">
              <img :src="product.image" class="w-12 h-12 object-cover rounded bg-gray-100" />
            </td>
            <td class="px-6 py-4 font-medium">{{ product.name }}</td>
            <td class="px-6 py-4 text-gray-500">{{ product.category?.name }}</td>
            <td class="px-6 py-4 text-gray-500">{{ product.brand?.name }}</td>
            <td class="px-6 py-4">
              <div class="flex flex-col">
                <span class="font-bold text-gray-900">${{ parseFloat(product.price).toLocaleString() }}</span>
                <span v-if="product.sale_price" class="text-xs text-green-600">Sale: ${{ parseFloat(product.sale_price).toLocaleString() }}</span>
                <span v-if="product.original_price" class="text-xs text-gray-400 line-through">Orig: ${{ parseFloat(product.original_price).toLocaleString() }}</span>
              </div>
            </td>
            <td class="px-6 py-4">
              <span :class="product.stock > 0 ? 'text-green-600' : 'text-red-600'">{{ product.stock }}</span>
            </td>
            <td class="px-6 py-4 text-right space-x-2">
              <button @click="openModal(product)" class="text-blue-600 hover:text-blue-800 font-medium">Edit</button>
              <button @click="deleteProduct(product.id)" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > 0" class="mt-6 flex items-center justify-between bg-white px-6 py-4 rounded-xl border border-gray-100 shadow-sm">
      <div class="flex flex-1 justify-between sm:hidden">
        <button 
          @click="changePage(pagination.current_page - 1)" 
          :disabled="pagination.current_page === 1" 
          class="relative inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 transition"
        >
          Previous
        </button>
        <button 
          @click="changePage(pagination.current_page + 1)" 
          :disabled="pagination.current_page === pagination.last_page" 
          class="relative ml-3 inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 transition"
        >
          Next
        </button>
      </div>
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-500">
            Showing <span class="font-bold text-gray-900">{{ pagination.from }}</span> to <span class="font-bold text-gray-900">{{ pagination.to }}</span> of <span class="font-bold text-gray-900">{{ pagination.total }}</span> results
          </p>
        </div>
        <div>
          <nav class="isolate inline-flex -space-x-px rounded-xl shadow-sm overflow-hidden border border-gray-200" aria-label="Pagination">
            <button 
              @click="changePage(pagination.current_page - 1)" 
              :disabled="pagination.current_page === 1" 
              class="relative inline-flex items-center px-3 py-2 text-gray-400 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 transition"
            >
              <span class="sr-only">Previous</span>
              <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
            </button>
            
            <button 
              v-for="page in visiblePages" 
              :key="page" 
              @click="changePage(page)" 
              :class="[
                page === pagination.current_page 
                  ? 'z-10 bg-blue-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' 
                  : 'text-gray-900 hover:bg-gray-50 focus:outline-offset-0'
              ]" 
              class="relative inline-flex items-center px-4 py-2 text-sm font-bold transition border-x border-gray-200"
            >
              {{ page }}
            </button>

            <button 
              @click="changePage(pagination.current_page + 1)" 
              :disabled="pagination.current_page === pagination.last_page" 
              class="relative inline-flex items-center px-3 py-2 text-gray-400 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 transition"
            >
              <span class="sr-only">Next</span>
              <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Product Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
          <h2 class="text-xl font-bold">{{ isEditing ? 'Edit Product' : 'Add New Product' }}</h2>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <XMarkIcon class="w-6 h-6" />
          </button>
        </div>
        
        <form @submit.prevent="saveProduct" class="p-6 space-y-4">
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input v-model="form.name" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Base Price</label>
              <input v-model="form.price" type="number" step="0.01" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
              <input v-model="form.stock" type="number" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Original Price (MSRP)</label>
              <input v-model="form.original_price" type="number" step="0.01" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
              <input v-model="form.sale_price" type="number" step="0.01" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Optional">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price (Purchase)</label>
              <input v-model="form.cost_price" type="number" step="0.01" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500" placeholder="Internal Only">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <select v-model="form.category_id" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
              <select v-model="form.brand_id" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
              <input v-model="form.image" type="url" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Gallery (JSON Array)</label>
              <input 
                :value="JSON.stringify(form.gallery)" 
                @input="e => form.gallery = JSON.parse(e.target.value || '[]')"
                placeholder='["url1", "url2"]'
                class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Specifications (Key: Value)</label>
            <div class="space-y-2">
              <div v-for="(val, key) in form.specifications" :key="key" class="flex gap-2">
                <input v-model="tempSpecKeys[key]" @change="updateSpecKey(key)" class="w-1/3 border rounded-lg px-2 py-1 text-sm">
                <input v-model="form.specifications[key]" class="w-2/3 border rounded-lg px-2 py-1 text-sm">
                <button type="button" @click="removeSpec(key)" class="text-red-500">&times;</button>
              </div>
              <button type="button" @click="addSpec" class="text-blue-600 text-sm font-bold">+ Add Specification</button>
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">
              {{ isEditing ? 'Update Product' : 'Create Product' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { useToastStore } from '../../stores/toast';
import { 
  PlusIcon, 
  XMarkIcon, 
  MagnifyingGlassIcon,
  ChevronLeftIcon,
  ChevronRightIcon 
} from '@heroicons/vue/24/outline';

const products = ref([]);
const categories = ref([]);
const brands = ref([]); // Need to fetch brands separately or assume seeded
const loading = ref(true);
const showModal = ref(false);
const isEditing = ref(false);
const form = ref({});
const tempSpecKeys = ref({});
const toastStore = useToastStore();

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
});

const filters = ref({
  name: '',
  min_price: '',
  max_price: '',
  stock_status: ''
});

let debounceTimer;
function debouncedFetch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchData(1);
  }, 500);
}

function clearFilters() {
  filters.value = {
    name: '',
    min_price: '',
    max_price: '',
    stock_status: ''
  };
  fetchData(1);
}

const visiblePages = computed(() => {
  const current = pagination.value.current_page;
  const last = pagination.value.last_page;
  const delta = 2;
  const left = current - delta;
  const right = current + delta + 1;
  const range = [];
  const rangeWithDots = [];
  let l;

  for (let i = 1; i <= last; i++) {
    if (i === 1 || i === last || (i >= left && i < right)) {
      range.push(i);
    }
  }

  return range;
});

async function changePage(page) {
  if (page >= 1 && page <= pagination.value.last_page) {
    await fetchData(page);
  }
}

function updateSpecKey(oldKey) {
  const newKey = tempSpecKeys.value[oldKey];
  if (newKey && newKey !== oldKey) {
    const value = form.value.specifications[oldKey];
    delete form.value.specifications[oldKey];
    form.value.specifications[newKey] = value;
    
    // Update the mapping
    delete tempSpecKeys.value[oldKey];
    tempSpecKeys.value[newKey] = newKey;
  }
}

function addSpec() {
  if (!form.value.specifications) form.value.specifications = {};
  const key = `spec_${Object.keys(form.value.specifications).length + 1}`;
  form.value.specifications[key] = '';
  tempSpecKeys.value[key] = key;
}

function removeSpec(key) {
  delete form.value.specifications[key];
  delete tempSpecKeys.value[key];
}

const brandOptions = [
    { id: 1, name: 'Canon' },
    { id: 2, name: 'Nikon' },
    { id: 3, name: 'Sony' }
];

onMounted(async () => {
  await fetchData();
});

async function fetchData(page = 1) {
  try {
    const params = { ...filters.value, page };
    const [prodRes, catRes] = await Promise.all([
      axios.get('/api/admin/products', { params }),
      axios.get('/api/categories')
    ]);
    
    const paginatedData = prodRes.data;
    products.value = paginatedData?.data || [];
    pagination.value = {
      current_page: paginatedData?.current_page || 1,
      last_page: paginatedData?.last_page || 1,
      total: paginatedData?.total || 0,
      from: paginatedData?.from || 0,
      to: paginatedData?.to || 0
    };

    categories.value = catRes.data;
    brands.value = brandOptions; 
  } catch (error) {
    console.error('Error fetching data:', error);
    toastStore.addToast('Failed to load data', 'error');
  } finally {
    loading.value = false;
  }
}

function openModal(product = null) {
  tempSpecKeys.value = {};
  if (product) {
    isEditing.value = true;
    form.value = { 
      ...product, 
      gallery: Array.isArray(product.gallery) ? product.gallery : [],
      specifications: product.specifications || {}
    };
    // Initialize temp keys
    Object.keys(form.value.specifications).forEach(k => {
      tempSpecKeys.value[k] = k;
    });
  } else {
    isEditing.value = false;
    form.value = { 
      stock: 0, 
      category_id: categories.value[0]?.id, 
      brand_id: brands.value[0]?.id,
      gallery: [],
      specifications: {}
    };
  }
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  form.value = {};
}

async function saveProduct() {
  try {
    if (isEditing.value) {
      await axios.put(`/api/admin/products/${form.value.id}`, form.value);
      toastStore.addToast('Product updated successfully');
    } else {
      await axios.post('/api/admin/products', form.value);
      toastStore.addToast('Product created successfully');
    }
    await fetchData();
    closeModal();
  } catch (error) {
    console.error('Save failed:', error);
    const message = error.response?.data?.message || 'Failed to save product';
    const detail = error.response?.data?.errors 
      ? Object.values(error.response.data.errors).flat().join(', ')
      : error.response?.data?.error || '';
    
    toastStore.addToast(`${message}${detail ? ': ' + detail : ''}`, 'error');
  }
}

async function deleteProduct(id) {
  if (!confirm('Are you sure you want to delete this product?')) return;
  
  try {
    await axios.delete(`/api/admin/products/${id}`);
    toastStore.addToast('Product deleted');
    await fetchData();
  } catch (error) {
    console.error('Delete failed:', error);
    toastStore.addToast('Failed to delete product', 'error');
  }
}
</script>