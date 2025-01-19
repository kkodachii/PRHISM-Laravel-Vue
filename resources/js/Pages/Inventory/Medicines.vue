<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { initFlowbite, initModals } from "flowbite";
import Pagination from "../../../js/Components/ApprovePagination.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { debounce } from "lodash";
import MedicineForm from "../../../js/Components/MedicineForm.vue";
import ToastList from "../../Components/ToastList.vue";
import MedicineEdit from "../../../js/Components/MedicineEdit.vue";
import MedicineDelete from "../../../js/Components/ItemDelete.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ExpiryCountdown from "../../Components/ExpiryCountdown.vue";
import StatusLabel from "../../Components/StatusLabel.vue";
import StatusFilter from "../../Components/medicineFilter.vue";
import GenericNameForm from "@/Components/GenericNameForm.vue";
import BreadCrumbs from "@/Components/BreadCrumbs.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    groupedMedicines: Object,
    genericNames: Array,
    batches: Array,
    allMedicines: Array,
    brandNames: Array,
    categoryNames: Array,
    auth: Object,
});

const user = ref(props.auth.user);

const genericNames = ref(props.genericNames);

const search = ref("");

const activeAccordion = ref(null);
const activeNestedAccordion = ref(null);

const showAddModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showGenericModal = ref(false);

const selectedMedicine = ref(null);

const openModal = (request, modal) => {
    selectedMedicine.value = request;

    if(modal =='add'){
        showAddModal.value = true;
    }if(modal =='edit'){
        showEditModal.value = true;
    }if(modal =='generic'){
        showGenericModal.value = true;
    }if(modal =='delete'){
        showDeleteModal.value = true;
    }
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    showGenericModal.value = false;
    showDeleteModal.value = false;
};

const checked = ref([]);
const selectAll = ref(false);
const url = ref("");

watch(checked, (newVal) => {
    url.value = "/medicines/export/" + newVal.join(",");
});

function getGenericName(id) {
    const generic = genericNames.value.find((name) => name.id === parseInt(id));
    return generic ? generic.generic_name : "Unknown";
}

function calculateTotalQuantity(medicines) {
    return medicines.reduce((total, med) => total + med.quantity, 0);
}

