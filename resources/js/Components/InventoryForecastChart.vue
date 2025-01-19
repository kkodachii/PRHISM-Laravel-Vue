<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    itemsRestock: {
        type: Array,
        required: true
    }
});

const inventoryForecastLabels = ref([]);
const inventoryForecastData = ref([]);
const backgroundColors = ref([]);

onMounted(() => {
    // Ensure itemsRestock is defined and is an array
    if (props.itemsRestock && Array.isArray(props.itemsRestock)) {
        // console.log('Items Restock:', props.itemsRestock); // Debugging

        // Get the top 5 items based on required restock
        const top5Items = props.itemsRestock
            .sort((a, b) => b.required_restock - a.required_restock)
            .slice(0, 5);

        // console.log('Top 5 Items:', top5Items); // Debugging

        // Populate chart labels and data
        inventoryForecastLabels.value = top5Items.map(item => item.name);
        inventoryForecastData.value = top5Items.map(item => item.required_restock);

        // Assign background colors based on item type
        backgroundColors.value = top5Items.map(item => {
            if (item.type === 'medical_supplies') {
                return 'rgba(138, 43, 226, 0.7)'; // Violet for medical supplies
            } else if (item.type === 'medicines') {
                return 'rgba(34, 139, 34, 0.7)'; // Green for medicines
            } else if (item.type === 'equipments') {
                return 'rgba(138, 43, 226, 0.7)'; // Violet for equipment
            } else {
                return 'rgba(169, 169, 169, 0.7)'; // Default gray for unknown types
            }
        });

        // console.log('Background Colors:', backgroundColors.value); // Debugging

        // Initialize the chart
        const inventoryForecastCtx = document.getElementById('inventoryForecastChart').getContext('2d');
        new Chart(inventoryForecastCtx, {
            type: 'bar',
            data: {
                labels: inventoryForecastLabels.value,
                datasets: [{
                    label: 'Required Restock',
                    data: inventoryForecastData.value,
                    backgroundColor: backgroundColors.value,
                    borderColor: backgroundColors.value.map(color => color.replace('0.7', '1')), // Full opacity for borders
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantity',
                            font: {
                                size: 16,
                                weight: 'bold',
                                style: 'italic'
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Items',
                            font: {
                                size: 12,
                                weight: 'bold',
                                style: 'italic'
                            }
                        }
                    }
                }
            }
        });
    } else {
        console.error("itemsRestock is undefined or not an array:", props.itemsRestock);
    }
});
</script>

<template>
  <div class="rounded-lg bg-white shadow h-72">
    <div class="flex justify-between p-4">
      <h6 class="text-xl font-semibold">Inventory Forecast</h6>
      <Link :href="route('predictions.index')" class="text-blue-600 hover:underline">See all</Link>
    </div>
    <div class="h-56 p-4">
      <canvas id="inventoryForecastChart"></canvas>
    </div>
  </div>
</template>
