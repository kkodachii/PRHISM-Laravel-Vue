<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { initFlowbite } from "flowbite";
import Pagination from "@/Components/Pagination.vue";
import { useForm } from "@inertiajs/vue3";
import Chart from "chart.js/auto";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    medicalsupplylog: Object,
    medicalSupplyUsageChart: Array,
    rhus: Array,
    barangays: Array,
});

const currentPage = ref(1);
const form = useForm({
    start_date: "",
    end_date: "",
    search: "",
    rhu: "",
    barangay: "",
});

const showDropdown = ref(false);
const filterType = ref("by_dates");

const filterLogs = () => {
    form.get("/medicalsupplylog", {
        preserveState: true,
        preserveScroll: true,
        page: currentPage.value,
    });
};

const medicalSupplyUsageData = ref([]);
const medicalSupplyUsageLabels = ref([]);
const selectedChartType = ref("bar");

// Computed property to format the date range
const formattedDateRange = computed(() => {
    if (form.start_date && form.end_date) {
        const startDate = new Date(form.start_date);
        const endDate = new Date(form.end_date);
        const options = { year: "numeric", month: "long", day: "numeric" };
        return `${startDate.toLocaleDateString(
            undefined,
            options
        )} - ${endDate.toLocaleDateString(undefined, options)}`;
    }
    return "";
});

onMounted(() => {
    initFlowbite();

    const today = new Date();
    const pastWeek = new Date();
    pastWeek.setDate(today.getDate() - 7);

    form.start_date = pastWeek.toISOString().split("T")[0];
    form.end_date = today.toISOString().split("T")[0];

    if (
        props.medicalSupplyUsageChart &&
        props.medicalSupplyUsageChart.length > 0
    ) {
        adjustChartData(props.medicalSupplyUsageChart);
        setupChart();
    } else {
        console.error("No data available for the chart");
    }
});

watch(
    () => props.medicalSupplyUsageChart,
    (newData) => {
        adjustChartData(newData);
        setupChart();
    }
);

const adjustChartData = (data) => {
    const startDate = new Date(form.start_date);
    const endDate = new Date(form.end_date);
    const diffTime = Math.abs(endDate - startDate);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (filterType.value === "by_dates") {
        if (diffDays >= 30) {
            const groupedData = groupDataByMonth(data);
            medicalSupplyUsageLabels.value = groupedData.labels;
            medicalSupplyUsageData.value = groupedData.data;
        } else {
            medicalSupplyUsageLabels.value = data.map((item) => item.date);
            medicalSupplyUsageData.value = data.map((item) => item.quantity);
        }
    } else if (filterType.value === "by_medsupply") {
        const supplyData = aggregateSupplyUsage(data);
        medicalSupplyUsageLabels.value = supplyData.labels;
        medicalSupplyUsageData.value = supplyData.data;
    }
};

const groupDataByMonth = (data) => {
    const monthlyData = {};
    data.forEach((item) => {
        const date = new Date(item.date);
        const monthYear = `${date.getFullYear()}-${String(
            date.getMonth() + 1
        ).padStart(2, "0")}`;

        if (!monthlyData[monthYear]) {
            monthlyData[monthYear] = 0;
        }
        monthlyData[monthYear] += item.quantity;
    });

    const labels = Object.keys(monthlyData).map((key) => {
        const [year, month] = key.split("-");
        return new Date(year, month - 1).toLocaleString("default", {
            month: "long",
            year: "numeric",
        });
    });

    const values = Object.values(monthlyData);

    return { labels, data: values };
};

const aggregateSupplyUsage = (data) => {
    const supplyData = {};

    data.forEach((item) => {
        if (item.supplies) {
            Object.keys(item.supplies).forEach((supplyName) => {
                if (!supplyData[supplyName]) {
                    supplyData[supplyName] = 0;
                }
                supplyData[supplyName] += item.supplies[supplyName];
            });
        }
    });

    return {
        labels: Object.keys(supplyData),
        data: Object.values(supplyData),
    };
};

