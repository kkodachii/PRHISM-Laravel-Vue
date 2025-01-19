<script setup>
import { ref, onMounted, watch } from "vue";
import { initFlowbite } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import { useForm, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    dispenseLogs: Object,
});

const search = ref("");

const getTypeDisplayName = (type) => {
    switch (type) {
        case "medicine":
            return "Medicine";
        case "equipment":
            return "Equipment";
        case "medical_supply":
            return "Medical Supply";
        default:
            return type;
    }
};

onMounted(() => {
    initFlowbite();
});


function updateQuery() {
    router.get(
        "/dispense_history",
        {
            search: search.value,
        },
        { preserveState: true }
    );
}

watch(
    search,
    debounce(
        (q) =>
            router.get(
                "/dispense_history",
                { search: q },
                { preserveState: true }
            ),
        500
    )
);
</script>

<template>
    <Head title="Recently Added" />
    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
        <div
            class="flex w-full px-5 mt-5 lg:w-auto flex-row justify-between gap-4"
        >
            <h2 class="text-2xl font-bold mb-2 px-1">Dispense History</h2>
            <Link
                :href="route('dispense.index')"
                class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-3 py-2 transition-colors duration-200"
                @click.prevent
            >
                <svg
                    class="h-5 w-4 mr-2"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd"
                    />
                </svg>
                Dispense
            </Link>
        </div>
        <!-- Search and filter -->
        <div
            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        >
            <div class="w-full md:w-1/2">
                <form class="flex items-center">
                    <label for="simple-search" class="sr-only">Search</label>
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
                            id="simple-search"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search"
                            required=""
                            v-model="search"
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
                        <th class="px-8 py-3 whitespace-nowrap">
                            Recipient's Name
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">Address</th>
                        <th class="px-6 py-3 whitespace-nowrap">Birthday</th>
                        <th class="px-6 py-3 whitespace-nowrap">Age</th>
                        <th class="px-6 py-3 whitespace-nowrap">Type</th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            Dispensed Item
                        </th>
                        <th class="px-6 py-3 whitespace-nowrap">Quantity</th>
                        <th class="px-6 py-3 whitespace-nowrap">
                            Dispensed Date
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- No data available row -->
                    <tr
                        v-if="
                            !dispenseLogs.data || dispenseLogs.data.length === 0
                        "
                    >
                        <td
                            colspan="8"
                            class="text-center py-4 text-gray-500 dark:text-gray-400"
                        >
                            No data available
                        </td>
                    </tr>

                    <!-- Dispense logs row -->
                    <tr
                        v-for="(log, index) in dispenseLogs.data"
                        :key="index"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700"
                    >

                        <td
                            class="px-8 py-4 font-medium text-black dark:text-white whitespace-nowrap"
                        >
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.dispensations.recipients_name ===
                                            null ||
                                        log.dispensations.recipients_name ===
                                            '',
                                }"
                            >
                                {{ log.dispensations.recipients_name || "N/A" }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 font-medium text-black dark:text-white whitespace-nowrap"
                        >
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.dispensations.address === null ||
                                        log.dispensations.address === '',
                                }"
                            >
                                {{ log.dispensations.address || "N/A" }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 font-medium text-black dark:text-white whitespace-nowrap"
                        >
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.dispensations.birthday === null ||
                                        log.dispensations.birthday === '',
                                }"
                            >
                                {{ log.dispensations.birthday || "N/A" }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 font-medium text-black dark:text-white whitespace-nowrap"
                        >
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.dispensations.age === null ||
                                        log.dispensations.age === '',
                                }"
                            >
                                {{ log.dispensations.age || "N/A" }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span
                                :class="{
                                    'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300':
                                        getTypeDisplayName(log.type) ===
                                        'Medicine',
                                    'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300':
                                        getTypeDisplayName(log.type) ===
                                        'Equipment',
                                    'bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-300':
                                        getTypeDisplayName(log.type) ===
                                        'Medical Supply',
                                }"
                                class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                            >
                                {{ getTypeDisplayName(log.type) }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.name === null || log.name === '',
                                }"
                            >
                                {{ log.name || "N/A" }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.quantity === null ||
                                        log.quantity === '',
                                }"
                            >
                                {{ log.quantity || "N/A" }}
                            </span>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap">
                            <span
                                :class="{
                                    'text-gray-500 dark:text-gray-400':
                                        log.dispensations.dispense_date ===
                                            null ||
                                        log.dispensations.dispense_date === '',
                                }"
                            >
                                {{ log.dispensations.dispense_date || "N/A" }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden mt-4">
            <div class="grid grid-cols-1 gap-6 p-4">
                <div
                    v-for="(log, index) in dispenseLogs.data"
                    :key="index"
                    class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-5 hover:shadow-xl transition-shadow duration-200"
                >
                    <div class="flex justify-between items-start">
                        <div>
                            <p
                                class="font-semibold text-gray-900 dark:text-white overflow-hidden overflow-ellipsis text-lg"
                            >
                                {{ log.name }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Quantity: {{ log.quantity }} <br />
                                Date: {{ log.dispensations.dispense_date }}
                            </p>
                        </div>
                        <span
                            :class="{
                                'bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300':
                                    getTypeDisplayName(log.type) === 'Medicine',
                                'bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300':
                                    getTypeDisplayName(log.type) ===
                                    'Equipment',
                                'bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-300':
                                    getTypeDisplayName(log.type) ===
                                    'Medical Supply',
                            }"
                            class="text-xs font-medium px-2.5 py-1 rounded-full"
                        >
                            {{ getTypeDisplayName(log.type) }}
                        </span>
                    </div>

                    <div
                        class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400"
                    >
                        <p v-if="log.dispensations.recipients_name">
                            <span
                                class="font-semibold text-gray-800 dark:text-gray-200"
                                >Recipient's Name:</span
                            >
                            {{ log.dispensations.recipients_name }}
                        </p>
                        <p v-if="log.dispensations.address">
                            <span
                                class="font-semibold text-gray-800 dark:text-gray-200"
                                >Address:</span
                            >
                            {{ log.dispensations.address }}
                        </p>
                        <p v-if="log.dispensations.birthday">
                            <span
                                class="font-semibold text-gray-800 dark:text-gray-200"
                                >Birthday:</span
                            >
                            {{ log.dispensations.birthday }}
                        </p>
                        <p v-if="log.dispensations.age">
                            <span
                                class="font-semibold text-gray-800 dark:text-gray-200"
                                >Age:</span
                            >
                            {{ log.dispensations.age }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <Pagination :paginator="dispenseLogs" :search="search" />
    </div>
</template>
