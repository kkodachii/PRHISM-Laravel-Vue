<script setup>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  medicineUsage: Object
});

const medicineUsageLabels = ref([]);
const medicineUsageData = ref([]);

onMounted(() => {
  medicineUsageLabels.value = Object.keys(props.medicineUsage).reverse();
  medicineUsageData.value = Object.values(props.medicineUsage).reverse();

  const medicineUsageCtx = document.getElementById('medicineUsageChart').getContext('2d');
  new Chart(medicineUsageCtx, {
    type: 'line',
    data: {
      labels: medicineUsageLabels.value,
      datasets: [{
        label: '(Quantity Consumed)',
        data: medicineUsageData.value,
        borderColor: 'rgba(110, 231, 183, 1)', // Updated border color
        backgroundColor: 'rgba(110, 231, 183, 0.2)', // Updated background color
        borderWidth: 2,
        tension: 0.4,
        pointStyle: 'circle',
        pointRadius: 5,
        pointHoverRadius: 8
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
      <h6 class="text-xl font-semibold">Medicine Consumption</h6>
      <Link :href="route('medicineusagelog.index')" class="text-blue-600 hover:underline">See all</Link>
    </div>
    <div class="h-56 p-4">
      <canvas id="medicineUsageChart"></canvas>
    </div>
  </div>
</template>
