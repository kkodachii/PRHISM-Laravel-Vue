<template>
  <div id="filterDropdown" class="z-10 hidden w-68 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
    <!-- Type Section -->
    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Category</h6>
    <select
      v-model="selectedCategory"
      class="block w-full px-3 py-2 mt-2 text-sm border border-gray-300 rounded-md dark:bg-gray-600 dark:text-white dark:border-gray-500 focus:ring-blue-500 focus:border-blue-500"
      @change="emitCategoryUpdate"
    >
      <option value="all" :selected="selectedCategory === 'all'">All</option>
      <option v-for="option in categoryNames" :key="option.id" :value="option.category">
        {{ option.category }}
      </option>
    </select>

    <!-- Status Section -->
    <h6 class="mb-3 mt-4 text-sm font-medium text-gray-900 dark:text-white">Choose Status</h6>
    <div class="grid grid-cols-2 gap-4 text-sm" aria-labelledby="filterDropdownButton">
      <div v-for="status in statuses" :key="status.id" class="flex items-center">
        <input
          type="checkbox"
          v-model="status.selected"
          :id="status.id"
          :value="status.id"
          @change="emitStatusUpdate"
          class="h-4 w-4 text-blue-500 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-500 dark:ring-offset-gray-800"
        />
        <label :for="status.id" class="ml-2 text-sm text-gray-900 dark:text-white">{{ status.name }}</label>
      </div>
    </div>
  </div>
</template>




  <script setup>
  import { ref, watch } from 'vue';
  import debounce from 'lodash/debounce';
  import { defineEmits } from 'vue';
  import { FwbRadio } from 'flowbite-vue'; // Importing the FwbRadio component

  // Props
  const props = defineProps({
    selectedStatus: {
      type: Array,
      required: true,
    },
    selectedCategory: {
      type: String,
      required: true,
    },
    categoryNames: {
      type: Array,
      required: true,
    }
  });

  // Emits
  const emit = defineEmits(['status-updated', 'category-updated']);

  // Local state
  const statuses = ref(props.selectedStatus);
  const selectedCategory = ref(props.selectedCategory || 'all'); // Default to "all" if no category is selected

  // Separate ref to manage the selected status ID
  const selectedStatusId = ref(statuses.value.find(status => status.selected)?.id || null);

  // Emit status update when selection changes
  const emitStatusUpdate = debounce(() => {
    emit('status-updated', selectedStatusId.value); // Emit the selected status ID

  }, 500);

  // Emit category update when selection changes
  const emitCategoryUpdate = debounce(() => {
    emit('category-updated', selectedCategory.value);

  }, 500);

  // Watch for changes in the prop
  watch(
    () => props.selectedStatus,
    () => {
      statuses.value = props.selectedStatus;
      selectedStatusId.value = statuses.value.find(status => status.selected)?.id || null; // Update selected status ID
    },
    { deep: true }
  );

  watch(
    () => props.selectedCategory,
    () => {
      selectedCategory.value = props.selectedCategory || 'all'; // Default to "all" if no category is selected
    }
  );
  </script>
