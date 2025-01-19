<script setup>
import { ref, onMounted } from "vue";
import { initFlowbite } from "flowbite";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Inertia } from "@inertiajs/inertia";
import formattedDate from "@/Components/formattedDate.vue";
import LoadingModal from "@/Components/LoadingModal.vue";
import dayjs from "dayjs";

defineOptions({ layout: DashboardLayout });

onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    message: String,
    requests: Object,
    pickup_date: Object,
    auth: Object,
});

const rhu = ref(props.auth.user.rhu_id);

if(props.auth.user.rhu_id == 1){
    rhu.value = "Main";
}

function pickupDate() {
    return dayjs(props.pickup_date.pickup_date).format('MMM D, YYYY');
}

const requests = ref(props.requests)
const selectedRequestID = ref('');
const showConfirmModal = ref(false);
const isLoading = ref(false);

const showReturnModal = ref(false);
const returnReason = ref("Incomplete/Missing supplies");
const otherText = ref("");

const closeConfirmModal = () => {
    showConfirmModal.value = false;
};

const openConfirmModal = (requestId) => {
    selectedRequestID.value = requestId
    showConfirmModal.value = true;
};

const closeReturnModal = () => {
    showReturnModal.value = false;
};

const openReturnModal = (requestId) => {
    selectedRequestID.value = requestId
    showReturnModal.value = true;
};

// Function to handle marking as delivered
function markAsDelivered(requestId) {
    closeConfirmModal();
    isLoading.value = true;
    Inertia.post(route('delivery.markAsDelivered', requestId), { requestId })
        .then(response => {
            isLoading.value = false;
        })
        .catch(error => {
            console.error(error);
            isLoading.value = false;
        });
}

function returnDelivery(requestId) {
    isLoading.value = true;
    if(returnReason.value =="Other"){
        returnReason.value = otherText.value;
    }
    closeReturnModal();
    Inertia.post(route('delivery.returnDelivery', requestId),{
        returnReason: returnReason.value,
    });
}

function reportDelivery(requestId) {
    Inertia.post(route('delivery.saveToInventory', requestId));
}

function requestAgain() {
    Inertia.get(route("requests.index"), {
        requestAgain: true,
    });
}

</script>

