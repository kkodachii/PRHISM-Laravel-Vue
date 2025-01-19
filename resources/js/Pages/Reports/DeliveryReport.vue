<script setup>
import { Inertia } from '@inertiajs/inertia';
import { ref, watch } from 'vue';

import DashboardLayout from '@/Layouts/DashboardLayout.vue';

defineOptions({layout: DashboardLayout});

defineProps ({
    barangays:Array
});

const barangay= ref('All');
const dateRange= ref('');
const date_option = ref('All');
const range = false;

function generateReport() {
    Inertia.get(route('reports.delivery.generate'), {
        barangay: barangay.value,
        dateRange: dateRange.value,
    });
};

</script>
<template>
    <Head title="Delivery Report" />
    <div
        class="bg-white p-5 dark:bg-gray-800 relative shadow-md sm:rounded-lg"
    >
        <form @submit.prevent="generateReport">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-xl md:text-3xl font-bold">
                        Generate Delivery Report
                    </h1>
                    <p class="text-md text-gray-700">List of Deliveries</p>
                </div>
                <button
                    type="submit"
                    class="flex items-center justify-center h-10 gap-2 text-white text-sm bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                >
                    <svg
                        class="w-[20px] h-[20px] text-white dark:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm-1 9a1 1 0 1 0-2 0v2a1 1 0 1 0 2 0v-2Zm2-5a1 1 0 0 1 1 1v6a1 1 0 1 1-2 0v-6a1 1 0 0 1 1-1Zm4 4a1 1 0 1 0-2 0v3a1 1 0 1 0 2 0v-3Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Generate Report
                </button>
            </div>

            <div class="grid grid-cols-1 mt-5 p-3 gap-5 md:w-1/3">
                <div class="col-span-1">
                    <label class="block mb-2 font-medium text-md text-gray-800 dark:text-white" for="barangay">Select Barangay:</label>
                    <select
                        v-model="barangay"
                        id="barangay"
                        required=""
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    >
                        <option value="All">All</option>
                        <option v-for="barangay in barangays" :key="barangay.id" :value="barangay.id">
                            {{ barangay.barangay_name }}
                        </option>
                    </select>
                </div>
                <div class="col-span-1">
                    <label class="block mb-2 font-medium text-md text-gray-800 dark:text-white" for="date_option">Select date:</label>
                    <select
                    v-model="date_option"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                        <option value="All">All</option>
                        <option value="Range">Date range</option>
                    </select>
                </div>
                <div v-if="date_option=='Range'" class="col-span-1">
                    <label class="block mb-2 font-medium text-md text-gray-800 dark:text-white" for="date_option">Select date:</label>
                    <vue-tailwind-datepicker
                        v-model="dateRange"
                        :shortcuts="false"
                        id="dateRange"
                        placeholder="Select date Range"
                    />
                </div>
            </div>
        </form>
    </div>
</template>
