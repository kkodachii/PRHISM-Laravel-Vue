<template>
  <div class="relative flex items-center space-x-3 w-full md:w-auto">
    <button
      :id="dropdownButtonId"
      @click="toggleDropdown"
      class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
      type="button"
    >
      <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-5 w-6 mr-2 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
      </svg>
      Filter
      <span v-if="selectedCount > 0">({{ selectedCount }})</span>
      <svg class="-mr-1 ml-1.5 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path clip-rule="evenodd" fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
      </svg>
    </button>

    <!-- Delivery Filter Component -->
    <deliveryFilter :selectedStatus="selectedStatus" :dropdownId="dropdownId" @status-updated="updateQuery" />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import deliveryFilter from './deliveryFilter.vue'; 

// Props
const props = defineProps({
  dropdownButtonId: {
    type: String,
    required: true,
  },
  dropdownId: {
    type: String,
    required: true,
  },
  selectedStatus: {
    type: Array,
    required: true,
  },
});

// Emit
const emit = defineEmits(['status-updated']);

// Function to toggle the dropdown visibility
const toggleDropdown = () => {
  const dropdown = document.getElementById(props.dropdownId);
  if (dropdown) {
    dropdown.classList.toggle('hidden');
  }
};

// Handle the status update from the filter
const updateQuery = () => {
  emit('status-updated');
};

// Computed property to get the count of selected checkboxes
const selectedCount = computed(() => {
  return props.selectedStatus.filter(status => status.selected).length;
});
</script>
