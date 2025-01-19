<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { initFlowbite } from "flowbite";
import { useForm } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import Pagination from "@/Components/Pagination.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    itemsRestock: Object,
    duration: String,
});

const form = useForm({
    search: "",
    duration: props.duration || "7_days",
});

const selectedChartType = ref("bar");
const selectedFilterType = ref("all");
const showDropdown = ref(false);
const selectedDuration = ref(form.duration);

// New state for sorting
const sortField = ref("current_stock");
const sortOrder = ref("desc");

const combinedItems = computed(() => {
    const medicines = props.itemsRestock.data.filter(
        (item) => item.type === "medicines"
    );
    const equipments = props.itemsRestock.data.filter(
        (item) => item.type === "equipments"
    );
    const medicalSupplies = props.itemsRestock.data.filter(
        (item) => item.type === "medical_supplies"
    );

    return [
        ...medicines.map((item) => ({ ...item, type: "Medicine" })),
        ...equipments.map((item) => ({ ...item, type: "Equipment" })),
        ...medicalSupplies.map((item) => ({ ...item, type: "Medical Supply" })),
    ];
});

const filteredItems = computed(() => {
    let items = combinedItems.value.filter((item) => {
        const matchesSearch =
            form.search === "" ||
            item.name.toLowerCase().includes(form.search.toLowerCase());
        const matchesType =
            selectedFilterType.value === "all" ||
            item.type === selectedFilterType.value;
        return matchesSearch && matchesType;
    });

    // Sort the items
    items.sort((a, b) => {
        let comparison = 0;
        if (sortField.value === "current_stock") {
            comparison = a.current_stock - b.current_stock;
        } else if (sortField.value === "required_restock") {
            comparison = a.required_restock - b.required_restock;
        }
        return sortOrder.value === "asc" ? comparison : -comparison;
    });

    return items;
});

let chartInstance = null;

const setupChart = () => {
    const ctx = document.getElementById("restockPredictionChart");

    if (!ctx) {
        console.error("Chart context not found!");
        return;
    }

    if (chartInstance) {
        chartInstance.destroy();
    }

    // Slice the top 5 items only for the chart
    const top5Items = [...filteredItems.value]
        .sort((a, b) => b.required_restock - a.required_restock) // Ensure sorting by `required_restock`
        .slice(0, 5); // Get the top 5 items

    const labels = top5Items.map((item) => item.name);
    const data = top5Items.map((item) =>
        item.required_restock > 0 ? item.required_restock : 0
    );
    const backgroundColors = top5Items.map((item) => {
        if (item.type === "Medical Supply") {
            return "rgba(138, 43, 226, 0.7)"; // Violet for medical supplies
        } else if (item.type === "Medicine") {
            return "rgba(34, 139, 34, 0.7)"; // Green for medicines
        } else if (item.type === "Equipment") {
            return "rgba(138, 43, 226, 0.7)"; // Violet for equipment
        } else {
            return "rgba(169, 169, 169, 0.7)"; // Default gray for unknown types
        }
    });

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        return (
                            context.label +
                            ": " +
                            (context.raw ? context.raw.toFixed(2) : "0") +
                            " items"
                        );
                    },
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: "Quantity",
                    font: { size: 16, weight: "bold", style: "italic" },
                },
            },
            x: {
                title: {
                    display: true,
                    text: "Items",
                    font: { size: 16, weight: "bold", style: "italic" },
                },
            },
        },
    };

    chartInstance = new Chart(ctx, {
        type: selectedChartType.value,
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Required Restock",
                    data: data,
                    backgroundColor: backgroundColors,
                },
            ],
        },
        options: chartOptions,
    });
};

const updateDuration = () => {
    form.duration = selectedDuration.value;
    form.post(route("predictions.index"), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            setupChart();
        },
    });
};

const toggleSort = (field) => {
    if (sortField.value === field) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortField.value = field;
        sortOrder.value = "desc";
    }
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

watch([() => form.search, selectedFilterType, sortField, sortOrder], () => {
    setupChart();
});

