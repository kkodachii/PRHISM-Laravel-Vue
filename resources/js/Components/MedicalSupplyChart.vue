<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  medicalSupplyUsage: Object
});

const medicalSupplyUsageLabels = ref([]);
const medicalSupplyUsageData = ref([]);

onMounted(() => {
  medicalSupplyUsageLabels.value = Object.keys(props.medicalSupplyUsage).reverse();
  medicalSupplyUsageData.value = Object.values(props.medicalSupplyUsage).reverse();

  const medicalSupplyUsageCtx = document.getElementById('medicalSupplyUsageChart').getContext('2d');
  new Chart(medicalSupplyUsageCtx, {
    type: 'line',
    data: {
      labels: medicalSupplyUsageLabels.value,
      datasets: [{
        label: '(Quantity Consumed)',
        data: medicalSupplyUsageData.value,
        borderColor: 'rgba(192, 132, 252, 1)', // Updated for bg-violet-300
        backgroundColor: 'rgba(192, 132, 252, 0.2)', // Updated for bg-violet-300
        borderWidth: 2,
        tension: 0.4,
        pointStyle: 'circle',
        pointRadius: 4,
        pointHoverRadius: 7
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
      <h6 class="text-xl font-semibold">Medical Supply Consumption</h6>
      <Link :href="route('medicalsupplylog.index')" class="text-blue-600 hover:underline">See all</Link>
    </div>
    <div class="h-56 p-4">
      <canvas id="medicalSupplyUsageChart"></canvas>
    </div>
  </div>
</template>
