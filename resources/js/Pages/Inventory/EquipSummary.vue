<script setup>
import { ref, watch, onMounted } from "vue";
import { initFlowbite } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";
import ToastList from "@/Components/ToastList.vue";
import { debounce } from "lodash";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import StatusLabel from "../../Components/StatusLabel.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    equipmentStatus: Object,
    auth: Object,

});

const userRole = ref(props.auth.user.role_id);

onMounted(() => {
    initFlowbite();
    const status = new URLSearchParams(window.location.search).get("status");
    if (status) {
        selectedstatus.value = status;
    }
});
const selectedstatus = ref("");
const search = ref("");

watch(
  search,
  debounce(
    (q) => router.get(
      "/equipment_summary",
      { status: selectedstatus.value, search: q },
      { preserveState: true, preserveScroll: true }
    ),
    500
  )
);

const toggleAccordion = (itemId) => {
    if (activeAccordion.value && activeAccordion.value !== itemId) {
        const previousBodyRow = document.getElementById(
            `accordion-collapse-body-${activeAccordion.value}`
        );
        if (previousBodyRow) {
            previousBodyRow.classList.add("hidden");
        }
    }

    const bodyRow = document.getElementById(
        `accordion-collapse-body-${itemId}`
    );
    if (bodyRow) {
        bodyRow.classList.toggle("hidden");
    }

    activeAccordion.value = bodyRow.classList.contains("hidden")
        ? null
        : itemId;
};


</script>

<template>
    <Head title="Filter" />

    <div class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg">
        <ToastList />
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

        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
        <!-- Mobile View -->
        <div class="block md:hidden">
            <div v-for="equip in equipmentStatus.data" :key="equip.id" class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md border-b dark:border-gray-700 relative">
                <div class="flex flex-row justify-start">
                    <div class="flex items-center md:w-10 w-10 mx-4">
                        <img v-if="equip.type === 'Medicine'" src="/storage/icons/midwife/pill.svg" alt="Medicine Icon" />
                        <img v-if="equip.type === 'Equipment'" src="/storage/icons/midwife/microscope.svg" alt="Equipment Icon" />
                        <img v-if="equip.type === 'Medical Supply'" src="/storage/icons/midwife/bandaid.svg" alt="Medical Supply Icon" />
                    </div>
                    <div class="flex flex-col flex-grow">
                        <div class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ equip.name }}
                        </div>
                        <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            <template v-if="equip.brand">{{ equip.brand }}</template>
                            <template v-else>{{ equip.description }}</template>
                        </div>
                        <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                            Quantity: {{ equip.quantity }}
                        </div>
                    </div>
                </div>
                <div class="absolute top-4 right-4 whitespace-nowrap">
                    <StatusLabel :all="equip.status" />
                </div>
            </div>
        </div>

        <!-- Desktop View -->
        <div class="hidden md:block">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>

                        <th scope="col" class="px-8 py-3 whitespace-nowrap">NAME</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">TYPE</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">QUANTITY</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">STATUS</th>
                    </tr>
                </thead>
                <tbody
                    v-if="
                        !equipmentStatus.data ||
                        equipmentStatus.data.length === 0
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
                    <tr v-for="equip in equipmentStatus.data" :key="equip.id" class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer">
                        <td class="flex items-center px-8 p-4 mr-12 space-x-6 whitespace-nowrap">
                            <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                <div class="text-base font-semibold text-gray-900 dark:text-white">{{ equip.name }}</div>
                                <div class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                    <template v-if="equip.brand">{{ equip.brand }}</template>
                                    <template v-else>{{ equip.description }}</template>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <StatusLabel :all="equip.type" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ equip.quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <StatusLabel :all="equip.status" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
        <Pagination :paginator="equipmentStatus"  :status="selectedstatus" />
    </div>
</template>
