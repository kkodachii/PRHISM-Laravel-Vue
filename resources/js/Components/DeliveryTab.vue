<template>
    <div v-bind="$attrs">
      <div class="-m-4 text-sm font-medium text-center bg-white text-gray-500 border-b border-blue-200 dark:text-gray-400 dark:border-blue-700">
        <ul class="flex flex-wrap -mb-px px-4">
          <li v-for="(tab, index) in tabs" :key="index" class="me-2">
            <div
              @click="selectTab(tab)"
              :class="[
                'cursor-pointer inline-block p-4 border-b-2 rounded-t-lg hover:border-blue-600',
                tab.value === selectedTab ? 'text-blue-600 border-blue-600 dark:text-blue-500 dark:border-blue-500' : ''
              ]"
            >
              {{ tab.label }}
            </div>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  
  <script setup>
  import { ref, watch, defineProps, defineEmits } from 'vue';
  
  // Props
  const props = defineProps({
    initialSelectedTab: {
      type: String,
      required: true,
    },
    tabOptions: {
      type: Array,
      required: true,
    }
  });
  
  // Emits
  const emit = defineEmits(['tab-selected']);
  
  // Local state
  const tabs = ref(props.tabOptions);
  const selectedTab = ref(props.initialSelectedTab);
  
  // Emit tab update when selection changes
  const selectTab = (tab) => {
    selectedTab.value = tab.value;
    emit('tab-selected', tab.value);
  };
  
  // Watch for changes in the prop and update accordingly
  watch(
    () => props.initialSelectedTab,
    () => {
      selectedTab.value = props.initialSelectedTab;
    }
  );
  </script>
  