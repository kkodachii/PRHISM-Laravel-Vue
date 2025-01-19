<script setup>
import { ref, onMounted} from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { unshiftMedsup } from "../unshiftmodal.js";
import LoadingModal from "@/Components/LoadingModal.vue";

const props = defineProps({
    MsupplyNames: Array,
    user: Object,
    batches: Array,
});

const emit1 = defineEmits();

const date_now = new Date().toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    timeZone: "Asia/Manila",
});

// Define the form using useForm
const form = useForm({
    user_id: props.user.id,
    batch_id: "",
    barangay_id: props.user.barangay_id,
    med_sup_name: "",
    description: "",
    quantity: "",
    provider: "",
    date_acquired: date_now,
});

const isLoading = ref(false);
const showDropdown = ref(false);
const filteredSupplies = ref(props.MsupplyNames);

// Function to filter equipment based on input
function filterSupplies() {
    const query = form.med_sup_name.toLowerCase();
    filteredSupplies.value = props.MsupplyNames.filter((supply) =>
        supply.med_sup_name.toLowerCase().includes(query)
    );
}

// Function to select an equipment name from the dropdown
function selectSupply(medName) {
    form.med_sup_name = medName;
    showDropdown.value = false; // Close the dropdown after selection
}

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
    const isClickInside = e.target.closest(".relative");
    if (!isClickInside) {
        showDropdown.value = false;
    }
});

onMounted(() => {
    initFlowbite();
});

const providerOptions = ref([
    { value: "DOH", label: "DOH" },
    { value: "LGO", label: "LGO" },
    { value: "Donation", label: "Donation" },
]);

const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

function resetForm() {
    emit1('close-modal');
    form.reset();
}

function submit() {
    isLoading.value = true;
    form.post(route("medical_supplies.store"), {
        onSuccess: () => {
            isLoading.value = false;
            unshiftMedsup(filteredSupplies.value, form);
            resetForm();
        },
        onError: () => {
            isLoading.value = false;
            resetForm();
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
                    Add New Medical Supply
                </h3>
                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-toggle="supply-crud-modal"
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
                <div class="grid gap-4 mb-4 grid-cols-3">
                    <div class="col-span-2">
                        <label
                            for="med_sup_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Medical Supply Name
                        </label>
                        <div class="relative">
                            <div class="relative">
                                <!-- Input field that allows selection or typing -->
                                <input
                                    v-model="form.med_sup_name"
                                    @focus="showDropdown = true"
                                    @input="filterSupplies"
                                    placeholder="Select or type an Medical Supply Name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                />

                                <!-- Dropdown menu -->
                                <div
                                    v-show="
                                        showDropdown && filteredSupplies.length
                                    "
                                    class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 dark:bg-gray-700 dark:border-gray-500"
                                >
                                    <ul class="max-h-48 overflow-y-auto">
                                        <li
                                            v-for="supply in filteredSupplies"
                                            :key="supply.id"
                                            @click="
                                                selectSupply(
                                                    supply.med_sup_name
                                                )
                                            "
                                            class="cursor-pointer px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white"
                                        >
                                            {{ supply.med_sup_name }}
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <p
                                v-if="form.errors.med_sup_name"
                                class="text-red-600 text-sm"
                            >
                                {{ form.errors.med_sup_name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-span-1">
                        <label
                            for="batch_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Batch number
                        </label>
                        <select
                            v-model="form.batch_id"
                            id="batch_id"
                            required=""
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" selected disabled>
                                Select Batch ID
                            </option>
                            <option
                                v-for="batch in props.batches"
                                :key="batch.id"
                                :value="batch.id"
                            >
                                {{ batch.batch_number }}
                            </option>
                        </select>
                        <p
                            v-if="form.errors.batch_id"
                            class="text-red-600 text-sm"
                        >
                            {{ form.errors.batch_id }}
                        </p>
                    </div>
                </div>
                <div class="grid gap-4 mb-4 grid-cols-3">
                    <div class="col-span-1 sm:col-span-1">
                        <label
                            for="quantity"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Quantity
                        </label>
                        <input
                            v-model="form.quantity"
                            type="number"
                            id="quantity"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Input quantity"
                            required
                        />
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label
                            for="provider"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Provider
                        </label>
                        <select
                            v-model="form.provider"
                            required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" disabled>Select Provider</option>
                            <option
                                v-for="option in providerOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label
                            for="date_acquired"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Received Date
                        </label>
                        <div class="relative max-w-sm">
                            <div
                                class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none"
                            >
                                <svg
                                    class="w-4 h-4 text-gray-500 dark:text-gray-400"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"
                                    />
                                </svg>
                            </div>
                            <vue-tailwind-datepicker
                                :formatter="date_format"
                                v-model="form.date_acquired"
                                id="date_acquired"
                                as-single
                                placeholder="Select date"
                                required
                            />
                        </div>
                    </div>
                </div>
                <div class="grid gap-4 mb-4 grid-cols-3">
                    <div class="col-span-2">
                        <label
                            for="description"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Description
                        </label>
                        <textarea
                            v-model="form.description"
                            type="text"
                            rows="3"
                            id="description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Description"
                        ></textarea>
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
                        Add new medical supply
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
