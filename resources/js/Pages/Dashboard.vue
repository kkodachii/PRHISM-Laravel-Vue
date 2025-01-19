<script setup>
import { ref } from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import ToastList from "@/Components/ToastList.vue";
import Cards from "@/Components/DashboardCard.vue";
import RecentlyAdded from "@/Components/RecentlyAdded.vue";
import ActivityLog from "@/Components/ActivityLog.vue";
import DashboardSummary from "@/Components/DashboardSummary.vue";
import DashboardInventorySummary from "@/Components/DashboardInventorySummary.vue";
import EquipmentUsageChart from "@/Components/EquipmentUsageChart.vue";
import MedicineUsageChart from "@/Components/MedicineUsageChart.vue";
import MedicalSupplyUsageChart from "@/Components/MedicalSupplyChart.vue";
import InventoryForecastChart from "@/Components/InventoryForecastChart.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    totalQuantity: Number,
    lowStock: Number,
    stock: Number,
    low: Number,
    expiring: Number,
    expired: Number,
    expiresoon: Number,
    activities: Array,
    equipmentUsage: Object,
    medicineUsage: Object,
    medicalSupplyUsage: Object,
    recentlyAddedItems: Array,
    itemsRestock: Array,
    auth: Object,
    totalCategory: Number,
    barangayInventory: Number,
    totalBarangay: Number,
    requestCount: Number,
    dispenseCount: Number,
    equipStatus: Array,
    supplyStatus: Array,
});

const userRole = ref(props.auth.user.role_id);
</script>

<template>
    <Head title="Dashboard" />

    <ToastList />
    <!-- Dashboard levels -->

    <Cards
        :totalQuantity="totalQuantity"
        :lowStock="lowStock"
        :expiresoon="expiresoon"
        :requestCount="requestCount"
        :dispenseCount="dispenseCount"
        :userRole="userRole"
    />

    <DashboardSummary
        v-if="userRole !== 1"
        :stock="stock"
        :low="low"
        :expiring="expiring"
        :totalCategory="totalCategory"
        :equipStatus="equipStatus"
    />
    <DashboardInventorySummary
        v-if="userRole !== 1"
        :supplyStatus="supplyStatus"
        :totalCategory="totalCategory"
        :totalBarangay="totalBarangay"
        :barangayInventory="barangayInventory"
        :requestCount="requestCount"
    />

    <div class="grid grid-cols-1 p-2 gap-3 lg:gap-5 mb-4 lg:grid-cols-2">
        <!-- Recently Added Section -->
        <RecentlyAdded
            v-if="userRole !== 1"
            :recentlyAddedItems="recentlyAddedItems"
        />

        <!-- Activity Log -->
        <ActivityLog :activities="activities" />

        <!-- Inventory Forecast -->
        <InventoryForecastChart :itemsRestock="itemsRestock" />

        <!-- Medicine Data -->
        <MedicineUsageChart :medicineUsage="medicineUsage" />

        <!-- Equipment Data -->
        <EquipmentUsageChart :equipmentUsage="equipmentUsage" />

        <!-- Medical Supply Data -->
        <MedicalSupplyUsageChart :medicalSupplyUsage="medicalSupplyUsage" />
    </div>
</template>