onMounted(() => {
    document.addEventListener("click", (event) => {
        const dropdown = document.getElementById("filterDropdown");
        if (dropdown && !dropdown.contains(event.target)) {
            showDropdown.value = false;
        }
    });
    initFlowbite();
    setupChart();
});
</script>
<template>
    <Head title="Restock Predictions" />

    <div class="bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
            <div class="w-full md:w-1/2 flex items-center space-x-2">
                <form class="flex items-center w-full" @submit.prevent="setupChart">
                    <input
                        type="search"
                        class="bg-gray-50 border text-gray-900 rounded-lg w-full pl-10 p-2"
                        placeholder="Search by name"
                        v-model="form.search"
                    />
                    <button type="submit" class="bg-blue-700 text-white rounded-lg px-4 py-2 ml-2">Search</button>
                </form>

                <div class="relative">
                    <button
                        @click="toggleDropdown"
                        id="filterDropdownButton"
                        class="flex items-center justify-center py-2.5 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
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

                    <div v-if="showDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-50 border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                        <div class="p-4 space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration Filter</label>
                            <select v-model="selectedDuration" @change="updateDuration" class="border rounded-lg w-full p-2">
                                <option value="" disabled selected>Select Duration</option>
                                <option value="7_days">Next 7 Days</option>
                                <option value="15_days">Next 15 Days</option>
                                <option value="30_days">Next 30 Days</option>
                                <option value="6_months">Next 6 Months</option>
                                <option value="1_year">Next Year</option>
                            </select>

                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type Filter</label>
                            <select v-model="selectedFilterType" @change="setupChart" class="border rounded-lg w-full p-2">
                                <option value="" disabled selected>Select Category</option>
                                <option value="all">All</option>
                                <option value="Medicine">Medicines</option>
                                <option value="Equipment">Equipments</option>
                                <option value="Medical Supply">Medical Supplies</option>
                            </select>

                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Chart Type</label>
                            <select v-model="selectedChartType" @change="setupChart" class="border rounded-lg w-full p-2">
                                <option value="" disabled selected>Select Chart Type</option>
                                <option value="bar">Bar Chart</option>
                                <option value="line">Line Chart</option>
                                <option value="pie">Pie Chart</option>
                            </select>

                            <button @click="setupChart" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
                
                    <button
                    id="sortFieldDropdownButton"
                    data-dropdown-toggle="sortFieldDropdown"
                    @click="toggleSort('required_restock')"
                    class="flex items-center justify-center py-2.5 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    type="button">
                    <svg 
                        class="w-[24px] h-[24px] text-gray-400" 
                        aria-hidden="true" 
                        xmlns="http://www.w3.org/2000/svg" 
                        width="24" height="24" 
                        fill="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 20V10m0 10-3-3m3 3 3-3m5-13v10m0-10 3 3m-3-3-3 3"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="h-72 p-4">
            <canvas id="restockPredictionChart" class="mb-4"></canvas>
        </div>

        <div class="relative overflow-x-auto sm:rounded-lg">
            <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Type</th>
                            <th scope="col" class="px-6 py-3">Name</th>
                            <th scope="col" class="px-6 py-3 cursor-pointer" @click="toggleSort('current_stock')">
                                Current Stock
                                <span v-if="sortField === 'current_stock'">
                                    {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                </span>
                            </th>
                            <th scope="col" class="px-6 py-3">Predicted Usage</th>
                        </tr>
                    </thead>
                    <tbody v-if="filteredItems.length === 0">
                    <tr>
                        <td
                            colspan="8"
                            class="text-center py-4 text-gray-500 dark:text-gray-400"
                        >
                            No data available
                        </td>
                    </tr>
                </tbody>
                    <tbody>
                        <tr v-for="item in filteredItems" :key="item.name" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300': item.type === 'Medicine',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': item.type === 'Equipment',
                                        'bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-300': item.type === 'Medical Supply'
                                    }"
                                    class="text-xs font-medium px-2.5 py-0.5 rounded-full"
                                >
                                    {{ item.type.charAt(0).toUpperCase() + item.type.slice(1) }}
                                </span>
                            </td>
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">{{ item.name }}</th>
                            <td class="px-6 py-4">{{ item.current_stock }}</td>
                            <td class="px-6 py-4">{{ item.predicted_usage.toFixed(2) }}</td>
                            <td class="px-6 py-4">{{ item.required_restock.toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <Pagination :paginator="props.itemsRestock" />
    </div>
</template>