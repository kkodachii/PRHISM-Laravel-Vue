<template>
    <div class="grid grid-cols-1 gap-5 xl:gap-8 p-2 mb-2 md:grid-cols-2">
        <!-- Inventory Summary -->
        <div
            class="flex flex-col justify-between gap-3 p-3 bg-white border border-gray-200 rounded-lg shadow"
        >
            <p
                class="ml-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Supply Summary
            </p>
            <div class="flex flex-row justify-around w-full">
                <!-- Stock -->
                <Link
                    :href="
                        route('supply_summary.index', { status: 'On Stock' })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg

                                    class="flex-shrink-0 w-6 h-6 text-violet-500 transition duration-75 dark:text-violet-400 group-hover:text-violet-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M184 48H328c4.4 0 8 3.6 8 8V96H176V56c0-4.4 3.6-8 8-8zm-56 8V96v32V480H384V128 96 56c0-30.9-25.1-56-56-56H184c-30.9 0-56 25.1-56 56zM96 96H64C28.7 96 0 124.7 0 160V416c0 35.3 28.7 64 64 64H96V96zM416 480h32c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H416V480zM224 208c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16v48h48c8.8 0 16 7.2 16 16v32c0 8.8-7.2 16-16 16H288v48c0 8.8-7.2 16-16 16H240c-8.8 0-16-7.2-16-16V320H176c-8.8 0-16-7.2-16-16V272c0-8.8 7.2-16 16-16h48V208z"
                                    />
                                </svg>
                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ supplyStatus.stock }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            On stock Supplies
                        </p>
                    </div>
                </Link>

                <!-- Low -->
                <Link
                    :href="
                        route('supply_summary.index', {
                            status: 'Low on Stock',
                        })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg
                            class="w-6 h-6 text-yellow-300 dark:text-primary-dark"
                            viewBox="0 0 16 16"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                        >
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g
                                id="SVGRepo_tracerCarrier"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    fill="currentColor"
                                    fill-rule="evenodd"
                                    d="M9.71093422,1.9716895 C8.93428422,0.6761035 7.05680422,0.6761035 6.28015422,1.9716895 L0.287692215,11.9681595 C-0.511423785,13.3011595 0.448847215,14.9964595 2.00308422,14.9964595 L13.9880442,14.9964595 C15.5422442,14.9964595 16.5025442,13.3011595 15.7034442,11.9681595 L9.71093422,1.9716895 Z M7.99556422,9.9964595 L9.06454422,6.4331995 C9.27936422,5.7171295 8.74315422,4.9964595 7.99556422,4.9964595 C7.24796422,4.9964595 6.71176422,5.7171295 6.92658422,6.4331995 L7.99556422,9.9964595 Z M7.99554422,12.9964595 C8.54783422,12.9964595 8.99554422,12.5487595 8.99554422,11.9964595 C8.99554422,11.4441595 8.54783422,10.9964595 7.99554422,10.9964595 C7.44326422,10.9964595 6.99554422,11.4441595 6.99554422,11.9964595 C6.99554422,12.5487595 7.44326422,12.9964595 7.99554422,12.9964595 Z"
                                ></path>
                            </g>
                        </svg>
                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ supplyStatus.low }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Low on stock
                        </p>
                    </div>
                </Link>

                <Link
                :href="
                        route('supply_summary.index', { status: 'Out of stock' })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg
                            class="w-6 h-6 text-red-500 dark:text-primary-dark"
                            viewBox="0 0 16 16"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor"
                        >
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g
                                id="SVGRepo_tracerCarrier"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    fill="currentColor"
                                    fill-rule="evenodd"
                                    d="M9.71093422,1.9716895 C8.93428422,0.6761035 7.05680422,0.6761035 6.28015422,1.9716895 L0.287692215,11.9681595 C-0.511423785,13.3011595 0.448847215,14.9964595 2.00308422,14.9964595 L13.9880442,14.9964595 C15.5422442,14.9964595 16.5025442,13.3011595 15.7034442,11.9681595 L9.71093422,1.9716895 Z M7.99556422,9.9964595 L9.06454422,6.4331995 C9.27936422,5.7171295 8.74315422,4.9964595 7.99556422,4.9964595 C7.24796422,4.9964595 6.71176422,5.7171295 6.92658422,6.4331995 L7.99556422,9.9964595 Z M7.99554422,12.9964595 C8.54783422,12.9964595 8.99554422,12.5487595 8.99554422,11.9964595 C8.99554422,11.4441595 8.54783422,10.9964595 7.99554422,10.9964595 C7.44326422,10.9964595 6.99554422,11.4441595 6.99554422,11.9964595 C6.99554422,12.5487595 7.44326422,12.9964595 7.99554422,12.9964595 Z"
                                ></path>
                            </g>
                        </svg>
                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ supplyStatus.out }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Out of stock
                        </p>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Delivery Summary -->
        <div
            class="flex flex-col justify-between gap-3 p-3 bg-white border border-gray-200 rounded-lg shadow"
        >
            <p
                class="ml-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Barangay Summary
            </p>
            <div class="flex flex-row justify-around w-full">
                <!-- Repeated items for Delivery Summary (Dummy Data for illustration) -->
                <Link
                    :href="route('barangay.index')"
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg class="w-6 h-6 text-orange-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z" clip-rule="evenodd"/>
                          </svg>

                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ totalBarangay }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                           Total Barangays
                        </p>
                    </div>
                </Link>

                <Link
                    :href="route('barangay_list.index')"
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg class="w-6 h-6 text-gray-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M5.024 3.783A1 1 0 0 1 6 3h12a1 1 0 0 1 .976.783L20.802 12h-4.244a1.99 1.99 0 0 0-1.824 1.205 2.978 2.978 0 0 1-5.468 0A1.991 1.991 0 0 0 7.442 12H3.198l1.826-8.217ZM3 14v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5h-4.43a4.978 4.978 0 0 1-9.14 0H3Z" clip-rule="evenodd"/>
                          </svg>
                    </div>
                    <div class="flex flex-col text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ barangayInventory }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Stock Inventory
                        </p>
                    </div>
                </Link>
                <Link
                    :href="route('delivery.index')"
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg class="w-6 h-6 text-gray-700 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                          </svg>

                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ requestCount }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Total Requests
                        </p>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    supplyStatus: Array,
    totalCategory: Number,
    barangayInventory: Number,
    totalBarangay: Number,
    requestCount: Number,
});
</script>
