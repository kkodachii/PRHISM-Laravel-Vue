<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';

// Define component props
const props = defineProps({
  paginator: {
    type: Object,
    required: true,
  },
  search: {
    type: String,
    default: '',
  },
  selectedTab: {
    type: String,
    default: '',
  },
  selectedStatus: {
    type: Array,
    default: () => ([
      { id: 'new', name: 'New', selected: false },
      { id: 'goodCondition', name: 'Good Condition', selected: false },
      { id: 'fairCondition', name: 'Fair Condition', selected: false },
      { id: 'poorCondition', name: 'Poor Condition', selected: false },
      { id: 'condemned', name: 'Condemned', selected: false },
      { id: 'onStock', name: 'On stock', selected: false },
      { id: 'lowOnStock', name: 'Low on stock', selected: false },
      { id: 'outOfStock', name: 'Out of stock', selected: false },
      { id: 'expiring', name: 'Expiring', selected: false },
      { id: 'expired', name: 'Expired', selected: false }
    ]),
  },
  selecteddeliveryStatus: {
    type: Object,
    default: () => ({
      medicine: [],
      equipment: [],
      supply: [],
    }),
  },
  sortField: {
    type: String,
    default: 'id',
  },
  sortDirection: {
    type: String,
    default: 'asc',
  },
  batchId: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'all',
  }
});

// Helper function to format pagination labels
const makeLabel = (label) => {
  if (label.includes('Previous')) {
    return '<';
  } else if (label.includes('Next')) {
    return '>';
  } else {
    return label;
  }
};

// Function to handle pagination clicks and update query parameters
const handlePaginationClick = (url) => {
  if (url) {
    // Extract query parameters from the URL
    const urlParams = new URLSearchParams(url.split('?')[1]);

    // Build query parameters object
    const params = {};

    // Add search parameter if provided
    if (props.search) {
      params.search = props.search;
    }

    // Add the selected tab to the parameters
    if (props.selectedTab) {
      params.status = props.selectedTab;
    }

    // Add selected status filters
    const statusFilters = props.selectedStatus
      .filter(status => status.selected)
      .map(status => status.id)
      .join(',');

    if (statusFilters) {
      params.status = statusFilters;
    }

    // Collect status filters for medicines, equipment, and supplies
    const statusMedicine = props.selecteddeliveryStatus.medicine
      .filter(status => status.selected)
      .map(status => status.id)
      .join(',');

    const statusEquipment = props.selecteddeliveryStatus.equipment
      .filter(status => status.selected)
      .map(status => status.id)
      .join(',');

    const statusSupply = props.selecteddeliveryStatus.supply
      .filter(status => status.selected)
      .map(status => status.id)
      .join(',');

    // Add status filters to query parameters
    if (statusMedicine) {
      params.status_medicine = statusMedicine;
    }

    if (statusEquipment) {
      params.status_equipment = statusEquipment;
    }

    if (statusSupply) {
      params.status_supply = statusSupply;
    }

    // Add sort parameters
    if (props.sortField && props.sortDirection) {
      params.sort = props.sortField;
      params.direction = props.sortDirection;
    }

    // Include batchId and type if they are set
    if (props.batchId) {
      params.batchId = props.batchId;
    }

    if (props.type) {
      params.type = props.type;
    }

    // Add current pagination page if available in the URL
    const page = urlParams.get('page');
    if (page) {
      params.page = page;
    }

    // Navigate to the new URL with updated parameters using Inertia
    router.get(url, params, { preserveScroll: true, preserveState: true});
  }
};
</script>

<template>
  <nav
    class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
    aria-label="Table navigation"
  >
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
      Showing
      <span class="font-semibold text-gray-900 dark:text-white">
        {{ paginator.to }}
      </span>
      of
      <span class="font-semibold text-gray-900 dark:text-white">
        {{ paginator.total }}
      </span>
    </span>

    <ul class="inline-flex items-stretch -space-x-px">
      <li v-for="link in paginator.links" :key="link.label">
        <button
          v-if="link.url"
          @click="handlePaginationClick(link.url)"
          v-html="makeLabel(link.label)"
          class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300"
          :class="{
            'hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white':
              link.url,
            'text-zinc-400': !link.url,
            '!font-bold !text-blue-500': link.active,
          }"
        ></button>
        <span
          v-else
          v-html="makeLabel(link.label)"
          class="flex items-center justify-center text-sm py-2 px-3 leading-tight text-gray-500 bg-white border border-gray-300"
          :class="{
            'text-zinc-400': !link.url,
            '!font-bold !text-blue-500': link.active,
          }"
        ></span>
      </li>
    </ul>
  </nav>
</template>
