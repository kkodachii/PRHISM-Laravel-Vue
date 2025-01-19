<script setup>
import { ref, watch, onMounted } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { initFlowbite } from "flowbite";
import Stepper from "@/Components/Stepper.vue";
import InputLabel from "@/Components/InputLabel.vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { debounce } from "lodash";
import ApprovePagination from "@/Components/ApprovePagination.vue";
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from "@headlessui/vue";
import SupplySelectModal from "../../Components/SelectSupplyModal.vue";
import StatusLabel from "../../Components/StatusLabel.vue";
import deliveryFilterButton from "../../Components/deliveryFilterButton.vue";
import ExpiryCountdown from "../../Components/ExpiryCountdown.vue";
import { parsePhoneNumber } from "libphonenumber-js";
import LoadingModal from "@/Components/LoadingModal.vue";

defineOptions({ layout: DashboardLayout });

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    pendingRequest: Object,
    medicines: Object,
    equipments: Object,
    med_supplies: Object,
    auth: Object,
});

const isLoading = ref(false);

const medicineChecked = ref([]); // For checkbox states
const equipmentChecked = ref([]); // For checkbox states
const medSupplyChecked = ref([]); // For checkbox states

const medicineSelected = ref([]); // For selected entry information
const equipmentSelected = ref([]); // For selected entry information
const medSupplySelected = ref([]); // For selected entry information

const approver_name = ref(props.auth.user.name);
const approver_rhu = ref(props.auth.user.rhu_id);
const destination = ref(props.pendingRequest?.barangay_id ?? "");
const destinationBarangay = ref(props.pendingRequest?.barangay.barangay_name);
const approveMode = ref("");
const date_option = ref("Anyday");
const date_format = ref({
    date: "DD MMM YYYY",
    month: "MMM",
});

const requestform = useForm({
    id: props.pendingRequest?.id ?? "",
    barangay_name: props.pendingRequest?.barangay?.barangay_name ?? "",
    requested_at: props.pendingRequest?.requested_at ?? "",
    requester_name: props.pendingRequest?.requester_user.name ?? "",
    requester_rhu: props.pendingRequest?.requester_user.rhu_id ?? "",
});

const form = useForm({
    id: props.pendingRequest?.id ?? "",
    status: "",
    date_approved: "",
    approver_name: approver_name ?? "",
    approver_rhu: approver_rhu ?? "",
    approver_position: "",
    for_pickup: "",
    pickup_date: "",
    approver_position: "",
    delivery_name: "",
    delivery_number: "",
    delivery_address: "",
    destination_id: destination.value ?? "",
    date_delivery: "",
    delivery_medicines: medicineSelected,
    delivery_equipments: equipmentSelected,
    delivery_med_supplies: medSupplySelected,
});

watch(
    () => approveMode.value,
    (newMode) => {
        if (newMode === "Pickup") {
            form.delivery_name = "";
            form.delivery_number = "";
            form.delivery_address = "";
        } else if (newMode === "Delivery") {
            form.for_pickup = "";
            form.pickup_date = "";
        }
    }
);

const formatPhoneNumber = (number) => {
    // Remove all non-numeric characters
    let numericNumber = number.replace(/\D/g, "");

    // Auto-add '0' if the first digit is '9'
    if (numericNumber.startsWith("9")) {
        numericNumber = "0" + numericNumber;
    }

    // Limit to 11 digits
    numericNumber = numericNumber.slice(0, 11);

    // Format using libphonenumber-js
    const phoneNumber = parsePhoneNumber(numericNumber, "PH");
    return phoneNumber ? phoneNumber.formatNational() : numericNumber;
};

