<template>
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50">
      <div class="relative p-4 w-full max-w-lg">
        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8">
          <h3 class="text-lg font-semibold mb-1">Select Quantity for {{ entry.name }}</h3>
          <p>Available: {{ available }}</p>
          <p v-if="entry.reserved > 0">Reserved: {{ entry.reserved }}</p>
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">
              Quantity
            </label>
            <input
              v-model="entry.requestedQuantity"
              @input="updateRequestedQuantity($event.target.value)"
              type="number"
              min="0"
              class="mt-1 block w-full p-2 border border-gray-300 rounded-md"
            />
            <!-- Conditional error message -->
            <p v-if="error" class="text-red-500 text-sm mt-2">Quantity cannot be more than available or less than 1</p>
          </div>
          <div class="mt-4 flex justify-end">
            <button
              @click="confirmSelection"
              :disabled="error || entry.requestedQuantity <= 0"
              class="bg-blue-500 text-white px-4 py-2 rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Confirm
            </button>
            <button @click="closeModal" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script setup>
  import { ref } from 'vue';

  const props = defineProps({
    showModal: Boolean,
    entry: Object
  });

  const emit = defineEmits(['confirm', 'close']);

  const error = ref(false);
  const available = props.entry.availableQuantity - props.entry.reserved;

  const updateRequestedQuantity = (value) => {
    const requestedQuantity = Number(value);
    error.value = requestedQuantity > available || requestedQuantity <= 0;
  };

  const confirmSelection = () => {
    if (!error.value) {
      emit('confirm', { ...props.entry, quantity: props.entry.requestedQuantity });
      closeModal();
    }
  };

  const closeModal = () => {
    emit('close');
  };
  </script>
