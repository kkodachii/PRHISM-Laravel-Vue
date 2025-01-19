<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { debounce } from "lodash";
import Pagination from "../../../js/Components/Pagination.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import StatusLabel from "../../Components/StatusLabel.vue";
import ExpiryCountdown from "../../Components/ExpiryCountdown.vue";
import { Link } from "@inertiajs/vue3";
import StatusFilter from "../../Components/mw_statusFilter.vue";
import SortField from "../../Components/mw_sort.vue"; // Importing SortField

defineOptions({ layout: DashboardLayout });
const selectedBarangay = ref("");

const getInventoryTypeName = (type) => {
    const typeMapping = {
        medicine: "Medicine",
        equipment: "Equipment",
        medical_supply: "Medical Supply",
    };
    return typeMapping[type] || type;
};

onMounted(() => {
    initFlowbite();

    const barangay = new URLSearchParams(window.location.search).get(
        "barangay"
    );
    if (barangay) {
        selectedBarangay.value = barangay;
    }
});

const props = defineProps({
    mw_inventory: Object,
    auth: Object,
});

// Existing state variables
const barangay = ref(props.auth.user.barangay_name);
const search = ref("");

watch(
    search,
    debounce(
        (q) =>
            router.get(
                "/barangay_inventory",
                { search: q, barangay: selectedBarangay.value },
                { preserveState: true, preserveScroll: true }
            ),
        500
    )
);

const activeAccordion = ref(null);

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

const selectedStatus = ref([]);
const selectedType = ref("all");

// Define sort field and direction state
const selectedSortField = ref("name"); // Default sort field
const selectedSortDirection = ref("asc"); // Default sort direction

const sortFieldOptions = ref([
    { value: "name", label: "Name" },
    { value: "quantity", label: "Quantity" },
    { value: "date_acquired", label: "Date Acquired" },
    { value: "status", label: "Status" },
]);

const typeOptions = ref([
    { value: "all", label: "All" },
    { value: "medicine", label: "medicine" },
    { value: "equipment", label: "Equipment" },
    { value: "medical_supply", label: "Medical Supply" },
]);

const statusOptionsByType = {
    all: [
        { id: "onStock", name: "On stock", selected: false },
        { id: "lowOnStock", name: "Low on stock", selected: false },
        { id: "outOfStock", name: "Out of stock", selected: false },
    ],
    inventory: [
        { id: "onStock", name: "On stock", selected: false },
        { id: "lowOnStock", name: "Low on stock", selected: false },
        { id: "outOfStock", name: "Out of stock", selected: false },
        { id: "expiring", name: "Expiring", selected: false },
        { id: "expired", name: "Expired", selected: false },
    ],
    equipment: [
        { id: "new", name: "New", selected: false },
        { id: "goodCondition", name: "Good condition", selected: false },
        { id: "fairCondition", name: "Fair condition", selected: false },
        { id: "poorCondition", name: "Poor condition", selected: false },
        { id: 'condemned', name: 'Condemned', selected: false },
    ],
    medical_supply: [
        { id: "onStock", name: "On stock", selected: false },
        { id: "lowOnStock", name: "Low on stock", selected: false },
        { id: "outOfStock", name: "Out of stock", selected: false },
    ],
};

const initializeSelectedStatus = () => {
    selectedStatus.value = statusOptionsByType[selectedType.value].map(
        (status) => ({
            ...status,
            selected: false,
        })
    );
};

initializeSelectedStatus();

const availableStatuses = computed(() => {
    return statusOptionsByType[selectedType.value] || [];
});

// Update the query based on status, type, and sort options
function updateQuery() {
    const statusFilters = selectedStatus.value
        .filter((status) => status.selected)
        .map((status) => status.id)
        .join(",");

    router.get(
        "/barangay_inventory",
        {
            barangay: selectedBarangay.value,
            search: search.value,
            status: statusFilters,
            type: selectedType.value,
            sort: selectedSortField.value,
            direction: selectedSortDirection.value,
        },
        { preserveState: true }
    );
}

