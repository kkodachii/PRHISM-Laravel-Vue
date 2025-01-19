<script setup>
import { ref, onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    batches: Object,
    auth: Object,
    equipNames: Array,
});

const date_now = new Date().toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    timeZone: "Asia/Manila",
});

const userID = ref(props.auth.user.id);
const dateAcquired = ref(date_now);
const barangay_id = ref(props.auth.user.barangay_id);

const equip = ref([
    {
        equipment_name: "",
        description: "",
        quantity: "",
        provider: "",
        status: "New",
        accountable: "",
        showDropdown: false,
        filteredEquipments: [],
    },
]);

const debounceTimeout = ref(null); // Define debounceTimeout here

const form = useForm({
    user_id: userID.value,
    batch_id: "New",
    date_acquired: dateAcquired.value,
    equip: equip.value,
});

const addequip = () => {
    equip.value.push({
        equipment_name: "",
        description: "",
        quantity: "",
        provider: "",
        status: "",
        accountable: "",
        showDropdown: false,
        filteredEquipments: [],
    });
};

const removeequip = (index) => {
    equip.value.splice(index, 1);
};

// Create a Set for fast lookup
const equipmentSet = new Set(
    props.equipNames.map((e) => e.equipment_name.toLowerCase())
);

// Debounce function to limit the rate of input handling
const debounce = (func, delay) => {
    return function (...args) {
        clearTimeout(debounceTimeout.value);
        debounceTimeout.value = setTimeout(() => {
            func(...args);
        }, delay);
    };
};

// Function to filter equipment based on input
const filterEquipments = debounce((index) => {
    const query = equip.value[index].equipment_name.toLowerCase();

    // Early exit if the input is empty
    if (!query) {
        equip.value[index].filteredEquipments = [];
        return;
    }

    equip.value[index].filteredEquipments = props.equipNames.filter(
        (equipment) => equipment.equipment_name.toLowerCase().includes(query)
    );

    // Open or close dropdown based on matches
    equip.value[index].showDropdown =
        equip.value[index].filteredEquipments.length > 0;
}, 800); // Adjust delay as needed

// Function to select an equipment name from the dropdown
const selectEquipment = (index, equipmentName) => {
    equip.value[index].equipment_name = equipmentName;
    equip.value[index].showDropdown = false; // Close the dropdown after selection
};

// Close dropdown when clicking outside
document.addEventListener("click", (e) => {
    const isClickInside = e.target.closest(".relative");
    if (!isClickInside) {
        equip.value.forEach((item) => (item.showDropdown = false));
    }
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
    { value: "Poor condition", label: "Poor condition" },
]);

const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

onMounted(() => {
    initFlowbite();
});

const submitForm = () => {
    // Map over equip to create the structure needed for the form submission
    form.equip = equip.value.map((item) => ({
        user_id: userID.value,
        barangay_id: barangay_id.value,
        batch_id: form.batch_id,
        equipment_name: item.equipment_name || "", // Use item.equipment_name directly
        description: item.description,
        quantity: item.quantity,
        provider: item.provider,
        status: item.status,
        date_acquired: dateAcquired.value,
        accountable: item.accountable,
    }));

    form.post(route("batch-equipment.store"), {
        onSuccess: () => {},
        onError: () => {},
    });
};
</script>

<template>
    <Head title="Batch Equipment" />
    <div
        class="bg-white dark:bg-gray-800 relative overflow-x-auto lg:min-h-100 shadow-md sm:rounded-lg p-6"
    >
        <div class="mb-1">
            <h3
                class="text-lg font-semibold text-gray-900 dark:text-white mb-0"
            >
                Add New Equipment by Batch
            </h3>
        </div>

        <form @submit.prevent="submitForm">
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 p-2 gap-4 mb-2"
            >
                <div class="col-span-1">
                    <label
                        for="batch_id"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Batch no.
                    </label>
                    <div class="relative">
                        <select
                            v-model="form.batch_id"
                            id="batch_id"
                            required=""
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="New" selected>
                                Create new Batch
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
                            v-model="dateAcquired"
                            :formatter="date_format"
                            id="date_acquired"
                            as-single
                            placeholder="Select date"
                            required
                        />
                    </div>
                </div>
            </div>

            <div
                v-for="(item, index) in equip"
                :key="index"
                :class="{
                    'border-t border-gray-200 dark:border-gray-600 pt-6':
                        index !== 0,
                }"
                class="grid grid-cols-2 gap-6 mb-8 lg:grid-cols-4 p-2"
            >
                <div
                    class="flex flex-row justify-between col-span-2 lg:col-span-4"
                >
                    <span class="text-gray-700 font-semibold dark:text-gray-400"
                        >Equipment no. {{ index + 1 }}</span
                    >
                    <button
                        @click.prevent="removeequip(index)"
                        class="text-red-500 hover:text-red-700 focus:outline-none ms-4"
                    >
                        <svg
                            class="w-6 h-6 text-gray-800 dark:text-white"
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
                                d="M6 18 17.94 6M18 18 6.06 6"
                            />
                        </svg>
                    </button>
                </div>

                <div class="col-span-2 lg:col-span-2">
                    <label
                        for="equipment_name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Equipment Name</label
                    >
                    <div class="relative">
                        <input
                            v-model="item.equipment_name"
                            @focus="item.showDropdown = true"
                            @input="filterEquipments(index)"
                            placeholder="Select or type an Equipment Name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />

                        <!-- Dropdown Menu for Equipment Suggestions -->
                        <div
                            v-show="
                                item.showDropdown &&
                                item.filteredEquipments.length
                            "
                            class="absolute mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg z-10 dark:bg-gray-700 dark:border-gray-500"
                        >
                            <ul class="max-h-48 overflow-y-auto">
                                <li
                                    v-for="filteredEquipment in item.filteredEquipments"
                                    :key="filteredEquipment.id"
                                    @click="
                                        selectEquipment(
                                            index,
                                            filteredEquipment.equipment_name
                                        )
                                    "
                                    class="cursor-pointer px-4 py-2 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white"
                                >
                                    {{ filteredEquipment.equipment_name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-span-1">
                    <label
                        for="quantity"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Quantity</label
                    >
                    <input
                        type="number"
                        v-model="item.quantity"
                        placeholder="Input quantity"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    />
                </div>
                <div class="col-span-1">
                    <label
                        for="provider"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Provider</label
                    >
                    <select
                        v-model="item.provider"
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
                <div class="col-span-2 lg:col-span-2">
                    <label
                        for="accountable"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Accountable Name</label
                    >
                    <input
                        type="text"
                        v-model="item.accountable"
                        placeholder="Type accountable name"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    />
                </div>

                <div class="col-span-1">
                    <label
                        for="status"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Status
                    </label>
                    <select
                        required
                        v-model="item.status"
                        id="status"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    >
                        <option value="" disabled>Select Status</option>
                        <option
                            v-for="option in statusOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>
                <div class="col-span-2 lg:col-span-4">
                    <label
                        for="description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400"
                        >Description</label
                    >
                    <div class="flex items-center">
                        <textarea
                            row="3"
                            type="text"
                            v-model="item.description"
                            placeholder="Type description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 mt-1 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col justify-between sm:flex-row gap-4 p-2 mt-6"
            >
                <button
                    type="button"
                    @click="addequip"
                    class="bg-gray-200 py-2 px-4 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Add another
                </button>
                <button
                    type="submit"
                    class="inline-flex justify-center items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-white dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    <svg
                        class="w-5 h-5 me-2"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Add Equipment
                </button>
            </div>
        </form>
    </div>
</template>
