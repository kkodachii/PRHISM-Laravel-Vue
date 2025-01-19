<script setup></script>
<template>
    <div
        class="-m-4 mb-4 text-sm font-medium text-center bg-white text-gray-500 border-b border-blue-200 dark:text-gray-400 dark:border-blue-700"
    >
        <ul class="flex flex-wrap -mb-px px-4">

            <li class="me-2">
                <Link
                    :href="route('generic_name.index')"
                    :class="{ 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500': $page.url.split('?')[0] ===
                    '/generic_name' }"
                    class="inline-block p-4 border-b-2 rounded-t-lg  hover:border-blue-600 "
                    >Generic Name
                </Link>
            </li>

            <li class="me-2">
                <Link
                :href="route('medical_category.index')"
                    :class="{ 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500': $page.url.split('?')[0] ===
                    '/medical_category' }"
                    class="inline-block p-4 border-b-2 rounded-t-lg  hover:border-blue-600 "
                    >Medical Category</Link
                >
            </li>
            <li class="me-2">
                <Link
                :href="route('barangay.index')"
                    :class="{ 'text-blue-600 border-blue-600 active dark:text-blue-500 dark:border-blue-500': $page.url.split('?')[0] ===
                    '/barangay' }"
                    class="inline-block p-4 border-b-2 rounded-t-lg  hover:border-blue-600 "
                    >Barangay</Link
                >
            </li>
        </ul>
    </div>
</template>