// Handlers for updating statuses and types
function handleStatusUpdate(updatedStatusId) {
    selectedStatus.value.forEach((status) => {
        status.selected = status.id === updatedStatusId;
    });
    updateQuery();
}

function handleTypeUpdate(newType) {
    selectedType.value = newType;
    initializeSelectedStatus();
    updateQuery();
}

watch(selectedType, (newType) => {
    initializeSelectedStatus();
});

// New handlers for sort field and direction updates
function updateSortField(newSortField) {
    selectedSortField.value = newSortField;
    updateQuery(); // Call updateQuery to refresh the inventory list
}

function updateSortDirection(newSortDirection) {
    selectedSortDirection.value = newSortDirection;
    updateQuery(); // Call updateQuery to refresh the inventory list
}

function handlePageChange(page) {
    updateQuery(page);
}
const headingText = computed(() => {
    return selectedBarangay.value
        ? `${selectedBarangay.value} Inventory`
        : "Inventory";
});
</script>

<template>
    <Head title="Barangay Inventory" />

    <div
        class="col-span-1 bg-white rounded-lg border border-gray-200 shadow-sm"
    >
        <div
            class="grid grid-cols-1 md:grid-cols-2 mb-2 items-center w-full mt-4 md:mt-2 lg:w-auto flex-row justify-between"
        >
            <div class="col-span-1">
                <h2 class="text-2xl font-bold px-5">{{ headingText }}</h2></div>
            <div class="col-span-1 w-full">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 p-4">
            <!-- Search Bar and Filter Button Section -->
            <div class="flex items-center w-full space-x-3">
                <form class="flex-grow">
                    <label for="simple-search" class="sr-only"
                        >Search</label
                    >
                    <div class="relative w-full">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                aria-hidden="true"
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
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
                            v-model="search"
                        />
                    </div>
                </form>

                <!-- Filter Button -->
                <button
                    id="filterDropdownButton"
                    data-dropdown-toggle="filterDropdown"
                    class="flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    type="button"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                        class="h-5 w-6 text-gray-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </button>
                <!-- Sort Button  -->
                <button
                    id="sortFieldDropdownButton"
                    data-dropdown-toggle="sortFieldDropdown"
                    class="flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    type="button"
                >
                    <svg
                        class="w-[24px] h-[24px] text-gray-400"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 20V10m0 10-3-3m3 3 3-3m5-13v10m0-10 3 3m-3-3-3 3"
                        />
                    </svg>
                </button>
            </div>
        </div>
            </div>


        </div>

        <!-- Search and Filter Section -->
        <div class="w-full">
            <div
                class="bg-white dark:bg-gray-800 relative overflow-x-auto sm:rounded-lg"
            >

                <!-- Inventory List and Accordion -->
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <table
                        class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                    >
                        <thead
                            class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                        >
                            <tr>
                                <th scope="col" class="px-8 py-3">Inventory</th>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Quantity</th>
                                <th scope="col" class="px-6 py-3">
                                    date acquired
                                </th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody
                    v-if="
                        !mw_inventory.data ||
                        mw_inventory.data.length === 0
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
                        <tbody
                            v-for="inventory in mw_inventory.data"
                            data-accordion="collapse"
                            :key="inventory.id"
                        >
                            <tr
                                :id="`accordion-collapse-heading-${inventory.id}`"
                                class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                @click="toggleAccordion(inventory.id)"
                            >

                                <th
                                    scope="row"
                                    class="px-8 py-6 font-semibold text-gray-900 whitespace-nowrap dark:text-white"
                                >
                                    <StatusLabel
                                        :all="
                                            getInventoryTypeName(inventory.type)
                                        "
                                    />
                                </th>
                                <td class="px-6 py-6">
                                    <div>
                                        {{ inventory.name }}
                                        <div
                                            class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                        >
                                            <span v-if="inventory.brand"
                                                >({{ inventory.brand }})</span
                                            >
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-6">
                                    {{ inventory.quantity }}
                                </td>
                                <td class="px-6 py-6">
                                    {{ inventory.created_at }}
                                </td>
                                <td class="px-6 py-6">
                                    <StatusLabel :all="inventory.status" />
                                </td>
                            </tr>
                            <tr
                                :id="`accordion-collapse-body-${inventory.id}`"
                                :aria-labelledby="`accordion-collapse-heading-${inventory.id}`"
                                v-show="activeAccordion === inventory.id"
                                class="hidden w-full bg-gray-50 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                            >
                                
                                <td class="text-center px-8" colspan="10">
                                    <!-- Details -->
                                    <div class="text-left px-2 mt-2">
                                        <p
                                            class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                        >
                                            Details
                                        </p>
                                        <p
                                            class="p-1 text-m text-gray-500 lg:mb-0 dark:text-gray-400 lg:max-w-2xl"
                                            v-if="
                                                inventory.description &&
                                                inventory.description !== 'N/A'
                                            "
                                        >
                                            {{ inventory.description }}
                                        </p>
                                    </div>

                                    <!-- Cards -->
                                    <div class="grid grid-cols-4 gap-5 p-2">
                                        <!-- User card -->
                                        <div
                                            class="flex flex-row justify-between gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <div
                                                class="flex flex-col flex-grow items-left text-left leading-normal"
                                            >
                                                <div class="text-left">
                                                    <p
                                                        class="mb-2 text-l font-semibold tracking-tight text-gray-900 dark:text-white"
                                                    >
                                                        User updated
                                                    </p>
                                                    <p class="text-sm">
                                                        {{
                                                            inventory.user.name
                                                        }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Batches card -->
                                        <div
                                            class="flex flex-row justify-between gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                            v-if="inventory.category"
                                        >
                                            <div
                                                class="flex flex-col flex-grow items-left text-left leading-normal"
                                            >
                                                <div class="text-left">
                                                    <p
                                                        class="mb-2 text-l font-semibold tracking-tight text-gray-900 dark:text-white"
                                                    >
                                                        Category
                                                    </p>
                                                    <p class="text-sm">
                                                        {{
                                                            inventory.category
                                                                .category
                                                        }}
                                                        <!-- Access category name -->
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Provider card -->
                                        <div
                                            class="flex flex-row justify-between gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                            v-if="inventory.expiration_date"
                                        >
                                            <div
                                                class="flex flex-col flex-grow items-left text-left leading-normal"
                                            >
                                                <div class="text-left">
                                                    <p
                                                        class="mb-2 text-l font-semibold tracking-tight text-gray-900 dark:text-white"
                                                    >
                                                        Expiration
                                                    </p>
                                                    <p class="text-sm">
                                                        <ExpiryCountdown
                                                            :expirationDate="
                                                                inventory.expiration_date
                                                            "
                                                        />
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Component -->
                <Pagination
                    :paginator="mw_inventory"
                    :barangay="selectedBarangay"
                    :search="search"
                    :selectedStatus="selectedStatus"
                    :type="selectedType"
                    :sortField="selectedSortField"
                    :sortDirection="selectedSortDirection"
                    @page-change="handlePageChange"
                />
            </div>
            <StatusFilter
                :selected-status="selectedStatus"
                :selected-type="selectedType"
                :type-options="typeOptions"
                @status-updated="handleStatusUpdate"
                @type-updated="handleTypeUpdate"
            />
            <SortField
                :selectedSortField="selectedSortField"
                :selectedSortDirection="selectedSortDirection"
                :sortFieldOptions="sortFieldOptions"
                @sort-field-updated="updateSortField"
                @sort-direction-updated="updateSortDirection"
            />
        </div>
    </div>
</template>
