<!-- ToastListItem.vue -->
<script setup>
import { onMounted } from "vue";

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: 'success', // default to 'success'
        validator: (value) => ['success', 'warning', 'danger', 'error'].includes(value)
    },
    duration: {
        type: Number,
        default: 2000,
    },
});

const emit = defineEmits(["remove"]);

onMounted(() => {
    setTimeout(() => emit("remove"), props.duration);
});

const iconPaths = {
    success: "M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z",
    warning: "M10 1a9 9 0 1 0 9 9A9.01 9.01 0 0 0 10 1ZM9 4h2v5H9V4Zm0 7h2v2H9v-2Z",
    danger: "M10 1a9 9 0 1 0 9 9A9.01 9.01 0 0 0 10 1ZM9 4h2v6H9V4Zm0 8h2v2H9v-2Z",
    error: "M10 1a9 9 0 1 0 9 9A9.01 9.01 0 0 0 10 1ZM9 4h2v6H9V4Zm0 8h2v2H9v-2Z", // Add appropriate path for error icon
};

const iconClasses = {
    success: "text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200",
    warning: "text-yellow-500 bg-yellow-100 dark:bg-yellow-800 dark:text-yellow-200",
    danger: "text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200",
    error: "text-red-600 bg-red-200 dark:bg-red-800 dark:text-red-200", // Add styles for error
};
</script>

<template>
    <div
        class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800"
        role="alert"
    >
        <div
            :class="`inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg ${iconClasses[props.type]}`"
        >
            <svg
                class="w-5 h-5"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                viewBox="0 0 20 20"
            >
                <path
                    :d="iconPaths[props.type]"
                />
            </svg>
            <span class="sr-only">{{ props.type }} icon</span>
        </div>
        <div class="ml-3 text-sm font-normal">{{ props.message }}</div>
        <button
            @click="emit('remove')"
            type="button"
            class="-mx-1.5 -my-1.5 ml-auto inline-flex h-8 w-8 rounded-lg bg-white p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900 focus:ring-2 focus:ring-gray-300 dark:bg-gray-800 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-white"
            data-dismiss-target="#toast-default"
            aria-label="Close"
        >
            <span class="sr-only">Close</span>
            <svg
                aria-hidden="true"
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                ></path>
            </svg>
        </button>
    </div>
</template>
