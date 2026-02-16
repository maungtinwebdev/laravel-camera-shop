import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useMostViewedStore = defineStore('mostViewed', () => {
    const mostViewedProducts = ref([]);
    const loading = ref(false);

    let requestId = 0;

    async function fetchMostViewed() {
        const currentRequestId = ++requestId;
        loading.value = true;

        try {
            const response = await axios.get('/api/products/most-viewed');

            if (currentRequestId !== requestId) return;

            mostViewedProducts.value = Array.isArray(response.data) ? response.data : [];
        } finally {
            if (currentRequestId === requestId) {
                loading.value = false;
            }
        }
    }

    return {
        mostViewedProducts,
        loading,
        fetchMostViewed,
    };
});
