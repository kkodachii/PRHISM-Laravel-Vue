<script setup>
import { ref, onMounted, watch } from "vue";
import { initFlowbite } from "flowbite";
import { useForm } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import formattedDate from "@/Components/formattedDate.vue";
import LoadingModal from "@/Components/LoadingModal.vue";

defineOptions({ layout: DashboardLayout });

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    requests: Object,
    inventories: Object,
    medicines: Object,
    equipments: Object,
    medical_supplies: Object,
});

const reports = ref([
   {
    supply_id: "",
    problem: "incorrect_quantity",
    quantity: 1,
   }
]);

const request = ref(props.requests);
const inventories = ref(props.inventories);
const medicines = ref(props.medicines);
const equipments = ref(props.equipments);
const medical_supplies = ref(props.medical_supplies);

const selectedRequestID = ref('');
const showConfirmModal = ref(false);
const isLoading = ref(false);
const date_now = new Date().toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    timeZone: "Asia/Manila",
});

const typeChecked = ref([]);
const brokenEquipmentSelected = ref([]);
const packageIssue =ref();
const wrongSupplyText = ref('');
const otherText = ref('');

const closeConfirmModal = () => {
    showConfirmModal.value = false;
};

const openConfirmModal = (requestId) => {
    selectedRequestID.value = requestId
    showConfirmModal.value = true;
};

// Toggle equipment selection and track quantities
function toggleEquipment(equipment) {
    // Check if the equipment is already in the list
    const equipmentIndex = brokenEquipmentSelected.value.findIndex(e => e.name === equipment.name);

    if (equipmentIndex === -1) {
        // Add the equipment with a default quantity of 1 if it is not in the list
        brokenEquipmentSelected.value.push({
            name: equipment.name, // Ensure correct structure
            quantity: 1           // Set default quantity
        });
    } else {
        // Remove the equipment if it is already selected
        brokenEquipmentSelected.value.splice(equipmentIndex, 1);
    }
}


// Check if equipment is selected
function isEquipmentSelected(equipment) {
    return brokenEquipmentSelected.value.some(e => e.name === equipment.name);
}

// Get the equipment object for a selected equipment
function getEquipmentObject(equipment) {
    return brokenEquipmentSelected.value.find(e => e.name === equipment.name);
}

function addReport() {
    reports.value.push({ supply_id: "", problem: 'incorrect_quantity', quantity: 1 });
};
function removeReport(index) {
    reports.value.splice(index, 1);
}

// Function to handle marking as delivered
function markAsDelivered(requestId) {
    closeConfirmModal();
    isLoading.value = true;
    Inertia.post(route('delivery.markAsDelivered', requestId))
        .then(response => {
            isLoading.value = false;
        })
        .catch(error => {
            console.error(error);
            isLoading.value = false;
        });
}

function submitReport() {
    try {
        // Build the report data object
        const reportData = {
            request_id: request.value.id,
            reports: reports.value,
            broken_equipment: brokenEquipmentSelected.value.map(equipment => {
                return {
                    equipment: equipment.name,  // Store equipment name
                    quantity: equipment.quantity // Store the associated quantity
                };
            }),
            package_issue: packageIssue.value ? true : false,
            wrong_supply_text: wrongSupplyText.value,
            other_text: otherText.value
        };

        // console.log(reportData);

        // Send the report data to the backend
        Inertia.post('/delivery/report', reportData);
    } catch (error) {
        console.error('Error submitting report:', error);
    }
}
</script>

