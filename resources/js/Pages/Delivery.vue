<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { initFlowbite } from "flowbite";
import { Inertia } from "@inertiajs/inertia";
import { Link, router, usePage } from "@inertiajs/vue3";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import DeliveryTab from "@/Components/DeliveryTab.vue";
import Pagination from "@/Components/Pagination.vue";
import DeliveryPaginate from "@/Components/DeliveryPaginate.vue";
import { useForm } from "@inertiajs/inertia-vue3";
import LoadingModal from "@/Components/LoadingModal.vue";
import DeliveryCards from "@/Components/deliveryCards.vue";
import DeliveryViewModal from "@/Components/DeliveryViewModal.vue";

defineOptions({ layout: DashboardLayout });

// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
});

const props = defineProps({
    requests: Object,
    ongoingDeliveries: Object,
    ongoingRequests: Object,
    userRHU: Object,
    statusCounts: Object,
});

const isLoading = ref(false);

const selectedTab = ref("all");
const tabOptions = [
    { label: "All", value: "all" },
    { label: "Approved", value: "approved" },
    { label: "Pending", value: "pending" },
    { label: "Ongoing", value: "ongoing" },
    { label: "Claimed", value: "claimed" },
    { label: "Delivered", value: "delivered" },
    { label: "Delivered with Report", value: "report" },
    { label: "Returned", value: "returned" },
    { label: "Rejected", value: "rejected" },
];

const handleTabSelected = (selectedTabValue) => {
    selectedTab.value = selectedTabValue;
    updateQuery(selectedTabValue);
};

function updateQuery(selectedTabValue) {
    router.get(
        "/delivery",
        {
            status: selectedTabValue,
            sort: sortField.value,
            direction: sortDirection.value,
        },
        { preserveState: true }
    );
}
const sortField = ref("id"); // Default sort field
const sortDirection = ref("asc"); // Default sort direction

function sort(field) {
    if (field === sortField.value) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortDirection.value = "asc";
    }
    updateQuery();
}
const getStatusClasses = (status) => {
    switch (status) {
        case "Rejected":
            return "bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300";
        case "Ongoing":
            return "bg-blue-200 text-blue-800 dark:bg-blue-900 dark:text-blue-300";
        case "Delivered":
            return "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300";
        case "Pending":
            return "bg-yellow-200 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300";
        case "Delivery Approved":
            return "bg-purple-200 text-purple-800 dark:bg-purple-900 dark:text-purple-300";
        case "Pickup Approved":
            return "bg-purple-200 text-purple-800 dark:bg-purple-900 dark:text-purple-300";
        case "Returned":
            return "bg-orange-200 text-orange-800 dark:bg-orange-900 dark:text-orange-300";
        case "Delivered with Report":
            return "bg-lime-200 text-lime-800 dark:bg-lime-900 dark:text-yellow-300";
        case "Claimed":
            return "bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300";

        default:
            return "bg-[#FFFFFF] text-black px-2 py-1 rounded";
    }
};

const activeAccordion = ref(null);
const toggleAccordion = (itemId) => {
    if (activeAccordion.value && activeAccordion.value !== itemId) {
        const previousBodyRow = document.getElementById(
            `accordion-collapse-body-${activeAccordion.value}`
        );
        if (previousBodyRow) {
            previousBodyRow.classList.add("hidden");
        }
    }

    const bodyRow = document.getElementById(
        `accordion-collapse-body-${itemId}`
    );
    if (bodyRow) {
        bodyRow.classList.toggle("hidden");
    }

    activeAccordion.value = bodyRow.classList.contains("hidden")
        ? null
        : itemId;
};

function filteredOngoingDeliveries(request) {
    return Object.values(props.ongoingDeliveries.data).filter(
        (delivery) => delivery.request_id === request.id
    );
}

function openApproveRequest(request) {
    Inertia.visit(`/approve-request/${request.id}`, {
        method: "get",
    });
}

const showRejectModal = ref(false);
const showDeliverModal = ref(false);
const showClaimModal = ref(false);
const showViewModal = ref(false);

const requestToReject = ref(null);
const requestToDeliver = ref(null);
const requestToClaim = ref(null);
const requestToView = ref(null);

const form = useForm({});
const otherText = ref('');
const rejectReason = ref("Insufficient Stock/Inventory");

const selectedRequest = ref(null);
const getRequestDetail = (id) => {


};

// Open modal functions
const openRejectModal = (request) => {
    requestToReject.value = request;
    showRejectModal.value = true;
};