const setupChart = () => {
    const ctx = document
        .getElementById("medicalSupplyUsageChart")
        .getContext("2d");
    if (window.myChart) {
        window.myChart.destroy();
    }

    const vibrantColors = [
        "rgba(0, 0, 0, 0.7)", // Black
        "rgba(54, 162, 235, 0.7)", // Vibrant Blue
        "rgba(255, 206, 86, 0.7)", // Vibrant Yellow
        "rgba(75, 192, 192, 0.7)", // Vibrant Teal
        "rgba(153, 102, 255, 0.7)", // Vibrant Purple
        "rgba(255, 159, 64, 0.7)", // Vibrant Orange
        "rgba(0, 255, 127, 0.7)", // Vibrant Green
        "rgba(255, 99, 132, 0.7)", // Vibrant Red
    ];

    window.myChart = new Chart(ctx, {
        type: selectedChartType.value,
        data: {
            labels: medicalSupplyUsageLabels.value,
            datasets: [
                {
                    label:
                        filterType.value === "by_dates"
                            ? "Medical Supply Consumption byDate"
                            : "Medical Supply Consumption bySupply",
                    data: medicalSupplyUsageData.value,
                    borderColor:
                        selectedChartType.value === "line"
                            ? "rgba(192, 132, 252, 1)"
                            : "rgba(192, 132, 252, 0)", // Updated for bg-violet-300
                    backgroundColor:
                        selectedChartType.value === "pie"
                            ? vibrantColors.slice(
                                  0,
                                  medicalSupplyUsageLabels.value.length
                              ) // Apply vibrant colors
                            : selectedChartType.value === "line"
                            ? "rgba(192, 132, 252, 0.2)" // Updated for bg-violet-300
                            : "rgba(192, 132, 252, 0.7)", // Updated for bg-violet-300
                    borderWidth: 2,
                    tension: selectedChartType.value === "line" ? 0.3 : 0,
                    pointStyle:
                        selectedChartType.value === "line"
                            ? "circle"
                            : undefined,
                    pointRadius: selectedChartType.value === "line" ? 5 : 0,
                    pointHoverRadius:
                        selectedChartType.value === "line" ? 8 : 0,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales:
                selectedChartType.value === "pie"
                    ? {}
                    : {
                          x: {
                              title: {
                                  display: true,
                                  text:
                                      filterType.value === "by_dates"
                                          ? "Dates"
                                          : "Medical Supplies",
                                  font: {
                                      size: 16,
                                      weight: "bold",
                                      style: "italic",
                                  },
                              },
                              ticks: {
                                  align: "center",
                              },
                          },
                          y: {
                              beginAtZero: true,
                              title: {
                                  display: true,
                                  text:
                                      filterType.value === "by_dates"
                                          ? "Quantity"
                                          : "Supply Quantity",
                                  font: {
                                      size: 16,
                                      weight: "bold",
                                      style: "italic",
                                  },
                              },
                          },
                      },
            plugins: {
                legend: {
                    display: selectedChartType.value !== "bar",
                    position: "top",
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return (
                                context.label + ": " + context.raw + " items"
                            );
                        },
                    },
                },
            },
        },
    });
};

const updateChart = () => {
    setupChart();
};

// Define state for sorting
const sortAscending = ref(true);

// Computed property to sort medical supply logs
const sortedMedicalSupplyLogs = computed(() => {
    const sortedLogs = [...props.medicalsupplylog.data];
    return sortedLogs.sort((a, b) => {
        const comparison = a.quantity_difference - b.quantity_difference;
        return sortAscending.value ? comparison : -comparison;
    });
});

// Function to toggle sorting order
const sortByQuantity = () => {
    sortAscending.value = !sortAscending.value;
};

function toggleDropdown() {
    showDropdown.value = !showDropdown.value;
}
</script>

