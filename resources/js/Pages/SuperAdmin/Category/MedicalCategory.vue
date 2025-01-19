<script setup>
import { ref, watch, onMounted, unref  } from "vue";
import { initFlowbite, initModals } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import { router, useForm } from "@inertiajs/vue3";
import { debounce } from "lodash";
import Delete from "@/Components/ItemDelete.vue";
import ToastList from "@/Components/ToastList.vue";
import CategoryTab from "@/Components/CategoryTab.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    medical_categories: Object,
    unusedCategories: Array,
});

const unusedCategories = ref(props.unusedCategories);

const search = ref("");
watch(() => props.unusedCategories, (newVal) => {
    unusedCategories.value = newVal;
}, { deep: true });

watch(
    search,
    debounce(
        (q) => router.get("/medical_category", { search: q }, { preserveState: true }),
        500
    )
);

const isAdding = ref(false);
const addForm = useForm({
    category: "",
});
const selectedID = ref(null);
function openDeleteModal(ID) {
    selectedID.value = ID;
}
function openAdding() {
    isAdding.value = true;
    isEditing.value = null;
}

function closeAdding() {
    isAdding.value = false;
    addForm.reset();
}

const isEditing = ref(null);
const openEditing = ref(false);

const editForm = useForm({
    id: null,
    category: "",
});

function startEditing(medical_category) {
    isAdding.value = false;
    openEditing.value = true;
    isEditing.value = medical_category.id;
    editForm.category = medical_category.category;
    editForm.id = medical_category.id;
}

function cancelEdit() {
    isEditing.value = null;
    openEditing.value = false;
    editForm.reset();
}

const deleteForm = useForm({
    id: null,
});

function submitAdd() {
    addForm.post(route("medical_category.store"), {
        onSuccess: () => {
            addForm.reset();
            isAdding.value = false;
            openEditing.value = false;
        },
        onError: () => {},
    });
}

function submitEdit() {
    editForm.put(route("medical_category.update", editForm.id), {
        onSuccess: () => {
            editForm.reset();
            isAdding.value = false;
            openEditing.value = false;
        },
        onError: () => {
            // Handle error
        },
    });
}

function submitDelete(medical_category) {
    deleteForm.delete(route("medical_category.destroy", { id: medical_category.id }), {
        onSuccess: () => {
            deleteForm.reset();
            isAdding.value = false;
            openEditing.value = false;
        },
        onError: () => {
            alert("Failed to delete item");
        },
    });
}



