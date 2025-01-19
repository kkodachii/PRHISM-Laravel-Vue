<script setup>
import { computed } from "vue";

const props = defineProps({
  activities: Array,
});

// Define the status classes for each type/status
const statusClasses = {
  Delete: "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300",
  Deactivate: "bg-gray-200 text-gray-800 dark:bg-gray-900 dark:text-gray-300",
  Reactivate: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
  Dispense: "bg-purple-200 text-purple-800 dark:bg-purple-900 dark:text-purple-300",
  Update: "bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300",
  Request: "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
  Delivery: "bg-teal-200 text-teal-800 dark:bg-teal-900 dark:text-teal-300",
  Create: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
  Restore: "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
  Reject: "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300",
  Return: "bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300",
};

// Create a computed property to return the class for each activity type
const getStatusClass = (type) => {
  return statusClasses[type] || "";
};

const formatDate = (date) => {
    const options = {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZone: 'UTC',
    };
    return new Date(date).toLocaleDateString('en-US', options);
};
</script>

<template>
  <div class="rounded-lg bg-white shadow sm:rounded-lg border-gray-200 dark:border-gray-600 mb-5">
    <!-- Header Section -->
    <div class="flex flex-row justify-between p-4">
      <h6 class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white">
        Activity Log
      </h6>
      <Link
        :href="route('activitylog.index')"
        class="text-l font-medium text-blue-600 dark:text-blue-500 hover:underline"
      >See all</Link>
    </div>

    <!-- Desktop Table View -->
    <div class="overflow-hidden lg:h-52 md:h-48 h-20 hidden md:block">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
          <tr>
            <th scope="col" class="px-6 py-3 whitespace-nowrap">Type</th>
            <th scope="col" class="px-6 py-3 hidden md:table-cell whitespace-nowrap">User</th>
            <th scope="col" class="px-6 py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="activity in activities"
            :key="activity.id"
            class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
          >
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(activity.type)" class="px-3 py-1 rounded-full text-xs font-medium">
                {{ activity.type }}
              </span>
            </td>
            <td class="px-6 py-4 hidden md:table-cell whitespace-nowrap">{{ activity.name }}</td>
            <th
              scope="row"
              class="px-6 py-4 font-medium text-gray-900 overflow-hidden overflow-ellipsis dark:text-white"
            >
              {{ activity.description }}
            </th>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="block md:hidden overflow-y-auto h-48">
  <div class="grid grid-cols-1 gap-4 p-4">
    <div
      v-for="activity in activities"
      :key="activity.id"
      class="bg-white shadow-md rounded-lg p-4 border dark:bg-gray-800"
    >
      <div class="flex justify-between items-start">
        <p class="font-medium text-gray-900 dark:text-white overflow-hidden overflow-ellipsis">
          {{ activity.name }}
        </p>
        <span :class="getStatusClass(activity.type)" class="px-2 py-1 rounded-full text-xs font-medium">
          {{ activity.type }}
        </span>
      </div>
      <p class="mt-2 text-sm text-gray-500 overflow-hidden overflow-ellipsis">
        {{ activity.description }}
      </p>
    </div>
  </div>
</div>

  </div>
</template>

