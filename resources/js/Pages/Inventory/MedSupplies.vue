<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { initFlowbite, initModals } from "flowbite";
import Pagination from "../../../js/Components/ApprovePagination.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { debounce } from "lodash";
import MSupplyForm from "../../../js/Components/MSupplyForm.vue";
import ToastList from "../../Components/ToastList.vue";
import MedSupplyEditModal from "../../../js/Components/MSupplyEdit.vue";
import MedSupplyDelete from "../../../js/Components/ItemDelete.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import StatusLabel from "../../Components/StatusLabel.vue";
import StatusFilter from "../../Components/statusFilter.vue";
import BreadCrumbs from "@/Components/BreadCrumbs.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    medical_supplies: Object,
    allMedsup: Array,
    MsupplyNames: Array,
    batches: Array,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const search = ref("");
const resetFlag = ref(false);
const activeAccordion = ref(null);

const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);

const selectedMedSupply= ref(null);

const openModal = (request, modal) => {
    selectedMedSupply.value = request;

    if(modal =='add'){
        showAddModal.value = true;
    }if(modal =='edit'){
        showEditModal.value = true;
    }if(modal =='delete'){
        showDeleteModal.value = true;
    }
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    showDeleteModal.value = false;
};

const toggleAccordion = (itemId) => {
    // Close currently open accordion if it's not the one being clicked
    if (activeAccordion.value && activeAccordion.value !== itemId) {
        const previousBodyRow = document.getElementById(
            `accordion-collapse-body-${activeAccordion.value}`
        );
        if (previousBodyRow) {
            previousBodyRow.classList.add("hidden");
        }
    }

    // Toggle the clicked accordion
    const bodyRow = document.getElementById(
        `accordion-collapse-body-${itemId}`
    );
    if (bodyRow) {
        bodyRow.classList.toggle("hidden");
    }

    // Update the currently active accordion
    activeAccordion.value = bodyRow.classList.contains("hidden")
        ? null
        : itemId;
};

function handleFormSubmitted() {
    resetFlag.value = true;
    setTimeout(() => {
        resetFlag.value = false;
    }, 0);
}

watch(
    search,
    debounce(
        (q) =>
            router.get(
                "/medical_supplies",
                { search: q },
                { preserveState: true }
            ),
        500
    )
);

onMounted(() => {
    initFlowbite();
    initModals();
});

const selectedStatus = ref([
    { id: "onStock", name: "On stock", selected: false },
    { id: "lowOnStock", name: "Low on stock", selected: false },
    { id: "outOfStock", name: "Out of stock", selected: false },
]);

function updateQuery() {
    const statusFilters = selectedStatus.value
        .filter((status) => status.selected)
        .map((status) => status.id)
        .join(",");
    router.get(
        "/medical_supplies",
        {
            search: search.value,
            status: statusFilters,
            sort: sortField.value,
            direction: sortDirection.value,
        },
        { preserveState: true }
    );
}

const sortField = ref("id"); // Default sort field
const sortDirection = ref("asc"); // Default sort direction

function sort(field) {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    updateQuery();
}

const checked = ref([]);
const selectAll = ref(false);
const url = ref("");

watch(checked, (newVal) => {
    url.value = "/medical_supplies/export/" + newVal.join(",");
});

const handleselectAll = () => {
    if (selectAll.value) {
        checked.value = props.allMedsup.map((med_supply) => med_supply.id);
    } else {
        checked.value = [];
    }
};

function closeexport() {
    const dropdown = document.getElementById("actionsDropdown");
    if (dropdown) {
        dropdown.classList.add("hidden"); // Manually hide the dropdown
    }
}

watch(
    () => props.medical_supplies,
    (newData) => {},
    { deep: true }
);
</script>

