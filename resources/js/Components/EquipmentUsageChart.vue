<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  equipmentUsage: Object,
});

const equipmentUsageLabels = ref([]);
const equipmentUsageData = ref([]);

onMounted(() => {
  // Log data for debugging
//   console.log('Equipment Usage Props:', props.equipmentUsage);

  equipmentUsageLabels.value = Object.keys(props.equipmentUsage).reverse();
  equipmentUsageData.value = Object.values(props.equipmentUsage).reverse();

  const equipmentUsageCtx = document.getElementById('equipmentUsageChart').getContext('2d');
  new Chart(equipmentUsageCtx, {
    type: 'line',
    data: {
      labels: equipmentUsageLabels.value,
      datasets: [{
        label: '(Quantity Consumed)',
        data: equipmentUsageData.value,
        borderColor: 'rgba(147, 197, 253, 1)', // Updated to bg-blue-300
        backgroundColor: 'rgba(147, 197, 253, 0.2)', // Updated to bg-blue-300
        borderWidth: 2,
        tension: 0.4,
        hoverBackgroundColor: 'rgba(147, 197, 253, 1)', // Updated to bg-blue-300
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
            text: 'Months',
            font: {
              size: 12,
              weight: 'bold',
              style: 'italic'
            }
          }
        }
      },
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
      }
    }
  });
});

</script>

<template>
  <div class="rounded-lg bg-white shadow h-72">
    <div class="flex justify-between p-4">
      <h6 class="text-xl font-semibold">Equipment Consumption</h6>
      <Link :href="route('equipmentlog.index')" class="text-blue-600 hover:underline">See all</Link>
    </div>
    <div class="h-56 p-4">
      <canvas id="equipmentUsageChart"></canvas>
    </div>
  </div>
</template>
