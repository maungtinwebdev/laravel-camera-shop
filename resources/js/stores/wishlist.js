import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import axios from 'axios';
import { useAuthStore } from './auth';
import router from '../router';

export const useWishlistStore = defineStore('wishlist', () => {
    const items = ref([]);
    const loading = ref(false);
    const authStore = useAuthStore();

    const itemCount = computed(() => items.value.length);

    async function fetchWishlist() {
        if (!authStore.token) return;
        loading.value = true;
        try {
            const response = await axios.get('/api/wishlist');
            items.value = Array.isArray(response.data) ? response.data : [];
        } catch (error) {
            console.error('Failed to fetch wishlist', error);
        } finally {
            loading.value = false;
        }
    }

    async function toggleWishlist(product) {
        if (!authStore.token) {
            router.push('/login');
            return false;
        }

        const index = items.value.findIndex(item => item.id === product.id);
        const originalItems = [...items.value];
        
        // Optimistic Update
        if (index > -1) {
            items.value.splice(index, 1);
        } else {
            items.value.push(product);
        }
        
        try {
            if (index > -1) {
                // Remove from backend
                await axios.delete(`/api/wishlist/${product.id}`);
            } else {
                // Add to backend
                await axios.post('/api/wishlist', { product_id: product.id });
            }
            return true;
        } catch (error) {
            // Revert on error
            items.value = originalItems;
            console.error('Failed to toggle wishlist', error);
            return false;
        }
    }

    function isInWishlist(productId) {
        return items.value.some(item => item.id === productId);
    }

    return { items, loading, itemCount, fetchWishlist, toggleWishlist, isInWishlist };
});
