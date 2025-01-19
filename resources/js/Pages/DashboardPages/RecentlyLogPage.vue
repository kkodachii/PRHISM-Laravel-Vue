<script setup>
import { ref, onMounted, watch } from "vue";
import { initFlowbite } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import { useForm, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    recentlylog: Object,
});

const form = useForm({
    start_date: "",
    end_date: "",
    search: "",
});

onMounted(() => {
    initFlowbite();

    const today = new Date();
    const pastWeek = new Date();
    pastWeek.setDate(today.getDate() - 7);

    form.start_date = pastWeek.toISOString().split("T")[0];
    form.end_date = today.toISOString().split("T")[0];
});

const filterLogs = () => {
    form.get("/recentlylog", {
        preserveState: true,
        preserveScroll: true,
    });
};

// Search functionality with debounce
watch(
    () => form.search,
    debounce(() => {
        filterLogs();
    }, 500)
);
</script>

<template>
    <Head title="Recently Added" />

    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
        <!-- Search and filter -->
        <div
            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        >
            <div class="w-full md:w-1/2">
                <form class="flex items-center" @submit.prevent="filterLogs">
                    <div class="relative w-full">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                aria-hidden="true"
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewbox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <input
                            type="search"
                            class="bg-gray-50 border text-gray-900 rounded-lg w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search by name"
                            v-model="form.search"
                        />
                    </div>
                </form>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div
            class="hidden md:block relative overflow-x-auto sm:rounded-lg mt-4"
        >
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700"
                >
                    <tr>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Date Added</th>
                    </tr>
                </thead>
                <tbody
                    v-if="
                        !recentlylog.data ||
                        recentlylog.data.length === 0
                    "
                >
                    <tr>
                        <td
                            colspan="8"
                            class="text-center py-4 text-gray-500 dark:text-gray-400"
                        >
                            No data available
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr
                        v-for="(item, index) in recentlylog.data"
                        :key="index"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <td class="px-6 py-4">
                            <span
                                :class="{
                                    'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300':
                                        item.type === 'Medicine',
                                    'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300':
                                        item.type === 'Equipment',
                                    'bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-300':
                                        item.type === 'Medical Supply',
                                }"
                                class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                            >
                                {{ item.type }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 font-medium text-black dark:text-white"
                        >
                            {{ item.name }}
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                            {{ item.date_added }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden mt-4">
            <div class="grid grid-cols-1 gap-4 p-4">
                <div
                    v-for="(item, index) in recentlylog.data"
                    :key="index"
                    class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4"
                >
                    <div class="flex justify-between items-start">
                        <p
                            class="font-medium text-gray-900 dark:text-white overflow-hidden overflow-ellipsis"
                        >
                            {{ item.name }}
                        </p>
                        <span
                            :class="{
                                'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300':
                                    item.type === 'Medicine',
                                'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300':
                                    item.type === 'Equipment',
                                'bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-300':
                                    item.type === 'Medical Supply',
                            }"
                            class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                        >
                            {{ item.type }}
                        </span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ item.date_added }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination :paginator="recentlylog" />
    </div>
</template>