const openDeliverModal = (request) => {
    requestToDeliver.value = request;
    showDeliverModal.value = true;
};

const openClaimModal = (request) => {
    requestToClaim.value = request;
    showClaimModal.value = true;
};

const openViewModal = (id) => {
    selectedRequest.value = props.ongoingRequests.find(request => request.id === id);
    showViewModal.value = true;
};

const closeModal = () => {
    showClaimModal.value = false;
    showDeliverModal.value = false;
    showRejectModal.value = false;
    showViewModal.value = false;
};


// Function to handle rejection request
function rejectRequest(request_id) {
    closeModal();
    isLoading.value = true;
    if(rejectReason.value =="Other"){
        rejectReason.value = otherText.value;
    }

    Inertia.post(route('request.rejectRequest'), {
        request_id: request_id,
        rejectReason: rejectReason.value,
    })
        .then(response => {
            isLoading.value = false;
        })
        .catch(error => {
            console.error(error);
            isLoading.value = false;
        });
}

// Function to handle marking as delivered
function markAsOnDelivery(requestId) {
    closeModal();
    isLoading.value = true;
    form.put(route("delivery.update", {
        delivery: requestId
    } ), {
        onSuccess: () => {
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        },
    });
}

function markAsClaimed(requestId) {
    closeModal();
    isLoading.value = true;
    form.post(route("delivery.markAsClaimed", {
        requestId: requestId
    } ), {
        onSuccess: () => {
            isLoading.value = false;
        },
        onError: () => {
            isLoading.value = false;
        },
    });
}
</script>

