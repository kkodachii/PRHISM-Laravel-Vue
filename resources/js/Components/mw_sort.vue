<template>
  <div id="sortFieldDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">


    <!-- Sort Direction Section -->
    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Sort By</h6>
    <select
      v-model="selectedSortDirection"
      class="block w-full px-3 py-2 mt-2 text-sm border border-gray-300 rounded-md dark:bg-gray-600 dark:text-white dark:border-gray-500 focus:ring-blue-500 focus:border-blue-500"
      @change="emitSortDirectionUpdate"
    >
      <option v-for="direction in sortDirections" :key="direction.value" :value="direction.value">
        {{ direction.label }}
      </option>
    </select>

        <!-- Sort Field Section -->
        <h6 class="mb-3 mt-4 text-sm font-medium text-gray-900 dark:text-white"></h6>
    <div class="space-y-0.5">
      <fwb-radio
        v-for="option in sortFieldOptions"
        :key="option.value"
        v-model="selectedSortField"
        :label="option.label"
        :value="option.value"
        :name="'sortField'"
        @change="emitSortFieldUpdate"
      />
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
  selectedSortField: {
    type: String,
    required: true,
  },
  selectedSortDirection: {
    type: String,
    required: true,
  },
  sortFieldOptions: {
    type: Array,
    required: true,
  },
});

// Emits
const emit = defineEmits(['sort-field-updated', 'sort-direction-updated']);

// Local state
const selectedSortField = ref(props.selectedSortField);
const selectedSortDirection = ref(props.selectedSortDirection);

// Options for sort directions
const sortDirections = [
  { value: 'asc', label: 'Ascending' },
  { value: 'desc', label: 'Descending' },
];

// Emit sort field update when selection changes
const emitSortFieldUpdate = debounce(() => {
  emit('sort-field-updated', selectedSortField.value); // Emit the selected sort field
}, 500);

// Emit sort direction update when selection changes
const emitSortDirectionUpdate = debounce(() => {
  emit('sort-direction-updated', selectedSortDirection.value);
}, 500);

// Watch for changes in the props
watch(
  () => props.selectedSortField,
  () => {
    selectedSortField.value = props.selectedSortField;
  }
);

watch(
  () => props.selectedSortDirection,
  () => {
    selectedSortDirection.value = props.selectedSortDirection;
  }
);
</script>