<template>
    <Head title="Request" />
    <LoadingModal :isLoading="isLoading" />

    <div v-if="message== 'Pending' || message== 'Delivery Approved' || message== 'Pickup Approved'"  class="flex flex-col justify-center items-center px-6 mx-auto h-screen xl:px-0 dark:bg-gray-900">
        <div class="block mb-5 md:w-44 w-36 -mt-10">
            <div v-if="message== 'Pending'">
                <img src="/storage/icons/general/pending.svg">
            </div>
            <div v-if="message== 'Delivery Approved'">
                <img src="/storage/icons/general/delivery.svg" >
            </div>
            <div v-if="message== 'Pickup Approved'">
                <img src="/storage/icons/general/pickup.svg" >
            </div>
        </div>
        <div class="text-center xl:max-w-xl">
            <div v-if="message == 'Pending'">
                <h1 class="mb-5 text-3xl font-bold leading-tight text-gray-900 sm:text-3xl lg:text-4xl dark:text-white">Pending Request</h1>
                <p class="text-base px-10 font-normal text-gray-700 md:text-lg dark:text-gray-400">You already have submitted a request. Just wait for the Admin to approve your request.</p>
                <button
                   class="text-white my-5 bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center mr-3 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                    @click="requestAgain()"
                >
                    Submit Another Request
                </button>
            </div>
            <div v-if="message== 'Delivery Approved'">
                <h1 class="mb-5 text-3xl font-bold leading-tight text-gray-900 sm:text-3xl lg:text-4xl dark:text-white">Ongoing Request</h1>
                <p class="text-base px-10 mb-10 font-normal text-gray-700 md:text-lg dark:text-gray-400">Your request is now currently being processed. You will be notified when your request is on delivery.</p>
            </div>
            <div v-if="message== 'Pickup Approved'">
                <h1 class="mb-5 text-2xl font-bold leading-tight text-gray-900 lg:text-3xl dark:text-white">Supplies are ready for Pickup</h1>
                <p class="text-base px-10 font-normal text-gray-700 md:text-lg dark:text-gray-400">Supplies are now ready for pickup at RHU {{ rhu }}.</p>
                <div v-if="props.pickup_date">
                    <p v-if="props.pickup_date.pickup_date" class="text-base px-10 mb-10 font-normal text-gray-700 md:text-lg dark:text-gray-400">Your schedule for pickup is <span class="text-gray-900 font-bold">{{ pickupDate() }}</span>.</p>
                    <p v-else class="text-base px-10 mb-10 font-normal text-gray-700 md:text-lg dark:text-gray-400">You can pickup your supplies <span class="text-gray-900 font-bold">anytime.</span></p>
                </div>
            </div>
            <Link :href="('/dashboard')" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center mr-3 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                <svg class="mr-2 -ml-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                Go back home
            </Link>
        </div>
    </div>


    <div v-if="message == 'Ongoing'">
        <div class="flex flex-col justify-center items-center dark:bg-gray-900">
            <div class="block mb-2 md:w-25 w-36 mt-5">
                <img src="/storage/icons/general/delivery.svg" />
            </div>
            <h1 class="mb-5 text-3xl lg:text-2xl font-bold leading-tight text-gray-900  dark:text-white">Request On Delivery</h1>
                <div class="text-base text-center font-normal px-10 mb-10 text-gray-700 md:text-md  dark:text-gray-400">
                    <p>Your request is now currently being delivered to your location. Kindly wait for the delivery to arrive.</p>
                    <p>Kindly confirm receipt only after you have received the supplies, so you can report any <span class="font-bold text-red-600">damages</span> or <span class="font-bold text-red-600">missing</span> items.</p>
                </div>
        </div>
        <div class="grid grid-cols-1 gap-5">
            <div v-for="request in requests" class="col-span-1">
                <div class="relative sm:rounded-lg border border-gray-300 bg-white">
                    <div class="p-2 px-5">
                        <!-- Ongoing Details  -->
                        <div
                            v-if="request.status === 'Ongoing'"
                        >
                            <div class="text-left px-2">
                                <p
                                    class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                >
                                    Request details
                                </p>
                            </div>
                            <!-- Approve detail  -->
                            <div
                                class="grid grid-cols-2 md:grid-cols-3 px-5 mt-2 gap-3 mb-3"
                            >
                                <div
                                    class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                >
                                    <span
                                        class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                    >
                                        Approved by
                                    </span>
                                    <span
                                        class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                    >
                                        {{
                                            request.approver_position
                                        }}
                                        :
                                        {{ request.approver_name }}
                                    </span>
                                </div>
                                <div
                                    class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                >
                                    <span
                                        class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                    >
                                        Date approved
                                    </span>
                                    <span
                                        class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                    >
                                    <formattedDate
                                            :date="`${request.date_approved}`"
                                        ></formattedDate>
                                    </span>
                                </div>
                                <div
                                    class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                >
                                <span
                                    class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                >
                                    Delivery personal
                                </span>
                                <span
                                    class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                >
                                    {{ request.delivery.delivery_name}}
                                </span>
                                </div>
                            </div>
                        </div>

                        <!-- Supplies to be delivered -->
                        <div v-if="request.deliveredItems">
                            <div class="text-left px-2">
                                <p
                                    class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                >
                                    Supplies to be delivered
                                </p>
                            </div>
                            <div
                                class="grid lg:grid-cols-3 grid-cols-1 gap-5 px-5 mt-2 max-w-screen-md"
                            >
                            <div v-if="request.deliveredItems.medicines && request.deliveredItems.medicines.length > 0"
                            class="flex flex-row justify-between gap-5 max-w-sm p-2 bg-green-100 border border-green-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
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
                                        <table
                                            class="w-full"
                                        >
                                            <thead>
                                                <tr
                                                    class="bg-gray-100"
                                                >
                                                    <th
                                                        class="px-2 text-center"
                                                    >
                                                        Total
                                                    </th>
                                                    <th
                                                        class="px-2"
                                                    >
                                                        Name
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                v-for="medicine in request?.deliveredItems.medicines"
                                            >
                                                <tr
                                                    :key="
                                                        medicine.id
                                                    "
                                                    class="bg-white"
                                                >
                                                    <td
                                                        class="px-2 text-center"
                                                    >
                                                        {{
                                                            medicine.quantity
                                                        }}
                                                    </td>
                                                    <td
                                                        class="px-2"
                                                    >
                                                        {{
                                                            medicine.name
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
                        <div v-if="request.deliveredItems.equipments && request.deliveredItems.equipments.length > 0"
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
                                        <table
                                            class="w-full"
                                        >
                                            <thead>
                                                <tr
                                                    class="bg-gray-100"
                                                >
                                                    <th
                                                        class="px-2 text-center"
                                                    >
                                                        Total
                                                    </th>
                                                    <th
                                                        class="px-2"
                                                    >
                                                        Name
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                v-for="equipment in request?.deliveredItems.equipments"
                                            >
                                                <tr
                                                    :key="
                                                        equipment.id
                                                    "
                                                >
                                                    <td
                                                        class="px-2 text-center"
                                                    >
                                                        {{
                                                            equipment.quantity
                                                        }}
                                                    </td>
                                                    <td
                                                        class="px-2"
                                                    >
                                                        {{
                                                            equipment.name
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="request.deliveredItems.supplies && request.deliveredItems.supplies.length > 0"
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
                                        <table
                                            class="w-full"
                                        >
                                            <thead>
                                                <tr
                                                    class="bg-gray-100"
                                                >
                                                    <th
                                                        class="px-2 text-center"
                                                    >
                                                        Total
                                                    </th>
                                                    <th
                                                        class="px-2"
                                                    >
                                                        Name
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                v-for="supply in request?.deliveredItems.supplies"
                                            >
                                                <tr
                                                    :key="
                                                        supply.id
                                                    "
                                                >
                                                    <td
                                                        class="px-2 text-center"
                                                    >
                                                        {{
                                                            supply.quantity
                                                        }}
                                                    </td>
                                                    <td
                                                        class="px-2"
                                                    >
                                                        {{
                                                            supply.name
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

                        <!-- Buttons -->
                        <div class="p-2 my-3">

                            <div class="flex flex-col space-y-2 md:hidden">
                                <button
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 justify-center inline-flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                                    @click="openConfirmModal(request.id)"
                                >
                                    Confirm as delivered
                                </button>

                                <button
                                    @click="
                                        reportDelivery(request.id)
                                    "
                                    :key="request.id"
                                    type="button"
                                    class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-yellow-500 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 focus:outline-none dark:focus:ring-yellow-800"
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
                                    Confirm and Report Problem
                                </button>

                                <button
                                    @click="
                                    openReturnModal(request.id)
                                    "
                                    :key="request.id"
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
                                    Return Delivery
                                </button>

                            </div>

                            <div class="hidden md:flex justify-end space-x-2">
                                <button
                                    @click="
                                    openReturnModal(request.id)
                                    "
                                    :key="request.id"
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
                                    Return Delivery
                                </button>

                                <button
                                    @click="
                                        reportDelivery(request.id)
                                    "
                                    :key="request.id"
                                    type="button"
                                    class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-yellow-500 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 focus:outline-none dark:focus:ring-yellow-800"
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
                                    Confirm and Report Problem
                                </button>

                                <button
                                    class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center mr-3 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800"
                                    @click="openConfirmModal(request.id)"
                                >
                                    Confirm as delivered
                                </button>
                            </div>
                        </div>
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

        <!-- Return Modal -->
        <div
        v-if="showReturnModal"
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
                    Return delivery?
                </h3>
                <div
                    class="mb-4 text-sm font-light p-4 text-gray-500 dark:text-gray-400"
                >
                    <div class="col-span-1">
                        <label
                            class="block mb-2 font-medium text-md text-gray-800 dark:text-white"
                            for="threshold"
                            >Select reason for returning:
                        </label>
                            <select v-model="returnReason"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="Incomplete/Missing supplies">Incomplete/Missing supplies</option>
                                <option value="Damaged or Contaminated">Damaged or Contaminated</option>
                                <option value="Transport Issues">Transport Issues</option>
                                <option value="Delivery Timeliness">Delivery Timeliness</option>
                                <option value="Other">Other</option>
                        </select>
                    </div>
                    <div v-if="returnReason == 'Other'" class="col-span-1">
                        <label
                            for="description"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                        >
                            Please specify:
                        </label>
                        <textarea
                            v-model="otherText"
                            type="text"
                            rows="2"
                            id="description"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            placeholder="Write your delivery problems"
                        ></textarea>
                    </div>
                </div>
                <div
                    class="flex justify-center mt-4"
                >
                    <button
                        @click="
                            closeReturnModal()
                        "
                        class="py-2 px-4 bg-gray-300 rounded-md"
                    >
                        Cancel
                    </button>
                    <button
                        @click="
                            returnDelivery(selectedRequestID)
                        "
                        class="py-2 px-4 bg-blue-700 text-white rounded-md ml-2"
                    >
                        Return
                    </button>
                </div>
            </div>
        </div>
        </div>
    </div>

</template>
