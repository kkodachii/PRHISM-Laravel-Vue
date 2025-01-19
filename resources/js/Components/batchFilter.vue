<template>
    <div id="filterDropdown" class="z-50 hidden w-64 p-3 bg-white rounded-lg shadow-lg dark:bg-gray-700">
      <!-- Type Filter -->
      <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Type</h6>
      <select v-model="selectedType" @change="updateQuery" class="w-full bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
        <option v-for="option in typeOptions" :key="option.value" :value="option.value">
          {{ option.label }}
        </option>
      </select>
  
      <!-- Status Filter -->
      <h6 class="mt-6 mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Status</h6>
      <ul class="grid grid-cols-2 gap-1 text-sm">
        <li v-for="status in statusOptions" :key="status.id" class="flex items-center">
          <input 
            :id="status.id" 
            type="checkbox" 
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" 
            v-model="status.selected"
            @change="updateQuery"
          />
          <label :for="status.id" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ status.name }}</label>
        </li>
      </ul>
  
      <!-- Condition Filter -->
      <h6 class="mt-6 mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Condition</h6>
      <ul class="grid grid-cols-2 gap-1 text-sm">
        <li v-for="condition in conditionOptions" :key="condition.id" class="flex items-center">
          <input 
            :id="condition.id" 
            type="checkbox" 
            class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500" 
            v-model="condition.selected"
            @change="updateQuery"
          />
          <label :for="condition.id" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ condition.name }}</label>
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
    selectedBatchID: {
      type: String,
      required: true
    },
    selectedType: {
      type: String,
      required: true
    },
    selectedStatus: {
      type: Array,
      required: true
    },
    selectedCondition: {
      type: Array,
      required: true
    },
    typeOptions: {
      type: Array,
      required: true
    },
    statusOptions: {
      type: Array,
      required: true
    },
    conditionOptions: {
      type: Array,
      required: true
    }
  });
  
  // Emits
  const emit = defineEmits(['filter-updated']);
  
  // Local state
  const statuses = ref(props.statusOptions);
  const conditions = ref(props.conditionOptions);
  
  // Emit filter update when selection changes
  const updateQuery = debounce(() => {
    const statusFilters = statuses.value
      .filter(status => status.selected)
      .map(status => status.id)
      .join(',');
    
    const conditionFilters = conditions.value
      .filter(condition => condition.selected)
      .map(condition => condition.id)
      .join(',');
    
    emit('filter-updated', {
      batchId: props.selectedBatchID,
      type: props.selectedType,
      status: statusFilters,
      condition: conditionFilters
    });
  
    closeDropdown(); // Call closeDropdown here
  }, 500);
  
  // Function to close the dropdown
  function closeDropdown() {
    const dropdownButton = document.getElementById('filterDropdownButton');
    if (dropdownButton) {
      dropdownButton.click(); // Simulate a click to close the dropdown
    }
  }
  
  // Watch for changes in props
  watch(
    () => props.statusOptions,
    () => {
      statuses.value = props.statusOptions;
    },
    { deep: true }
  );
  
  watch(
    () => props.conditionOptions,
    () => {
      conditions.value = props.conditionOptions;
    },
    { deep: true }
  );
  </script>
  