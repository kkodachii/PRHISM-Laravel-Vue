<script setup>
const props = defineProps({
    totalQuantity: Number,
    lowStock: Number,
    expiresoon: Number,
    requestCount: Number,
    dispenseCount: Number,
    userRole: Number,
});
</script>

<template>
    <div
        class="grid grid-cols-2 gap-5 xl:gap-8 p-2 mb-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4"
    >
        <!-- Inventory card -->
        <div
        class="flex flex-row gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg class="w-10 h-10 text-gray-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M5.024 3.783A1 1 0 0 1 6 3h12a1 1 0 0 1 .976.783L20.802 12h-4.244a1.99 1.99 0 0 0-1.824 1.205 2.978 2.978 0 0 1-5.468 0A1.991 1.991 0 0 0 7.442 12H3.198l1.826-8.217ZM3 14v5a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-5h-4.43a4.978 4.978 0 0 1-9.14 0H3Z" clip-rule="evenodd"/>
                      </svg>
                </span>
            </div>
            <div
                class="flex flex-col items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Total Inventory</span
                    >
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >{{totalQuantity}}
                    </h5>
                </div>
            </div>
        </div>

        <!-- Stock card -->
        <Link
            :href="route('inventory_filter.index', { status: 'Low on Stock' })"
             class="flex flex-row gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
            @click.stop
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg class="w-10 h-10 text-yellow-300 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4.5V19a1 1 0 0 0 1 1h15M7 10l4 4 4-4 5 5m0 0h-3.207M20 15v-3.207"/>
                      </svg>
                </span>
            </div>
            <div
                class="flex flex-col flex-grow items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Low on stock</span
                    >
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ lowStock }}
                    </h5>
                </div>
            </div>
        </Link>

        <!-- Expiring card -->
        <Link
            :href="route('inventory_filter.index', { status: 'Expiring' })"
             class="flex flex-row gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
            @click.stop
        >
            <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg
                            class="w-10 h-10 text-red-500 dark:text-primary-dark"
                            width="24px"
                            height="24px"
                            viewBox="0 0 48 48"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <title>expire-solid</title>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="icons_Q2" data-name="icons Q2">
                                    <g>
                                        <rect
                                            width="48"
                                            height="48"
                                            fill="none"
                                        />
                                        <g>
                                            <path
                                                d="M14.2,31.9h0a2,2,0,0,0-.9-2.9A11.8,11.8,0,0,1,6.1,16.8,12,12,0,0,1,16.9,6a12.1,12.1,0,0,1,11.2,5.6,2.3,2.3,0,0,0,2.3.9h0a2,2,0,0,0,1.1-3,15.8,15.8,0,0,0-15-7.4,16,16,0,0,0-4.8,30.6A2,2,0,0,0,14.2,31.9Z"
                                                fill="currentColor"
                                            />
                                            <path
                                                d="M16.5,11.5v5h-5a2,2,0,0,0,0,4h9v-9a2,2,0,0,0-4,0Z"
                                                fill="currentColor"
                                            />
                                            <path
                                                d="M45.7,43l-15-26a2,2,0,0,0-3.4,0l-15,26A2,2,0,0,0,14,46H44A2,2,0,0,0,45.7,43ZM29,42a2,2,0,1,1,2-2A2,2,0,0,1,29,42Zm2-8a2,2,0,0,1-4,0V26a2,2,0,0,1,4,0Z"
                                                fill="currentColor"
                                            />
                                        </g>
                                    </g>
                                </g>
                            </g>
                        </svg>
                </span>
            </div>
            <div
                class="flex flex-col flex-grow items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400"
                        >Expires soon</span
                    >
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{ expiresoon }}
                    </h5>
                </div>
            </div>
        </Link>

        <!-- Requests card -->
        <a
            :href="
                props.userRole === 1
                    ? route('dispense_history.index')
                    : route('delivery.index')
            "
             class="flex flex-row gap-5 max-w-sm p-3 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
        >
        <div class="flex items-center px-2 w-1/4 justify-center">
                <span>
                    <svg class="w-10 h-10 text-violet-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                      </svg>
                </span>
            </div>
            <div
                class="flex flex-col flex-grow items-left text-left leading-normal"
            >
                <div class="text-left">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{
                            props.userRole === 1
                                ? "Dispense"
                                : "Pending Requests"
                        }}
                    </span>
                    <h5
                        class="text-lg md:text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
                    >
                        {{
                            props.userRole === 1 ? dispenseCount : requestCount
                        }}
                    </h5>
                </div>
            </div>
        </a>
    </div>
</template>
