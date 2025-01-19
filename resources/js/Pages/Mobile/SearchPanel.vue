<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import { ref, watch } from "vue";
const searchInput = ref("");
const searchResults = ref([]);

const itemRoutes = {
    1: [
        { name: "Dashboard", route: "/dashboard" },
        { name: "Profile", route: "/profile" },
        { name: "Inventory", route: "/inventory" },
        { name: "Dispense", route: "/dispense" },
    ],
    2: [
        { name: "Dashboard", route: "/dashboard" },
        { name: "Batch add (Equipment)", route: "/batch-equipment" },
        { name: "Batch add (Medical Supply)", route: "/batch-msupply" },
        { name: "Batch add (Medicine)", route: "/batch-medicine" },
        { name: "Equipment", route: "/equipments" },
        { name: "Medical Supply", route: "/medical_supplies" },
        { name: "Medicine", route: "/medicines" },
        { name: "Delivery", route: "/delivery" },
        { name: "Profile", route: "/profile" },
        { name: "Reports", route: "/reports" },
        { name: "Add/Edit Category", route: "/generic_name" },
        { name: "Batches", route: "/batches" },
        { name: "Midwife Inventory", route: "/barangay_list" },
        { name: "Activity log", route: "/activitylog" },
        { name: "Usage log (Equipment)", route: "/equipmentlog" },
        { name: "Usage log (Medical Supply)", route: "/medicineusagelog" },
        { name: "Usage log (Medicine)", route: "/medicalsupplylog" },
        { name: "Reports", route: "/reports" },
    ],
    3: [
        { name: "Dashboard", route: "/dashboard" },
        { name: "Batch add (Equipment)", route: "/batch-equipment" },
        { name: "Batch add (Medical Supply)", route: "/batch-msupply" },
        { name: "Batch add (Medicine)", route: "/batch-medicine" },
        { name: "Equipment", route: "/equipments" },
        { name: "Medical Supply", route: "/medical_supplies" },
        { name: "Medicine", route: "/medicines" },
        { name: "Delivery", route: "/delivery" },
        { name: "Profile", route: "/profile" },
        { name: "Reports", route: "/reports" },
        { name: "Add/Edit Category", route: "/generic_name" },
        { name: "Batches", route: "/batches" },
        { name: "Midwife Inventory", route: "/barangay_list" },
        { name: "Backups", route: "/backups" },
        { name: "Activity log", route: "/activitylog" },
        { name: "Usage log (Equipment)", route: "/equipmentlog" },
        { name: "Usage log (Medical Supply)", route: "/medicineusagelog" },
        { name: "Usage log (Medicine)", route: "/medicalsupplylog" },
        { name: "Reports", route: "/reports" },
    ],
};

const props = defineProps({
    notifications: Array,
    users: Array,
    auth: Object,
});

// Watch for changes in the search input to filter results
watch(searchInput, (newValue) => {
    if (newValue && newValue.length >= 2) {
        const userRoleRoutes = itemRoutes[props.auth.user.role_id] || [];
        searchResults.value = userRoleRoutes.filter(({ name }) =>
            name.toLowerCase().includes(newValue.toLowerCase())
        );
    } else {
        searchResults.value = [];
    }
});
</script>
<template>
    <Sidebar></Sidebar>
    <div class="p-4">
        <div class="flex justify-between mb-2">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold">
                    Search
                </h1>
            </div>
        </div>
        <form
            action="#"
            method="GET"
            class="md:block md:pl-2"
        >
            <label for="topbar-search" class="sr-only"
                >Search</label
            >
            <div class="relative sm:w-64 md:w-96">
                <div
                    class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
                >
                    <svg
                        class="w-5 h-5 text-gray-500 dark:text-gray-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                        ></path>
                    </svg>
                </div>
                <input
                    type="text"
                    v-model="searchInput"
                    id="topbar-search"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="Search"
                />
                <!-- Dropdown for search results -->
                <div
                    v-if="searchInput.length >= 2"
                    class="absolute z-10 bg-white rounded-lg mt-1 w-full shadow-lg"
                >
                </div>
            </div>
            <div v-if="searchInput.length >= 2">
                <ul v-if="searchResults.length > 0">
                    <li
                        v-for="(result, index) in searchResults"
                        :key="index"
                        class="p-1 pl-10 my-5 hover:bg-gray-100 cursor-pointer"
                    >
                        <a
                            :href="result.route"
                            class="text-gray-700 text-md font-medium block w-full"
                            >{{ result.name }}</a
                        >
                    </li>
                </ul>
                <!-- Show "Nothing found" if no results are returned -->
                <div
                    v-else
                    class="p-2 pl-10 text-gray-700 text-left"
                >
                    <p>Nothing found</p>
                    <p class="text-gray-500">
                        We couldn't find any matches for "{{
                            searchInput
                        }}".
                    </p>
                </div>
            </div>
        </form>
    </div>
</template>
