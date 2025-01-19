<script setup>
import { ref, watch, onMounted, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import Stepper from "@/Components/Stepper.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { debounce } from "lodash";
import approvePagination from "../../../js/Components/ApprovePagination.vue";
import SupplySelectModal from "../../Components/SelectSupplyModal.vue";
import StatusLabel from "../../Components/StatusLabelDispense.vue";
import LoadingModal from "@/Components/LoadingModal.vue";

defineOptions({ layout: DashboardLayout });

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    mw_inventory: Object,
    auth: Object,
});

const isSubmitDisabled = computed(() => {
    return (
        medicineSelected.value.length === 0 &&
        equipmentSelected.value.length === 0 &&
        medSupplySelected.value.length === 0
    );
});

const isLoading = ref(false);
const brgy_id = ref(props.auth.user.barangay_id);
const role = ref(props.auth.user.role_id);
const provider_name = ref(props.auth.user.name);
const roleNames = {
    1: "Midwife",
    2: "RHU Admin",
    3: "Super Admin",
};
const roleName = computed(() => roleNames[role.value]);

const form = useForm({
    user_id: props.auth.user.id,
    barangay_id: brgy_id.value,
    provider_name: provider_name.value,
    position: roleName.value,
    recipients_name: "",
    address: "",
    reason: "",
    age: "",
    birthday: "",
    dispense_date: new Date().toISOString().slice(0, 10),
    medicine: [],
    equipment: [],
    medicalSupply: [],
});

const submit = () => {
    isLoading.value = true;
    form.medicine = medicineSelected.value.map((item) => ({
        id: item.id,
        quantity: item.requestedQuantity,
    }));

    form.equipment = equipmentSelected.value.map((item) => ({
        name: item.name,
        quantity: item.requestedQuantity,
    }));

    form.medicalSupply = medSupplySelected.value.map((item) => ({
        name: item.name,
        quantity: item.requestedQuantity,
    }));

    // Console log the form data
    console.log("Form data before submission:", form);

    // Submit the form data
    form.post(route("dispense.submit"), {
        onFinish: () => {
            medicineSelected.value = [];
            equipmentSelected.value = [];
            medSupplySelected.value = [];

            isLoading.value = false;
        },
    });
};
const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

const calculateAge = (birthday) => {
    if (!birthday) return null;
    const birthDate = new Date(birthday);
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
};

watch(
    () => form.birthday,
    (newBirthday) => {
        form.age = calculateAge(newBirthday);
    }
);

const search = ref("");

watch(
    search,
    debounce(
        (q) => router.get("/dispense", { search: q }, { preserveState: true }),
        500
    )
);

function updateQuery() {
    router.get(
        "/dispense",
        {
            search: search.value,
        },
        { preserveState: true }
    );
}

const medicineSelected = ref([]);
const equipmentSelected = ref([]);
const medSupplySelected = ref([]);

const currentStep = ref(0);

const steps = ref([
    { title: "Personal", subtitle: "Info", isActive: true, isCompleted: false },
    {
        title: "Supply",
        subtitle: "Selection",
        isActive: false,
        isCompleted: false,
    },
    { title: "Confirmation", isActive: false, isCompleted: false },
]);

const nextStep = () => {
    // Manually validate the required fields
    form.clearErrors();

    if (!form.provider_name) {
        form.setError("provider_name", "Provider name is required.");
    }
    if (!form.recipients_name) {
        form.setError("recipients_name", "Recipient's name is required.");
    }
    if (!form.position) {
        form.setError("position", "Position of provider is required.");
    }

    // Check if there are any errors
    if (Object.keys(form.errors).length > 0) {
        return; // Prevent from proceeding to the next step
    }

    if (currentStep.value < steps.value.length - 1) {
        steps.value[currentStep.value].isCompleted = true;
        currentStep.value++;
        updateStepState();
    }
};

const previousStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
        updateStepState();
    }
};

const setStep = (index) => {
    currentStep.value = index;
    updateStepState();
};

const updateStepState = () => {
    steps.value.forEach((step, index) => {
        step.isActive = index === currentStep.value;
        step.isCompleted = index < currentStep.value;
    });
};

const searchMedicines = ref("");

const showModal = ref(false);
const selectedEntry = ref({});