</script>
<template>
<Head title="Medical Categories" />


    <!-- Tabs -->
    <CategoryTab />

    <div class="rounded-lg bg-gray-50 dark:bg-gray-800">
        <div
            class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
        >
            <!-- Search and filter -->
            <div
                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
            >
                <!-- Search -->
                <div class="w-full md:w-1/2">
                    <form class="flex items-center">
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
                        v-if="isAdding==true"
                        @click="closeAdding"
                        type="button"
                        class="flex items-center justify-center font-medium rounded-lg text-sm px-3 p-2 text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300  dark:bg-gray-600 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-gray-800"
                    >
                        Cancel
                    </button>
                    <button
                        v-else
                        @click="openAdding"
                        type="button"
                        class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    >
                        <svg
                            class="h-5 w-4 mr-3"
                            fill="currentColor"
                            viewbox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                clip-rule="evenodd"
                                fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            />
                        </svg>
                        Add Medical category
                    </button>
                </div>
            </div>
            <div
        id="crud-modal3"
        tabindex="-1"
        aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
    >
        <Delete :ID="selectedID" :item="'category'" />
    </div>
            <!-- add new -->

            <template v-if="isAdding === true">

            <form @submit.prevent="submitAdd">

                <div class="bg-gray-50 p-2">
                <div
                    class="bg-white flex-row items-center justify-between p-4 overflow-x-auto sm:rounded-lg dark:bg-gray-800"
                >
                    <h1
                        class="text-lg font-semibold mb-2 text-gray-900 dark:text-white"
                    >
                        Add new Medical category
                    </h1>

                    <div class="flex lg:flex-row flex-col gap-3">
                        <input
                            v-model="addForm.category"
                            type="text"
                            name="category"
                            id="category"
                            class="block w-full lg:w-5/12 p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type Medical category name"
                            required=""
                        />
                        <div
                            class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
                        >
                            <button
                        type="submit"
                        :class="{ 'opacity-25': addForm.processing }"
                        :disabled="addForm.processing"
                                class="flex items-center justify-center text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                            >
                                <svg
                                    class="w-6 h-6 mr-1 text-white-800 dark:text-black"
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
                                        d="M5 11.917 9.724 16.5 19 7.5"
                                    />
                                </svg>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
                                </template>

            <!-- Edit-->
            <template v-if="openEditing === true">
                <form @submit.prevent="submitEdit">

            <div class="bg-gray-50 p-2">
            <div
                class="bg-white flex-row items-center justify-between p-4 overflow-x-auto sm:rounded-lg dark:bg-gray-800"
            >
                <h1
                    class="text-lg font-semibold mb-2 text-gray-900 dark:text-white"
                >
                    Edit Medical category
                </h1>

                <div class="flex lg:flex-row flex-col gap-3">
                    <input
                        v-model="editForm.category"
                        type="text"
                        name="category"
                        id="category"
                        class="block w-full lg:w-5/12 p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Type Medical category name"
                        required=""
                    />
                    <div
                        class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0"
                    >
                        <button
                    type="submit"
                    :class="{ 'opacity-25': editForm.processing }"
                    :disabled="editForm.processing"
                            class="flex items-center justify-center font-medium rounded-lg text-sm px-3 py-2 text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300  dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                        >
                            <svg
                                class="w-6 h-6 mr-1 text-white-800 dark:text-black"
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
                                    d="M5 11.917 9.724 16.5 19 7.5"
                                />
                            </svg>
                            Save
                        </button>
                        <button
                                        @click="cancelEdit"
                                        class="flex items-center justify-center px-3 py-2.5 mx-2 text-sm font-medium text-center text-white rounded-lg bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800"
                                    >
                                        Cancel
                                    </button>
                    </div>
                </div>
            </div>
        </div>
        </form>
            </template>


            <!-- Table -->
            <div class="relative overflow-x-auto sm:rounded-lg">
                <table
                    class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="px-8 w-48 py-3">Category Name</th>
                            <th scope="col" class="px-2 w-80 text-center py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody v-for="medical_category in medical_categories.data">

                        <tr
                            :key="medical_category.id"
                            class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        >

                            <th class="px-8 w-48 py-4">
                                {{ medical_category.category }}
                            </th>
                            <td class="px-2 w-80 py-4">
                                <div class="flex justify-center">
                                    <button
                                        v-if="openEditing === true"
                                        @click="cancelEdit"
                                        class="cursor-not-allowed inline-flex items-center px-3 py-1.5 mx-2 text-sm font-medium text-center text-white rounded-lg bg-blue-400 dark:bg-blue-500" disabled>
                                        Edit
                                    </button>
                                    <button
                                        v-else
                                        @click="startEditing(medical_category)"
                                        class="inline-flex items-center px-3 py-1.5 mx-2 text-sm font-medium text-center text-white rounded-lg bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                    >
                                        Edit
                                    </button>
                                    
                                    <button
                v-if="unref(unusedCategories).some(item => item.id === medical_category.id)"
                data-modal-target="crud-modal3"
                                                        data-modal-toggle="crud-modal3"
  @click="openDeleteModal(medical_category.id)"
                class="inline-flex items-center px-3 py-1.5 mx-2 text-sm font-medium text-center text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:focus:ring-red-900"
              >

                Delete
              </button>
          
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :paginator="medical_categories" />
        </div>
    </div>



</template>
