<template>
  <div id="filterDropdown" class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
    <!-- Type Section -->
    <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">Choose Type</h6>
    <select
      v-model="selectedType"
      class="block w-full px-3 py-2 mt-2 text-sm border border-gray-300 rounded-md dark:bg-gray-600 dark:text-white dark:border-gray-500 focus:ring-blue-500 focus:border-blue-500"
      @change="emitTypeUpdate"
    >
      <option v-for="option in typeOptions" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>

    <!-- Status Section -->
    <h6 class="mb-3 mt-4 text-sm font-medium text-gray-900 dark:text-white">Choose Status</h6>
    <div class="space-y-0.5" aria-labelledby="filterDropdownButton">
      <fwb-radio
        v-for="status in statuses"
        :key="status.id"
        v-model="selectedStatusId"
        :label="status.name"
        :value="status.id"
        :name="'status'"
        @change="emitStatusUpdate"
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
  selectedStatus: {
    type: Array,
    required: true,
  },
  selectedType: {
    type: String,
    required: true,
  },
  typeOptions: {
    type: Array,
    required: true,
  }
});

// Emits
const emit = defineEmits(['status-updated', 'type-updated']);

// Local state
const statuses = ref(props.selectedStatus);
const selectedType = ref(props.selectedType);

// Separate ref to manage the selected status ID
const selectedStatusId = ref(statuses.value.find(status => status.selected)?.id || null);

// Emit status update when selection changes
const emitStatusUpdate = debounce(() => {
  emit('status-updated', selectedStatusId.value); // Emit the selected status ID

}, 500);

// Emit type update when selection changes
const emitTypeUpdate = debounce(() => {
  emit('type-updated', selectedType.value);

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
  () => props.selectedType,
  () => {
    selectedType.value = props.selectedType;
  }
);
</script>