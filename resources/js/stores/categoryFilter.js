import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useCategoryFilterStore = defineStore('categoryFilter', () => {
    const categories = ref([]);
    const selectedCategories = ref([]);

    async function fetchCategories() {
        const response = await axios.get('/api/categories');
        categories.value = Array.isArray(response.data) ? response.data : [];
    }

    function setSelectedCategories(value) {
        selectedCategories.value = Array.isArray(value) ? value : [];
    }

    function setFromQuery(queryValue) {
        if (!queryValue) {
            selectedCategories.value = [];
            return;
        }

        selectedCategories.value = String(queryValue)
            .split(',')
            .map(item => item.trim())
            .filter(Boolean);
    }

    function clear() {
        selectedCategories.value = [];
    }

    return {
        categories,
        selectedCategories,
        fetchCategories,
        setSelectedCategories,
        setFromQuery,
        clear,
    };
});
