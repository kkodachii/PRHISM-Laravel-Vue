<template>
  <span>
    {{ formattedDate }}
  </span>
</template>

<script setup>
import { ref, computed } from 'vue';

// Define a prop to accept the date
const props = defineProps({
  date: {
    type: String,
    required: true
  }
});

// Format the date string
const formatDate = (dateString) => {
  const now = new Date();
  const givenDate = new Date(dateString);

  const diffInSeconds = Math.floor((now - givenDate) / 1000);
  const diffInMinutes = Math.floor(diffInSeconds / 60);
  const diffInHours = Math.floor(diffInMinutes / 60);
  const diffInDays = Math.floor(diffInHours / 24);

  if (diffInSeconds < 60) {
    return `${diffInSeconds} seconds ago`;
  } else if (diffInMinutes < 60) {
    return `${diffInMinutes} minutes ago`;
  } else if (diffInHours < 24) {
    return `${diffInHours} hours ago`;
  } else if (diffInDays === 1) {
    return 'Yesterday';
  } else {
    // For dates further back, use the full date format
    return givenDate.toLocaleString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    });
  }
};

const formattedDate = computed(() => {
  return formatDate(props.date);
});
</script>

<style scoped>
/* Add any styling you need here */
</style>
