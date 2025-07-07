<template>
  <span :class="badgeClass">
    <slot />
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: v => ['default', 'secondary', 'outline', 'destructive', 'success', 'warning', 'info'].includes(v)
  }
})

const badgeClass = computed(() => {
  switch (props.variant) {
    case 'secondary':
      return 'inline-flex items-center rounded-md bg-gray-100 text-gray-800 px-2 py-0.5 text-xs font-medium ring-1 ring-inset ring-gray-200';
    case 'outline':
      return 'inline-flex items-center rounded-md border border-gray-300 px-2 py-0.5 text-xs font-medium text-gray-700';
    case 'destructive':
      return 'inline-flex items-center rounded-md bg-red-100 text-red-800 px-2 py-0.5 text-xs font-medium';
    case 'success':
      return 'inline-flex items-center rounded-md bg-green-100 text-green-800 px-2 py-0.5 text-xs font-medium';
    case 'warning':
      return 'inline-flex items-center rounded-md bg-yellow-100 text-yellow-800 px-2 py-0.5 text-xs font-medium';
    case 'info':
      return 'inline-flex items-center rounded-md bg-blue-100 text-blue-800 px-2 py-0.5 text-xs font-medium';
    default:
      return 'inline-flex items-center rounded-md bg-gray-900 text-white px-2 py-0.5 text-xs font-medium';
  }
})
</script> 