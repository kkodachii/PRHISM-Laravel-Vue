<template>
    <div>
      <span v-for="(status, index) in computedStatuses" :key="index"
            :class="statusClasses[status]"
            class="text-xs font-medium me-2 px-3 py-1 rounded-full">
        {{ status }}
      </span>
    </div>
  </template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    all: {
        type: String,
        default: "",
    },
});

// Define the status classes for each status
const statusClasses = {
    "Out of stock": "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300",
    "Low on stock":
        "bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300",
    "On stock":
        "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    New: "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
    "Good condition":
        "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    "Fair condition":
        "bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300",
    "Poor condition":
        "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300",
    Expiring:
        "bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300",
    Expired: "bg-gray-200 text-gray-800 dark:bg-gray-900 dark:text-gray-300",
    Online: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    Offline: "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300",
    Medicine: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    Equipment: "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
    "Medical Supply": "bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-300",
    Deleted: "bg-gray-200 text-gray-800 dark:bg-gray-900 dark:text-gray-300",
    Create: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    Delete: "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300",
    Restore: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    Dispense: "bg-purple-200 text-purple-800 dark:bg-purple-900 dark:text-purple-300",
    Update: "bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300",
    Request: "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
    Delivery: "bg-teal-200 text-teal-800 dark:bg-teal-900 dark:text-teal-300",
    Return: "bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300",
    Approved: "bg-purple-200 text-purple-800 dark:bg-purple-900 dark:text-purple-300", 
    Returned: "bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300",
    Claim: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
};

// Compute the statuses based on quantity, expiration date, and status prop
const computedStatuses = computed(() => {
    let statuses = [];

    // Handle the `all` prop
    if (props.all) {
        const allStatuses = props.all.split(",").map((status) => status.trim());

        // Check if "Expired" is in the list and prioritize it
        if (allStatuses.includes("Expired")) {
            statuses = ["Expired"];
        } else {
            statuses = allStatuses;
        }
    }

    // Add the single status if no "Expired" and `status` prop is provided
    if (statuses.length === 0 && props.status) {
        statuses.push(props.status);
    }

    return statuses;
});

// Get the class for each status in the computed statuses
const statusClassesComputed = computed(() => {
    return computedStatuses.value.map((status) => statusClasses[status] || "");
});
</script>
