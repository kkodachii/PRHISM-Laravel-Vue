<template>
    <div class="grid grid-cols-1 gap-5 xl:gap-8 p-2 mb-2 md:grid-cols-2">
        <!-- Inventory Summary -->
        <div
            class="flex flex-col justify-between gap-3 p-3 bg-white border border-gray-200 rounded-lg shadow"
        >
            <p
                class="ml-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Medicine Summary
            </p>
            <div class="flex flex-row justify-around w-full">
                <!-- Stock -->
                <Link
                    :href="
                        route('medicine_summary.index', { status: 'On Stock' })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg
                            class="w-6 h-6 text-green-400 dark:text-primary-dark"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 184 184"
                        >
                            <path
                                d="M160.324,38.56h-3.574v-5.678c0-1.381-1.119-2.5-2.5-2.5h-14c-1.381,0-2.5,1.119-2.5,2.5v5.678h-12.417v-6.341 c0-9.694-7.887-17.581-17.581-17.581H76.247c-9.694,0-17.581,7.887-17.581,17.581v6.341H46.25v-5.678c0-1.381-1.119-2.5-2.5-2.5h-14 c-1.381,0-2.5,1.119-2.5,2.5v5.678h-3.574C10.6,38.56,0,49.16,0,62.236v83.45c0,13.076,10.6,23.676,23.676,23.676h136.648 c13.076,0,23.676-10.6,23.676-23.676v-83.45C184,49.16,173.4,38.56,160.324,38.56z M68.666,32.219c0-4.18,3.401-7.581,7.581-7.581 h31.505c4.18,0,7.581,3.401,7.581,7.581v6.341H68.666V32.219z M92,146.606c-23.552,0-42.645-19.093-42.645-42.645 S68.448,61.316,92,61.316s42.645,19.093,42.645,42.645S115.552,146.606,92,146.606z M124.015,114.434 c0,0.663-0.262,1.299-0.73,1.769c-0.469,0.469-1.104,0.733-1.767,0.734l-16.515,0.015v16.514c0,1.381-1.119,2.5-2.5,2.5H81.497 c-1.381,0-2.5-1.119-2.5-2.5v-16.49l-16.49,0.015c-0.001,0-0.001,0-0.002,0c-0.662,0-1.297-0.263-1.766-0.73 c-0.469-0.469-0.733-1.104-0.734-1.767l-0.019-21.005c-0.001-1.381,1.117-2.501,2.498-2.502l16.514-0.015V74.456 c0-1.381,1.119-2.5,2.5-2.5h21.005c1.381,0,2.5,1.119,2.5,2.5v16.49l16.49-0.015c0.001,0,0.002,0,0.002,0 c1.38,0,2.499,1.118,2.5,2.498L124.015,114.434z"
                            ></path>
                        </svg>
                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ stock }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            On stock Medicines
                        </p>
                    </div>
                </Link>

                <!-- Low -->
                <Link
                    :href="
                        route('medicine_summary.index', {
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
                            {{ low }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Low on stock
                        </p>
                    </div>
                </Link>

                <Link
                    :href="route('medical_category.index')"
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg
                            class="w-6 h-6 text-blue-500 dark:text-primary-dark"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >
                            <path
                                d="M4 22h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2zm2-9h3v-3h2v3h3v2h-3v3H9v-3H6v-2z"
                            ></path>
                            <path
                                d="M20 2H8v2h12v12h2V4c0-1.103-.897-2-2-2z"
                            ></path>
                        </svg>
                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ totalCategory }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Medical Categories
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
                Equipment Summary
            </p>
            <div class="flex flex-row justify-around w-full">
                <!-- Repeated items for Delivery Summary (Dummy Data for illustration) -->
                <Link
                :href="
                        route('equipment_summary.index', { status: 'Good condition' })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg
                                    class="flex-shrink-0 w-6 h-6 text-blue-500 transition duration-75 dark:text-blue-400 group-hover:text-blue-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M142.4 21.9c5.6 16.8-3.5 34.9-20.2 40.5L96 71.1V192c0 53 43 96 96 96s96-43 96-96V71.1l-26.1-8.7c-16.8-5.6-25.8-23.7-20.2-40.5s23.7-25.8 40.5-20.2l26.1 8.7C334.4 19.1 352 43.5 352 71.1V192c0 77.2-54.6 141.6-127.3 156.7C231 404.6 278.4 448 336 448c61.9 0 112-50.1 112-112V265.3c-28.3-12.3-48-40.5-48-73.3c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V336c0 97.2-78.8 176-176 176c-92.9 0-168.9-71.9-175.5-163.1C87.2 334.2 32 269.6 32 192V71.1c0-27.5 17.6-52 43.8-60.7l26.1-8.7c16.8-5.6 34.9 3.5 40.5 20.2zM480 224a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"
                                    /></svg>

                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ equipStatus.goodCondition }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                           Good condition
                        </p>
                    </div>
                </Link>

                <Link
                :href="
                        route('equipment_summary.index', { status: 'Poor condition' })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                <div class="flex flex-col justify-center">
                        <svg
                            class="w-6 h-6 text-orange-400 dark:text-primary-dark"
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
                    <div class="flex flex-col text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ equipStatus.poorCondition }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Poor condition
                        </p>
                    </div>
                </Link>
                <Link
                :href="
                        route('equipment_summary.index', { status: 'Condemned' })
                    "
                    class="flex flex-col items-center rounded-md hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"
                >
                    <div class="flex flex-col justify-center">
                        <svg class="w-6 h-6 text-red-500 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                          </svg>

                    </div>
                    <div class="flex flex-col  text-center leading-normal">
                        <p
                            class="text-lg font-bold tracking-tight text-gray-900 dark:text-white"
                        >
                            {{ equipStatus.condemned }}
                        </p>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap"
                        >
                            Condemned
                        </p>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    stock: Number,
    low: Number,
    expiring: Number,
    totalCategory: Number,
    equipStatus: Array,
});
</script>
