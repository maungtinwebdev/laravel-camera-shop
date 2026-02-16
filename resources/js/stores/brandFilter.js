import { defineStore } from 'pinia';
import { ref } from 'vue';

const DEFAULT_BRANDS = ['Canon', 'Nikon', 'Sony', 'Fujifilm', 'Panasonic'];

export const useBrandFilterStore = defineStore('brandFilter', () => {
    const brands = ref(DEFAULT_BRANDS);
    const selectedBrands = ref([]);

    function setSelectedBrands(value) {
        selectedBrands.value = Array.isArray(value) ? value : [];
    }

    function setFromQuery(queryValue) {
        if (!queryValue) {
            selectedBrands.value = [];
            return;
        }

        selectedBrands.value = String(queryValue)
            .split(',')
            .map(item => item.trim())
            .filter(Boolean);
    }

    function clear() {
        selectedBrands.value = [];
    }

    return {
        brands,
        selectedBrands,
        setSelectedBrands,
        setFromQuery,
        clear,
    };
});