const handleselectAll = () => {
    if (selectAll.value) {
        checked.value = props.allMedicines.map((medicine) => medicine.id);
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

const toggleAccordion = (level, itemId) => {
    if (level === "main") {
        // Set the active accordion to toggle visibility based on itemId
        activeAccordion.value =
            activeAccordion.value === itemId ? null : itemId;
    } else if (level === "nested") {
        // Set the active nested accordion to toggle visibility based on itemId
        activeNestedAccordion.value =
            activeNestedAccordion.value === itemId ? null : itemId;
    }
};

watch(
    search,
    debounce(
        (q) => router.get("/medicines", { search: q }, { preserveState: true }),
        500
    )
);

onMounted(() => {
    initFlowbite();
    initModals();

    window.Echo.private("generic.name").listen("GenericNameAdded", (event) => {
        console.log("may bago");
        console.log(event);
        genericNames.value.unshift({
            id: event.generic_name.id,
            category_id: event.generic_name.category_id,
            generic_name: event.generic_name.generic_name,
        });
    });
});

const selectedStatus = ref([
    { id: "onStock", name: "On stock", selected: false },
    { id: "lowOnStock", name: "Low on stock", selected: false },
    { id: "outOfStock", name: "Out of stock", selected: false },
    { id: "expiring", name: "Expiring", selected: false },
    { id: "expired", name: "Expired", selected: false },
]);

const selectedCategory = ref("all");

function updateQuery(selectedIds = []) {
    const statusFilters = selectedIds.join(",");
    router.get(
        "/medicines",
        {
            search: search.value,
            status: statusFilters,
            category: selectedCategory.value,
            sort: sortField.value,
            direction: sortDirection.value,
        },
        { preserveState: true }
    );
}

function handleStatusUpdate(updatedStatusId) {
    const selectedIds = selectedStatus.value
        .filter((status) => status.selected)
        .map((status) => status.id);

    // Now, update the query with selected statuses (selectedIds contains an array of selected IDs)
    updateQuery(selectedIds);
}


const updateCategory = (category) => {
    selectedCategory.value = category;

    // Gather selected status IDs
    const selectedIds = selectedStatus.value
        .filter((status) => status.selected)
        .map((status) => status.id);

    // Call updateQuery with both category and status filters
    updateQuery(selectedIds);
};

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
</script>

<template>
    <Head title="Medicines" />

    <BreadCrumbs
        :breadcrumbs="[
            { label: 'Inventory', href: 'dashboard', disabled: true },
            { label: 'Medicine', href: 'medicines', disabled: false },
        ]"
    />

    <div v-if="showAddModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <MedicineForm
            :genericNames="genericNames"
            :batches="batches"
            :user="user"
            :brandNames="brandNames"
            :categoryNames="categoryNames"
            @close-modal="closeModal()"
        />
    </div>

    <div v-if="showEditModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <MedicineEdit
            :medicine="selectedMedicine"
            :genericNames="genericNames"
            :batches="batches"
            :brandNames="brandNames"
            :categoryNames="categoryNames"
            @close-modal="closeModal()"
        />
    </div>

    <div v-if="showDeleteModal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
    >
        <MedicineDelete :ID="selectedMedicine.id" :item="'med'"
        @close-modal="closeModal()"/>
    </div>

    <div v-if="showGenericModal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
    >
        <GenericNameForm :categories="categoryNames"
        @close-modal="closeModal()"></GenericNameForm>
    </div>


    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
        <!-- Search and filter -->
        <div
            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        >
            <div class="w-full md:w-1/3">
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
                    type="button"
                    @click="openModal(selectedMedicine, 'generic')"
                    class="flex items-center justify-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
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
                    Add Generic Name
                </button>

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
                    Add medicine
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
                                @click="openModal(selectedMedicine, 'add')"
                                class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white"
                            >
                                Add Medicine
                            </a>
                        </li>
                        <li>
                            <Link
                                :href="route('batch-medicine.index')"
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
                                    :href="route('archived-medicine.index')"
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
                        <th scope="col" class="px-6 py-3">Generic Name</th>
                        <!-- <th scope="col" class="px-6 py-3">Category</th> -->
                        <th scope="col" class="px-6 py-3">Total Quantity</th>
                    </tr>
                </thead>
                <tbody
                    v-if="
                        !groupedMedicines.data ||
                        groupedMedicines.data.length === 0
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
                    v-for="(medicines, genericNameId) in groupedMedicines.data"
                    :key="genericNameId"
                >
                    <tr
                        @click="toggleAccordion('main', genericNameId)"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                    >
                        <td>
                            <h2
                                class="px-6 py-6 text-lg font-bold text-gray-900"
                            >
                                {{ getGenericName(genericNameId) }}
                            </h2>
                        </td>
                        <!-- <td>
                            <h2 class="px-6 py-6 text-md text-gray-900">
                                {{ getCategoryName(medicines[0].category_id) }}
                            </h2>
                        </td> -->
                        <td>
                            <h2 class="px-6 py-6 text-md text-gray-900">
                                {{ calculateTotalQuantity(medicines) }}
                            </h2>
                        </td>
                    </tr>
                    <tr
                        :id="`accordion-collapse-body-${genericNameId}`"
                        v-show="activeAccordion === genericNameId"
                    >
                        <td colspan="12">
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
                                                <label
                                                    for="checkbox-all-search"
                                                    class="sr-only"
                                                    >checkbox</label
                                                >
                                            </div>
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-2 py-3 text-center"
                                        >
                                            ID
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-center"
                                        >
                                            Batch
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Brand
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            <div class="flex items-center">
                                                Quantity
                                                <a
                                                    href="#"
                                                    @click.prevent="
                                                        sort('quantity')
                                                    "
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
                                            Category
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            <div class="flex items-center">
                                                Expiration
                                                <a
                                                    href="#"
                                                    @click.prevent="
                                                        sort('expiration_date')
                                                    "
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
                                            Date Acquired
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            <div class="flex items-center">
                                                Status
                                                <a
                                                    href="#"
                                                    @click.prevent="
                                                        sort('status')
                                                    "
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
                                    </tr>
                                </thead>
                                <tbody
                                    v-for="medicine in medicines"
                                    data-accordion="collapse"
                                    :key="medicine.id"
                                >
                                    <tr
                                        :id="`accordion-collapse-heading-${medicine.id}`"
                                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                        @click="
                                            toggleAccordion(
                                                'nested',
                                                medicine.id
                                            )
                                        "
                                    >
                                        <td class="w-4 p-4">
                                            <div class="flex items-center">
                                                <input
                                                    :id="`checkbox-table-search-${medicine.id}`"
                                                    @click.stop
                                                    :value="medicine.id"
                                                    v-model="checked"
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
                                        <td class="px-2 py-4 text-center">
                                            {{ medicine.id }}
                                        </td>
                                        <td class="px-2 py-4 text-center">
                                            {{ medicine.batch_id }}
                                        </td>
                                        <th class="px-2 py-4">
                                            {{ medicine.brand }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ medicine.quantity }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ medicine.category.category }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <ExpiryCountdown
                                                :expirationDate="
                                                    medicine.expiration_date
                                                "
                                            />
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ medicine.date_acquired }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <StatusLabel
                                                :all="medicine.status"
                                            />
                                        </td>
                                    </tr>
                                    <tr
                                        :id="`nested-accordion-collapse-body-${medicine.id}`"
                                        :aria-labelledby="`nested-accordion-collapse-heading-${medicine.id}`"
                                        v-show="
                                            activeNestedAccordion ===
                                            medicine.id
                                        "
                                        class="w-full bg-gray-50 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    >
                                        <td colspan="2"></td>
                                        <td class="text-center" colspan="10">
                                            <!-- Details -->
                                            <div class="text-left px-2 mt-2">
                                                <p
                                                    class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                                >
                                                    Details
                                                </p>
                                            </div>
                                            <!-- Cards -->
                                            <div
                                                class="grid grid-cols-4 gap-5 p-2"
                                            >
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
                                                                    medicine
                                                                        .user
                                                                        .name
                                                                }}
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
                                                                Provider name
                                                            </p>
                                                            <p class="text-sm">
                                                                {{
                                                                    medicine.provider
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
                                                        :key="medicine.id"
                                                        type="button"
                                                        @click="openModal(medicine, 'edit')"
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
                                                        :key="medicine.id"
                                                        type="button"
                                                        @click="openModal(medicine, 'delete')"
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination
            :paginator="groupedMedicines"
            :search="search"
            :selectedStatus="selectedStatus"
            :sortField="sortField"
            :sortDirection="sortDirection"
        />
    </div>
    <StatusFilter
        :selected-status="selectedStatus"
        :categoryNames="categoryNames"
        @status-updated="handleStatusUpdate"
        @category-updated="updateCategory"
    />
</template>
