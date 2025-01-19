<script setup>
import { ref, watch, onMounted } from "vue";
import { initFlowbite, initModals } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import { router } from "@inertiajs/vue3";
import { debounce } from "lodash";
import ToastList from "@/Components/ToastList.vue";
import formattedDate from "@/Components/formattedDate.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Inertia } from "@inertiajs/inertia";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    notifications_list: Object,
});

const notifs = ref(props.notifications_list.data);

const parseData = (data) => {
    try {
        return JSON.parse(data); // Convert the JSON string into an object
    } catch (e) {
        console.error("Failed to parse data:", e);
        return {};
    }
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

const markAsRead = (notif_data) => {
    const id = notif_data.id;
    console.log(id);
    if (notif_data.read_at == null) {
        Inertia.post(
            `/notifications/${id}/mark-read`,
            {},
            {
                preserveScroll: true,
                preserveState: true,
                onSuccess: () => {
                    const index = notifications.findIndex((n) => n.id === id);
                    if (index !== -1) {
                        notifications[index].read_at = new Date().toISOString(); // Mark as read locally
                    }
                    toggleAccordion(id);
                },
            }
        );
    } else {
        toggleAccordion(id);
    }
};
</script>

<template>
    <Head title="Notifications" />

    <div
        class="col-span-1 bg-white rounded-lg border border-gray-200 shadow-sm"
    >
        <div class="grid lg:grid-cols-2 grid-cols-1">
            <!-- Title -->
            <div class="col-span-1 px-5 mt-5 gap-4">
                <h2 class="text-2xl font-bold mb-2 px-1">Notifications</h2>
            </div>
            <!-- Search Bar and Filter Button Section -->
            <div class="col-span-1 w-full">
                <div
                    class="col-span-1 flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 p-4"
                >
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

        <!-- Table -->
        <div class="w-full">
            <div
                class="bg-white dark:bg-gray-800 relative overflow-x-auto sm:rounded-lg"
            >
                <div class="relative overflow-x-auto sm:rounded-lg">
                    <div
                        v-for="notif in notifs"
                        :key="notif.id"
                        data-accordion="collapse"
                    >
                        <div
                            :id="`accordion-collapse-heading-${notif.id}`"
                            class="p-4 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                            :class="{ 'bg-gray-50': notif.read_at == null }"
                            @click="markAsRead(notif)"
                        >
                            <div class="flex flex-row justify-start">
                                <div
                                    v-if="notif.user_info"
                                    class="flex items-center w-16 mx-4"
                                >
                                    <img
                                        class="rounded-full"
                                        :src="
                                            notif.user_info.profile_picture
                                                ? `/storage/${notif.user_info.profile_picture}`
                                                : '/storage/icons/general/default_profile.svg'
                                        "
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="
                                        parseData(notif.data).action ===
                                        'Medicine Alert'
                                    "
                                    class="flex items-center mx-3 md:mx-4"
                                >
                                    <img
                                        class="rounded-full bg-red-100 max-w-10 p-1"
                                        src="/storage/icons/notifs/medicine.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="!parseData(notif.data).user_name && parseData(notif.data).action.includes('Delivery')"
                                    class="flex items-center mx-3 md:mx-4"
                                >
                                    <img
                                        class="rounded-full bg-orange-100 max-w-10 p-1"
                                        src="/storage/icons/notifs/delivery.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="!parseData(notif.data).user_name && parseData(notif.data).action.includes('Database')"
                                    class="flex items-center mx-3 md:mx-4"
                                >
                                    <img
                                        class="rounded-full bg-orange-100 max-w-10 p-1"
                                        src="/storage/icons/notifs/database.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div
                                    v-if="!parseData(notif.data).user_name && parseData(notif.data).action.includes('Request')"
                                    class="flex items-center mx-3 md:mx-4"
                                >
                                    <img
                                        class="rounded-full bg-orange-100 max-w-10 p-1"
                                        src="/storage/icons/notifs/request.svg"
                                        alt="User Profile Picture"
                                    />
                                </div>
                                <div class="flex flex-col w-full mt-2">
                                    <div class="justify-between">
                                        <p class="text-md font-semibold">
                                            {{ parseData(notif.data).title }}
                                        </p>
                                        <p
                                            v-if="notif.user_info"
                                            class="text-md font-semibold"
                                        >
                                            <span
                                                class="font-normal text-gray-500"
                                                >{{
                                                    parseData(notif.data)
                                                        .message
                                                }}</span
                                            >
                                        </p>
                                        <p
                                            v-if="notif.medicine_info"
                                            class="text-md font-semibold"
                                        >
                                            <span
                                                class="font-normal text-gray-500"
                                                >{{
                                                    parseData(notif.data)
                                                        .message
                                                }}</span
                                            >
                                        </p>
                                    </div>
                                    <div v-if="notif.user_info" class="my-1">
                                        <span class="text-sm font-normal">{{
                                            notif.user_info.role.name
                                        }}</span>
                                    </div>
                                </div>
                                <div
                                    class="flex justify-end w-2/4"
                                >
                                    <button v-if="notif.read_at == null"
                                        class="text-blue-500 text-sm cursor-pointer"
                                        @click.stop
                                        @click="markAsRead(notif)"
                                    >
                                        Mark as read
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Accordion Body -->
                        <div
                            :id="`accordion-collapse-body-${notif.id}`"
                            :aria-labelledby="`accordion-collapse-heading-${notif.id}`"
                            v-show="activeAccordion === notif.id"
                            class="hidden w-full border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="ml-14 my-2 grid grid-cols-3">
                                <p class="col-span-1" v-if="notif.user_info">
                                    <span class="font-semibold">Barangay: </span
                                    >{{
                                        notif.user_info.barangay.barangay_name
                                    }}
                                </p>
                                <p class="col-span-1">
                                    <span class="font-semibold"
                                        >Acquired:
                                    </span>
                                    <formattedDate
                                        :date="`${notif.created_at}`"
                                    />
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Pagination
            :paginator="notifications_list"
            :search="search"
            :sortField="sortField"
            :sortDirection="sortDirection"
        />
    </div>
</template>