const inventoryChecked = ref([]);
const inventorySelected = ref([]);

const openModal = (type, entry) => {
    let name = entry.name ?? "Unknown Item Name";
    let availableQuantity = entry.quantity ?? 0;
    let reserved = entry.reserved ?? 0;

    // Determine if the entry is already selected based on the type
    let isChecked = inventoryChecked.value.includes(entry.id);
    let isSelected = false;
    let selectedItem = {};

    if (type === "medicine") {
        selectedItem = medicineSelected.value.find((e) => e.id === entry.id);
        isSelected = !!selectedItem;
    } else if (type === "equipment") {
        selectedItem = equipmentSelected.value.find((e) => e.id === entry.id);
        isSelected = !!selectedItem;
    } else if (type === "medical_supply") {
        selectedItem = medSupplySelected.value.find((e) => e.id === entry.id);
        isSelected = !!selectedItem;
    }

    if (isChecked) {
        // If already checked, remove from checked list
        inventoryChecked.value = inventoryChecked.value.filter(
            (id) => id !== entry.id
        );

        // If already selected, remove from selected list
        if (isSelected) {
            if (type === "medicine") {
                medicineSelected.value = medicineSelected.value.filter(
                    (e) => e.id !== entry.id
                );
            } else if (type === "equipment") {
                equipmentSelected.value = equipmentSelected.value.filter(
                    (e) => e.id !== entry.id
                );
            } else if (type === "medical_supply") {
                medSupplySelected.value = medSupplySelected.value.filter(
                    (e) => e.id !== entry.id
                );
            }
            console.log(
                `${type.charAt(0).toUpperCase() + type.slice(1)} unchecked:`,
                entry.id
            );
        }
    } else {
        // If not checked, open the modal for selection
        selectedEntry.value = {
            type,
            id: entry.id,
            name,
            availableQuantity,
            requestedQuantity: 0,
            reserved,
        };
        showModal.value = true;

        // Optional: If it’s already in the selected list, load its current requested quantity
        if (isSelected) {
            selectedEntry.value.requestedQuantity =
                selectedItem.requestedQuantity;
        }
    }
};

const saveEntry = (entry) => {
    let selectedArray;

    if (entry.type === "medicine") {
        selectedArray = medicineSelected;
    } else if (entry.type === "equipment") {
        selectedArray = equipmentSelected;
    } else if (entry.type === "medical_supply") {
        selectedArray = medSupplySelected;
    }

    // Check if the item is already selected
    const existingIndex = selectedArray.value.findIndex(
        (e) => e.id === entry.id
    );

    if (existingIndex !== -1) {
        // If already selected, remove it
        selectedArray.value.splice(existingIndex, 1);
        removeFromSelected(inventorySelected, entry.id);

        // Remove from checked list
        inventoryChecked.value = inventoryChecked.value.filter(
            (id) => id !== entry.id
        );

        console.log("Unselected inventory:", entry.name);
    } else {
        // If not already selected, add it
        selectedArray.value.push(entry);
        addToSelected(inventorySelected, entry);

        // Add to checked list
        if (!inventoryChecked.value.includes(entry.id)) {
            inventoryChecked.value.push(entry.id);
        }

        console.log("Selected inventory:", inventorySelected.value);
    }

    // Close the modal after confirmation
    closeModal();
};

// Utility functions
const addToSelected = (selectedArray, entry) => {
    if (!selectedArray.value.find((e) => e.id === entry.id)) {
        selectedArray.value.push(entry);
    }
};

const removeFromSelected = (selectedArray, id) => {
    selectedArray.value = selectedArray.value.filter((e) => e.id !== id);
};

const closeModal = () => {
    showModal.value = false;
};
</script>

