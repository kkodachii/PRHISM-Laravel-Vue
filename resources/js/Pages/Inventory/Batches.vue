<script setup>
import { ref, watch, onMounted } from "vue";
import { initFlowbite, initModals } from "flowbite";
import Pagination from "../../../js/Components/Pagination.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { debounce } from "lodash";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

defineProps({
    batches: Object,
});

const search = ref("");
const resetFlag = ref(false);
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

watch(
    search,
    debounce(
        (q) => router.get("/batches", { search: q }, { preserveState: true }),
        500
    )
);

onMounted(() => {
    initFlowbite();
    initModals();
});

const addForm = useForm({
    batch_number: "",
});

const sortField = ref("batch_number"); // Default sort field
const sortDirection = ref("asc"); // Default sort direction

function updateQuery() {
    router.get(
        "/batches",
        {
            search: search.value,
            sort: sortField.value,
            direction: sortDirection.value,
        },
        { preserveState: true }
    );
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

function submitAdd() {
    addForm.post(route("batch.store"), {
        onSuccess: () => {
            addForm.reset();
            isAdding.value = false;
        },
        onError: () => {},
    });
}
</script>

<template>
    <Head title="Batches" />
    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
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

            <div
                class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
            >
                <button
                    id="addProductDropdownButton"
                    @click="submitAdd()"
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
                    Add batch
                </button>
                <div
                    id="addProductDropdown"
                    class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow-lg dark:bg-gray-700 dark:divide-gray-600"
                ></div>
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
                </div>
            </div>
        </div>

        <!-- Table -->
        <table
            class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
        >
            <thead
                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
            >
                <tr>

                    <th scope="col" class="px-8 py-3">
                        <div class="flex items-center">
                            Batch no.
                            <a href="#" @click.prevent="sort('batch_number')">
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
                        <div class="flex items-center">Total entries</div>
                    </th>
                    <th scope="col" class="px-6 py-3">
                        <div class="flex items-center">
                            Date created
                            <a href="#" @click.prevent="sort('created_at')">
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

                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody
                    v-if="
                        !batches.data ||
                        batches.data.length === 0
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
                v-for="batch in batches.data"
                :key="batch.id"
                data-accordion="collapse"
            >
                <tr
                    :id="`accordion-collapse-heading-${batch.id}`"
                    @click="toggleAccordion(batch.id)"
                    class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                >

                    <td class="px-8 py-6">
                        {{ batch.batch_number }}
                    </td>
                    <td class="px-6 py-4">{{ batch.quantity }}</td>
                    <td class="px-6 py-4">{{ batch.created_at }}</td>
                    <td class="px-6 py-4">
                        <Link
                            :href="
                                route('batches-inventory.index', {
                                    batchId: batch.id,
                                })
                            "
                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                            @click.stop
                        >
                            View full details
                        </Link>
                    </td>
                </tr>
                <tr
                    :id="`accordion-collapse-body-${batch.id}`"
                    :aria-labelledby="`accordion-collapse-heading-${batch.id}`"
                    v-show="activeAccordion === batch.id"
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
                            <p
                                class="p-1 text-m text-gray-500 lg:mb-0 dark:text-gray-400 lg:max-w-2xl"
                            >
                                Total quantity of each inventory
                            </p>
                        </div>
                        <!-- Cards -->
                        <div class="grid grid-cols-3 gap-5 p-2">
                            <!-- Medicines -->
                            <div
                                class="flex flex-row justify-between gap-5 p-2 max-w-sm bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div
                                    class="flex flex-col flex-grow items-left text-left leading-normal"
                                >
                                    <div class="text-left">
                                        <p
                                            class="mb-2 text-lg font-semibold tracking-tight text-green-500 dark:text-white"
                                        >
                                            Medicines
                                        </p>
                                        <p class="text-sm">
                                            {{ batch.medicine_quantity }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Equipment -->
                            <div
                                class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-blue-100 border border-blue-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div
                                    class="flex flex-col flex-grow items-left text-left leading-normal"
                                >
                                    <div class="text-left">
                                        <p
                                            class="mb-2 text-lg font-semibold tracking-tight text-blue-600 dark:text-white"
                                        >
                                            Equipment
                                        </p>
                                        <p class="text-sm">
                                            {{ batch.equipment_quantity }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Medical supply-->
                            <div
                                class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-violet-100 border border-violet-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div
                                    class="flex flex-col flex-grow items-left text-left leading-normal"
                                >
                                    <div class="text-left">
                                        <p
                                            class="mb-2 text-lg font-semibold tracking-tight text-violet-500 dark:text-white"
                                        >
                                            Medical Supply
                                        </p>
                                        <p class="text-sm">
                                            {{
                                                batch.medical_supplies_quantity
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
                            ></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <Pagination
            :paginator="batches"
            :search="search"
            :sortField="sortField"
            :sortDirection="sortDirection"
        />
    </div>
</template>