<template>
    <Head title="Report Delivery" />
    <LoadingModal :isLoading="isLoading" />

    <div class="relative p-5 sm:rounded-lg border border-gray-300 bg-white">
        <div class="flex justify-between">
            <h1 class="mb-5 text-3xl lg:text-2xl font-bold leading-tight text-gray-900  dark:text-white">Report Delivery</h1>
            <p class="text-gray-700 font-normal">
                <span class="text-gray-800 font-medium">Request ID:</span>
                {{ request.id }}
            </p>
        </div>

        <!-- Delivery Details -->
        <div class="px-3 mb-5">
            <div class="text-left">
                <p
                    class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                >
                    Request details
                </p>
            </div>
            <div
                class="grid grid-cols-2 md:grid-cols-3 px-2 mt-2 gap-3 mb-3"
            >
                <div
                    class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                >
                    <span
                        class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                    >
                        Current Date
                    </span>
                    <span
                        class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                    >
                    {{ date_now }}
                    </span>
                </div>
                <div
                    class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                >
                <span
                    class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                >
                Request ID
                </span>
                <span
                    class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                >
                {{ request.id }}
                </span>
                </div>
            </div>
        </div>

        <!-- Select Problem -->
        <div class="">
            <p class="text-gray-800 text-xl font-semibold">
                Select Type of Issue(Check all that apply):
            </p>
            <div class="p-3 mb-4">
                <div v-if="equipments.length > 0" class="flex items-center mb-2">
                    <input
                        v-model="typeChecked"
                        id="brokenEquipment"
                        value="brokenEquipment"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="default-checkbox" class="ms-2 text-md font-medium text-gray-900 dark:text-gray-300">Broken/Damaged Equipment</label>
                </div>
                <div class="flex items-center mb-2">
                    <input
                        v-model="typeChecked"
                        id="incorrectQuantity"
                        value="incorrectQuantity"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="default-checkbox" class="ms-2 text-md font-medium text-gray-900 dark:text-gray-300">Incorrect quantity of medicine/supplies</label>
                </div>
                <div class="flex items-center mb-2">
                    <input
                        v-model="typeChecked"
                        id="wrongProducts"
                        value="wrongProducts"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="default-checkbox" class="ms-2 text-md font-medium text-gray-900 dark:text-gray-300">Wrong Supplies Delivered</label>
                </div>
                <div class="flex items-center mb-2">
                    <input
                        v-model="packageIssue"
                        id="packageIssue"
                        value="true"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="default-checkbox" class="ms-2 text-md font-medium text-gray-900 dark:text-gray-300">Package Issues (e.g., torn boxes, unsealed packages)</label>
                </div>
                <div class="flex items-center mb-2">
                    <input
                        v-model="typeChecked"
                        id="otherIssue"
                        value="otherIssue"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    />
                    <label for="default-checkbox" class="ms-2 text-md font-medium text-gray-900 dark:text-gray-300">Others</label>
                </div>
            </div>
        </div>
        <div v-if="typeChecked.includes('brokenEquipment')"
        class="relative border-b border-t py-3 p-2 border-gray-300 bg-white">
            <p class="text-gray-800 text-xl font-semibold">
                Broken/Damaged Equipment
            </p>
            <div class="ml-3 my-2">
                <p class="text-gray-700 text-md font-medium mb-3">
                    Select the equipments:
                </p>
                <div>
                    <div class="grid grid-cols-1 md:grid-cols-1 ml-2 gap-2" v-for="equipment in equipments" :key="equipment.id">
                        <div class="col-span-1 flex items-center mb-2">
                            <input
                                v-model="brokenEquipmentSelected"
                                id="brokenEquipment"
                                :value="equipment.name"
                                type="checkbox"
                                @change="toggleEquipment(equipment)"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                            />
                            <label for="default-checkbox" class="ms-2 text-md font-medium text-gray-900 dark:text-gray-300">{{equipment.name}}</label>

                            <!-- Only show quantity input if the equipment is selected -->
                            <input
                                v-if="isEquipmentSelected(equipment)"
                                v-model="getEquipmentObject(equipment).quantity"
                                type="number"
                                placeholder="Quantity"
                                required
                                class="ml-2 bg-gray-50 block p-2.5 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            />
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <div v-if="typeChecked.includes('incorrectQuantity')" class="relative border-b border-t py-3 px-4 border-gray-300 bg-white">
    <p class="text-gray-800 text-xl font-semibold">
        Incorrect quantity of medicine/supplies
    </p>
    <div class="ml-3 my-2">
        <p class="text-gray-700 text-md font-medium mb-3">
            Select the supplies:
        </p>
        <form @submit.prevent="submitReport">
            <div>
                <div v-for="(report, index) in reports" :key="index" class="flex flex-row items-center gap-3 py-2">
                    <div class="w-10/12 xl:w-1/2 flex flex-col space-y-2">
                        <select
                            v-model="reports[index].supply_id"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option v-for="item in inventories" :value="item.id" :key="item.id">
                                {{ item.name }}
                            </option>
                        </select>

                        <select
                            v-model="reports[index].problem"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        >
                            <option value="incorrect_quantity">Missing Quantity</option>
                            <option value="damaged">Damaged</option>
                        </select>

                        <input
                            type="number"
                            v-model="reports[index].quantity"
                            placeholder="Quantity"
                            min="1"
                            class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        />
                    </div>

                    <div class="w-2/12 flex justify-center">
                        <button type="button" @click="removeReport(index)">
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
                </div>

                <button
                    type="button"
                    @click="addReport"
                    class="bg-gray-200 mt-4 py-2 px-4 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    Add Another Supply
                </button>
            </div>
        </form>
    </div>
