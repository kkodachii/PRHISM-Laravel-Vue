<script setup>
import { ref, watch, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import { unshiftEquipment } from "../unshiftmodal.js";
import LoadingModal from "@/Components/LoadingModal.vue";

// Props
const props = defineProps({
    equipment: Object,
    equipNames: Array,
    batches: Array,
});
const emit2 = defineEmits();

// Form Setup
const form = useForm({
    id: props.equipment?.id ?? "",
    user_id: props.equipment?.user_id ?? "1",
    barangay_id: props.equipment?.barangay_id ?? "1",
    batch_id: props.equipment?.batch_id ?? "",
    equipment_name: props.equipment?.equipment_name ?? "",
    description: props.equipment?.description ?? "",
    quantity: props.equipment?.quantity ?? "",
    provider: props.equipment?.provider ?? "",
    date_acquired: props.equipment?.rawDateAcquired ?? "",
    status: props.equipment?.status ?? "",
    accountable: props.equipment?.accountable ?? "",
});

function resetForm() {
    emit2('close-modal');
    form.reset();
}

const isLoading = ref(false);

const showDropdown = ref(false);
const filteredEquipments = ref(props.equipNames);

// Function to filter equipment based on input
function filterEquipments() {
    const query = form.equipment_name.toLowerCase();
    filteredEquipments.value = props.equipNames.filter((equipment) =>
        equipment.equipment_name.toLowerCase().includes(query)
    );
}

// Function to select an equipment name from the dropdown
function selectEquipment(equipmentName) {
    form.equipment_name = equipmentName;
    showDropdown.value = false; // Close the dropdown after selection
}

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
    const isClickInside = e.target.closest(".relative");
    if (!isClickInside) {
        showDropdown.value = false;
    }
});

const formatDate = (dateString) => {
    if (!dateString) return "";
    const options = { year: 'numeric', month: 'short', day: '2-digit' };
    return new Date(dateString).toLocaleDateString('en-GB', options).replace(',', '');
};

// Watch for equipment prop changes
watch(
    () => props.equipment,
    (newVal) => {
        if (newVal) {
            form.user_id = newVal.user_id || "";
            form.barangay_id = newVal.barangay_id || "";
            form.batch_id = newVal.batch_id || "";
            form.equipment_name = newVal.equipment_name || "";
            form.description = newVal.description || "";
            form.quantity = newVal.quantity || "";
            form.provider = newVal.provider || "";
            form.date_acquired = newVal.rawDateAcquired ? formatDate(newVal.rawDateAcquired) : "";
            form.status = newVal.status || "";
            form.accountable = newVal.accountable || "";
        }
    },
    { immediate: true, deep: true }
);

// Lifecycle Hook
onMounted(() => {
    initFlowbite();
});

const providerOptions = ref([
    { value: "DOH", label: "DOH" },
    { value: "LGO", label: "LGO" },
    { value: "Donation", label: "Donation" },
]);
const statusOptions = ref([
    { value: "New", label: "New" },
    { value: "Good condition", label: "Good condition" },
    { value: "Fair condition", label: "Fair condition" },
    { value: "Poor condition", label: "Poor Condition" },
    { value: "Condemned", label: "Condemned" },
]);

const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

// Submit Form
function submit() {
    isLoading.value = true;

    form.put(route("equipments.update", props.equipment.id), {
        onSuccess: () => {
            isLoading.value = false;
            unshiftEquipment(filteredEquipments.value, form);
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
                    Edit Equipment
                </h3>
                <button
                    type="button"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
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
                            for="equipment_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Equipment Name
                        </label>
                        <div class="relative">
                            <!-- Input field that allows selection or typing -->
                            <input
                                v-model="form.equipment_name"
                                @focus="showDropdown = true"
                                @input="filterEquipments"
                                placeholder="Select or type an Equipment Name"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            />

                            <!-- Dropdown menu -->
                            <div
                                v-show="
                                    showDropdown && filteredEquipments.length
                                "
                                class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 dark:bg-gray-700 dark:border-gray-500"
                            >
                                <ul class="max-h-48 overflow-y-auto">
                                    <li
                                        v-for="equipment in filteredEquipments"
                                        :key="equipment.id"
                                        @click="
                                            selectEquipment(
                                                equipment.equipment_name
                                            )
                                        "
                                        class="cursor-pointer px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white"
                                    >
                                        {{ equipment.equipment_name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label
                            for="batch_id"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Batch Number
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
                            for="accountable"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Accountable Name
                        </label>
                        <input
                            v-model="form.accountable"
                            type="text"
                            id="accountable"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Type accountable name"
                        />
                    </div>
                    <div class="col-span-1 sm:col-span-1">
                        <label
                            for="status"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Status
                        </label>
                        <select
                            required
                            v-model="form.status"
                            id="status"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="" disabled>Select status</option>
                            <option
                                v-for="option in statusOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </div>
                </div>

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
                        rows="2"
                        id="description"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Description"
                    >
                    </textarea>
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
                        Edit equipment
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
