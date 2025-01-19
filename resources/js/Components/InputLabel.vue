<script setup>
defineProps({
    value: {
        type: null, // Accept any data type
    },
});
</script>

<template>
    <label class="block mb-2 font-medium text-sm text-gray-700 dark:text-white">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
