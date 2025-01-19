<script setup>
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";

const props = defineProps({
    ID: Number,
    item: String,
});
const emit4 = defineEmits();

const form = useForm({ status: "Condemned" });

function closeDefectModal() {
    emit4('close-modal');
}

onMounted(() => {
    initFlowbite();
});

function submit() {
    let routeName;
    switch (props.item) {
        case "med":
            routeName = "medicines.updateStatus";
            break;
        case "equip":
            routeName = "equipments.updateStatus";
            break;
        case "medsup":
            routeName = "medical_supplies.updateStatus";
            break;
        default:
            console.error("Unknown item type");
            return;
    }

    form.post(route(routeName, { id: props.ID }), {
        onSuccess: () => {
            form.reset();
            closeDefectModal();
        },
        onError: () => {
            alert("Failed to update status");
            closeDefectModal();
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
                    @click="closeDefectModal"
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
                <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                </svg>
                <p class="mb-4 text-gray-500 dark:text-gray-300">
                    Are you sure you want to mark this item as Condemned?
                </p>
                <div class="flex justify-center items-center space-x-4">
                    <button
                        type="button"
                        @click="closeDefectModal"
                        class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                    >
                        No, cancel
                    </button>
                    <button
    data-modal-toggle="crud-modal4"
    type="submit"
    class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-orange-400 hover:bg-orange-500 focus:ring-4 focus:ring-orange-100 dark:bg-orange-300 dark:hover:bg-orange-400 focus:outline-none dark:focus:ring-orange-500"
>
                        Yes, I'm sure
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
