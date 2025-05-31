<template>
  <input
    type="checkbox"
    :checked="checked"
    @change="$emit('update:checked', $event.target.checked)"
    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
  />
</template>

<script setup>
defineProps({
  checked: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:checked'])
</script> 