<script setup>
import { ref, watch, onMounted } from "vue";
import { initFlowbite, initModals } from "flowbite";
import Pagination from "../../../js/Components/ApprovePagination.vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import UserForm from "../../../js/Components/UserForm.vue";
import UserEditModal from "../../../js/Components/UserEdit.vue";
import UserEditPWModal from "../../../js/Components/userEditPW.vue";
import UserDeactivate from "@/Components/UserDeactivate.vue";
import UserReactivate from "@/Components/UserReactivate.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import StatusLabel from "../../Components/StatusLabel.vue";
import StatusFilter from "../../Components/statusFilter.vue";
import DangerButton from "@/Components/DangerButton.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    user_page: Object,
    barangayNames: Array,
    rhuBarangaysMap: Object,
});

const showAddModal = ref(false);
const showEditModal = ref(false);
const showPassModal = ref(false);
const showDeacModal = ref(false);
const showReacModal = ref(false);

const selectedUser = ref(null);

const openModal = (request, modal) => {
    selectedUser.value = request;

    if(modal =='add'){
        showAddModal.value = true;
    }if(modal =='edit'){
        showEditModal.value = true;
    }if(modal =='pass'){
        showPassModal.value = true;
    }if(modal =='deac'){
        showDeacModal.value = true;
    }if(modal =='reac'){
        showReacModal.value = true;
    }
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    showPassModal.value = false;
    showDeacModal.value = false;
    showReacModal.value = false;
};

const search = ref("");

watch(
    search,
    debounce(
        (q) => router.get("/users", { search: q }, { preserveState: true }),
        500
    )
);

onMounted(() => {
    initFlowbite();
    initModals();
});
const selectedStatus = ref([
    { id: "active", name: "Active", selected: false },
    { id: "deactivated", name: "Deactivated", selected: false },
]);
function updateQuery() {
    const statusFilters = selectedStatus.value
        .filter((status) => status.selected)
        .map((status) => status.id)
        .join(",");

    router.get(
        "/users",
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

function handleFormSubmitted() {
    resetFlag.value = true;
    setTimeout(() => {
        resetFlag.value = false;
    }, 0);
}

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
</script>

<template>
    <Head title="Users" />

    <div v-if="showAddModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <UserForm
            :resetFlag="resetFlag"
            @submitted="handleFormSubmitted"
            :barangayNames="barangayNames"
            :rhuBarangaysMap="rhuBarangaysMap"
            @close-modal="closeModal()"
        />
    </div>

    <div v-if="showEditModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <UserEditModal
            :edit_user="selectedUser"
            :barangayNames="barangayNames"
            :rhuBarangaysMap="rhuBarangaysMap"
            @close-modal="closeModal()"
        />
    </div>

    <div v-if="showPassModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <UserEditPWModal :user="selectedUser" @close-modal="closeModal()"/>
    </div>

    <div v-if="showDeacModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <UserDeactivate
            :deac_user="selectedUser"
            @close-modal="closeModal()"
        />
    </div>

    <div v-if="showReacModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <UserReactivate
            :reac_user="selectedUser"
            @close-modal="closeModal()"
        />
    </div>


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
                    type="button"
                    @click="openModal(selectedUser, 'add')"
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
                    Add new user
                </button>
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

                        <th scope="col" class="px-12 py-3">Name / Email</th>
                        <th scope="col" class="px-4 py-3">
                            <div class="flex items-center">
                                Role
                                <a href="#" @click.prevent="sort('role')"
                                    ><svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        /></svg
                                ></a>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <div class="flex items-center">
                                RHU
                                <a href="#" @click.prevent="sort('rhu')"
                                    ><svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        /></svg
                                ></a>
                            </div>
                        </th>
                        <th scope="col" class="px-4 py-3">
                            <div class="flex items-center">
                                Barangay
                                <a href="#" @click.prevent="sort('barangay')"
                                    ><svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        /></svg
                                ></a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3">
                            <div class="flex items-center">
                                Status
                                <a href="#" @click.prevent="sort('status')"
                                    ><svg
                                        class="w-3 h-3 ms-1.5"
                                        aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                        /></svg
                                ></a>
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody
                    v-for="usr in user_page.data"
                    data-accordion="collapse"
                    :key="usr.id"
                >
                    <tr
                        :key="usr.id"
                        :id="`accordion-collapse-heading-${usr.id}`"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        @click="toggleAccordion(usr.id)"
                    >

                        <td
                            class="flex items-center px-10 p-4 mr-12 space-x-6 whitespace-nowrap"
                        >
                            <img
                                v-if="usr.profile_picture"
                                class="w-10 h-10 rounded-full"
                                :src="`/storage/${usr.profile_picture}`"
                                alt="User Profile Picture"
                            />
                            <img
                                v-else
                                class="w-10 h-10 rounded-full"
                                src='/storage/icons/general/default_profile.svg'
                                alt="User Profile Picture"
                            />

                            <div
                                class="text-sm font-normal text-gray-500 dark:text-gray-400"
                            >
                                <div
                                    class="text-base font-semibold text-gray-900 dark:text-white"
                                >
                                    {{ usr.name }}
                                </div>
                                <div
                                    class="text-sm font-normal text-gray-500 dark:text-gray-400"
                                >
                                    {{ usr.email }}
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            {{ usr.role.name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ usr.rhus.rhu_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ usr.barangay.barangay_name }}
                        </td>
                        <td class="px-6 py-6">
                            <StatusLabel :all="usr.status" />
                        </td>
                    </tr>
                    <tr
                        :key="usr.id"
                        :id="`accordion-collapse-body-${usr.id}`"
                        :aria-labelledby="`accordion-collapse-heading-${usr.id}`"
                        v-show="activeAccordion === usr.id"
                        class="hidden w-full bg-gray-50 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        
                        <td class="text-center px-8" colspan="10">
                            <div class="grid grid-cols-1 gap-1 p-1"></div>
                            <!-- Details -->
                            <div class="text-left px-2">
                                <p
                                    class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                >
                                    Actions
                                </p>
                            </div>
                            <!-- Buttons -->
                            <div class="p-2 mb-3">
                                <div
                                    class="w-auto flex flex-row space-y-0 items-center justify-start space-x-3 flex-shrink-0"
                                >
                                    <button
                                        v-if="usr.status !== 'Deactivated'"
                                        :key="usr.id"
                                        type="button"
                                        @click="openModal(usr, 'edit')"
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
                                        v-if="usr.status !== 'Deactivated'"
                                        :key="usr.id"
                                        type="button"
                                        @click="openModal(usr, 'pass')"
                                        class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-green-500 hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
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
                                        Change Password
                                    </button>

                                    <DangerButton
                                        v-if="usr.status !== 'Deactivated'"
                                        @click="openModal(usr, 'deac')"
                                    >
                                        Deactivate
                                    </DangerButton>
                                    <button
                                        v-if="usr.status === 'Deactivated'"
                                        type="button"
                                        @click="openModal(usr, 'reac')"
                                        class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-green-500 hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800"
                                    >
                                        Reactivate
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination
            :paginator="user_page"
            :search="search"
            :selectedStatus="selectedStatus"
            :sortField="sortField"
            :sortDirection="sortDirection"
        />
    </div>
    <StatusFilter
        :selectedStatus="selectedStatus"
        @status-updated="updateQuery"
    />
</template>
