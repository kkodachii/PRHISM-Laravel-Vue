<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  steps: {
    type: Array,
    required: true,
  },
});

const emit = defineEmits(['updateStep']);

const goToStep = (index) => {
  emit('updateStep', index);
};
</script>

<template>
    <div class="max-w-screen-lg">
      <div class="bg-gray-100 rounded-md border border-gray-300 p-2 px-5">
        <ol class="flex text-sm font-medium text-center text-gray-500 dark:text-gray-400 sm:text-base">
          <li
            v-for="(step, index) in steps"
            :key="index"
            :class="[
              'flex items-center',
              step.isActive ? 'text-blue-600 dark:text-blue-500' : '',
              index !== steps.length - 1 ? 'md:w-full' : '',
              index !== steps.length - 1
                ? 'after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-6 xl:after:mx-10 dark:after:border-gray-700'
                : '',
            ]"
          >
            <span
              class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500"
            >
              <div class="flex lg:flex-col lg:items-center flex-row gap-1 xl:gap-0">
                <svg
                  class="w-3.5 h-3.5 mt-0.5"
                  aria-hidden="true"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                  />
                </svg>
                <div class="flex flex-row">
                  <p class="text-sm">{{ step.title }}</p>
                  <span v-if="step.subtitle" class="hidden sm:flex text-sm sm:ms-2"
                    >{{ step.subtitle }}</span
                  >
                </div>
              </div>
            </span>
          </li>
        </ol>
      </div>
    </div>
  </template>
  
  
  
  
  
  
  
  