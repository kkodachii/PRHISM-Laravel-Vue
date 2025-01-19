<script setup>
import { ref, onMounted} from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import LoadingModal from "@/Components/LoadingModal.vue";

const props = defineProps({
    categories: Object,
});

const emit4 = defineEmits();

const form = useForm({
    category_id: "",
    generic_name: "",
});

const isLoading = ref(false);
onMounted(() => {
    initFlowbite();
});

function resetForm() {
    emit4('close-modal');
    form.reset();
}

function submit() {
    isLoading.value = true;
    form.post(route("generic_name.store"), {
        onSuccess: () => {
            isLoading.value = false;
            form.reset();
        },
        onError: () => {
            isLoading.value = false;
        },
    });
}
</script>

<template>
    <LoadingModal :isLoading="isLoading" />
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Add New Generic Name
                </h3>
                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="generic-crud-modal"
                    @click="resetForm"
                >
                    <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <form @submit.prevent="submit" class="p-4 md:p-5">
                <div class="grid gap-4 mb-4 grid-cols-2">
                    <div class="col-span-1">
                        <label
                            for="category_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Medical Category</label>
                        <select
                            v-model="form.category_id"
                            id="category_id"
                            required=""
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option disabled value="">Select Medical Category</option>
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.category }}
                            </option>
                        </select>
                    </div>
                    <div class=" col-span-1">
                        <label
                            for="generic_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >Generic Name</label>
                        <input
                            v-model="form.generic_name"
                            type="text"
                            name="generic_name"
                            id="generic_name"
                            class="block w-full  p-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type Generic name"
                            required=""
                        />
                    </div>
                </div>

                <div class="flex justify-end items-center space-x-4">
                    <button
                        type="submit"
                        class="text-white inline-flex items-center mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        <svg
                            class="me-1 -ms-1 w-5 h-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                        Add new Generic Name
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
