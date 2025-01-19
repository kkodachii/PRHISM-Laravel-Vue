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
    activitylog: Object,
    auth: Object,
});

const userRole = ref(props.auth.user.role_id);

onMounted(() => {
    initFlowbite();
});

const search = ref("");

watch(
    search,
    debounce(
        (q) =>
            router.get("/activitylog", { search: q }, { preserveState: true }),
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

const formatDate = (date) => {
    const options = {
        year: "numeric",
        month: "short",
        day: "2-digit",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
        timeZone: "Asia/Manila",
    };
    return new Date(date).toLocaleDateString("en-US", options);
};
</script>

<template>
    <Head title="Activity Log" />

    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
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
                            class="bg-gray-50 border text-gray-900 rounded-lg w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search by name"
                            v-model="search"
                        />
                    </div>
                </form>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="block md:hidden mt-4">
            <div class="grid grid-cols-1 gap-4 p-4">
                <div
                    v-for="activity in activitylog.data"
                    :key="activity.id"
                    class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md"
                >
                    <div class="flex justify-between items-start">
                        <div
                            class="text-base font-semibold text-gray-900 dark:text-white"
                        >
                            {{ activity.name }}
                        </div>
                        <StatusLabel :all="activity.type" />
                    </div>
                    <div
                        class="text-sm font-normal text-gray-500 dark:text-gray-400"
                    >
                        <template v-if="activity.role_id === 1"
                            >Midwife</template
                        >
                        <template v-else-if="activity.role_id === 2"
                            >Admin</template
                        >
                        <template v-else-if="activity.role_id === 3"
                            >SuperAdmin</template
                        >
                        <template v-else>Unknown Role</template>
                    </div>
                    <p class="mt-2 text-gray-700 dark:text-gray-300">
                        {{ activity.description }}
                    </p>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ formatDate(activity.updated_at) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="relative overflow-x-auto sm:rounded-lg hidden md:block">
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700"
                >
                    <tr>
                        <th class="px-6 py-3">USER</th>
                        <th class="px-6 py-3">TYPE</th>
                        <th class="px-6 py-3">ACTION</th>
                        <th class="px-6 py-3">DATE ADDED</th>
                    </tr>
                </thead>
                <tbody
                    v-if="
                        !activitylog.data ||
                        activitylog.data.length === 0
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
                    <tr
                        v-for="activity in activitylog.data"
                        :key="activity.id"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <td class="px-6 py-4">
                            <div
                                class="text-base font-semibold text-gray-900 dark:text-white"
                            >
                                {{ activity.name }}
                            </div>
                            <div
                                class="text-sm font-normal text-gray-500 dark:text-gray-400"
                            >
                                <template v-if="activity.role_id === 1"
                                    >Midwife</template
                                >
                                <template v-else-if="activity.role_id === 2"
                                    >Admin</template
                                >
                                <template v-else-if="activity.role_id === 3"
                                    >SuperAdmin</template
                                >
                                <template v-else>Unknown Role</template>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <StatusLabel :all="activity.type" />
                        </td>
                        <td class="px-6 py-4">
                            {{ activity.description }}
                        </td>
                        <td class="px-6 py-4">
                            {{ formatDate(activity.updated_at) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :paginator="activitylog" />
    </div>
</template>
