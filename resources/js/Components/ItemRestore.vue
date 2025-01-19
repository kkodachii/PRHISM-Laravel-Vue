<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";

const props = defineProps({
    ID: Number,
    item: String,
});

const form = useForm({});

onMounted(() => {
    initFlowbite();
});

function submit() {
    let routeName;
    switch (props.item) {
        case "med":
            routeName = "medicines.restore"; // Use restore route
            break;
        case "equip":
            routeName = "equipments.restore"; // Use restore route
            break;
        case "medsup":
            routeName = "supplies.restore"; // Use restore route
            break;
        default:
            console.error("Unknown item type");
            return;
    }

    form.put(route(routeName, { id: props.ID }), {
        onSuccess: () => {
            form.reset();
            const modal = document.getElementById("crud-modal4");
            if (modal) {
                modal.dispatchEvent(
                    new CustomEvent("click", {
                        bubbles: true,
                        cancelable: true,
                    })
                );
            }
        },
        onError: () => {
            alert("Failed to restore item");
        },
    });
}
</script>


<template>
    <div class="relative p-4 w-full max-w-md h-full md:h-auto">
        <!-- Modal content -->
        <div class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5">
            <form @submit.prevent="submit" class="p-4 md:p-5">
                <button
                    type="button"
                    class="text-gray-400 absolute top-2.5 right-2.5 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="crud-modal4"
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
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm-2 16c-2.05 0-3.81-1.24-4.58-3h1.71c.63.9 1.68 1.5 2.87 1.5 1.93 0 3.5-1.57 3.5-3.5S13.93 9.5 12 9.5c-1.35 0-2.52.78-3.1 1.9l1.6 1.6h-4V9l1.3 1.3C8.69 8.92 10.23 8 12 8c2.76 0 5 2.24 5 5s-2.24 5-5 5z"
                        clip-rule="evenodd"
                    ></path>
                </svg>
                <p class="mb-4 text-gray-500 dark:text-gray-300">
                    Are you sure you want to restore this item?
                </p>
                <div class="flex justify-center items-center space-x-4">
                    <button
                        data-modal-toggle="crud-modal4"
                        type="button"
                        class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                    >
                        No, cancel
                    </button>
                    <button
                        data-modal-toggle="crud-modal4"
                        type="submit"
                        class="py-2 px-3 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-900"
                    >
                        Yes, restore it
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