<template>
    <Head title="Delivery" />
    <LoadingModal :isLoading="isLoading" />

    <div v-if="showViewModal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
        >
        <DeliveryViewModal
                :request="selectedRequest"
                :ongoingDeliveries="props.ongoingDeliveries"
                @closeAddModal="closeModal()" />
        </div>

    <div class="grid grid-cols-1 gap-4 mb-4 lg:grid-cols=1 xl:grid-cols-2">
        <!-- delivery levels -->
        <DeliveryCards :statusCounts="props.statusCounts" />

        <!-- Ongoing Delivery -->
        <div
            class="col-span-2 xl:col-span-1 p-3 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
        >
            <div
                v-if="ongoingDeliveries.data == 0"
                class="flex flex-col items-center w-full"
            >
                <div class="block m-5 md:w-30 w-20">
                    <img src="/storage/icons/general/delivery.svg" />
                </div>
                <p class="text-xl font-bold">You're all caught up!</p>
                <p class="text-gray-600">You have no ongoing deliveries</p>
            </div>
            <div v-for="ongoing in ongoingDeliveries.data">
                <div class="flex flex-row items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        Ongoing Delivery
                    </h2>
                    <a href="#" @click="openViewModal(ongoing.request_id)" class="text-xs text-blue-500 dark:text-blue-400"
                        >View Details</a
                    >
                </div>

                <div class="grid grid-cols-3">
                    <div class="col-span-1 flex flex-col items-center">
                        <div class="mt-2 mb-2">
                            <p
                                class="col-span-1 text-sm text-gray-500 dark:text-gray-400"
                            >
                                Delivery ID : {{ ongoing.id }}
                            </p>
                            <p
                                class="col-span-1 text-sm text-gray-500 dark:text-gray-400"
                            >
                                Delivery Personnel : {{ ongoing.delivery_name }}
                            </p>
                        </div>
                        <div class="flex flex-col items-center w-full p-2">
                            <div class="flex items-center mb-1">
                                <div
                                    class="w-3 h-3 bg-red-600 rounded-full mr-2"
                                ></div>
                                <p
                                    class="text-sm text-gray-900 dark:text-white font-bold"
                                >
                                    RHU {{ ongoing.rhu_id }}
                                </p>
                            </div>
                            <div>
                                <div class="flex items-center mb-1">
                                    <div
                                        class="w-0.5 h-0.5 bg-black rounded-full ml-1"
                                    ></div>
                                </div>
                                <div class="flex items-center mb-1">
                                    <div
                                        class="w-0.5 h-0.5 bg-black rounded-full ml-1"
                                    ></div>
                                </div>
                                <div class="flex items-center mb-1">
                                    <div
                                        class="w-0.5 h-0.5 bg-black rounded-full ml-1"
                                    ></div>
                                </div>
                            </div>
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-3 h-3 bg-green-600 rounded-full mr-2"
                                ></div>
                                <p
                                    class="text-sm text-gray-900 dark:text-white font-bold"
                                >
                                    <span
                                        v-if="ongoing && ongoing.destination_id"
                                    >
                                        {{ ongoing.barangay.barangay_name }}
                                    </span>
                                    <span v-else>
                                        RHU {{ ongoing.destination_rhu_id }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 ml-6 px-3 mt-2">
                        <div class="">
                            <div class="">
                                <table
                                    class="w-full text-sm text-center rtl:text-right text-gray-500 dark:text-gray-400"
                                >
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400 sticky top-0"
                                    >
                                        <tr>
                                            <th scope="col" class="px-8 py-2">
                                                Item
                                            </th>
                                            <th scope="col" class="px-3 py-2">
                                                Quantity
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                                <div
                                    class="overflow-y-auto"
                                    style="max-height: 80px"
                                >
                                    <table
                                        class="w-full text-sm text-center rtl:text-right text-gray-500 dark:text-gray-400"
                                    >
                                        <tbody>
                                            <tr
                                                v-for="item in ongoing.deliveryItems.medicines"
                                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                            >
                                                <th
                                                    scope="row"
                                                    class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                                >
                                                    {{ item.name }}
                                                </th>
                                                <td class="px-8 py-2">
                                                    {{ item.quantity }}
                                                </td>
                                            </tr>
                                            <tr
                                            v-for="item in ongoing.deliveryItems.equipments"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <th
                                                scope="row"
                                                class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                            >
                                                {{ item.name }}
                                            </th>
                                            <td class="px-8 py-2">
                                                {{ item.quantity }}
                                            </td>
                                        </tr>
                                        <tr
                                        v-for="item in ongoing.deliveryItems.supplies"
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                    >
                                        <th
                                            scope="row"
                                            class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white"
                                        >
                                            {{ item.name }}
                                        </th>
                                        <td class="px-8 py-2">
                                            {{ item.quantity }}
                                        </td>
                                    </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <Pagination :paginator="ongoingDeliveries"/>
            </div>
        </div>

        <!-- Request list -->
        <div
            class="col-span-2 grid-cols-2 bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
        >
            <div class="p-4">
                <h2
                    class="text-xl mb-5 font-bold text-gray-900 dark:text-white"
                >
                    Request List
                </h2>

                <DeliveryTab
                    :initialSelectedTab="selectedTab"
                    :tabOptions="tabOptions"
                    @tab-selected="handleTabSelected"
                />
            </div>
            <!-- Table -->
            <div class="relative overflow-x-auto sm:rounded-lg">
                <table
                    class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400"
                >
                    <thead
                        class="text-xs text-black uppercase text-center bg-gray-50 dark:bg-gray-700 dark:text-gray-400"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">ID</th>
                            <th scope="col" class="px-6 py-3 text-center">
                                <div class="flex justify-center items-center">
                                    Status
                                    <a href="#" @click.prevent="sort('status')">
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
                            <th scope="col" class="px-6 py-3 text-left">
                                Barangay
                            </th>
                            <th scope="col" class="px-6 py-3 text-left">
                                Requested by
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                <div class="flex justify-center items-center">
                                    Date
                                    <a
                                        href="#"
                                        @click.prevent="sort('requested_at')"
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
                    v-if="
                        !requests.data ||
                        requests.data.length === 0
                    "
                >
                    <tr>
                        <td
                            colspan="8"
                            class="text-center py-4 text-gray-500 dark:text-gray-400"
                        >
                            No data available
                        </td>
                    </tr>
                </tbody>
                    <tbody
                        v-for="request in requests.data"
                        :key="request.id"
                        data-accordion="collapse"
                    >
                        <tr
                            :key="request.id"
                            :id="`accordion-collapse-heading-${request.id}`"
                            class="bg-white border-b text-center text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                            @click="toggleAccordion(request.id)"
                        >
                            <td class="px-6 py-6 text-left">
                                {{ request.id }}
                            </td>
                            <td class="px-6 whitespace-nowrap py-4 text-center">
                                <span
                                    :class="getStatusClasses(request.status)"
                                    class="text-xs font-medium me-2 px-3 py-1 rounded-full"
                                >
                                    {{ request.status }}
                                </span>
                            </td>
                            <th
                                scope="row"
                                class="px-6 py-4 font-semibold text-left text-gray-900 whitespace-nowrap dark:text-white"
                            >
                                <div v-if="request && request.barangay">
                                    {{ request.barangay.barangay_name }}
                                </div>
                                <div v-else>
                                    RHU {{ request.requester_user.rhu_id }}
                                </div>
                            </th>
                            <td class="px-6 py-4 text-left">
                                {{ request.requester_user.name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ request.requested_at }}
                            </td>
                        </tr>

                        <tr
                            :id="`accordion-collapse-body-${request.id}`"
                            :aria-labelledby="`accordion-collapse-heading-${request.id}`"
                            v-show="activeAccordion === request.id"
                            class="hidden w-full bg-gray-50 border-b text-gray-900 dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                        >
                            <td class="text-center" colspan="10">
                                <div class="p-2 px-5">

                                    <!-- Reason for Returning -->
                                    <div
                                        v-if="request.status == 'Returned'"
                                    >
                                        <div class="text-left px-2">
                                            <p
                                                class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                            >
                                                Return Details
                                            </p>
                                        </div>
                                        <!-- Return detail  -->
                                        <div
                                            class="grid grid-cols-2 px-5 mt-2 gap-5 max-w-screen-md"
                                        >
                                            <div
                                                class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                            >
                                                <span
                                                    class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                >
                                                    Reason for returning
                                                </span>
                                                <span
                                                    class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                >
                                                    {{
                                                        request.returnReason
                                                    }}
                                                </span>
                                            </div>
                                            <div
                                                class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                            >
                                                <span
                                                    class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                >
                                                    Date returned
                                                </span>
                                                <span
                                                    class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                >
                                                    {{ request.returnDate }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Ongoing Details  -->
                                    <div v-if="request.status !== 'Rejected' && request.status !== 'Pending'"
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
                                            class="grid grid-cols-2 px-5 mt-2 gap-5 max-w-screen-md"
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
                                                    {{ request.date_approved }}
                                                </span>
                                            </div>
                                        </div>
                                        <!-- Delivery detail  -->
                                         <div>
                                            <div
                                                v-for="delivery in filteredOngoingDeliveries(
                                                    request
                                                )"
                                                class="grid grid-cols-2 px-5 py-2 gap-5 max-w-screen-md"
                                            >
                                                    <div v-if="delivery.pickup_date"
                                                        class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                                    >
                                                        <span
                                                            class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                        >
                                                            Pickup Schedule
                                                        </span>
                                                        <span
                                                            class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                        >
                                                            {{ delivery.pickup_date}}
                                                        </span>
                                                    </div>
                                                    <div v-if="delivery"
                                                        class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                                    >
                                                        <span
                                                            class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                        >
                                                            Pickup Person
                                                        </span>
                                                        <span
                                                            class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                        >
                                                            {{
                                                                request.requester_user.name
                                                            }}
                                                        </span>
                                                    </div>
                                                    <div v-if="request.status == 'Claimed'"
                                                        class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                                    >
                                                        <span
                                                            class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                        >
                                                            Date claimed
                                                        </span>
                                                        <span
                                                            class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                        >
                                                            {{
                                                                delivery.updated_at
                                                            }}
                                                        </span>
                                                    </div>
                                                    <div v-if="delivery.delivery_name"
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
                                                            {{ delivery.delivery_name }}
                                                        </span>
                                                    </div>
                                                    <div v-if="!delivery.for_pickup"
                                                        class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                                    >
                                                        <span
                                                            class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                        >
                                                            Destination
                                                        </span>
                                                        <span
                                                            class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                        >
                                                            {{
                                                                delivery.barangay
                                                                    ?.barangay_name ??
                                                                ""
                                                            }}
                                                        </span>
                                                    </div>
                                            </div>
                                         </div>
                                    </div>

                                    <!-- Report Detail  -->
                                    <div v-if="request.deliveryProblem"
                                    >
                                        <div class="text-left px-2">
                                            <p
                                                class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                            >
                                                Reported issues
                                            </p>
                                        </div>
                                        <div
                                            class="grid grid-cols-1 px-5 mt-2 gap-5 max-w-screen-md"
                                        >

                                            <!-- Table for Incorrect Quantities -->
                                            <div v-if="request.deliveryProblem.incorrect_quantity && request.deliveryProblem.incorrect_quantity.length > 0"
                                                class="col-span-1 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm">
                                                <span
                                                    class="block text-base mb-2 font-semibold text-gray-900 truncate dark:text-white"
                                                >
                                                    Incorrect Quantity
                                                </span>
                                                <table class="min-w-full bg-white border border-gray-300">
                                                    <thead>
                                                        <tr>
                                                            <th class="px-4 py-2 border">Supply</th>
                                                            <th class="px-4 py-2 border">Missing Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in request.deliveryProblem.incorrect_quantity" :key="index">
                                                            <td class="px-4 py-2 border">{{ item.supply }}</td>
                                                            <td class="px-4 py-2 border">{{ item.quantity }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- Table for Broken Equipment -->
                                            <div v-if="request.deliveryProblem.broken_equipment && request.deliveryProblem.broken_equipment.length > 0"
                                                class="col-span-1 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm">
                                                <span
                                                    class="block text-base mb-2 font-semibold text-gray-900 truncate dark:text-white"
                                                >
                                                    Broken Equipment
                                                </span>
                                                <table v-if="request.deliveryProblem.broken_equipment && request.deliveryProblem.broken_equipment.length > 0" class="min-w-full bg-white border border-gray-300">
                                                    <thead>
                                                        <tr>
                                                            <th class="px-4 py-2 border">Equipment Name</th>
                                                            <th class="px-4 py-2 border">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item, index) in request.deliveryProblem.broken_equipment" :key="index">
                                                            <td class="px-4 py-2 border">{{ item.equipment }}</td>
                                                            <td class="px-4 py-2 border">{{ item.quantity }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Other -->
                                        <div
                                            class="grid grid-cols-2 px-5 mt-2 gap-5 max-w-screen-md"
                                        >
                                            <div v-if="request.deliveryProblem.other_text"
                                                class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                            >
                                                <span
                                                    class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                >
                                                    Other issues
                                                </span>
                                                <span
                                                    class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                >
                                                    {{ request.deliveryProblem.other_text }}
                                                </span>
                                            </div>
                                            <div v-if="request.deliveryProblem.wrong_supply_text"
                                                class="col-span-1 flex-1 min-w-0 text-left bg-white border border-gray-300 rounded-md p-2 px-4 shadow-sm"
                                            >
                                                <span
                                                    class="block text-base font-semibold text-gray-900 truncate dark:text-white"
                                                >
                                                    Wrong Supply Received
                                                </span>
                                                <span
                                                    class="block text-sm font-normal text-gray-500 truncate dark:text-gray-400"
                                                >
                                                    {{ request.deliveryProblem.wrong_supply_text }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Requested supplies -->
                                    <div>
                                        <div class="text-left px-2">
                                            <p
                                                class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                            >
                                                Requested supplies
                                            </p>
                                        </div>
                                        <div
                                            class="grid lg:grid-cols-3 grid-cols-1 gap-5 p-2 max-w-screen-md"
                                        >
                                            <!-- Medicine -->
                                            <div v-if="request.medicine_requests && request.medicine_requests.length > 0"
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
                                                                    v-for="medicine in request.medicine_requests"
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
                                            <!-- Equipment -->
                                            <div v-if="request.equipment_requests && request.equipment_requests.length > 0"
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
                                                                    v-for="equipment in request.equipment_requests"
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
                                            <!-- Med Supply -->
                                            <div v-if="request.supply_requests && request.supply_requests.length > 0"
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
                                                                    v-for="supply in request.supply_requests"
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

                                    <!-- Supplies to be delivered -->
                                    <div v-if="request?.deliveredItems"
                                    >
                                        <div class="text-left px-2">
                                            <p v-if="request.status=='Returned'"
                                                class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                            >
                                                Supplies Returned
                                            </p>
                                            <p v-if="request.status=='Claimed'"
                                                class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                            >
                                                Supplies Claimed
                                            </p>
                                            <p v-if="request.status.includes('Delivered')"
                                                class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                            >
                                                Supplies delivered
                                            </p>
                                        </div>
                                        <div
                                            class="grid lg:grid-cols-3 grid-cols-1 gap-5 p-2 max-w-screen-md"
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
                                    <div class="p-2 mb-3">
                                        <div class="flex justify-end space-x-2">
                                            <!-- Reject Button -->
                                            <button
                                                v-if="
                                                    request.status === 'Pending'
                                                "
                                                type="button"
                                                @click="
                                                    openRejectModal(request.id)
                                                "
                                                class="flex items-center justify-center font-medium rounded-lg text-sm px-3 py-2 text-white bg-red-700 hover:bg-red-800"
                                            >
                                                Reject
                                            </button>
                                            <!-- Approve Button -->
                                            <button
                                                v-if="
                                                    request.status === 'Pending'
                                                "
                                                @click="
                                                    openApproveRequest(request)
                                                "
                                                :key="request.id"
                                                type="button"
                                                class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
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
                                                Approve
                                            </button>
                                            <!-- Confirm Pickup -->
                                            <button
                                                v-if="
                                                    request.status === 'Pickup Approved'
                                                "
                                                type="button"
                                                @click="
                                                    openClaimModal(request.id)
                                                "
                                                class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-green-700 hover:bg-green-800"
                                            >
                                                Supplies Claimed
                                            </button>
                                            <!-- To Deliver -->
                                            <button
                                                v-if="
                                                    request.status === 'Delivery Approved'
                                                "
                                                type="button"
                                                @click="
                                                    openDeliverModal(request.id)
                                                "
                                                class="flex items-center justify-center font-medium rounded-lg text-sm gap-1 px-3 py-2 text-white bg-green-700 hover:bg-green-800"
                                            >
                                                Deliver
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :paginator="requests" :selectedTab="selectedTab" />
        </div>
    </div>

    <!-- Reject Modal -->
    <div
    v-if="showRejectModal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
>
    <div
    class="flex flex-col max-w-screen-lg items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8"
>
            <h3
                class="mb-3 text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
            >
               Reject Request?
            </h3>
            <div
                class="text-sm font-light p-4 text-gray-500 dark:text-gray-400"
            >
                <div class="">
                    <label
                        class="block mb-2 font-medium text-md text-gray-800 dark:text-white"
                        for="threshold"
                        >Select reason for rejecting:
                    </label>
                        <select v-model="rejectReason"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                            <option value="Insufficient Stock/Inventory">Insufficient Stock/Inventory</option>
                            <option value="Duplicate Request">Duplicate Request</option>
                            <option value="Other">Other (Please Specify)</option>
                    </select>
                </div>
                <div v-if="rejectReason == 'Other'">
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
                class="grid grid-cols-2 mt-4 w-full gap-10"
            >
                <button
                    @click="
                        closeModal
                    "
                    class="col-span-1 py-2 w-full bg-gray-300 rounded-md"
                >
                    Cancel
                </button>
                <button
                    @click="
                        rejectRequest(
                            requestToReject
                        )
                    "
                    class="col-span-1 py-2 w-full bg-red-700 text-white rounded-md ml-2"
                >
                    Reject
                </button>
            </div>
    </div>
</div>

<!-- Deliver Modal -->
<div
    v-if="showDeliverModal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
>
        <div
            class="flex flex-col items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8"
        >
            <h3
                class="mb-3 text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
            >
                Send supplies on delivery?
            </h3>
            <div
                class="mb-1 text-xs md:text-sm font-light p-4 text-gray-500 dark:text-gray-400"
            >
                <p
                    class="font-semibold text-gray-600"
                >
                    Make you the
                    supplies to be delivered are
                    complete.
                </p>
            </div>
            <div
                class="grid grid-cols-2 mt-4 w-full gap-10"
            >
                <button
                    @click="
                        closeModal()
                    "
                    class="col-span-1 py-2 w-full bg-gray-300 rounded-md"
                >
                    Cancel
                </button>
                <button
                    @click="
                        markAsOnDelivery(
                            requestToDeliver
                        )
                    "
                    class="col-span-1 py-2 w-full bg-blue-700 text-white rounded-md"
                >
                    Deliver
                </button>
            </div>
        </div>

</div>

<!-- Deliver Modal -->
<div
    v-if="showClaimModal"
    tabindex="-1"
    aria-hidden="true"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50"
>
        <div
            class="flex flex-col items-center p-4 bg-white rounded-lg shadow dark:bg-gray-800 md:p-8"
        >
            <h3
                class="mb-3 text-2xl md:text-3xl font-bold text-gray-900 dark:text-white"
            >
                Supplies already got claimed?
            </h3>
            <div
                class="mb-1 text-xs md:text-sm font-light p-4 text-gray-500 dark:text-gray-400"
            >
                <p
                    class="font-semibold text-gray-600"
                >
                    This is to confirm if the supplies are already pickup.
                </p>
            </div>
            <div
                class="grid grid-cols-2 mt-4 w-full gap-10"
            >
                <button
                    @click="
                        closeModal()
                    "
                    class="col-span-1 py-2 w-full bg-gray-300 rounded-md"
                >
                    Cancel
                </button>
                <button
                    @click="
                        markAsClaimed(
                            requestToClaim
                        )
                    "
                    class="col-span-1 py-2 w-full bg-blue-700 text-white rounded-md"
                >
                    Confirm
                </button>
            </div>
        </div>

</div>

</template>
