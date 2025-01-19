<script setup>
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { initFlowbite } from "flowbite";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import formattedDate from "@/Components/formattedDate.vue";
import Pagination from "@/Components/Pagination.vue";
import { Inertia } from "@inertiajs/inertia";
import LoadingModal from "@/Components/LoadingModal.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    backups: Object,
});

const current_backups = props.backups.data;
const isLoading = ref(false);
const showRestoreModal = ref(false);
const selectedBackup = ref("");

onMounted(() => {
    initFlowbite;
});

const form = useForm({});

const openRestoreModal = (path) => {
    selectedBackup.value = path;
    showRestoreModal.value = true;
};

const closeRestoreModal = () => {
    showRestoreModal.value = false;
};

// Perform the backup process
const backup = () => {
    isLoading.value = true;
    form.post("/backup", {
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            isLoading.value = false;
        },
    });
};

function restoreBackup(filePath) {
    isLoading.value = true;
    form.post(route("backup.restore", filePath), {
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            isLoading.value = false;
        },
    });
}

// function downloadBackup(filePath) {
//     window.location.href = route('backup.download', { filePath: filePath });
// }
</script>

<template>
    <Head title="Backups" />
    <LoadingModal :isLoading="isLoading" />
    <div
        class="bg-white p-5 dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
        <form @submit.prevent="backup">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Backup and Restore</h1>
                    <p class="text-md text-gray-700">Recent backups</p>
                </div>
                <button
                    type="submit"
                    class="flex items-center justify-center h-10 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="backup()"
                >
                    <svg
                        class="w-6 h-6 mr-2 text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7.414A2 2 0 0 0 20.414 6L18 3.586A2 2 0 0 0 16.586 3H5Zm3 11a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v6H8v-6Zm1-7V5h6v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z"
                            clip-rule="evenodd"
                        />
                        <path
                            fill-rule="evenodd"
                            d="M14 17h-4v-2h4v2Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Backup now
                </button>
            </div>
        </form>

        <div class="bg-white mt-5 dark:bg-gray-800 relative sm:rounded-lg">
            <table
                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                >
                    <tr>
                        <th scope="col" class="px-6 py-3">Name</th>
                        <th scope="col" class="px-6 py-3">Backup Date</th>
                        <th scope="col" class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody v-for="backup in current_backups">
                    <tr
                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                    >
                        <td class="pl-6 py-4 flex flex-row gap-3">
                            <svg
                                class="w-6 h-6 text-gray-800 dark:text-white"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7Zm3 2h2.01v2.01h-2V8h2v2.01h-2V12h2v2.01h-2V16h2v2.01h-2v2H12V18h2v-1.99h-2V14h2v-1.99h-2V10h2V8.01h-2V6h2V4Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            {{ backup.path }}
                        </td>
                        <td class="px-6 py-4">
                            <formattedDate
                                :date="`${backup.created_at}`"
                            ></formattedDate>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="px-2 w-auto flex flex-row space-y-0 items-center justify-end space-x-3 flex-shrink-0"
                            >
                                <!-- <button
                                @click="downloadBackup(backup.path)"
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
                                    Download
                            </button> -->
                                <button
                                    @click="openRestoreModal(backup.path)"
                                    class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-orange-500 hover:bg-orange-600 focus:ring-4 focus:ring-orange-500 dark:bg-orange-500 dark:hover:bg-orange-600 focus:outline-none dark:focus:ring-orange-600"
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
                                    Restore
                                </button>
                                <!-- <button
                                    type="button"
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
                            </button> -->
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :paginator="backups" />

        <!-- Restore Modal -->
        <div
            v-if="showRestoreModal"
            tabindex="-1"
            aria-hidden="true"
            class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
            <div class="relative w-full max-w-lg">
                <div
                    class="relative bg-white rounded-lg shadow dark:bg-gray-800 md:p-8"
                >
                    <h3
                        class="mb-3 text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
                    >
                        Restore selected backup?
                    </h3>
                    <div
                        class="mb-1 text-xs md:text-sm font-light p-4 text-gray-500 dark:text-gray-400"
                    >
                        <p class="font-semibold text-gray-600 mb-2">
                            This action will overwrite the current database with
                            the backup data, which
                            <span class="text-red-600">cannot be undone.</span>
                        </p>
                        <p class="font-semibold text-gray-600">
                            Please ensure that you have a recent backup of the
                            current database before proceeding.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 mt-4 w-full gap-10">
                        <button
                            @click="closeRestoreModal()"
                            class="col-span-1 py-2 w-full bg-gray-300 rounded-md"
                        >
                            Cancel
                        </button>
                        <button
                            @click="restoreBackup(selectedBackup)"
                            class="col-span-1 py-2 w-full bg-blue-700 text-white rounded-md"
                        >
                            Restore
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