<template>
    <Head title="Medical Supplies" />

    <BreadCrumbs
        :breadcrumbs="[
            { label: 'Inventory', href: 'dashboard', disabled: true },
            { label: 'Medical Supply', href: 'medical_supplies', disabled: false },
        ]"
    />

    <div v-if="showAddModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <MSupplyForm
            :resetFlag="resetFlag"
            @submitted="handleFormSubmitted"
            :user="user"
            :MsupplyNames="MsupplyNames"
            :batches="batches"
            @close-modal="closeModal()"
        ></MSupplyForm>
    </div>

    <div v-if="showEditModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <MedSupplyEditModal
            :med_supply="selectedMedSupply"
            :batches="batches"
            :MsupplyNames="MsupplyNames"
            @close-modal="closeModal()"
        />
    </div>

    <div v-if="showDeleteModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <MedSupplyDelete :ID="selectedMedSupply.id" :item="'medsup'"
        @close-modal="closeModal()"/>
    </div>

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
                <button
                    id="addProductDropdownButton"
                    data-dropdown-toggle="addProductDropdown"
                    type="button"
                    class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                >
                    <svg
                        class="h-5 w-4 mr-3"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            clip-rule="evenodd"
                            fill-rule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        />
                    </svg>
                    Add med supply
                </button>
                <div
                    id="addProductDropdown"
                    class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow-lg dark:bg-gray-700 dark:divide-gray-600"
                >
                    <ul
                        class="py-2 text-sm text-gray-700 dark:text-gray-200"
                        aria-labelledby="addProductDropdownButton"
                    >
                        <li>
                            <a
                                href="#"
                                @click="openModal(selectedMedSupply,'add')"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                                Add Supply
                            </a>
                        </li>
                        <li>
                            <Link
                                :href="route('batch-msupply.index')"
                                class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                                Batch Add
                            </Link>
                        </li>
                    </ul>
                </div>
                <div class="flex items-center space-x-3 w-full md:w-auto">
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

                    <StatusFilter
                        :selectedStatus="selectedStatus"
                        @status-updated="updateQuery"
                    />
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
                        <span v-if="checked.length > 0">
                            ({{ checked.length }})</span
                        >
                    </button>
                    <div
                        id="actionsDropdown"
                        class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600"
                    >
                        <ul
                            class="py-1 text-sm text-gray-700 dark:text-gray-200"
                            aria-labelledby="actionsDropdownButton"
                        >
                            <li v-if="checked.length > 0">
                                <a
                                    :href="url"
                                    @click="closeexport"
                                    class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                    >Export</a
                                >
                            </li>
                            <li>
                                <Link
                                    :href="route('archived-supply.index')"
                                    class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                                >
                                    Show deleted
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table -->
        <div class="relative overflow-x-auto sm:rounded-lg">
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input
                                    v-model="selectAll"
                                    @change="handleselectAll"
                                    id="checkbox-all-search"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                />
                                <label for="checkbox-all-search" class="sr-only"
                                    >checkbox</label
                                >
                            </div>
                        </th>
                        <th scope="col" class="px-2 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Medical supply name
                                <a
                                    href="#"
                                    @click.prevent="sort('med_sup_name')"
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
                                Quantity
                                <a href="#" @click.prevent="sort('quantity')">
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
                        <th scope="col" class="px-6 py-3">Provider</th>
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
                                <a href="#" @click.prevent="sort('status')">
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
                        !medical_supplies.data ||
                        medical_supplies.data.length === 0
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
                    v-for="med_supply in medical_supplies.data"
                    data-accordion="collapse"
                    :key="med_supply.id"
                >
                    <tr
                        :key="med_supply.id"
                        :id="`accordion-collapse-heading-${med_supply.id}`"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                        @click="toggleAccordion(med_supply.id)"
                    >
                        <td class="w-4 p-4">
                            <div class="flex items-center">
                                <input
                                    @click.stop
                                    :value="med_supply.id"
                                    v-model="checked"
                                    :id="`checkbox-table-search-${med_supply.id}`"
                                    type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                />
                                <label
                                    for="checkbox-table-search-1"
                                    class="sr-only"
                                    >checkbox</label
                                >
                            </div>
                        </td>
                        <td class="px-2 py-6">
                            {{ med_supply.id }}
                        </td>
                        <th
                            scope="row"
                            class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap dark:text-white"
                        >
                            {{ med_supply.med_sup_name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ med_supply.quantity }}
                        </td>
                        <td class="px-6 py-4">
                            {{ med_supply.provider }}
                        </td>
                        <td class="px-6 py-4">
                            {{ med_supply.date_acquired }}
                        </td>
                        <td class="px-6 py-6">
                            <StatusLabel :all="med_supply.status" />
                        </td>
                    </tr>
                    <tr
                        :key="med_supply.id"
                        :id="`accordion-collapse-body-${med_supply.id}`"
                        :aria-labelledby="`accordion-collapse-heading-${med_supply.id}`"
                        v-show="activeAccordion === med_supply.id"
                        class="hidden w-full bg-gray-50 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <td colspan="2"></td>
                        <td class="text-center" colspan="10">
                            <div class="grid grid-cols-1 gap-1 p-1"></div>
                            <!-- Details -->
                            <div class="text-left px-2">
                                <p
                                    class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                >
                                    Details
                                </p>
                                <p
                                    class="p-1 text-m text-gray-500 lg:mb-0 dark:text-gray-400 lg:max-w-2xl"
                                >
                                    {{ med_supply.description }}
                                </p>
                            </div>
                            <!-- Cards -->
                            <div class="grid grid-cols-3 gap-5 p-2">
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
                                                {{ med_supply.user.name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Provider card -->
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
                                                Batch number
                                            </p>
                                            <p class="text-sm">
                                                {{
                                                    med_supply.batch
                                                        .batch_number
                                                }}
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
                                        :key="med_supply.id"
                                        type="button"
                                        @click="openModal(med_supply, 'edit')"
                                        class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                                    >
                                        <svg
                                            class="w-w-5 h-5 text-white"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"
                                            />
                                        </svg>
                                        Edit
                                    </button>
                                    <button
                                        :key="med_supply.id"
                                        type="button"
                                        @click="openModal(med_supply, 'delete')"
                                        class="flex items-center justify-center font-medium rounded-lg text-sm px-3 gap-1 py-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
                                    >
                                        <svg
                                            class="w-5 h-5 text-white"
                                            aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
                                            />
                                        </svg>

                                        Delete
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            :paginator="medical_supplies"
            :search="search"
            :selectedStatus="selectedStatus"
        />
    </div>
</template>
