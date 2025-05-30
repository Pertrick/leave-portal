<template>
  <div>
    <input
      type="text"
      ref="input"
      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
      :placeholder="placeholder"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Select date range'
  }
})

const emit = defineEmits(['update:modelValue'])

const input = ref(null)
let flatpickrInstance = null

onMounted(() => {
  flatpickrInstance = flatpickr(input.value, {
    mode: 'range',
    dateFormat: 'Y-m-d',
    defaultDate: props.modelValue ? props.modelValue.split(' to ') : undefined,
    onChange: (selectedDates) => {
      if (selectedDates.length === 2) {
        const startDate = selectedDates[0].toISOString().split('T')[0]
        const endDate = selectedDates[1].toISOString().split('T')[0]
        emit('update:modelValue', `${startDate} to ${endDate}`)
      }
    }
  })
})

watch(() => props.modelValue, (newValue) => {
  if (flatpickrInstance && newValue) {
    flatpickrInstance.setDate(newValue.split(' to '))
  }
})

onUnmounted(() => {
  if (flatpickrInstance) {
    flatpickrInstance.destroy()
  }
})
</script> 