watch(
    () => form.delivery_number,
    (newNumber) => {
            form.delivery_number = formatPhoneNumber(newNumber);
    }
);

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
    form.clearErrors();

    if (approveMode.value == "Delivery") {
        if (!form.delivery_name) {
            form.setError(
                "delivery_name",
                "Delivery personnel name is required."
            );
        }
        if (!form.delivery_number) {
            form.setError(
                "delivery_number",
                "Delivery personnel's contact is required."
            );
        }
    }

    if (Object.keys(form.errors).length > 0) {
        return;
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
const searchEquipment = ref("");
const searchSupplies = ref("");
const pendingRequestId = props.pendingRequest?.id ?? "";

// Watch for changes in the medicines search query
watch(
    searchMedicines,
    debounce((query) => {
        router.get(
            `/approve-request/${pendingRequestId}`,
            { searchMedicine: query },
            { preserveState: true, preserveScroll: true }
        );
    }, 500)
);

// Watch for changes in the equipment search query
watch(
    searchEquipment,
    debounce((query) => {
        router.get(
            `/approve-request/${pendingRequestId}`,
            { searchEquipment: query },
            { preserveState: true, preserveScroll: true }
        );
    }, 500)
);

// Watch for changes in the medical supplies search query
watch(
    searchSupplies,
    debounce((query) => {
        router.get(
            `/approve-request/${pendingRequestId}`,
            { searchSupply: query },
            { preserveState: true, preserveScroll: true }
        );
    }, 500)
);

// Checkbox functions

const showModal = ref(false);
const selectedEntry = ref({});

const openModal = (type, entry) => {
    let name = "";
    let availableQuantity = entry.quantity ?? 0;
    let reserved = entry.reserved ?? 0;

    // Determine the name based on the type of entry
    if (type === "medicine") {
        name = entry.generic_name?.generic_name ?? "Unknown Medicine Name";
    } else if (type === "equipment") {
        name = entry.equipment_name ?? "Unknown Equipment Name";
    } else if (type === "med_supply") {
        name = entry.med_sup_name ?? "Unknown Medical Supply Name";
    }

    // Check if the entry is already selected in the checked list
    if (type === "medicine") {
        if (medicineChecked.value.includes(entry.id)) {
            // If already checked, remove from checked list and selected list
            medicineChecked.value = medicineChecked.value.filter(
                (id) => id !== entry.id
            );
            medicineSelected.value = medicineSelected.value.filter(
                (e) => e.id !== entry.id
            );
            console.log("Medicine unchecked:", entry.id);
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
        }
    } else if (type === "equipment") {
        if (equipmentChecked.value.includes(entry.id)) {
            // If already checked, remove from checked list and selected list
            equipmentChecked.value = equipmentChecked.value.filter(
                (id) => id !== entry.id
            );
            equipmentSelected.value = equipmentSelected.value.filter(
                (e) => e.id !== entry.id
            );
            console.log("Equipment unchecked:", entry.id);
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
        }
    } else if (type === "med_supply") {
        if (medSupplyChecked.value.includes(entry.id)) {
            // If already checked, remove from checked list and selected list
            medSupplyChecked.value = medSupplyChecked.value.filter(
                (id) => id !== entry.id
            );
            medSupplySelected.value = medSupplySelected.value.filter(
                (e) => e.id !== entry.id
            );
            console.log("Medical supply unchecked:", entry.id);
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
        }
    }
};

const saveEntry = (entry) => {
    // Log entry after it is confirmed
    console.log("Confirmed entry:", entry);

    // Add the entry to the appropriate list
    if (entry.type === "medicine") {
        addToSelected(medicineSelected, entry);
    } else if (entry.type === "equipment") {
        addToSelected(equipmentSelected, entry);
    } else if (entry.type === "med_supply") {
        addToSelected(medSupplySelected, entry);
    }

    // Update checkbox state
    if (
        !medicineChecked.value.includes(entry.id) &&
        entry.type === "medicine"
    ) {
        medicineChecked.value.push(entry.id);
    }
    if (
        !equipmentChecked.value.includes(entry.id) &&
        entry.type === "equipment"
    ) {
        equipmentChecked.value.push(entry.id);
    }
    if (
        !medSupplyChecked.value.includes(entry.id) &&
        entry.type === "med_supply"
    ) {
        medSupplyChecked.value.push(entry.id);
    }

    console.log("Selected lists:", {
        medicines: medicineSelected.value,
        equipment: equipmentSelected.value,
        med_supplies: medSupplySelected.value,
    });

    // Close the modal after confirmation
    closeModal();
};

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

const sortField = ref("id"); // Default sort field
const sortDirection = ref("asc"); // Default sort direction

// Status filter
const selectedStatus = ref({
    medicine: [
        { id: "onStock", name: "On stock", selected: false },
        { id: "lowOnStock", name: "Low on stock", selected: false },
        { id: "outOfStock", name: "Out of stock", selected: false },
        { id: "expiring", name: "Expiring", selected: false },
        { id: "expired", name: "Expired", selected: false },
    ],
    equipment: [
        { id: "new", name: "New", selected: false },
        { id: "goodCondition", name: "Good Condition", selected: false },
        { id: "fairCondition", name: "Fair Condition", selected: false },
        { id: "poorCondition", name: "Poor Condition", selected: false },
        { id: "condemned", name: "Condemned", selected: false },
    ],
    supply: [
        { id: "onStock", name: "On stock", selected: false },
        { id: "lowOnStock", name: "Low on stock", selected: false },
        { id: "outOfStock", name: "Out of stock", selected: false },
    ],
});

// Function to update query parameters based on search, filters, and sorting
function updateQuery() {
    const inventoryTypes = ["medicine", "equipment", "supply"];
    const queryParams = {};

    inventoryTypes.forEach((type) => {
        const statusFilters = selectedStatus.value[type]
            .filter((status) => status.selected)
            .map((status) => status.id)
            .join(",");

        if (statusFilters) {
            queryParams[`status_${type}`] = statusFilters;
        }
    });

    // Conditionally add parameters only if they are not empty
    if (searchMedicines.value)
        queryParams.searchMedicine = searchMedicines.value;
    if (searchEquipment.value)
        queryParams.searchEquipment = searchEquipment.value;
    if (searchSupplies.value) queryParams.searchSupply = searchSupplies.value;
    if (sortField.value) queryParams.sort = sortField.value;
    if (sortDirection.value) queryParams.direction = sortDirection.value;

    router.get(`/approve-request/${pendingRequestId}`, queryParams, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Watchers for search inputs
watch(
    searchMedicines,
    debounce((query) => updateQuery(), 500)
);
watch(
    searchEquipment,
    debounce((query) => updateQuery(), 500)
);
watch(
    searchSupplies,
    debounce((query) => updateQuery(), 500)
);

// Watcher for status filter changes
watch(
    selectedStatus,
    () => {
        updateQuery(); // Trigger update when status changes
    },
    { deep: true }
);

// Sorting logic
function sort(field) {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    updateQuery();
}

const submit = () => {
    isLoading.value = true;

    const formattedMedicine = medicineSelected.value.map((item) => ({
        id: item.id, // Ensure this is correctly set
        requestedQuantity: item.requestedQuantity, // Ensure this is correctly set
    }));

    const formattedEquipment = equipmentSelected.value
        .filter((item) => item.id && item.requestedQuantity) // Filter out entries with null or empty values
        .map((item) => ({
            id: item.id, // Ensure this is correctly set
            requestedQuantity: item.requestedQuantity, // Ensure this is correctly set
        }));

    const formattedMedicalSupply = medSupplySelected.value
        .filter((item) => item.id && item.requestedQuantity) // Filter out entries with null or empty values
        .map((item) => ({
            id: item.id, // Ensure this is correctly set
            requestedQuantity: item.requestedQuantity, // Ensure this is correctly set
        }));

    useForm({
        id: form.id,
        status: form.status,
        date_approved: new Date().toISOString(), // Set the current date for approval
        approver_name: form.approver_name,
        approver_rhu: form.approver_rhu,
        approver_position: form.approver_position,
        for_pickup: form.for_pickup,
        pickup_date: form.pickup_date,
        delivery_name: form.delivery_name,
        delivery_number: form.delivery_number,
        delivery_address: form.delivery_address,
        destination_id: form.destination_id,
        date_delivery: new Date().toISOString(), // Set the current date for delivery
        delivery_medicines: formattedMedicine,
        delivery_equipments: formattedEquipment,
        delivery_med_supplies: formattedMedicalSupply,
    }).put(route("requests.update", props.pendingRequest.id), {
        onSuccess: () => {
            isLoading.value = false;
            form.reset();
        },
        onError: () => {
            isLoading.value = false;
        },
    });
};
</script>

<template>
    <Head title="Approve Request" />
    <LoadingModal :isLoading="isLoading" />
    <div class="bg-white is-fullscreen -m-4 p-3 px-10">
        <!-- stepper -->
        <Stepper :steps="steps" @updateStep="setStep" />

        <!-- Info -->
        <div v-show="currentStep === 0">
            <h2 class="text-xl font-bold my-3">1. Review approval details</h2>
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-3">
                    <div
                        class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                    >
                        <h2 class="text-lg font-semibold mb-2">
                            Requester details
                        </h2>
                        <div class="grid grid-cols-2 gap-2 xl:grid-cols-3">
                            <span class="font-semibold"
                                >Name:
                                <span class="ml-1 font-thin underline">{{
                                    requestform.requester_name
                                }}</span></span
                            >
                            <div>
                                <span class="font-semibold"
                                    >Requested from:
                                    <span class="ml-1 font-thin underline">{{
                                        requestform.barangay_name
                                    }}</span></span
                                >
                            </div>
                            <span class="font-medium"
                                >Date requested:<span
                                    class="ml-1 font-thin underline"
                                    >{{ requestform.requested_at }}</span
                                >
                            </span>
                        </div>
                    </div>

                    <div
                        class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                    >
                        <h2 class="text-lg font-semibold mb-4">
                            Approval's info
                        </h2>
                        <div class="grid gap-6 mb-6 md:grid-cols-4">
                            <div class="col-span-2">
                                <InputLabel for="approver_name" value="Name" />
                                <input
                                    required
                                    v-model="form.approver_name"
                                    type="text"
                                    id="approver_name"
                                    autocomplete="name"
                                    placeholder="Type your name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <InputLabel
                                    for="approver_position"
                                    value="Position"
                                />
                                <input
                                    v-model="form.approver_position"
                                    type="text"
                                    id="approver_position"
                                    placeholder="Approver's position"
                                    required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                            </div>
                            <div>
                                <InputLabel for="approver_rhu" value="RHU" />
                                <input
                                    disabled
                                    v-model="form.approver_rhu"
                                    type="text"
                                    id="approver_rhu"
                                    placeholder="Approved at"
                                    required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                            </div>
                        </div>
                    </div>
                    <div
                        class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                    >
                        <h2 class="text-lg font-semibold mb-4">
                            Select Mode of Service
                        </h2>
                        <div
                            class="flex flex-col md:flex-row items-center gap-4 p-4 border rounded-lg bg-gray-50"
                        >
                            <label
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="radio"
                                    v-model="approveMode"
                                    value="Pickup"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                />
                                <span class="text-gray-700">Pickup</span>
                            </label>
                            <label
                                class="flex items-center gap-2 cursor-pointer"
                            >
                                <input
                                    type="radio"
                                    v-model="approveMode"
                                    value="Delivery"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                />
                                <span class="text-gray-700">Delivery</span>
                            </label>
                        </div>
                    </div>
                    <div
                        v-if="approveMode == 'Pickup'"
                        class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                    >
                        <h2 class="text-lg font-semibold mb-4">Pickup info</h2>
                        <div class="grid gap-6 mb-6 md:grid-cols-4">
                            <div class="col-span-1">
                                <InputLabel
                                    for="date_option"
                                    value="Pickup Date Schedule"
                                />
                                <select
                                    v-model="date_option"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                >
                                    <option value="Anyday">
                                        Any Day available
                                    </option>
                                    <option value="Range">Select Date</option>
                                </select>
                            </div>
                            <div
                                v-if="date_option == 'Range'"
                                class="col-span-1"
                            >
                                <InputLabel
                                    for="pickup_date"
                                    value="Select Date"
                                />
                                <vue-tailwind-datepicker
                                    v-model="form.pickup_date"
                                    as-single
                                    :formatter="date_format"
                                    :shortcuts="false"
                                    id="pickup_date"
                                    placeholder="Select date Range"
                                />
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="approveMode == 'Delivery'"
                        class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                    >
                        <h2 class="text-lg font-semibold mb-4">
                            Delivery info
                        </h2>
                        <div class="grid gap-6 mb-6 md:grid-cols-4">
                            <div class="col-span-2">
                                <InputLabel
                                    for="delivery_name"
                                    value="Delivery Personnel"
                                />
                                <input
                                    required
                                    v-model="form.delivery_name"
                                    type="text"
                                    id="delivery_name"
                                    placeholder="Driver's name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                                <p
                                    v-if="form.errors.delivery_name"
                                    class="text-red-600 text-sm"
                                >
                                    {{ form.errors.delivery_name }}
                                </p>
                            </div>
                            <div>
                                <InputLabel
                                    for="delivery_number"
                                    value="Phone number"
                                />
                                <input
                                    v-model="form.delivery_number"
                                    type="phone"
                                    id="delivery_number"
                                    placeholder="Phone number"
                                    maxlength="13"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                                <p
                                    v-if="form.errors.delivery_number"
                                    class="text-red-600 text-sm"
                                >
                                    {{ form.errors.delivery_number }}
                                </p>
                            </div>
                            <div>
                                <InputLabel
                                    for="destination"
                                    value="Destination"
                                />
                                <input
                                    v-if="destinationBarangay"
                                    v-model="requestform.barangay_name"
                                    type="text"
                                    id="destination"
                                    placeholder="Destination (Barangay)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />

                                <input
                                    v-else
                                    v-model="destinationRHU"
                                    type="text"
                                    id="destination"
                                    placeholder="Destination (RHU)"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                />
                            </div>
                            <div class="col-span-2">
                                <InputLabel
                                    for="delivery_address"
                                    value="Delivery Address"
                                />
                                <textarea
                                    v-model="form.delivery_address"
                                    type="text"
                                    rows="2"
                                    id="delivery_address"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                    placeholder="Delivery Address"
                                >
                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-span-1 my-2 flex justify-end">
                <button
                    type="submit"
                    @click="nextStep"
                    :disabled="!approveMode"
                    :class="{
                        'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800':
                            approveMode,
                        'bg-gray-400 cursor-not-allowed opacity-50':
                            !approveMode,
                    }"
                    class="font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center"
                >
                    Next
                </button>
            </div>
        </div>

        <!-- Select Supplies -->
        <div v-show="currentStep === 1">
            <h2 class="text-xl font-bold my-3">
                2. Select the supplies you want to deliver:
            </h2>

            <div class="grid grid-cols-1 gap-3">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-3">
                        <!-- Display requested -->
                        <div
                            class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                        >
                            <h2 class="text-lg font-medium mb-2">
                                <span class="font-bold">Requested</span>
                                supplies
                            </h2>
                            <div
                                class="grid lg:grid-cols-3 grid-cols-1 gap-5 p-2 max-w-screen-md"
                            >
                                <!-- User card -->
                                <div
                                    v-if="
                                        pendingRequest.medicine_requests &&
                                        pendingRequest.medicine_requests
                                            .length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 max-w-sm p-3 bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                                class="px-2 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th class="px-2">
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="medicine in pendingRequest.medicine_requests"
                                                    >
                                                        <tr
                                                            :key="medicine.id"
                                                            class="bg-white"
                                                        >
                                                            <td
                                                                class="px-2 text-center"
                                                            >
                                                                {{
                                                                    medicine.quantity
                                                                }}
                                                            </td>
                                                            <td class="px-2">
                                                                {{
                                                                    medicine
                                                                        .generic_name
                                                                        .generic_name
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Provider card -->
                                <div
                                    v-if="
                                        pendingRequest.equipment_requests &&
                                        pendingRequest.equipment_requests
                                            .length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-blue-100 border border-blue-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                                class="px-2 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th class="px-2">
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="equipment in pendingRequest.equipment_requests"
                                                    >
                                                        <tr :key="equipment.id">
                                                            <td
                                                                class="px-2 text-center"
                                                            >
                                                                {{
                                                                    equipment.quantity
                                                                }}
                                                            </td>
                                                            <td class="px-2">
                                                                {{
                                                                    equipment.equipment_name
                                                                }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        pendingRequest.supply_requests &&
                                        pendingRequest.supply_requests.length >
                                            0
                                    "
                                    class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-violet-100 border border-violet-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                                class="px-2 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th class="px-2">
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="supply in pendingRequest.supply_requests"
                                                    >
                                                        <tr :key="supply.id">
                                                            <td
                                                                class="px-2 text-center"
                                                            >
                                                                {{
                                                                    supply.quantity
                                                                }}
                                                            </td>
                                                            <td class="px-2">
                                                                {{
                                                                    supply.medical_supply_name
                                                                }}
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

                        <!-- Display Selected -->
                        <div
                            v-if="
                                medicineSelected.length > 0 ||
                                equipmentSelected > 0 ||
                                medSupplySelected > 0
                            "
                            class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                        >
                            <h2 class="text-lg font-medium mb-2">
                                <span class="font-bold">Selected</span> supplies
                            </h2>
                            <div
                                class="grid lg:grid-cols-3 grid-cols-1 gap-5 p-2 max-w-screen-md"
                            >
                                <!-- User card -->
                                <div
                                    v-if="
                                        medicineSelected &&
                                        medicineSelected.length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 max-w-sm p-3 bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                                class="px-2 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th class="px-2">
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="item in medicineSelected"
                                                    >
                                                        <tr
                                                            :key="item.id"
                                                            class="bg-white"
                                                        >
                                                            <td
                                                                class="px-2 text-center"
                                                            >
                                                                {{
                                                                    item.requestedQuantity
                                                                }}
                                                            </td>
                                                            <td class="px-2">
                                                                {{ item.name }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Provider card -->
                                <div
                                    v-if="
                                        equipmentSelected &&
                                        equipmentSelected.length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-blue-100 border border-blue-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                                class="px-2 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th class="px-2">
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="item in equipmentSelected"
                                                    >
                                                        <tr :key="item.id">
                                                            <td
                                                                class="px-2 text-center"
                                                            >
                                                                {{
                                                                    item.requestedQuantity
                                                                }}
                                                            </td>
                                                            <td class="px-2">
                                                                {{ item.name }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="
                                        medSupplySelected &&
                                        medSupplySelected.length > 0
                                    "
                                    class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-violet-100 border border-violet-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                                class="px-2 text-center"
                                                            >
                                                                Total
                                                            </th>
                                                            <th class="px-2">
                                                                Name
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        v-for="item in medSupplySelected"
                                                    >
                                                        <tr :key="item.id">
                                                            <td
                                                                class="px-2 text-center"
                                                            >
                                                                {{
                                                                    item.requestedQuantity
                                                                }}
                                                            </td>
                                                            <td class="px-2">
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
                        <TabGroup>
                            <TabList
                                class="flex space-x-1 rounded-xl gap-1 py-2"
                            >
                                <Tab as="template" v-slot="{ selected }">
                                    <button
                                        :class="[
                                            'inline-block px-4 py-2 rounded-lg ',
                                            selected
                                                ? 'inline-block px-4 py-2 text-white bg-blue-600 rounded-lg'
                                                : 'bg-gray-100 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white',
                                        ]"
                                    >
                                        Medicine
                                    </button>
                                </Tab>
                                <Tab as="template" v-slot="{ selected }">
                                    <button
                                        :class="[
                                            'inline-block px-4 py-2 rounded-lg ',
                                            selected
                                                ? 'inline-block px-4 py-2 text-white bg-blue-600 rounded-lg'
                                                : 'bg-gray-100 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white',
                                        ]"
                                    >
                                        Equipment
                                    </button>
                                </Tab>
                                <Tab as="template" v-slot="{ selected }">
                                    <button
                                        :class="[
                                            'inline-block px-4 py-2 rounded-lg ',
                                            selected
                                                ? 'inline-block px-4 py-2 text-white bg-blue-600 rounded-lg'
                                                : 'bg-gray-100 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white',
                                        ]"
                                    >
                                        Medical Supply
                                    </button>
                                </Tab>
                            </TabList>
                            <TabPanels class="mt-2">
                                <TabPanel>
                                    <div
                                        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
                                    >
                                        <!-- Search and filter -->
                                        <div
                                            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
                                        >
                                            <div class="w-full md:w-1/2">
                                                <form class="flex items-center">
                                                    <label
                                                        for="simple-search"
                                                        class="sr-only"
                                                        >Search</label
                                                    >
                                                    <div
                                                        class="relative w-full"
                                                    >
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
                                                            required=""
                                                            v-model="
                                                                searchMedicines
                                                            "
                                                        />
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- medicine filter  -->
                                            <deliveryFilterButton
                                                :selectedStatus="
                                                    selectedStatus.medicine
                                                "
                                                dropdownButtonId="medicineDropdown"
                                                dropdownId="filterDropdown"
                                                @status-updated="updateQuery"
                                            />
                                        </div>
                                        <!-- Table -->
                                        <div
                                            class="relative overflow-x-auto sm:rounded-lg"
                                        >
                                            <table
                                                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                                            >
                                                <thead
                                                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                                                >
                                                    <tr>
                                                        <th
                                                            scope="col"
                                                            class="p-4"
                                                        ></th>
                                                        <th
                                                            scope="col"
                                                            class="px-2 py-3"
                                                        >
                                                            ID
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-2 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Batch
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'batch_id'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Medicine name
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'generic_name'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>

                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Quantity
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'quantity'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Date acquired
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'date_acquired'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Expiration
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'expiration_date'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Status
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'status'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    v-for="medicine in medicines.data"
                                                    data-accordion="collapse"
                                                >
                                                    <tr
                                                        @click="
                                                            openModal(
                                                                'medicine',
                                                                medicine
                                                            )
                                                        "
                                                        :key="medicine.id"
                                                        :id="`accordion-collapse-heading-${medicine.id}`"
                                                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                                    >
                                                        <td class="w-4 p-4">
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <input
                                                                    v-if="
                                                                        medicineChecked.includes(
                                                                            medicine.id
                                                                        )
                                                                    "
                                                                    @click.stop
                                                                    :value="
                                                                        medicine.id
                                                                    "
                                                                    v-model="
                                                                        medicineChecked
                                                                    "
                                                                    :id="`checkbox-table-search-${medicine.id}`"
                                                                    type="checkbox"
                                                                    :disabled="
                                                                        true
                                                                    "
                                                                    class="cursor-not-allowed w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                                />

                                                                <label
                                                                    :for="`checkbox-table-search-${medicine.id}`"
                                                                    class="sr-only"
                                                                    >checkbox</label
                                                                >
                                                            </div>
                                                        </td>
                                                        <td class="px-2 py-6">
                                                            {{ medicine.id }}
                                                        </td>
                                                        <td class="px-2 py-6">
                                                            {{
                                                                medicine.batch_id
                                                            }}
                                                        </td>
                                                        <th
                                                            scope="row"
                                                            class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap dark:text-white"
                                                        >
                                                            {{
                                                                medicine
                                                                    .generic_name
                                                                    .generic_name
                                                            }}
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{
                                                                medicine.quantity
                                                            }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            {{
                                                                medicine.date_acquired
                                                            }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <ExpiryCountdown
                                                                :expirationDate="
                                                                    medicine.expiration_date
                                                                "
                                                            />
                                                        </td>
                                                        <td class="px-6 py-6">
                                                            <StatusLabel
                                                                :all="
                                                                    medicine.status
                                                                "
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <ApprovePagination
                                            :paginator="medicines"
                                            :key="currentStep"
                                            :search="search"
                                            :selecteddeliveryStatus="
                                                selectedStatus
                                            "
                                            :sortField="sortField"
                                            :sortDirection="sortDirection"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel>
                                    <div
                                        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
                                    >
                                        <!-- Search and filter -->
                                        <div
                                            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
                                        >
                                            <div class="w-full md:w-1/2">
                                                <form class="flex items-center">
                                                    <label
                                                        for="simple-search"
                                                        class="sr-only"
                                                        >Search</label
                                                    >
                                                    <div
                                                        class="relative w-full"
                                                    >
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
                                                            required=""
                                                            v-model="
                                                                searchEquipment
                                                            "
                                                        />
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- equipmentfilter -->
                                            <deliveryFilterButton
                                                :selectedStatus="
                                                    selectedStatus.equipment
                                                "
                                                dropdownButtonId="equipDropdown"
                                                dropdownId="filterDropdown"
                                                @status-updated="updateQuery"
                                            />
                                        </div>
                                        <!-- Table -->
                                        <div
                                            class="relative overflow-x-auto sm:rounded-lg"
                                        >
                                            <table
                                                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                                            >
                                                <thead
                                                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                                                >
                                                    <tr>
                                                        <th
                                                            scope="col"
                                                            class="p-4"
                                                        ></th>
                                                        <th
                                                            scope="col"
                                                            class="px-2 py-3"
                                                        >
                                                            ID
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-2 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Batch
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'batch_id'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                equipment name
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'equipment_name'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Quantity
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'quantity'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Date acquired
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'date_acquired'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Status
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'status'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    v-for="equipment in equipments.data"
                                                    data-accordion="collapse"
                                                >
                                                    <tr
                                                        :key="equipment.id"
                                                        @click="
                                                            openModal(
                                                                'equipment',
                                                                equipment
                                                            )
                                                        "
                                                        :id="`accordion-collapse-heading-${equipment.id}`"
                                                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                                    >
                                                        <td class="w-4 p-4">
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <input
                                                                    v-if="
                                                                        equipmentChecked.includes(
                                                                            equipment.id
                                                                        )
                                                                    "
                                                                    :value="
                                                                        equipment.id
                                                                    "
                                                                    v-model="
                                                                        equipmentChecked
                                                                    "
                                                                    :id="`checkbox-table-search-${equipment.id}`"
                                                                    type="checkbox"
                                                                    :disabled="
                                                                        true
                                                                    "
                                                                    class="cursor-not-allowed w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                                />

                                                                <label
                                                                    :for="`checkbox-table-search-${equipment.id}`"
                                                                    class="sr-only"
                                                                    >checkbox</label
                                                                >
                                                            </div>
                                                        </td>
                                                        <td class="px-2 py-6">
                                                            {{ equipment.id }}
                                                        </td>
                                                        <td class="px-2 py-6">
                                                            {{
                                                                equipment.batch_id
                                                            }}
                                                        </td>
                                                        <th
                                                            scope="row"
                                                            class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap dark:text-white"
                                                        >
                                                            {{
                                                                equipment.equipment_name
                                                            }}
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{
                                                                equipment.quantity
                                                            }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            {{
                                                                equipment.date_acquired
                                                            }}
                                                        </td>
                                                        <td class="px-6 py-6">
                                                            <StatusLabel
                                                                :all="
                                                                    equipment.status
                                                                "
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <ApprovePagination
                                            :paginator="equipments"
                                            :key="currentStep"
                                            :search="search"
                                            :selecteddeliveryStatus="
                                                selectedStatus
                                            "
                                            :sortField="sortField"
                                            :sortDirection="sortDirection"
                                        />
                                    </div>
                                </TabPanel>
                                <TabPanel>
                                    <div
                                        class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
                                    >
                                        <!-- Search and filter -->
                                        <div
                                            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
                                        >
                                            <div class="w-full md:w-1/2">
                                                <form class="flex items-center">
                                                    <label
                                                        for="simple-search"
                                                        class="sr-only"
                                                        >Search</label
                                                    >
                                                    <div
                                                        class="relative w-full"
                                                    >
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
                                                            required=""
                                                            v-model="
                                                                searchSupplies
                                                            "
                                                        />
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- medsuppfilter -->

                                            <deliveryFilterButton
                                                :selectedStatus="
                                                    selectedStatus.supply
                                                "
                                                dropdownButtonId="supplyDropdown"
                                                dropdownId="filterDropdown"
                                                @status-updated="updateQuery"
                                            />
                                        </div>
                                        <!-- Table -->
                                        <div
                                            class="relative overflow-x-auto sm:rounded-lg"
                                        >
                                            <table
                                                class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                                            >
                                                <thead
                                                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                                                >
                                                    <tr>
                                                        <th
                                                            scope="col"
                                                            class="p-4"
                                                        ></th>
                                                        <th
                                                            scope="col"
                                                            class="px-2 py-3"
                                                        >
                                                            ID
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-2 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Batch
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'batch_id'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                medical supply
                                                                name
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'med_sup_name'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Quantity
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'quantity'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Date acquired
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'date_acquired'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                        <th
                                                            scope="col"
                                                            class="px-6 py-3"
                                                        >
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                Status
                                                                <a
                                                                    href="#"
                                                                    @click.prevent="
                                                                        sort(
                                                                            'status'
                                                                        )
                                                                    "
                                                                >
                                                                    <svg
                                                                        class="w-3 h-3 ms-1.5"
                                                                        aria-hidden="true"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="currentColor"
                                                                        viewBox="0 0 24 24"
                                                                    >
                                                                        <path
                                                                            d="M8.574 11.024h6.852a2.075 2.075 0 0 0 1.847-1.086 1.9 1.9 0 0 0-.11-1.986L13.736 2.9a2.122 2.122 0 0 0-3.472 0L6.837 7.952a1.9 1.9 0 0 0-.11 1.986 2.074 2.074 0 0 0 1.847 1.086Zm6.852 1.952H8.574a2.072 2.072 0 0 0-1.847 1.087 1.9 1.9 0 0 0 .11 1.985l3.426 5.05a2.123 2.123 0 0 0 3.472 0l3.427-5.05a1.9 1.9 0 0 0 .11-1.985 2.074 2.074 0 0 0-1.846-1.087Z"
                                                                        />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    v-for="med_supply in med_supplies.data"
                                                    data-accordion="collapse"
                                                >
                                                    <tr
                                                        :key="med_supply.id"
                                                        @click="
                                                            openModal(
                                                                'med_supply',
                                                                med_supply
                                                            )
                                                        "
                                                        :id="`accordion-collapse-heading-${med_supply.id}`"
                                                        class="bg-white border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                                    >
                                                        <td class="w-4 p-4">
                                                            <div
                                                                class="flex items-center"
                                                            >
                                                                <input
                                                                    v-if="
                                                                        medSupplyChecked.includes(
                                                                            med_supply.id
                                                                        )
                                                                    "
                                                                    @click.stop
                                                                    :value="
                                                                        med_supply.id
                                                                    "
                                                                    v-model="
                                                                        medSupplyChecked
                                                                    "
                                                                    :id="`checkbox-table-search-${med_supply.id}`"
                                                                    type="checkbox"
                                                                    :disabled="
                                                                        true
                                                                    "
                                                                    class="cursor-not-allowed w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                                                                />

                                                                <label
                                                                    :for="`checkbox-table-search-${med_supply.id}`"
                                                                    class="sr-only"
                                                                    >checkbox</label
                                                                >
                                                            </div>
                                                        </td>
                                                        <td class="px-2 py-6">
                                                            {{ med_supply.id }}
                                                        </td>
                                                        <td class="px-2 py-6">
                                                            {{
                                                                med_supply.batch_id
                                                            }}
                                                        </td>
                                                        <th
                                                            scope="row"
                                                            class="px-6 py-4 font-semibold text-gray-900 whitespace-nowrap dark:text-white"
                                                        >
                                                            {{
                                                                med_supply.med_sup_name
                                                            }}
                                                        </th>
                                                        <td class="px-6 py-4">
                                                            {{
                                                                med_supply.quantity
                                                            }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            {{
                                                                med_supply.date_acquired
                                                            }}
                                                        </td>
                                                        <td class="px-6 py-6">
                                                            <StatusLabel
                                                                :all="
                                                                    med_supply.status
                                                                "
                                                            />
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <ApprovePagination
                                            :paginator="med_supplies"
                                            :key="currentStep"
                                            :search="search"
                                            :selecteddeliveryStatus="
                                                selectedStatus
                                            "
                                            :sortField="sortField"
                                            :sortDirection="sortDirection"
                                        />
                                    </div>
                                </TabPanel>
                            </TabPanels>
                        </TabGroup>
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

            <div class="col-span-1 my-2 flex gap-4 justify-end">
                <button
                    type="submit"
                    @click="previousStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Back
                </button>
                <button
                    type="submit"
                    @click="nextStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Next
                </button>
            </div>
        </div>

        <!-- Confirmation -->
        <div v-show="currentStep === 2">
            <h2 class="text-xl font-bold my-3">2. Confirmation:</h2>
            <div class="grid xl:grid-cols-2 grid-cols-1 gap-2">
                <div
                    class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                >
                    <h2 class="text-lg font-bold mb-2">
                        Supplies to be delivered:
                    </h2>
                    <div class="grid grid-cols-1 gap-5 p-2 max-w-screen-md">
                        <!-- User card -->
                        <div
                            v-if="
                                medicineSelected && medicineSelected.length > 0
                            "
                            class="flex flex-row justify-between gap-5 max-w-sm p-3 bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                        class="px-2 text-center"
                                                    >
                                                        Total
                                                    </th>
                                                    <th class="px-2">Name</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                v-for="item in medicineSelected"
                                            >
                                                <tr
                                                    :key="item.id"
                                                    class="bg-white"
                                                >
                                                    <td
                                                        class="px-2 text-center"
                                                    >
                                                        {{
                                                            item.requestedQuantity
                                                        }}
                                                    </td>
                                                    <td class="px-2">
                                                        {{ item.name }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Provider card -->
                        <div
                            v-if="
                                equipmentSelected &&
                                equipmentSelected.length > 0
                            "
                            class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-blue-100 border border-blue-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                        class="px-2 text-center"
                                                    >
                                                        Total
                                                    </th>
                                                    <th class="px-2">Name</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                v-for="item in equipmentSelected"
                                            >
                                                <tr :key="item.id">
                                                    <td
                                                        class="px-2 text-center"
                                                    >
                                                        {{
                                                            item.requestedQuantity
                                                        }}
                                                    </td>
                                                    <td class="px-2">
                                                        {{ item.name }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="
                                medSupplySelected &&
                                medSupplySelected.length > 0
                            "
                            class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-violet-100 border border-violet-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                                        class="px-2 text-center"
                                                    >
                                                        Total
                                                    </th>
                                                    <th class="px-2">Name</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                v-for="item in medSupplySelected"
                                            >
                                                <tr :key="item.id">
                                                    <td
                                                        class="px-2 text-center"
                                                    >
                                                        {{
                                                            item.requestedQuantity
                                                        }}
                                                    </td>
                                                    <td class="px-2">
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
                <div
                    class="col-span-1 p-5 bg-white rounded-lg border border-gray-200 shadow-sm="
                >
                    <h2 class="text-lg font-bold mb-2">Delivery details:</h2>
                    <h2 class="text-md font-bold mx-1 border-b border-gray-300">
                        Requester details:
                    </h2>
                    <div class="p-2 px-3">
                        <div class="space-y-1">
                            <dl class="flex items-center justify-between gap-4">
                                <dt
                                    class="text-md font-medium text-gray-900 dark:text-white"
                                >
                                    Name
                                </dt>
                                <dd
                                    class="text-base font-thin text-gray-700 dark:text-white"
                                >
                                    {{ requestform.requester_name }}
                                </dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt
                                    class="text-md font-medium text-gray-900 dark:text-white"
                                >
                                    Requested from:
                                </dt>
                                <dd
                                    class="text-base font-thin text-gray-900 dark:text-white"
                                >
                                    {{ requestform.barangay_name }}
                                </dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt
                                    class="text-md font-medium text-gray-900 dark:text-white"
                                >
                                    Date requested
                                </dt>
                                <dd
                                    class="text-base font-thin text-gray-900 dark:text-white"
                                >
                                    {{ requestform.requested_at }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <h2
                        class="text-md font-bold px-1 mt-2 border-b border-gray-300"
                    >
                        RHU details:
                    </h2>
                    <div class="p-2 px-3">
                        <div class="space-y-1">
                            <dl class="flex items-center justify-between gap-4">
                                <dt
                                    class="text-md font-medium text-gray-900 dark:text-white"
                                >
                                    Name
                                </dt>
                                <dd
                                    class="text-base font-thin text-gray-700 dark:text-white"
                                >
                                    {{ form.approver_name }}
                                </dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt
                                    class="text-md font-medium text-gray-900 dark:text-white"
                                >
                                    Position
                                </dt>
                                <dd
                                    class="text-base font-thin text-gray-900 dark:text-white"
                                >
                                    {{ form.approver_position }}
                                </dd>
                            </dl>

                            <dl class="flex items-center justify-between gap-4">
                                <dt
                                    class="text-md font-medium text-gray-900 dark:text-white"
                                >
                                    Rhu
                                </dt>
                                <dd
                                    class="text-base font-thin text-gray-900 dark:text-white"
                                >
                                    {{ form.approver_rhu }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <div v-if="approveMode == 'Pickup'">
                        <h2
                            class="text-md font-bold px-1 mt-2 border-b border-gray-300"
                        >
                            Pickup details:
                        </h2>
                        <div class="p-2 px-3">
                            <div class="space-y-1">
                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-md font-medium text-gray-900 dark:text-white"
                                    >
                                        Pickup Date
                                    </dt>
                                    <dd
                                        v-if="
                                            date_option == 'Anyday' ||
                                            !form.pickup_date
                                        "
                                        class="text-base font-thin text-gray-700 dark:text-white"
                                    >
                                        Any day available
                                    </dd>
                                    <dd
                                        v-if="
                                            form.pickup_date &&
                                            date_option == 'Range'
                                        "
                                        class="text-base font-thin text-gray-700 dark:text-white"
                                    >
                                        {{ form.pickup_date }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div v-if="approveMode == 'Delivery'">
                        <h2
                            class="text-md font-bold px-1 mt-2 border-b border-gray-300"
                        >
                            Delivery details:
                        </h2>
                        <div class="p-2 px-3">
                            <div class="space-y-1">
                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-md font-medium text-gray-900 dark:text-white"
                                    >
                                        Delivery Person
                                    </dt>
                                    <dd
                                        class="text-base font-thin text-gray-700 dark:text-white"
                                    >
                                        {{ form.delivery_name }}
                                    </dd>
                                </dl>
                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-md font-medium text-gray-900 dark:text-white"
                                    >
                                        Contact number
                                    </dt>
                                    <dd
                                        class="text-base font-thin text-gray-700 dark:text-white"
                                    >
                                        {{ form.delivery_number }}
                                    </dd>
                                </dl>

                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-md font-medium text-gray-900 dark:text-white"
                                    >
                                        Destination
                                    </dt>
                                    <dd
                                        class="text-base font-thin text-gray-900 dark:text-white"
                                    >
                                        {{ requestform.barangay_name }}
                                    </dd>
                                </dl>

                                <dl
                                    class="flex items-center justify-between gap-4"
                                >
                                    <dt
                                        class="text-md font-medium text-gray-900 dark:text-white"
                                    >
                                        Delivery Address
                                    </dt>
                                    <dd
                                        class="text-base font-thin text-gray-900 dark:text-white"
                                    >
                                        {{ form.delivery_address }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-1 my-2 flex gap-4 justify-end">
                <button
                    type="submit"
                    @click="previousStep"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                >
                    Back
                </button>
                <form @submit.prevent="submit">
                    <button
                        type="submit"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    >
                        Approve
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
