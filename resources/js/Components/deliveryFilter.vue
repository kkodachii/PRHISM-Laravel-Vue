<template>
    <div :id="dropdownId" class="dropdown-content z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
      <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Status</h6>
      <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
        <li v-for="status in statuses" :key="status.id" class="flex items-center">
          <input
            :id="status.id"
            type="checkbox"
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
            v-model="status.selected"
            @change="emitStatusUpdate"
          />
          <label :for="status.id" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ status.name }}</label>
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { ref, watch } from 'vue';
  import debounce from 'lodash/debounce';
  import { defineEmits, defineProps } from 'vue';
  
  // Props
  const props = defineProps({
    selectedStatus: {
      type: Array,
      required: true
    },
    dropdownId: {
      type: String,
      required: true
    }
  });
  
  // Emits
  const emit = defineEmits(['status-updated']);
  
  // Local state
  const statuses = ref(props.selectedStatus);
  
  // Emit status update when selection changes
  const emitStatusUpdate = debounce(() => {
    emit('status-updated');
    closeDropdown(); // Call closeDropdown here
  }, 500);
  
  // Function to close the dropdown
  function closeDropdown() {
    const dropdown = document.getElementById(props.dropdownId);
    if (dropdown) {
      dropdown.classList.add('hidden');
    }
  }
  
  // Watch for changes in the prop
  watch(
    () => props.selectedStatus,
    () => {
      statuses.value = props.selectedStatus;
    },
    { deep: true }
  );
  </script>
  
  <style scoped>
  .dropdown-content {   
    position: absolute;
    top: 120%; 
    left: 50%; 
    transform: translateX(-50%);
    width: 100%; 
    max-width: 16rem; 
  }
  
  @media (min-width: 640px) {
    .dropdown-content {
      max-width: none;
      width: 12rem;
    }
  }
  </style>
  