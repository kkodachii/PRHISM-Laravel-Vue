<script setup>
import { computed } from "vue";

const props = defineProps({
    recentlyAddedItems: Object,
});

const statusClasses = {
    Medicine:
        "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300",
    Equipment: "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300",
    "Medical Supply":
        "bg-violet-200 text-violet-800 dark:bg-violet-900 dark:text-violet-300",
};

const getStatusClass = (type) => {
    return statusClasses[type] || "";
};
</script>

<template>
    <div
        class="rounded-lg bg-white shadow sm:rounded-lg border-gray-200 dark:border-gray-600 mb-5"
    >
        <div class="flex flex-row justify-between p-4">
            <h6
                class="mb-2 text-xl font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Recently Added
            </h6>
            <a
                :href="route('recentlylog.index')"
                class="text-l font-medium text-blue-600 dark:text-blue-500 hover:underline"
                >See all</a
            >
        </div>

        <div class="overflow-hidden lg:h-52 md:h-48 h-20 hidden md:block">
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Type</th>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3 hidden md:table-cell">
                            Date Added
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="item in recentlyAddedItems"
                        :key="item.name"
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <td class="px-6 py-4">
                            <span
                                :class="getStatusClass(item.type)"
                                class="px-3 py-1 rounded-full text-xs font-medium"
                            >
                                {{ item.type }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 font-medium text-gray-900 dark:text-white"
                        >
                            {{ item.name }}
                        </td>
                        <td class="px-6 py-4 hidden md:table-cell">
                            {{ item.date_added }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="block md:hidden overflow-y-auto h-48">
            <div class="grid grid-cols-1 gap-4 p-4">
                <div
                    v-for="item in recentlyAddedItems"
                    :key="item.name"
                    class="bg-white shadow-md rounded-lg p-4 border dark:bg-gray-800"
                >
                    <div class="flex justify-between items-start">
                        <p
                            class="font-medium text-gray-900 dark:text-white overflow-hidden overflow-ellipsis"
                        >
                            {{ item.name }}
                        </p>
                        <span
                            :class="getStatusClass(item.type)"
                            class="px-2 py-1 rounded-full text-xs font-medium"
                        >
                            {{ item.type }}
                        </span>
                    </div>
                    <p
                        class="mt-2 text-sm text-gray-500 overflow-hidden overflow-ellipsis"
                    >
                        {{ item.date_added }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
