<script setup>

const props = defineProps({

    statusCounts: Object,
});

</script>


<template>
    <div
        class="grid grid-cols-2 rounded-lg md:h-30 lg:h-60 col-span-2 xl:col-span-1 border-gray-300 gap-5 dark:border-gray-600"
    >
        <!-- Inventory card -->
        <div
        class="colspan-1 flex flex-row gap-5 p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg class="w-8 h-8 md:h-10 md:w-10 text-green-500  " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Z"/>
                        <path fill-rule="evenodd" d="M11 7V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm4.707 5.707a1 1 0 0 0-1.414-1.414L11 14.586l-1.293-1.293a1 1 0 0 0-1.414 1.414l2 2a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                      </svg>
                </span>
            </div>
            <div
                class="flex flex-col items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Total Delivered</span>
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{statusCounts.delivered_count ?? 0}}
                    </h5>
                </div>
            </div>
        </div>

        <!-- Deliveries card -->
        <div
        class="colspan-1 flex flex-row gap-5 p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <span>
                        <svg class="w-8 h-8 md:w-10 md:h-10 text-violet-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                          </svg>
                    </span>
                </span>
            </div>
            <div
                class="flex flex-col items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Pending Request</span>
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{statusCounts.pending_count ?? 0}}
                    </h5>
                </div>
            </div>
        </div>

        <!-- Stock card -->
        <div
        class="colspan-1 flex flex-row gap-5 p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg class="w-8 h-8 md:h-10 md:w-10 text-blue-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2.586 4.586A2 2 0 0 1 4 4h8a2 2 0 0 1 2 2h5a1 1 0 0 1 .894.553l2 4c.07.139.106.292.106.447v4a1 1 0 0 1-1 1h-.535a3.5 3.5 0 1 1-6.93 0h-3.07a3.5 3.5 0 1 1-6.93 0H3a1 1 0 0 1-1-1V6a2 2 0 0 1 .586-1.414ZM18.208 15.61a1.497 1.497 0 0 0-2.416 0 1.5 1.5 0 1 0 2.416 0Zm-10 0a1.498 1.498 0 0 0-2.416 0 1.5 1.5 0 1 0 2.416 0Zm5.79-7.612v2.02h5.396l-1-2.02h-4.396ZM9 8.667a1 1 0 1 0-2 0V10a1 1 0 0 0 .293.707l1.5 1.5a1 1 0 0 0 1.414-1.414L9 9.586v-.92Z" clip-rule="evenodd"/>
                      </svg>
                </span>
            </div>
            <div
                class="flex flex-col items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Ongoing Delivery</span
                    >
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{statusCounts.ongoing_count ?? 0}}
                    </h5>
                </div>
            </div>
        </div>

        <!-- Delivery Report -->
        <div
        class="colspan-1 flex flex-row gap-5 p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg class="w-9 h-9 text-yellow-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11.5c.07 0 .14-.007.207-.021.095.014.193.021.293.021h2a2 2 0 0 0 2-2V7a1 1 0 0 0-1-1h-1a1 1 0 1 0 0 2v11h-2V5a2 2 0 0 0-2-2H5Zm7 4a1 1 0 0 1 1-1h.5a1 1 0 1 1 0 2H13a1 1 0 0 1-1-1Zm0 3a1 1 0 0 1 1-1h.5a1 1 0 1 1 0 2H13a1 1 0 0 1-1-1Zm-6 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1Zm0 3a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1ZM7 6a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H7Zm1 3V8h1v1H8Z" clip-rule="evenodd"/>
                      </svg>
                </span>
            </div>
            <div
                class="flex flex-col items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Delivery Reports</span
                    >
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{statusCounts.reported_count ?? 0}}
                    </h5>
                </div>
            </div>
        </div>
    </div>
</template>
