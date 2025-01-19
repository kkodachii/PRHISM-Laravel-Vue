<template>
    <div class="relative"> <!-- Add relative class to parent container -->
      <button
        @click="toggleDropdown"
        id="filterDropdownButton"
        class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
        type="button"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
          class="h-5 w-6 mr-2 text-gray-400"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z"
            clip-rule="evenodd"
          />
        </svg>
        Filter
        <svg
          class="-mr-1 ml-1.5 w-5 h-5"
          fill="currentColor"
          viewBox="0 0 20 20"
          xmlns="http://www.w3.org/2000/svg"
          aria-hidden="true"
        >
          <path
            clip-rule="evenodd"
            fill-rule="evenodd"
            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          />
        </svg>
      </button>
      <div
        v-if="isDropdownOpen"
        id="filterDropdown"
        class="absolute z-10 w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700 mt-2"
      >
        <h6 class="mb-3 text-sm font-medium text-gray-900 dark:text-white">
          Filter
        </h6>
        <ul class="space-y-2 text-sm" aria-labelledby="filterDropdownButton">
          <li class="flex items-center" v-for="(filter, index) in filters" :key="index">
            <input
              :id="filter.id"
              type="checkbox"
              v-model="selectedFilters"
              :value="filter.value"
              class="w-4 h-4 bg-gray-100 border-gray-300 rounded text-blue-600 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
            />
            <label
              :for="filter.id"
              class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100"
              >{{ filter.label }}</label
            >
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        isDropdownOpen: false,
        selectedFilters: [],
        filters: [
          { id: 'onStock', label: 'On stock', value: 'on_stock' },
          { id: 'lowStock', label: 'Low on stock', value: 'low_stock' },
          { id: 'outStock', label: 'Out of stock', value: 'out_of_stock' },
        ],
      };
    },
    methods: {
      toggleDropdown() {
        this.isDropdownOpen = !this.isDropdownOpen;
      },
    },
  };
  </script>
  
  