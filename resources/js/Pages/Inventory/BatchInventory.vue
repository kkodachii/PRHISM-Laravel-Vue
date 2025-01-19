<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { initFlowbite, initModals } from "flowbite";
import { Link, router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import StatusLabel from "../../Components/StatusLabel.vue";
import Pagination from "@/Components/ApprovePagination.vue";

defineOptions({ layout: DashboardLayout });

defineProps({
    inventory: Object,
});

// State variables
const search = ref("");
watch(
  search,
  debounce(
    (q) => router.get(
      "/batches-inventory",
      { search: q, batchId: selectedBatchID.value },
      { preserveState: true, preserveScroll: true }
    ),
    500
  )
);

const activeAccordion = ref(null);

const selectedBatchID = ref("");
const selectedType = ref("all");

const typeOptions = ref([
    { value: "all", label: "All" },
    { value: "medicine", label: "Medicine" },
    { value: "equipment", label: "Equipment" },
    { value: "medicalSupply", label: "Medical Supply" },
]);

const sortField = ref("type");
const sortDirection = ref("asc");
const selectedStatus = ref([
    { id: "onStock", name: "On stock", selected: false },
    { id: "expiring", name: "Expiring", selected: false },
    { id: "lowOnStock", name: "Low on stock", selected: false },
    { id: "expired", name: "Expired", selected: false },
    { id: "outOfStock", name: "Out of stock", selected: false },
]);

const selectedCondition = ref([
    { id: "new", name: "New", selected: false },
    { id: "fairCondition", name: "Fair", selected: false },
    { id: "goodCondition", name: "Good", selected: false },
    { id: "poorCondition", name: "Poor", selected: false },
    { id: 'condemned', name: 'Condemned', selected: false },
]);

onMounted(() => {
    initFlowbite();
    initModals();

    const batchId = new URLSearchParams(window.location.search).get("batchId");
    if (batchId) {
        selectedBatchID.value = batchId;
    }

    const type = new URLSearchParams(window.location.search).get("type");
    if (type) {
        selectedType.value = type;
    }

    const statusFilters = new URLSearchParams(window.location.search).get(
        "status"
    );
    if (statusFilters) {
        const filtersArray = statusFilters.split(",");
        selectedStatus.value.forEach((status) => {
            status.selected = filtersArray.includes(status.id);
        });
        selectedCondition.value.forEach((condition) => {
            condition.selected = filtersArray.includes(condition.id);
        });
    }

    updateQuery(); // Ensure the filters are applied on mount
});

function updateQuery() {
    const statusFilters = selectedStatus.value
        .filter((status) => status.selected)
        .map((status) => status.id)
        .join(",");

    const conditionFilters = selectedCondition.value
        .filter((condition) => condition.selected)
        .map((condition) => condition.id)
        .join(",");

    const combinedFilters = [
        ...statusFilters.split(","),
        ...conditionFilters.split(","),
    ].join(",");

    router.get(
        "/batches-inventory",
        {
            batchId: selectedBatchID.value,
            search: search.value,
            status: combinedFilters,
            sort: sortField.value,
            direction: sortDirection.value,
            type: selectedType.value,
        },
        { preserveState: true }
    );
}

// Function to toggle accordion
const toggleAccordion = (index) => {
    if (activeAccordion.value !== null && activeAccordion.value !== index) {
        const previousBodyRow = document.getElementById(
            `accordion-collapse-body-${activeAccordion.value}`
        );
        if (previousBodyRow) {
            previousBodyRow.classList.add("hidden");
        }
    }
    const bodyRow = document.getElementById(`accordion-collapse-body-${index}`);
    if (bodyRow) {
        bodyRow.classList.toggle("hidden");
    }
    activeAccordion.value = bodyRow.classList.contains("hidden") ? null : index;
};

const getTypeClasses = (type) => {
    switch (type) {
        case "Equipment":
            return "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300";
        case "Medical Supply":
            return "bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-100";
        case "Medicine":
            return "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300";
        default:
            return "bg-[#FFFFFF] text-black px-2 py-1 rounded";
    }
};

function changeType(type) {
    selectedType.value = type;
    updateQuery();
}

function sort(field) {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    updateQuery();
}

function navigateToDetails(type) {
    switch (type) {
        case "Equipment":
            router.get(route("equipments.index"));
            break;
        case "Medical Supply":
            router.get(route("medical_supplies.index"));
            break;
        case "Medicine":
            router.get(route("medicines.index"));
            break;
        default:
            console.warn("Unknown type:", type);
    }
}
</script>

<template>
    <Head title="Batch inventory" />
    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >

        <!-- Search and Filter -->
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
            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
            >
                <div class="flex items-center space-x-3 w-full md:w-auto">
                    <button
                        id="actionsDropdownButton"
                        data-dropdown-toggle="actionsDropdown"
                        class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        type="button"
                    >
                        <svg
                            class="-ml-1 mr-1.5 w-5 h-5"
                            fill="currentColor"
                            viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            />
                        </svg>
                        Actions
                    </button>
                    <div
                        id="actionsDropdown"
                        class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600"
                    >
                        <ul
                            class="py-1 text-sm text-gray-700 dark:text-gray-200"
                            aria-labelledby="actionsDropdownButton"
                        >
                            <li>
                                <a
                                    href="#"
                                    class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    >Mass Edit</a
                                >
                            </li>
                        </ul>

                    </div>
                    <button
                        id="filterDropdownButton"
                        data-dropdown-toggle="filterDropdown"
                        class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                        type="button"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                            class="h-5 w-6 mr-2 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Filter
                        <svg
                            class="-mr-1 ml-1.5 w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            />
                        </svg>
                    </button>

                </div>
            </div>
        </div>
        <!-- Supplies Table -->
        <div class="relative overflow-x-auto sm:rounded-lg">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-8 py-3">Batch no.</th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Inventory
                                <a href @click.prevent="sort('type')">
                                    <svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                name
                                <a href="#" @click.prevent="sort('name')">
                                    <svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Quantity
                                <a href @click.prevent="sort('quantity')">
                                    <svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Date acquired
                                <a
                                    href="#"
                                    @click.prevent="sort('date_acquired')"
                                >
                                    <svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Status
                                <a href @click.prevent="sort('status')">
                                    <svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody
                    v-if="
                        !inventory.data ||
                        inventory.data.length === 0
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
                    v-for="(batch_inv, index) in inventory.data"
                    :key="index"
                >
                    <tr
                        :id="`accordion-collapse-heading-${index}`"
                        @click="toggleAccordion(index)"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                    >
                        <td class="px-8 py-6">
                            {{ batch_inv.batch_id }}
                        </td>
                        <td class="px-6 py-6">
                            <span
                                :class="getTypeClasses(batch_inv.type)"
                                class="text-xs font-medium px-3 py-1 rounded-full"
                            >
                                {{ batch_inv.type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ batch_inv.name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ batch_inv.quantity }}
                        </td>
                        <td class="px-6 py-4">
                            {{ batch_inv.date_acquired }}
                        </td>
                        <td class="px-6 py-6">
                            <StatusLabel :all="batch_inv.status" />
                        </td>
                    </tr>
                    <tr
                        :id="`accordion-collapse-body-${index}`"
                        :aria-labelledby="`accordion-collapse-heading-${index}`"
                        v-show="activeAccordion === index"
                        class="hidden w-full bg-gray-50 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                       
                        <td class="text-center px-6" colspan="10">
                            <div class="grid grid-cols-1 gap-1 p-1"></div>
                            <!-- Details -->
                            <div class="text-left px-2">
                                <p
                                    class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                >
                                    Details
                                </p>
                            </div>
                            <!-- Cards -->
                            <div class="grid grid-cols-3 gap-5 p-2">
                                <!-- Medicines -->
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
                                                {{ batch_inv.user }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical supply-->
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
                                                Provider
                                            </p>
                                            <p class="text-sm">
                                                {{ batch_inv.provider }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Buttons -->
                            <div class="p-2 mb-3">
                                <div
                                    class="w-auto flex flex-row space-y-0 items-center justify-start space-x-3 flex-shrink-0"
                                >
                                    <button
                                        @click="
                                            navigateToDetails(batch_inv.type)
                                        "
                                        :key="index"
                                        type="button"
                                        class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                    >
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            :paginator="inventory"
            :search="search"
            :selectedStatus="selectedStatus"
            :sortField="sortField"
            :sortDirection="sortDirection"
            :batchId="selectedBatchID"
            :type="selectedType"
        />
    </div>
    <div
                        id="filterDropdown"
                        class="z-50 hidden w-64 p-3 bg-white rounded-lg shadow-lg dark:bg-gray-700"
                    >
                        <!-- Type Filter -->
                        <h6
                            class="mb-3 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Choose Type
                        </h6>
                        <select
                            v-model="selectedType"
                            @change="updateQuery"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option
                                v-for="option in typeOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>

                        <!-- Status Filter -->
                        <h6
                            class="mt-6 mb-3 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Choose Status
                        </h6>
                        <ul class="grid grid-cols-2 gap-1 text-sm">
                            <li
                                v-for="status in selectedStatus"
                                :key="status.id"
                                class="flex items-center"
                            >
                                <input
                                    :id="status.id"
                                    type="checkbox"
                                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                    v-model="status.selected"
                                    @change="updateQuery"
                                />
                                <label
                                    :for="status.id"
                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ status.name }}
                                </label>
                            </li>
                        </ul>

                        <h6
                            class="mt-6 mb-3 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Choose Condition
                        </h6>
                        <ul class="grid grid-cols-2 gap-1 text-sm">
                            <li
                                v-for="condition in selectedCondition"
                                :key="condition.id"
                                class="flex items-center"
                            >
                                <input
                                    :id="condition.id"
                                    type="checkbox"
                                    class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                    v-model="condition.selected"
                                    @change="updateQuery"
                                />
                                <label
                                    :for="condition.id"
                                    class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
                                >
                                    {{ condition.name }}
                                </label>
                            </li>
                        </ul>
                    </div>
</template>
