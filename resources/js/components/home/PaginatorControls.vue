<template>
  <div class="py-10 flex flex-col md:flex-row items-center justify-between gap-4">
    <p class="text-sm text-gray-500">
      Page {{ currentPage }} of {{ lastPage }}
    </p>

    <div class="flex items-center gap-2 flex-wrap justify-center">
      <button
        @click="$emit('go-to-page', currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-2 rounded-lg border text-sm font-bold transition"
        :class="currentPage === 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
      >
        Prev
      </button>

      <button
        v-for="page in visiblePages"
        :key="page"
        @click="$emit('go-to-page', page)"
        class="w-9 px-3 py-2 rounded-lg border text-sm font-bold transition"
        :class="page === currentPage ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
      >
        {{ page }}
      </button>

      <button
        @click="$emit('go-to-page', currentPage + 1)"
        :disabled="currentPage === lastPage"
        class="px-3 py-2 rounded-lg border text-sm font-bold transition"
        :class="currentPage === lastPage ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-700 hover:bg-gray-50'"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  lastPage: {
    type: Number,
    required: true,
  },
  visiblePages: {
    type: Array,
    required: true,
  },
});

defineEmits(['go-to-page']);
</script>