<template>
    <Head title="Dispense" />

    <LoadingModal :isLoading="isLoading" />
    <div class="bg-white is-fullscreen -m-4 p-3">
        <!-- stepper -->
         <div class="px-5">
            <Stepper :steps="steps" @updateStep="setStep" />
         </div>

        <!-- Info -->
        <div v-show="currentStep === 0">
            <h2 class="text-xl font-bold my-3 whitespace-nowrap px-3">
                1. Review Dispensing Details
            </h2>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-3">
                    <!-- Provider's Info Section -->
                    <div
                        class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm"
                    >
                        <h2
                            class="text-lg font-semibold mb-4 whitespace-nowrap"
                        >
                            Authorized Provider's Info
                        </h2>
                        <div
                            class="grid gap-6 mb-6 sm:grid-cols-2 md:grid-cols-4"
                        >
                            <div class="sm:col-span-2">
                                <InputLabel
                                    for="provider_name"
                                    value="Provider Name"
                                />
                                <input
                                    required
                                    v-model="form.provider_name"
                                    type="text"
                                    id="provider_name"
                                    autocomplete="name"
                                    placeholder="Type provider's name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                />
                                <p
                                    v-if="form.errors.provider_name"
                                    class="text-red-600 text-sm"
                                >
                                    {{ form.errors.provider_name }}
                                </p>
                            </div>
                            <div>
                                <InputLabel
                                    for="position"
                                    value="Provider Position"
                                />
                                <input
                                    v-model="form.position"
                                    type="text"
                                    id="position"
                                    placeholder="Type provider's position"
                                    required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                />
                                <p
                                    v-if="form.errors.position"
                                    class="text-red-600 text-sm"
                                >
                                    {{ form.errors.position }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recipient's Info Section -->
                    <div
                        class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm"
                    >
                        <h2
                            class="text-lg font-semibold mb-4 whitespace-nowrap"
                        >
                            Recipient Info
                        </h2>
                        <div
                            class="grid gap-6 mb-6 sm:grid-cols-2 md:grid-cols-4"
                        >
                            <div class="sm:col-span-2">
                                <InputLabel
                                    for="recipient_name"
                                    value="Recipient Name"
                                />
                                <input
                                    required
                                    v-model="form.recipients_name"
                                    type="text"
                                    id="recipient_name"
                                    placeholder="Recipient's name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                />
                                <p
                                    v-if="form.errors.recipients_name"
                                    class="text-red-600 text-sm"
                                >
                                    {{ form.errors.recipients_name }}
                                </p>
                            </div>

                            <div class="col-span-1 hidden md:block">
                                <InputLabel for="birthday" value="Birthday" />
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
                                        v-model="form.birthday"
                                        :formatter="date_format"
                                        id="birthday"
                                        as-single
                                        placeholder="Select birthday (optional)"
                                    />
                                </div>
                            </div>
                            <div class="col-span-1 block md:hidden">
                                <InputLabel for="birthday" value="Birthday" />
                                <input
                                    type="date"
                                    v-model="form.birthday"
                                    id="birthday"
                                    class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5"
                                    placeholder="Select birthday (optional)"
                                />
                            </div>
                            <div>
                                <InputLabel for="age" value="Recipient Age" />
                                <input
                                    v-model="form.age"
                                    type="number"
                                    id="age"
                                    placeholder="Enter age  (optional)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                />
                            </div>

                            <div class="sm:col-span-2">
                                <InputLabel
                                    for="address"
                                    value="Recipient Address"
                                />
                                <textarea
                                    v-model="form.address"
                                    id="address"
                                    rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    placeholder="Type Address  (optional)"
                                ></textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <InputLabel
                                    for="reason"
                                    value="Reason for Dispense"
                                />
                                <textarea
                                    v-model="form.reason"
                                    id="reason"
                                    rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    placeholder="Add any note/info  (optional)"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Button Section -->
            <div class="my-2 flex justify-end">
                <button
                    type="submit"
                    @click="nextStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center"
                >
                    Next
                </button>
            </div>
        </div>

        <div v-show="currentStep === 1">
            <h2 class="text-xl font-bold my-3 px-3">
                2. Select the supplies you want to dispense:
            </h2>

            <div class="grid grid-cols-1 gap-3">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-3">
                        <!-- Display Selected -->
                        <div

                            class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm"
                        >
                            <h2 class="text-lg font-medium mb-2">
                                <span class="font-bold">Selected</span> supplies
                            </h2>
                            <div
                                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 p-2"
                            >
                                <!-- Medicine Card -->
                                <div
                                    v-if="
                                        medicineSelected &&
                                        medicineSelected.length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 p-3 bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                >
                                    <div
                                        class="flex flex-col flex-grow items-left text-left leading-normal"
                                    >
                                        <div class="text-left">
                                            <p
                                                class="mb-2 text-lg font-bold tracking-tight text-green-500 dark:text-white"
                                            >
                                                Medicine
                                            </p>
                                            <div
                                                class="rounded-lg w-full overflow-clip bg-white flex justify-center"
                                            >
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th
                                                                class="px-2 w-1/4 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th
                                                                class="px-2 w-3/4 text-left"
                                                            >
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="item in medicineSelected"
                                                        :key="item.id"
                                                    >
                                                        <tr class="bg-white">
                                                            <td
                                                                class="px-2 w-1/4 text-center"
                                                            >
                                                                {{
                                                                    item.requestedQuantity
                                                                }}
                                                            </td>
                                                            <td
                                                                class="px-2 w-3/4 text-left"
                                                            >
                                                                {{ item.name }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Equipment Card -->
                                <div
                                    v-if="
                                        equipmentSelected &&
                                        equipmentSelected.length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 p-3 bg-blue-100 border border-blue-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                >
                                    <div
                                        class="flex flex-col flex-grow items-left text-left leading-normal"
                                    >
                                        <div class="text-left">
                                            <p
                                                class="mb-2 text-lg font-bold tracking-tight text-blue-600 dark:text-white"
                                            >
                                                Equipment
                                            </p>
                                            <div
                                                class="rounded-lg w-full overflow-clip bg-white flex justify-center"
                                            >
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th
                                                                class="px-2 w-1/4 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th
                                                                class="px-2 w-3/4 text-left"
                                                            >
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="item in equipmentSelected"
                                                        :key="item.id"
                                                    >
                                                        <tr class="bg-white">
                                                            <td
                                                                class="px-2 w-1/4 text-center"
                                                            >
                                                                {{
                                                                    item.requestedQuantity
                                                                }}
                                                            </td>
                                                            <td
                                                                class="px-2 w-3/4 text-left"
                                                            >
                                                                {{ item.name }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Medical Supply Card -->
                                <div
                                    v-if="
                                        medSupplySelected &&
                                        medSupplySelected.length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 p-3 bg-violet-100 border border-violet-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                                >
                                    <div
                                        class="flex flex-col flex-grow items-left text-left leading-normal"
                                    >
                                        <div class="text-left">
                                            <p
                                                class="mb-2 text-lg font-bold tracking-tight text-violet-500 dark:text-white"
                                            >
                                                Medical Supply
                                            </p>
                                            <div
                                                class="rounded-lg w-full overflow-clip bg-white flex justify-center"
                                            >
                                                <table class="w-full">
                                                    <thead>
                                                        <tr class="bg-gray-100">
                                                            <th
                                                                class="px-2 w-1/4 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th
                                                                class="px-2 w-3/4 text-left"
                                                            >
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="item in medSupplySelected"
                                                        :key="item.id"
                                                    >
                                                        <tr class="bg-white">
                                                            <td
                                                                class="px-2 w-1/4 text-center"
                                                            >
                                                                {{
                                                                    item.requestedQuantity
                                                                }}
                                                            </td>
                                                            <td
                                                                class="px-2 w-3/4 text-left"
                                                            >
                                                                {{ item.name }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Display Table -->
                <div
                    class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                >
                    <h2 class="text-lg font-medium mb-2">Select Supplies</h2>
                    <div class="w-full">
                        <div
                            class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
                        >
                            <!-- Search and filter -->
                            <div
                                class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
                            >
                                <div class="w-full md:w-1/2">
                                    <form class="flex items-center">
                                        <label class="sr-only">Search</label>
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
                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                placeholder="Search"
                                                v-model="search"
                                            />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Table -->
                            <div class="relative overflow-x-auto sm:rounded-lg">
                                <div
                                    v-for="inventory in mw_inventory.data"
                                    :key="inventory.id"
                                    class="p-4 bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer flex flex-row justify-start"
                                    @click="
                                        openModal(inventory.type, inventory)
                                    "
                                >
                                    <div
                                        class="flex items-center md:w-10 w-10 mx-4"
                                    >
                                        <img
                                            v-if="inventory.type === 'medicine'"
                                            src="/storage/icons/midwife/pill.svg"
                                        />
                                        <img
                                            v-if="
                                                inventory.type === 'equipment'
                                            "
                                            src="/storage/icons/midwife/microscope.svg"
                                        />
                                        <img
                                            v-if="
                                                inventory.type ===
                                                'medical_supply'
                                            "
                                            src="/storage/icons/midwife/bandaid.svg"
                                        />
                                    </div>
                                    <div class="flex flex-col w-full mt-2">
                                        <div
                                            class="flex flex-row justify-between"
                                        >
                                            <div>
                                                <p class="text-l font-semibold">
                                                    {{ inventory.name }}
                                                    <span
                                                        v-if="inventory.brand"
                                                        class="text-base font-normal"
                                                        >({{
                                                            inventory.brand
                                                        }})</span
                                                    >
                                                </p>
                                            </div>
                                            <input
                                                v-if="
                                                    inventoryChecked.includes(
                                                        inventory.id
                                                    )
                                                "
                                                @click.stop
                                                :value="inventory.id"
                                                v-model="inventoryChecked"
                                                :id="`checkbox-table-search-${inventory.id}`"
                                                type="checkbox"
                                                :disabled="true"
                                                class="cursor-not-allowed w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                            />
                                        </div>
                                        <div>
                                            Quantity: {{ inventory.quantity }}
                                        </div>
                                        <div>
                                            <StatusLabel
                                                :all="inventory.status"
                                                :expiration_date="
                                                    inventory.expiration_date
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <approvePagination
                                :paginator="mw_inventory"
                                :key="currentStep"
                            />
                        </div>
                        <SupplySelectModal
                            v-if="showModal"
                            :showModal="showModal"
                            :entry="selectedEntry"
                            @confirm="saveEntry"
                            @close="closeModal"
                        />
                    </div>
                </div>
            </div>

            <div
                class="col-span-1 my-2 flex flex-col sm:flex-row gap-4 justify-end"
            >
                <button
                    type="button"
                    @click="previousStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Back
                </button>
                <button
                    type="button"
                    @click="nextStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Next
                </button>
            </div>
        </div>

        <!-- Confirmation -->
        <div v-show="currentStep === 2">
            <h2 class="text-xl font-bold my-3 px-3">2. Confirmation:</h2>
            <div class="grid xl:grid-cols-2 grid-cols-1 gap-4">
                <!-- Supplies to be delivered Section -->
                <div
                    class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm"
                >
                    <h2 class="text-lg font-bold mb-2 whitespace-nowrap">
                        Supplies to be dispensed:
                    </h2>
                    <div class="grid grid-cols-1 gap-5 p-2">
                        <!-- Medicine Card -->
                        <div
                            v-if="
                                medicineSelected && medicineSelected.length > 0
                            "
                            class="flex flex-col md:flex-row justify-between gap-5 p-3 bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="flex-grow leading-normal">
                                <p
                                    class="mb-2 text-lg font-bold tracking-tight text-green-500 dark:text-white"
                                >
                                    Medicine
                                </p>
                                <div
                                    class="rounded-lg w-full overflow-clip bg-white flex justify-center"
                                >
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th
                                                    class="px-2 w-1/4 text-center"
                                                >
                                                    Total
                                                </th>
                                                <th
                                                    class="px-2 w-3/4 text-left"
                                                >
                                                    Name
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            v-for="item in medicineSelected"
                                            :key="item.id"
                                        >
                                            <tr class="bg-white">
                                                <td
                                                    class="px-2 w-1/4 text-center"
                                                >
                                                    {{ item.requestedQuantity }}
                                                </td>
                                                <td
                                                    class="px-2 w-3/4 text-left"
                                                >
                                                    {{ item.name }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Card -->
                        <div
                            v-if="
                                equipmentSelected &&
                                equipmentSelected.length > 0
                            "
                            class="flex flex-col md:flex-row justify-between gap-5 p-3 bg-blue-100 border border-blue-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="flex-grow leading-normal">
                                <p
                                    class="mb-2 text-lg font-bold tracking-tight text-blue-600 dark:text-white"
                                >
                                    Equipment
                                </p>
                                <div
                                    class="rounded-lg w-full overflow-clip bg-white flex justify-center"
                                >
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th
                                                    class="px-2 w-1/4 text-center"
                                                >
                                                    Total
                                                </th>
                                                <th
                                                    class="px-2 w-3/4 text-left"
                                                >
                                                    Name
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            v-for="item in equipmentSelected"
                                            :key="item.id"
                                        >
                                            <tr class="bg-white">
                                                <td
                                                    class="px-2 w-1/4 text-center"
                                                >
                                                    {{ item.requestedQuantity }}
                                                </td>
                                                <td
                                                    class="px-2 w-3/4 text-left"
                                                >
                                                    {{ item.name }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Supply Card -->
                        <div
                            v-if="
                                medSupplySelected &&
                                medSupplySelected.length > 0
                            "
                            class="flex flex-col md:flex-row justify-between gap-5 p-3 bg-violet-100 border border-violet-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="flex-grow leading-normal">
                                <p
                                    class="mb-2 text-lg font-bold tracking-tight text-violet-500 dark:text-white"
                                >
                                    Medical Supply
                                </p>
                                <div
                                    class="rounded-lg w-full overflow-clip bg-white flex justify-center"
                                >
                                    <table class="w-full">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th
                                                    class="px-2 w-1/4 text-center"
                                                >
                                                    Total
                                                </th>
                                                <th
                                                    class="px-2 w-3/4 text-left"
                                                >
                                                    Name
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            v-for="item in medSupplySelected"
                                            :key="item.id"
                                        >
                                            <tr class="bg-white">
                                                <td
                                                    class="px-2 w-1/4 text-center"
                                                >
                                                    {{ item.requestedQuantity }}
                                                </td>
                                                <td
                                                    class="px-2 w-3/4 text-left"
                                                >
                                                    {{ item.name }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dispensing Details Section -->
                <div
                    class="p-5 bg-white rounded-lg border border-gray-200 shadow-sm"
                >
                    <h2 class="text-lg font-bold mb-2 whitespace-nowrap">
                        Dispensing Details:
                    </h2>
                    <h2 class="text-md font-bold mx-1 border-b border-gray-300">
                        Authorized Provider's Info:
                    </h2>
                    <div class="p-2 px-3 space-y-1">
                        <dl class="flex items-center justify-between gap-4">
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Provider Name
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-700 dark:text-white"
                            >
                                {{ form.provider_name }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-4">
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Position:
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-900 dark:text-white"
                            >
                                {{ form.position }}
                            </dd>
                        </dl>
                        <dl class="flex items-center justify-between gap-4">
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Date
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-900 dark:text-white"
                            >
                                {{ form.dispense_date }}
                            </dd>
                        </dl>
                    </div>

                    <h2
                        class="text-md font-bold px-1 mt-2 border-b border-gray-300"
                    >
                        Recipient Info:
                    </h2>
                    <div class="p-2 px-3 space-y-1">
                        <dl class="flex items-center justify-between gap-4">
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Fullname
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-700 dark:text-white"
                            >
                                {{ form.recipients_name }}
                            </dd>
                        </dl>
                        <dl  v-if="form.birthday" class="flex items-center justify-between gap-4">
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Birthday
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-900 dark:text-white"
                            >
                                {{ form.birthday ?? " " }}
                            </dd>
                        </dl>
                        <dl
                            v-if="form.age"
                            class="flex items-center justify-between gap-4"
                        >
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Age
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-900 dark:text-white"
                            >
                                {{ form.age }}
                            </dd>
                        </dl>
                        <dl
                            v-if="form.address"
                            class="flex items-center justify-between gap-4"
                        >
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Address
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-900 dark:text-white"
                            >
                                {{ form.address }}
                            </dd>
                        </dl>
                        <dl
                            v-if="form.reason"
                            class="flex items-center justify-between gap-4"
                        >
                            <dt
                                class="text-md font-medium text-gray-900 dark:text-white"
                            >
                                Reason
                            </dt>
                            <dd
                                class="text-base font-thin text-gray-900 dark:text-white"
                            >
                                {{ form.reason }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Buttons Section -->
            <div class="my-2 flex flex-col sm:flex-row gap-4 justify-end">
                <button
                    type="button"
                    @click="previousStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Back
                </button>
                <button
                    type="button"
                    @click="submit"
                    :disabled="isSubmitDisabled || form.processing"
                    :class="{
                        'opacity-50 cursor-not-allowed':
                            isSubmitDisabled || form.processing,
                    }"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
</template>