</div>

        <div v-if="typeChecked.includes('wrongProducts')"
        class="relative border-b border-t py-3 p-2 border-gray-300 bg-white">
            <p class="text-gray-800 text-xl font-semibold">
                Wrong Supplies delivered
            </p>
            <div class="ml-3 my-2">
                <label
                    for="description"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                >
                   List the supplies
                </label>
                <textarea
                    v-model="wrongSupplyText"
                    type="text"
                    rows="3"
                    id="description"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                    placeholder="List the name of the supplies"
                ></textarea>
            </div>
        </div>
        <div v-if="typeChecked.includes('otherIssue')"
        class="relative border-b border-t py-3 p-2 border-gray-300 bg-white">
            <p class="text-gray-800 text-xl font-semibold mb-2">
                Others (Please specifiy)
            </p>
            <div>
                <div class="ml-3 my-2">
                    <label
                        for="description"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                    >
                        Delivery Problem
                    </label>
                    <textarea
                        v-model="otherText"
                        type="text"
                        rows="3"
                        id="description"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Write your delivery problems"
                    ></textarea>
                </div>
            </div>
        </div>

        <div>
            <div class="p-2 my-3">
                <div class="flex justify-end space-x-2">
                    <a
                        :href="route('requests.index')"
                        type="button"
                        class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800"
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
                                d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"
                            />
                        </svg>
                        Cancel
                    </a>

                    <button
    :class="{
        'text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800': typeChecked.length > 0,
        'bg-gray-400 text-gray-200 opacity-70 cursor-not-allowed': typeChecked.length === 0
    }"
    class="font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center mr-3"
    :disabled="typeChecked.length === 0"
    @click="submitReport()"
>
    Submit Report
</button>


                </div>
            </div>
        </div>

    </div>


    <!-- Confirm Modal -->
    <div
    v-if="showConfirmModal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
>
    <div
        class="relative p-4 w-full max-w-lg"
    >
        <div
            class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8"
        >
            <h3
                class="mb-3 text-4xl font-bold text-gray-900 dark:text-white"
            >
                Confirm as delivered?
            </h3>
            <div
                class="mb-4 text-sm font-light p-4 text-gray-500 dark:text-gray-400"
            >
                <p
                    class="font-semibold text-gray-800"
                >
                The system will
                automatically
                update your current inventory. Make sure the
                    supplies you have received are complete.
                </p>
                <p>
                    Click "Report Problems" if you have problems with the delivery(eg. Missing, Damaged, or Expired Supplies)
                </p>
            </div>
            <div
                class="flex justify-center mt-4"
            >
                <button
                    @click="
                        closeConfirmModal
                    "
                    class="py-2 px-4 bg-gray-300 rounded-md"
                >
                    Cancel
                </button>
                <button
                    @click="
                        markAsDelivered(selectedRequestID)
                    "
                    class="py-2 px-4 bg-blue-700 text-white rounded-md ml-2"
                >
                    Confirm
                </button>
            </div>
        </div>
    </div>
    </div>

</template>
