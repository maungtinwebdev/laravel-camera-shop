<template>
  <div>
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Products</h1>
      <button @click="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
        <PlusIcon class="w-5 h-5" /> Add Product
      </button>
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
            <th class="px-6 py-4">Price</th>
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
            <td class="px-6 py-4 font-bold">${{ parseFloat(product.price).toLocaleString() }}</td>
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
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input v-model="form.name" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
              <input v-model="form.price" type="number" step="0.01" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
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
             <label class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
             <input v-model="form.stock" type="number" required class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
            <input v-model="form.image" type="url" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea v-model="form.description" rows="4" class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-500"></textarea>
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
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const products = ref([]);
const categories = ref([]);
const brands = ref([]); // Need to fetch brands separately or assume seeded
const loading = ref(true);
const showModal = ref(false);
const isEditing = ref(false);
const form = ref({});
const toastStore = useToastStore();

// Mock brands fetching since we didn't create a public API for brands list yet, let's create a quick one or just fetch categories for now and hardcode brands or assume seed data
// Ideally we should have an API for brands. I will fetch products and extract unique brands or add an endpoint.
// For now let's assume brands id 1,2,3 exist. I'll fetch brands via a new endpoint or just use hardcoded for MVP.
// Let's create a quick API for brands in the backend later if needed. For now I'll hardcode the ones we seeded.
const brandOptions = [
    { id: 1, name: 'Canon' },
    { id: 2, name: 'Nikon' },
    { id: 3, name: 'Sony' }
];

onMounted(async () => {
  await fetchData();
});

async function fetchData() {
  try {
    const [prodRes, catRes] = await Promise.all([
      axios.get('/api/admin/products'),
      axios.get('/api/categories')
    ]);
    products.value = prodRes.data;
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
  if (product) {
    isEditing.value = true;
    form.value = { ...product };
  } else {
    isEditing.value = false;
    form.value = { stock: 0, category_id: categories.value[0]?.id, brand_id: brands.value[0]?.id };
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
    toastStore.addToast('Failed to save product', 'error');
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