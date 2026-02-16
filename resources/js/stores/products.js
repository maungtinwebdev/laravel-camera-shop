import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useProductsStore = defineStore('products', () => {
    const products = ref([]);
    const loading = ref(false);
    const currentPage = ref(1);
    const lastPage = ref(1);
    const totalProducts = ref(0);

    let requestId = 0;

    async function fetchProducts({ page = 1, search = '', category = '', brand = '' } = {}) {
        const currentRequestId = ++requestId;
        loading.value = true;

        try {
            const params = { page };
            if (search) params.search = search;
            if (category) params.category = category;
            if (brand) params.brand = brand;

            const response = await axios.get('/api/products', { params });
            const paginatedData = response.data || {};

            if (currentRequestId !== requestId) return;

            products.value = Array.isArray(paginatedData.data) ? paginatedData.data : [];
            currentPage.value = Number(paginatedData.current_page || 1);
            lastPage.value = Number(paginatedData.last_page || 1);
            totalProducts.value = Number(paginatedData.total || 0);
        } finally {
            if (currentRequestId === requestId) {
                loading.value = false;
            }
        }
    }

    return {
        products,
        loading,
        currentPage,
        lastPage,
        totalProducts,
        fetchProducts,
    };
});
