<!-- ToastList.vue -->
<script setup>
import ToastListItem from "../Components/ToastListItems.vue";
import { onMounted, onUnmounted } from "vue";
import toast from "../Stores/toast.js";
import { usePage } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';

const page = usePage();
let removeFinishEventListener = null;

onMounted(() => {
  if (!removeFinishEventListener) {
    removeFinishEventListener = Inertia.on("finish", () => {

      if (page.props.toast) {
        toast.add({
          message: page.props.toast.message,
          type: page.props.toast.type || 'success', 
          key: Date.now() 
        });
      }
    });
  }
});

onUnmounted(() => {
  if (removeFinishEventListener) {
    removeFinishEventListener();
    removeFinishEventListener = null;
  }
});

function remove(index) {
  toast.remove(index);
}
</script>

<template>
  <TransitionGroup
    tag="div"
    enter-from-class="translate-x-full opacity-0"
    enter-active-class="duration-500"
    leave-active-class="duration-500"
    leave-to-class="translate-x-full opacity-0"
    class="fixed top-4 right-4 z-50 w-full max-w-xs space-y-4">
    <ToastListItem
      v-for="(item, index) in toast.items"
      :key="item.key"
      :message="item.message"
      :type="item.type"
      :duration="5000"
      @remove="remove(index)"
    />
  </TransitionGroup>
</template>
