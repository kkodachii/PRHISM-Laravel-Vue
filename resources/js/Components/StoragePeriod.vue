<template>
  <div class="flex flex-col flex-grow items-left text-left leading-normal">
    <div class="text-left">
      <p class="mb-2 text-l font-semibold tracking-tight text-gray-900 dark:text-white">
        Storage period
      </p>
      <p class="text-sm">
        {{ formattedStoragePeriod }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  dateReceived: {
    type: String,
    required: true,
  },
});

function formatStoragePeriod(receivedDate) {
  const currentDate = new Date();

  // Check if the received date is in the future
  if (receivedDate > currentDate) {
    const daysDifference = Math.ceil((receivedDate - currentDate) / (1000 * 60 * 60 * 24));
    return `Available in ${daysDifference} day${daysDifference > 1 ? 's' : ''}`;
  }

  // Calculate years, months, and days
  const years = currentDate.getFullYear() - receivedDate.getFullYear();
  let months = currentDate.getMonth() - receivedDate.getMonth();
  let days = currentDate.getDate() - receivedDate.getDate();

  // Adjust months and days if necessary
  if (days < 0) {
    months -= 1;
    const daysInPreviousMonth = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth(),
      0
    ).getDate(); // get days in the previous month
    days += daysInPreviousMonth;
  }

  if (months < 0) {
    months += 12;
  }

  // Format the result
  if (years > 0) {
    return `${years} year${years > 1 ? 's' : ''} and ${months} month${months > 1 ? 's' : ''}`;
  } else if (months > 0) {
    return `${months} month${months > 1 ? 's' : ''} and ${days} day${days > 1 ? 's' : ''}`;
  } else {
    return `${days} day${days > 1 ? 's' : ''}`;
  }
}

const formattedStoragePeriod = computed(() => {
  const receivedDate = new Date(props.dateReceived);  
  return formatStoragePeriod(receivedDate);
});
</script>

<style scoped>
/* Add any necessary styles here */
</style>
