<template>
  <aside class="w-full md:w-64 shrink-0">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 sticky top-24">
      <div class="mb-8">
        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
          <FunnelIcon class="w-5 h-5 text-blue-600" /> Filters
        </h3>

        <div class="mb-6">
          <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Categories</h4>
          <div class="space-y-2">
            <label v-for="cat in categories" :key="cat.id" class="flex items-center gap-3 cursor-pointer group">
              <div class="relative flex items-center">
                <input
                  type="checkbox"
                  :value="cat.slug"
                  v-model="localSelectedCategories"
                  class="peer h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition"
                >
              </div>
              <span class="text-gray-600 group-hover:text-blue-600 transition">{{ cat.name }}</span>
            </label>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Brands</h4>
          <div class="space-y-2">
            <label v-for="brand in brands" :key="brand" class="flex items-center gap-3 cursor-pointer group">
              <input
                type="checkbox"
                :value="brand.toLowerCase()"
                v-model="localSelectedBrands"
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              >
              <span class="text-gray-600 group-hover:text-blue-600 transition">{{ brand }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed } from 'vue';
import { FunnelIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
  brands: {
    type: Array,
    required: true,
  },
  selectedCategories: {
    type: Array,
    required: true,
  },
  selectedBrands: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(['update:selectedCategories', 'update:selectedBrands']);

const localSelectedCategories = computed({
  get: () => props.selectedCategories,
  set: (value) => emit('update:selectedCategories', value),
});

const localSelectedBrands = computed({
  get: () => props.selectedBrands,
  set: (value) => emit('update:selectedBrands', value),
});
</script>
