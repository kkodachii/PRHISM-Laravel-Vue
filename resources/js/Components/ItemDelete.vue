<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted  } from "vue";
import { initFlowbite } from "flowbite";

const props = defineProps({
    ID: Number,
    item: String,
});


const emit = defineEmits();

const form = useForm({});

function closeDeleteModal() {
    emit('close-modal');
}

onMounted(() => {
    initFlowbite();
});

function submit() {
    let routeName;
    switch (props.item) {
        case "med":
            routeName = "medicines.destroy";
            break;
        case "equip":
            routeName = "equipments.destroy";
            break;
        case "medsup":
            routeName = "medical_supplies.destroy";
            break;
        case "batch":
            routeName = "batch.destroy";
            break;
        case "brgy":
            routeName = "barangay.destroy";
            break;
        case "category":
            routeName = "medical_category.destroy";
            break;
        case "genname":
            routeName = "generic_name.destroy";
            break;
        default:
            console.error("Unknown item type");
            return;
    }

    form.delete(route(routeName, { id: props.ID }), {
        onSuccess: () => {
            form.reset();
            closeDeleteModal();
        },
        onError: () => {
            alert("Failed to delete item");
            closeDeleteModal();
        },
    });
}
</script>

<template>
    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
        <!-- Modal content -->
        <div
            class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5"
        >
        <form @submit.prevent="submit" class="p-4 md:p-5">
                <button
                    type="button"
                    class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    @click="closeDeleteModal"
                >
                    <svg
                        aria-hidden="true"
                        class="w-5 h-5"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        ></path>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <svg
                    class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-3.5 mx-auto"
                    aria-hidden="true"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
                <p class="mb-4 text-gray-500 dark:text-gray-300">
                    Are you sure you want to delete this item?
                </p>
                <div class="flex justify-center items-center space-x-4">
                    <button
                        type="button"
                        @click="closeDeleteModal"
                        class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                    >
                        No, cancel
                    </button>
                    <button
                        data-modal-toggle="crud-modal3"
                        type="submit"
                        class="py-2 px-3 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-900"
                    >
                        Yes, I'm sure
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
