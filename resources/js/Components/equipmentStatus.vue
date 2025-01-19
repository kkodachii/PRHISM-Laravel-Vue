<template>

      <span :class="statusClasses" class="text-xs font-medium me-2 px-3 py-1 rounded-full">
        {{ statusText }}
      </span>

  </template>
  
  <script setup>
  import { computed, defineProps } from 'vue';
  
  // Define props for the component
  const props = defineProps({
    status: {
      type: String,
      required: true
    }
  });
  
  // Function to get status text
  const getStatus = (status) => {
    if (status === "Poor condition") {
      return "Poor condition";
    } else if (status === "Fair condition") {
      return "Fair condition";
    } else if (status === "Good condition") {
      return "Good condition";
    } else if (status === "New") {
      return "New";
    } 
    
    
    return 'Unknown';
  };
  
  // Function to get CSS classes based on status
  const getStatusClasses = (status) => {
    switch (status) {
      case 'Poor condition':
        return 'bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300';
      case 'Fair condition':
        return 'bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300';
      case 'Good condition':
        return 'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300';
      case 'New':
        return 'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
      default:
        return 'bg-[#FFFFFF] text-black px-2 py-1 rounded';
    }
  };
  
  // Computed properties for status text and classes
  const statusText = computed(() => getStatus(props.status));
  const statusClasses = computed(() => getStatusClasses(statusText.value));
  </script>
  