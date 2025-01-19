<template>
    <div class="flex flex-row items-center">
      <button
        @click="navigate(paginator.links[0].url)"
        :disabled="!paginator.links[0].url"
        class="flex items-center justify-center text-sm py-1.5 px-3 leading-tight text-gray-700 bg-white border rounded-lg border-gray-300"
      >
        &laquo;
      </button>
      <span class="flex items-center justify-center text-md py-1 px-3 leading-tight text-blue-500 bg-white">{{ paginator.current_page }}</span>
      <button
      @click="navigate(getNextPageUrl())"
      :disabled="!getNextPageUrl()"
      class="flex items-center justify-center text-sm py-1.5 px-3 leading-tight text-gray-700 bg-white border rounded-lg border-gray-300"
    >
        &raquo;
      </button>
    </div>
  </template>

  <script setup>
  import { defineProps } from 'vue';
  import { router } from '@inertiajs/vue3';

  const props = defineProps({
    paginator: Object,
  });

  const getNextPageUrl = () => {
  const nextLink = props.paginator.links.find(link => link.label.includes('Next'));
  return nextLink ? nextLink.url : null;
};

  const navigate = (url) => {
    if (url) {
      router.get(url, { preserveScroll: true });
    }
  };
  </script>
