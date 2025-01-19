<template>
  <span>
    {{ displayText }}
  </span>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  expirationDate: {
    type: String,
    required: true
  }
});

const displayText = ref('');

const getExpiryCountdown = (expirationDate) => {
  const now = new Date();
  const expiryDate = new Date(expirationDate);
  const threeMonthsFromNow = new Date();
  threeMonthsFromNow.setMonth(now.getMonth() + 3);

  if (expiryDate <= now) {
    // Return the original expiration date if expired
    return `${expirationDate}`;
  } else if (expiryDate <= threeMonthsFromNow) {
    const timeDiff = expiryDate - now;
    const daysLeft = Math.floor(timeDiff / (1000 * 60 * 60 * 24));
    const hoursLeft = Math.floor((timeDiff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

    const dayString = daysLeft > 0 ? `${daysLeft} day${daysLeft > 1 ? 's' : ''}` : '';
    const hourString = hoursLeft > 0 ? `${hoursLeft} hour${hoursLeft > 1 ? 's' : ''}` : '';

    return `${dayString}${daysLeft > 0 && hoursLeft > 0 ? ' and ' : ''}${hourString} left`;
  }
  return expirationDate;
};

const updateDisplayText = () => {
  displayText.value = getExpiryCountdown(props.expirationDate);
};

onMounted(() => {
  updateDisplayText();
  const interval = setInterval(updateDisplayText, 1000);

  onUnmounted(() => {
    clearInterval(interval);
  });
});

watch(() => props.expirationDate, updateDisplayText);
</script>