<template>
    <Head title="Medical Supply Consumption Log" />

    <div
        class="relative-container bg-white dark:bg-gray-800 relative overflow-x-auto shadow-md sm:rounded-lg"
    >
        <div
            class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4"
        >
            <div class="w-full md:w-1/2 flex items-center space-x-4">
                <form
                    class="flex items-center flex-grow"
                    @submit.prevent="filterLogs"
                >
                    <label for="simple-search" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none"
                        >
                            <svg
                                aria-hidden="true"
                                class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <input
                            type="search"
                            id="simple-search"
                            class="bg-gray-50 border text-gray-900 rounded-lg focus:ring-blue-500 w-full pl-10 p-2"
                            placeholder="Search"
                            v-model="form.search"
                        />
                    </div>
                    <button
                        type="submit"
                        class="bg-blue-700 hover:bg-blue-800 text-white rounded-lg px-4 py-2 ml-2"
                    >
                        Search
                    </button>
                </form>
                <div class="relative z-10 w-full md:w-auto">
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
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a 1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            />
                        </svg>
                    </button>
                    <div
                        v-if="showDropdown"
                        class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-50 border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                    >
                        <div class="p-4 space-y-4">
                            <select
                                v-model="filterType"
                                @change="filterLogs"
                                class="w-full bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5"
                            >
                                <option value="by_dates">By Dates</option>
                                <option value="by_medsupply">
                                    By Medical Supply
                                </option>
                            </select>
                            <select
                                v-model="form.rhu"
                                class="w-full bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5"
                            >
                                <option value="">Select RHU</option>
                                <option
                                    v-for="rhu in props.rhus"
                                    :key="rhu.id"
                                    :value="rhu.id"
                                >
                                    {{ rhu.rhu_name }}
                                </option>
                            </select>
                            <select
                                v-model="form.barangay"
                                class="w-full bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5"
                            >
                                <option value="">Select Barangay</option>
                                <option
                                    v-for="barangay in props.barangays"
                                    :key="barangay.id"
                                    :value="barangay.id"
                                >
                                    {{ barangay.barangay_name }}
                                </option>
                            </select>
                            <div class="space-y-2">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Date Range</label
                                >
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >From:</label
                                >
                                <input
                                    type="date"
                                    v-model="form.end_date"
                                    class="w-full bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5"
                                />
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >To:</label
                                >
                                <input
                                    type="date"
                                    v-model="form.start_date"
                                    class="w-full bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5"
                                />
                            </div>
                            <div class="space-y-2">
                                <label
                                    for="chartType"
                                    class="block text-sm font-medium text-gray-700"
                                    >Select Chart Type</label
                                >
                                <select
                                    v-model="selectedChartType"
                                    @change="updateChart"
                                    class="w-full border text-gray-900 text-sm rounded-lg p-2.5"
                                >
                                    <option value="bar">Bar Chart</option>
                                    <option value="line">Line Chart</option>
                                    <option value="pie">Pie Chart</option>
                                </select>
                            </div>
                            <button
                                @click="filterLogs"
                                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
                <button
                    id="sortFieldDropdownButton"
                    data-dropdown-toggle="sortFieldDropdown"
                    @click="sortByQuantity"
                    class="flex items-center justify-center py-2.5 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                    type="button"
                >
                    <svg
                        class="w-[24px] h-[24px] text-gray-400"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 20V10m0 10-3-3m3 3 3-3m5-13v10m0-10 3 3m-3-3-3 3"
                        />
                    </svg>
                </button>
            </div>
        </div>
        <div class="h-72 p-4">
            <p
                v-if="filterType === 'by_medsupply'"
                class="text-l text-gray-600 mt-2 text-center font-bold text-black bg-gray-50 dark:bg-gray-700"
            >
                {{ formattedDateRange }}
            </p>
            <canvas id="medicalSupplyUsageChart"></canvas>
        </div>
        <div
            class="relative overflow-x-auto sm:rounded-lg mt-4 hidden md:block"
        >
            <table
                class="w-full text-sm text-left text-gray-500 dark:text-gray-400"
            >
                <thead
                    class="text-xs text-black uppercase bg-gray-50 dark:bg-gray-700"
                >
                    <tr>

                        <th class="px-6 py-3">Medical Supply Name</th>
                        <th class="px-6 py-3">Quantity Used</th>
                        <th class="px-6 py-3">RHU</th>
                        <th class="px-6 py-3">Barangay</th>
                        <th class="px-6 py-3">Date Consumed</th>
                    </tr>
                </thead>
                <tbody v-if="sortedMedicalSupplyLogs.length === 0">
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
                    <tr
                        v-for="(log, index) in sortedMedicalSupplyLogs"
                        :key="index"
                        class="bg-white border-b hover:bg-gray-50"
                    >
                        <td class="px-6 py-4 font-medium text-black">
                            {{ log.med_sup_name }}
                        </td>
                        <td class="px-6 py-4">{{ log.quantity_difference }}</td>
                        <td class="px-6 py-4">{{ log.rhu_name }}</td>
                        <td class="px-6 py-4">{{ log.barangay_name }}</td>
                        <td class="px-6 py-4">{{ log.date_updated }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div
            class="relative overflow-hidden sm:rounded-lg mt-4 block md:hidden"
        >
            <div class="grid grid-cols-1 gap-4">
                <div
                    v-for="(log, index) in sortedMedicalSupplyLogs"
                    :key="index"
                    class="bg-white shadow-md rounded-lg p-4 border"
                >
                    <div class="flex items-start mb-2">
                        <h3 class="font-medium text-black">
                            {{ log.med_sup_name }}
                        </h3>
                    </div>
                    <div class="text-sm text-gray-600">
                        <p>
                            <strong>Quantity Used:</strong>
                            {{ log.quantity_difference }}
                        </p>
                        <p><strong>RHU:</strong> {{ log.rhu_name }}</p>
                        <p>
                            <strong>Barangay:</strong> {{ log.barangay_name }}
                        </p>
                        <p>
                            <strong>Date Consumed:</strong>
                            {{ log.date_updated }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <Pagination :paginator="medicalsupplylog" />
    </div>
</template>
