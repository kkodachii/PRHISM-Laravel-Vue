<script setup>
import { onMounted } from "vue";
import { initFlowbite } from "flowbite";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
});
</script>

<template>
    <Head title="Reports" />
    <h1>Reports</h1>
</template>
