<script setup>
import { onMounted, } from "vue";
import { initFlowbite } from "flowbite";

const props = defineProps({
    request: Object,
    ongoingDeliveries: Object,
});
const emit = defineEmits();

function filteredOngoingDeliveries(request) {
    return Object.values(props.ongoingDeliveries.data).filter(
        (delivery) => delivery.request_id === request.id
    );
}

onMounted(() => {
    initFlowbite();
});

function resetForm() {
    emit('closeAddModal');
}
</script>

<template>
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div
                class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600"
            >
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Ongoing Delivery Details
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
            <div>
                <div>
                    <div>
                        <div class="p-2 px-5 overflow-y-auto">
                            <!-- Ongoing Details  -->
                            <div
                                v-if="request.status !== 'Rejected' && request.status !== 'Pending'"
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
                                            <div v-if="delivery.for_pickup"
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

                            <!-- Requested supplies -->
                            <div v-if="request.medicine_requests && request.medicine_requests.length > 0">
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
                                    <div
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
                                    <p
                                        class="p-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white group"
                                    >
                                        Supplies to be Delivered
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
