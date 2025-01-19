<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Dropdown } from "flowbite";

const page = usePage();

const user = computed(() => page.props.auth.user);

const isSmallScreen = ref(window.innerWidth <= 768);
window.addEventListener("resize", () => {
    isSmallScreen.value = window.innerWidth <= 768;
});

const invTabOpen = ref(false);

function invOpen() {
    if (invTabOpen.value == false) {
        invTabOpen.value = true;
    } else {
        invTabOpen.value = false;
    }
}
</script>

<template>
    <aside
        v-if="!isSmallScreen"
        class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform -translate-x-full bg-white border-r border-gray-200 md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Sidenav"
        id="drawer-navigation"
    >
        <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
            <form action="#" method="GET" class="md:hidden mb-2">
                <label for="sidebar-search" class="sr-only">Search</label>
                <div class="relative">
                    <div
                        class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none"
                    >
                        <svg
                            class="w-5 h-5 text-gray-500 dark:text-gray-400"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            ></path>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        id="sidebar-search"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                        placeholder="Search"
                    />
                </div>
            </form>
            <ul class="space-y-2">
                <li>
                    <Link
                        :href="route('dashboard')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/dashboard',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/dashboard',
                            }"
                            aria-hidden="true"
                            class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"
                            ></path>
                            <path
                                d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"
                            ></path>
                        </svg>
                        <span class="ml-3">Dashboard</span>
                    </Link>
                </li>
                <li v-if="user.role_id === 3 || user.role_id === 2">
                    <button
                        type="button"
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        aria-controls="dropdown-pages"
                        data-collapse-toggle="dropdown-pages"
                    >
                        <svg
                            class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 8v-2h7v2H4Zm0 2v2h7v-2H4Zm9 2h7v-2h-7v2Zm7-4v-2h-7v2h7Z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <span class="flex-1 ml-3 text-left whitespace-nowrap"
                            >Inventory</span
                        >
                        <svg
                            aria-hidden="true"
                            class="w-6 h-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                    <ul id="dropdown-pages" class="hidden py-2 space-y-2">
                        <li>
                            <Link
                                :href="route('medicines.index')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/medicines',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/medicines',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M112 96c-26.5 0-48 21.5-48 48V256h96V144c0-26.5-21.5-48-48-48zM0 144C0 82.1 50.1 32 112 32s112 50.1 112 112V368c0 61.9-50.1 112-112 112S0 429.9 0 368V144zM554.9 399.4c-7.1 12.3-23.7 13.1-33.8 3.1L333.5 214.9c-10-10-9.3-26.7 3.1-33.8C360 167.7 387.1 160 416 160c88.4 0 160 71.6 160 160c0 28.9-7.7 56-21.1 79.4zm-59.5 59.5C472 472.3 444.9 480 416 480c-88.4 0-160-71.6-160-160c0-28.9 7.7-56 21.1-79.4c7.1-12.3 23.7-13.1 33.8-3.1L498.5 425.1c10 10 9.3 26.7-3.1 33.8z"
                                    /></svg
                                >Medicine
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('equipments.index')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/equipments',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/equipments',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M142.4 21.9c5.6 16.8-3.5 34.9-20.2 40.5L96 71.1V192c0 53 43 96 96 96s96-43 96-96V71.1l-26.1-8.7c-16.8-5.6-25.8-23.7-20.2-40.5s23.7-25.8 40.5-20.2l26.1 8.7C334.4 19.1 352 43.5 352 71.1V192c0 77.2-54.6 141.6-127.3 156.7C231 404.6 278.4 448 336 448c61.9 0 112-50.1 112-112V265.3c-28.3-12.3-48-40.5-48-73.3c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V336c0 97.2-78.8 176-176 176c-92.9 0-168.9-71.9-175.5-163.1C87.2 334.2 32 269.6 32 192V71.1c0-27.5 17.6-52 43.8-60.7l26.1-8.7c16.8-5.6 34.9 3.5 40.5 20.2zM480 224a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"
                                    /></svg
                                >Equipment
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('medical_supplies.index')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/medical_supplies',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/medical_supplies',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                                Med Supply
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('barangay_list.index')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/barangay_list',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/barangay_list',
                                    }"
                                    class="w-6 h-6 text-gray-500 dark:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M20 10H4v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8ZM9 13v-1h6v1a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z"
                                        clip-rule="evenodd"
                                    />
                                    <path
                                        d="M2 6a2 2 0 0 1 2-2h16a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Z"
                                    />
                                </svg>
                                Barangay Inventory
                            </Link>
                        </li>
                    </ul>
                </li>
                <li v-if="user.role_id === 1">
                    <Link
                        :href="route('inventory.index')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/inventory',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/inventory',
                            }"
                            class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 8v-2h7v2H4Zm0 2v2h7v-2H4Zm9 2h7v-2h-7v2Zm7-4v-2h-7v2h7Z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <span class="ml-3">Inventory</span>
                    </Link>
                </li>
                <li v-if="user.role_id === 1">
                    <Link
                        :href="route('dispense_history.index')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/dispense_history',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] ===
                                    '/dispense_history',
                            }"
                            class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <span class="ml-3">Dispense History</span>
                    </Link>
                </li>

                <li v-if="user.role_id === 3 || user.role_id === 2">
                    <Link
                        :href="route('batches.index')"
                        :class="{
                            'bg-gray-100': route().current('batches.index'),
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            class="w-6 h-6"
                            :class="{
                                'text-gray-800 dark:text-white':
                                    route().current('batches.index'),
                                'text-gray-500 dark:text-gray-400':
                                    !route().current('batches.index'),
                            }"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M6 5a2 2 0 0 1 2-2h4.157a2 2 0 0 1 1.656.879L15.249 6H19a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2v-5a3 3 0 0 0-3-3h-3.22l-1.14-1.682A3 3 0 0 0 9.157 6H6V5Z"
                                clip-rule="evenodd"
                            />
                            <path
                                fill-rule="evenodd"
                                d="M3 9a2 2 0 0 1 2-2h4.157a2 2 0 0 1 1.656.879L12.249 10H3V9Zm0 3v7a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-7H3Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="ml-3">Batches</span>
                    </Link>
                </li>
                <li v-if="user.role_id === 1 || (user.role_id === 2 && user.rhu_id !== 1)">
                    <Link
                        :href="route('requests.index')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/requests',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/requests',
                            }"
                            class="w-6 h-6 text-gray-500 dark:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm2-2a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm0 3a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm-6 4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-6Zm8 1v1h-2v-1h2Zm0 3h-2v1h2v-1Zm-4-3v1H9v-1h2Zm0 3H9v1h2v-1Z"
                                clip-rule="evenodd"
                            />
                        </svg>

                        <span class="ml-3">Request</span>
                    </Link>
                </li>
                <li v-if="user.role_id === 3 || user.role_id === 2">
                    <Link
                        :href="route('delivery.index')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/delivery',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/delivery',
                            }"
                            aria-hidden="true"
                            class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="currentColor"
                            viewBox="0 0 640 512"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M48 0C21.5 0 0 21.5 0 48V368c0 26.5 21.5 48 48 48H64c0 53 43 96 96 96s96-43 96-96H384c0 53 43 96 96 96s96-43 96-96h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V288 256 237.3c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7H416V48c0-26.5-21.5-48-48-48H48zM416 160h50.7L544 237.3V256H416V160zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"
                            />
                        </svg>
                        <span class="ml-3">Delivery</span>
                    </Link>
                </li>
            </ul>
            <ul
                v-if="user.role_id === 3 || user.role_id === 2"
                class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700"
            >
                <li v-if="user.role_id === 3 || user.role_id === 2">
                    <button
                        type="button"
                        class="flex items-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                        aria-controls="dropdown-pages-2"
                        data-collapse-toggle="dropdown-pages-2"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/reports',
                            }"
                            aria-hidden="true"
                            class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                            <path
                                fill-rule="evenodd"
                                d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>

                        <span class="flex-1 ml-3 text-left whitespace-nowrap"
                            >Reports</span
                        >
                        <svg
                            aria-hidden="true"
                            class="w-6 h-6"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </button>
                    <ul id="dropdown-pages-2" class="hidden py-2 space-y-2">
                        <li>
                            <Link
                                :href="route('reports.low-stock')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/low-stock',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/low-stock',
                                    }"
                                    class="w-[24px] h-[24px] text-gray-500 dark:text-white"
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
                                        d="M4 4.5V19a1 1 0 0 0 1 1h15M7 10l4 4 4-4 5 5m0 0h-3.207M20 15v-3.207"
                                    /></svg
                                >Low Stock Report
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.medicine-expiring')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/medicine-expiring',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/medicine-expiring',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M112 96c-26.5 0-48 21.5-48 48V256h96V144c0-26.5-21.5-48-48-48zM0 144C0 82.1 50.1 32 112 32s112 50.1 112 112V368c0 61.9-50.1 112-112 112S0 429.9 0 368V144zM554.9 399.4c-7.1 12.3-23.7 13.1-33.8 3.1L333.5 214.9c-10-10-9.3-26.7 3.1-33.8C360 167.7 387.1 160 416 160c88.4 0 160 71.6 160 160c0 28.9-7.7 56-21.1 79.4zm-59.5 59.5C472 472.3 444.9 480 416 480c-88.4 0-160-71.6-160-160c0-28.9 7.7-56 21.1-79.4c7.1-12.3 23.7-13.1 33.8-3.1L498.5 425.1c10 10 9.3 26.7-3.1 33.8z"
                                    /></svg
                                >Expiring Medicine Report
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.equipment-status')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/equipment-status',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/equipment-status',
                                    }"
                                    class="w-[24px] h-[24px] text-gray-500 dark:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="30"
                                    height="30"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M20 10H4v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8ZM9 13v-1h6v1a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1Z"
                                        clip-rule="evenodd"
                                    />
                                    <path
                                        d="M2 6a2 2 0 0 1 2-2h16a2 2 0 1 1 0 4H4a2 2 0 0 1-2-2Z"
                                    /></svg
                                >Equipment Status Report
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.barangay-inventory')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/barangay-inventory',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/barangay-inventory',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M11.293 3.293a1 1 0 0 1 1.414 0l6 6 2 2a1 1 0 0 1-1.414 1.414L19 12.414V19a2 2 0 0 1-2 2h-3a1 1 0 0 1-1-1v-3h-2v3a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2v-6.586l-.293.293a1 1 0 0 1-1.414-1.414l2-2 6-6Z"
                                        clip-rule="evenodd"
                                    /></svg
                                >Barangay Inventory Report
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.dispense')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/dispense',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/dispense',
                                    }"
                                    class="w-[24px] h-[24px] text-gray-500 dark:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm2-2a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm0 3a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm-6 4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-6Zm8 1v1h-2v-1h2Zm0 3h-2v1h2v-1Zm-4-3v1H9v-1h2Zm0 3H9v1h2v-1Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Dispense Report
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.request')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/request',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/request',
                                    }"
                                    class="w-[24px] h-[24px] text-gray-500 dark:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M8 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1h2a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h2Zm6 1h-4v2H9a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2h-1V4Zm-6 8a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1 3a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z"
                                        clip-rule="evenodd"
                                    /></svg
                                >Request Report
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.most-requested')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/most-requested',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/most-requested',
                                    }"
                                    class="w-[24px] h-[24px] text-gray-500 dark:text-white"
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
                                        d="M3 15v4m6-6v6m6-4v4m6-6v6M3 11l6-5 6 5 5.5-5.5"
                                    /></svg
                                >Frequently Requested
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('reports.delivery')"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/reports/delivery',
                                }"
                                class="flex items-center w-full p-2 text-gray-900 gap-2 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/reports/delivery',
                                    }"
                                    class="w-[24px] h-[24px] text-gray-500 dark:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4 4a2 2 0 0 0-2 2v9a1 1 0 0 0 1 1h.535a3.5 3.5 0 1 0 6.93 0h3.07a3.5 3.5 0 1 0 6.93 0H21a1 1 0 0 0 1-1v-4a.999.999 0 0 0-.106-.447l-2-4A1 1 0 0 0 19 6h-5a2 2 0 0 0-2-2H4Zm14.192 11.59.016.02a1.5 1.5 0 1 1-.016-.021Zm-10 0 .016.02a1.5 1.5 0 1 1-.016-.021Zm5.806-5.572v-2.02h4.396l1 2.02h-5.396Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                Delivery Report
                            </Link>
                        </li>
                    </ul>
                </li>
                <li v-if="user.role_id === 3">
                    <Link
                        :href="route('users.index')"
                        :class="{
                            'bg-gray-100': $page.url.split('?')[0] === '/users',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/users',
                            }"
                            class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M12 6a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm-1.5 8a4 4 0 0 0-4 4 2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-3Zm6.82-3.096a5.51 5.51 0 0 0-2.797-6.293 3.5 3.5 0 1 1 2.796 6.292ZM19.5 18h.5a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-1.1a5.503 5.503 0 0 1-.471.762A5.998 5.998 0 0 1 19.5 18ZM4 7.5a3.5 3.5 0 0 1 5.477-2.889 5.5 5.5 0 0 0-2.796 6.293A3.501 3.501 0 0 1 4 7.5ZM7.1 12H6a4 4 0 0 0-4 4 2 2 0 0 0 2 2h.5a5.998 5.998 0 0 1 3.071-5.238A5.505 5.505 0 0 1 7.1 12Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="ml-3">Users</span>
                    </Link>
                </li>
                <li v-if="user.role_id === 3 || user.role_id === 2">
                    <Link
                        :href="route('generic_name.index')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/generic_name',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/generic_name',
                            }"
                            class="w-6 h-6 text-gray-500 transition-colors duration-300 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true"
                        >
                            <path
                                d="M4 22h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2zm2-9h3v-3h2v3h3v2h-3v3H9v-3H6v-2z"
                            ></path>
                            <path
                                d="M20 2H8v2h12v12h2V4c0-1.103-.897-2-2-2z"
                            ></path>
                        </svg>
                        <span class="ml-3">Add/Edit Category</span>
                    </Link>
                </li>

                <li v-if="user.role_id === 3">
                    <Link
                        :href="route('backups.index')"
                        :class="{
                            'bg-gray-100':
                                $page.url.split('?')[0] === '/backups',
                        }"
                        class="flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                    >
                        <svg
                            :class="{
                                'text-gray-900':
                                    $page.url.split('?')[0] === '/backups',
                            }"
                            class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            fill="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M13.383 4.076a6.5 6.5 0 0 0-6.887 3.95A5 5 0 0 0 7 18h3v-4a2 2 0 0 1-1.414-3.414l2-2a2 2 0 0 1 2.828 0l2 2A2 2 0 0 1 14 14v4h4a4 4 0 0 0 .988-7.876 6.5 6.5 0 0 0-5.605-6.048Z"
                            />
                            <path
                                d="M12.707 9.293a1 1 0 0 0-1.414 0l-2 2a1 1 0 1 0 1.414 1.414l.293-.293V19a1 1 0 1 0 2 0v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-2-2Z"
                            />
                        </svg>

                        <span class="ml-3">Backup</span>
                    </Link>
                </li>
            </ul>
        </div>
    </aside>
    <nav
        v-if="isSmallScreen"
        class="fixed bottom-0 left-0 z-40 w-full overflow-y-auto bg-white border-t border-gray-200 dark:bg-gray-800 dark:border-gray-700"
        aria-label="Bottom Navigation"
    >
        <ul class="flex justify-around">
            <li>
                <Link
                    :href="route('dashboard')"
                    :class="{
                        'bg-gray-100': $page.url.split('?')[0] === '/dashboard',
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        :class="{
                            'text-gray-900':
                                $page.url.split('?')[0] === '/dashboard',
                        }"
                        aria-hidden="true"
                        class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                        <path
                            d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"
                        ></path>
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </Link>
            </li>
            <li v-if="user.role_id === 3 || user.role_id === 2">
                <button
                    type="button"
                    class="flex flex-col items-center justify-center p-2 w-full text-base font-medium text-gray-900 rounded-lg transition duration-75 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                    @click="invOpen()"
                >
                    <svg
                        class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 8v-2h7v2H4Zm0 2v2h7v-2H4Zm9 2h7v-2h-7v2Zm7-4v-2h-7v2h7Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="flex-1 text-sm text-left whitespace-nowrap"
                        >Inventory</span
                    >
                </button>
                <div
                    v-if="invTabOpen"
                    class="fixed bottom-16 z-10 bg-white shadow-lg rounded mt-2 flex"
                >
                    <ul>
                        <li>
                            <Link
                                :href="route('medicines.index')"
                                @click="invOpen()"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/medicines',
                                }"
                                class="flex justify-start w-full p-2 px-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/medicines',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M112 96c-26.5 0-48 21.5-48 48V256h96V144c0-26.5-21.5-48-48-48zM0 144C0 82.1 50.1 32 112 32s112 50.1 112 112V368c0 61.9-50.1 112-112 112S0 429.9 0 368V144zM554.9 399.4c-7.1 12.3-23.7 13.1-33.8 3.1L333.5 214.9c-10-10-9.3-26.7 3.1-33.8C360 167.7 387.1 160 416 160c88.4 0 160 71.6 160 160c0 28.9-7.7 56-21.1 79.4zm-59.5 59.5C472 472.3 444.9 480 416 480c-88.4 0-160-71.6-160-160c0-28.9 7.7-56 21.1-79.4c7.1-12.3 23.7-13.1 33.8-3.1L498.5 425.1c10 10 9.3 26.7-3.1 33.8z"
                                    /></svg
                                >Medicine
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('equipments.index')"
                                @click="invOpen()"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/equipments',
                                }"
                                class="flex justify-start w-full p-2 px-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/equipments',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M142.4 21.9c5.6 16.8-3.5 34.9-20.2 40.5L96 71.1V192c0 53 43 96 96 96s96-43 96-96V71.1l-26.1-8.7c-16.8-5.6-25.8-23.7-20.2-40.5s23.7-25.8 40.5-20.2l26.1 8.7C334.4 19.1 352 43.5 352 71.1V192c0 77.2-54.6 141.6-127.3 156.7C231 404.6 278.4 448 336 448c61.9 0 112-50.1 112-112V265.3c-28.3-12.3-48-40.5-48-73.3c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V336c0 97.2-78.8 176-176 176c-92.9 0-168.9-71.9-175.5-163.1C87.2 334.2 32 269.6 32 192V71.1c0-27.5 17.6-52 43.8-60.7l26.1-8.7c16.8-5.6 34.9 3.5 40.5 20.2zM480 224a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"
                                    /></svg
                                >Equipment
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('medical_supplies.index')"
                                @click="invOpen()"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/medical_supplies',
                                }"
                                class="flex justify-start w-full p-2 px-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/medical_supplies',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
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
                                Med Supply
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="route('barangay_list.index')"
                                @click="invOpen()"
                                :class="{
                                    'bg-gray-100':
                                        $page.url.split('?')[0] ===
                                        '/barangay_list',
                                }"
                                class="flex justify-start w-full p-2 px-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700"
                            >
                                <svg
                                    :class="{
                                        'text-gray-900':
                                            $page.url.split('?')[0] ===
                                            '/barangay_list',
                                    }"
                                    class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                    aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    fill="currentColor"
                                    viewBox="0 0 600 600"
                                >
                                    <path
                                        d="M142.4 21.9c5.6 16.8-3.5 34.9-20.2 40.5L96 71.1V192c0 53 43 96 96 96s96-43 96-96V71.1l-26.1-8.7c-16.8-5.6-25.8-23.7-20.2-40.5s23.7-25.8 40.5-20.2l26.1 8.7C334.4 19.1 352 43.5 352 71.1V192c0 77.2-54.6 141.6-127.3 156.7C231 404.6 278.4 448 336 448c61.9 0 112-50.1 112-112V265.3c-28.3-12.3-48-40.5-48-73.3c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V336c0 97.2-78.8 176-176 176c-92.9 0-168.9-71.9-175.5-163.1C87.2 334.2 32 269.6 32 192V71.1c0-27.5 17.6-52 43.8-60.7l26.1-8.7c16.8-5.6 34.9 3.5 40.5 20.2zM480 224a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"
                                    /></svg
                                >Barangay Inventory
                            </Link>
                        </li>
                    </ul>
                </div>
            </li>
            <li v-if="user.role_id === 1">
                <Link
                    :href="route('inventory.index')"
                    :class="{
                        'bg-gray-100': $page.url.split('?')[0] === '/inventory',
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        :class="{
                            'text-gray-900':
                                $page.url.split('?')[0] === '/inventory',
                        }"
                        class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Zm2 8v-2h7v2H4Zm0 2v2h7v-2H4Zm9 2h7v-2h-7v2Zm7-4v-2h-7v2h7Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="text-sm">Inventory</span>
                </Link>
            </li>
            <li v-if="user.role_id === 1">
                <Link
                    :href="route('dispense_history.index')"
                    :class="{
                        'bg-gray-100':
                            $page.url.split('?')[0] === '/dispense_history',
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        :class="{
                            'text-gray-900':
                                $page.url.split('?')[0] === '/dispense_history',
                        }"
                        class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="text-sm">History</span>
                </Link>
            </li>
            <li v-if="user.role_id === 3 || user.role_id === 2">
                <Link
                    :href="route('batches.index')"
                    :class="{
                        'bg-gray-100': route().current('batches.index'),
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        class="w-6 h-6"
                        :class="{
                            'text-gray-800 dark:text-white':
                                route().current('batches.index'),
                            'text-gray-500 dark:text-gray-400':
                                !route().current('batches.index'),
                        }"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M6 5a2 2 0 0 1 2-2h4.157a2 2 0 0 1 1.656.879L15.249 6H19a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2v-5a3 3 0 0 0-3-3h-3.22l-1.14-1.682A3 3 0 0 0 9.157 6H6V5Z"
                            clip-rule="evenodd"
                        />
                        <path
                            fill-rule="evenodd"
                            d="M3 9a2 2 0 0 1 2-2h4.157a2 2 0 0 1 1.656.879L12.249 10H3V9Zm0 3v7a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-7H3Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <span class="text-sm">Batches</span>
                </Link>
            </li>
            <li v-if="user.role_id === 1 || user.role_id === 2">
                <Link
                    :href="route('requests.index')"
                    :class="{
                        'bg-gray-100': $page.url.split('?')[0] === '/requests',
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        :class="{
                            'text-gray-900':
                                $page.url.split('?')[0] === '/requests',
                        }"
                        class="w-6 h-6 text-gray-500 dark:text-white"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Zm2 0V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm2-2a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm0 3a1 1 0 1 0 0 2h3a1 1 0 1 0 0-2h-3Zm-6 4a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-6Zm8 1v1h-2v-1h2Zm0 3h-2v1h2v-1Zm-4-3v1H9v-1h2Zm0 3H9v1h2v-1Z"
                            clip-rule="evenodd"
                        />
                    </svg>

                    <span class="text-sm">Request</span>
                </Link>
            </li>
            <li v-if="user.role_id === 3 || user.role_id === 2">
                <Link
                    :href="route('delivery.index')"
                    :class="{
                        'bg-gray-100': $page.url.split('?')[0] === '/delivery',
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        :class="{
                            'text-gray-900':
                                $page.url.split('?')[0] === '/delivery',
                        }"
                        aria-hidden="true"
                        class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        fill="currentColor"
                        viewBox="0 0 640 512"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M48 0C21.5 0 0 21.5 0 48V368c0 26.5 21.5 48 48 48H64c0 53 43 96 96 96s96-43 96-96H384c0 53 43 96 96 96s96-43 96-96h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V288 256 237.3c0-17-6.7-33.3-18.7-45.3L512 114.7c-12-12-28.3-18.7-45.3-18.7H416V48c0-26.5-21.5-48-48-48H48zM416 160h50.7L544 237.3V256H416V160zM112 416a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm368-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"
                        />
                    </svg>
                    <span class="text-sm">Delivery</span>
                </Link>
            </li>
            <li>
                <Link
                    :href="route('mobile.settings')"
                    :class="{
                        'bg-gray-100':
                            $page.url.split('?')[0] === '/mobile-settings',
                    }"
                    class="flex flex-col items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group"
                >
                    <svg
                        :class="{
                            'text-gray-900':
                                $page.url.split('?')[0] === '/mobile-settings',
                        }"
                        aria-hidden="true"
                        class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                        width="24"
                        height="24"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-width="2"
                            d="M5 7h14M5 12h14M5 17h14"
                        />
                    </svg>
                    <span class="text-sm">Settings</span>
                </Link>
            </li>
        </ul>
    </nav>
</template>
