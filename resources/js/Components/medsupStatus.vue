<template>
    <span :class="statusClasses" class="text-xs font-medium me-2 px-3 py-1 rounded-full">
      {{ status }}
    </span>
  </template>
  
  <script setup>
  import { computed } from 'vue';
  
  // Define the props
  const props = defineProps({
    quantity: {
      type: Number,
      required: true
    }
  });
  
  // Computed property to get the status based on quantity
  const status = computed(() => {
    if (props.quantity === 0) {
      return "Out of stock";
    } else if (props.quantity <= 20) {
      return "Low on stock";
    } else if (props.quantity >= 21) {
      return "On stock";
    }
    return 'Unknown'; // Default case if needed
  });
  
  // Computed property to get the status classes based on quantity
  const statusClasses = computed(() => {
    switch (status.value) {
      case 'Out of stock':
        return 'bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300';
      case 'Low on stock':
        return 'bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
      case 'On stock':
        return 'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300';
      default:
        return 'bg-[#FFFFFF] text-black px-2 py-1 rounded'; // Fallback style
    }
  });
  </script>
  
  <style scoped>
  /* You can add additional styling here if needed */
  </style>
  