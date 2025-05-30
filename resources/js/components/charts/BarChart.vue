<template>
  <div class="relative">
    <canvas ref="chartRef"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
  data: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({})
  }
})

const chartRef = ref(null)
let chart = null

const initChart = () => {
  if (chartRef.value) {
    const ctx = chartRef.value.getContext('2d')
    
    // Destroy existing chart if it exists
    if (chart) {
      chart.destroy()
    }

    // Create new chart
    chart = new Chart(ctx, {
      type: 'bar',
      data: props.data,
      options: {
        ...props.options,
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false
        },
        plugins: {
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false
          },
          legend: {
            position: 'top'
          }
        },
        scales: {
          x: {
            stacked: true,
            grid: {
              display: false
            }
          },
          y: {
            stacked: true,
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.1)'
            }
          }
        }
      }
    })
  }
}

// Watch for changes in data or options
watch(
  () => [props.data, props.options],
  () => {
    initChart()
  },
  { deep: true }
)

// Initialize chart on mount
onMounted(() => {
  initChart()
})

// Clean up chart on component unmount
onBeforeUnmount(() => {
  if (chart) {
    chart.destroy()
  }
})
</script> 